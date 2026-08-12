<template>
    <Card class="gap-4">
        <CardHeader>
            <CardTitle class="text-lg">Чат ({{ messages.length }})</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
            <p v-if="messages.length === 0" class="text-sm text-muted-foreground">
                Сообщений пока нет. Напишите первое — оно будет отправлено в задачу.
            </p>
            <div v-else class="space-y-3">
                <div
                    v-for="message in messages"
                    :key="message.id"
                    class="border rounded-lg p-3"
                >
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-sm font-medium">{{ message.author.name }}</span>
                        <span class="text-sm text-muted-foreground">
                            {{ dateFormat.formatDateTime(message.created_at) }}
                        </span>
                    </div>
                    <EditorReadonly
                        v-if="message.text"
                        :default-value="sanitizeHtml(message.text)"
                        class="text-foreground"
                    />
                    <div v-if="message.files.length" class="mt-2 space-y-2">
                        <DiskFile v-for="file in message.files" :key="file.id" :file="file" />
                    </div>
                </div>
            </div>

            <form class="space-y-2" @submit.prevent="handleSend">
                <Editor
                    v-model="newMessage"
                    placeholder="Введите сообщение…"
                />

                <!-- Выбранные вложения -->
                <div v-if="files.length" class="flex flex-wrap gap-2">
                    <div
                        v-for="(file, index) in files"
                        :key="index"
                        class="bg-muted/50 inline-flex items-center gap-1.5 rounded-md border px-2 py-1 text-xs"
                    >
                        <Paperclip class="h-3 w-3 shrink-0 text-muted-foreground" />
                        <span class="max-w-[12rem] truncate">{{ file.name }}</span>
                        <button
                            type="button"
                            class="text-muted-foreground hover:text-foreground"
                            title="Убрать файл"
                            @click="removeFile(index)"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        title="Прикрепить файлы"
                        @click="triggerFilePicker"
                    >
                        <Paperclip class="h-4 w-4" />
                    </Button>
                    <input
                        ref="fileInput"
                        type="file"
                        multiple
                        :accept="ACCEPT"
                        class="hidden"
                        @change="onFilesPicked"
                    />

                    <Button type="submit" :disabled="isSending || isEmpty">
                        <LoaderCircle v-if="isSending" class="mr-2 h-4 w-4 animate-spin" />
                        Отправить
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import type {TaskMessage} from '@/types'
import {Button, Card, CardContent, CardHeader, CardTitle, DiskFile, Editor, EditorReadonly} from "@/components";
import {LoaderCircle, Paperclip, X} from "lucide-vue-next";
import {computed, ref} from "vue";
import {Tasks as TasksFeature} from "@/features";
import {useDateFormat, useToast} from "@/composables";
import {sanitizeHtml} from "@/lib/sanitizeHtml";

interface Props {
    taskId: number
    messages: TaskMessage[]
}
interface Emits {
    (e: 'sent', message: TaskMessage): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const dateFormat = useDateFormat()
const toast = useToast()

// Ограничения должны совпадать с TaskMessageStoreRequest на бэкенде
const MAX_FILES = 5
const MAX_SIZE_MB = 15
const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024
const ACCEPT = 'image/*,video/*,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.pdf,.odt,.ods,.odp,.txt,.rtf'

const newMessage = ref<string>('')
const files = ref<File[]>([])
const fileInput = ref<HTMLInputElement | null>(null)
const isSending = ref<boolean>(false)

// Пустой Tiptap отдаёт «<p></p>» — считаем пустым по очищенному от тегов тексту
const isTextEmpty = computed<boolean>(() => {
    return newMessage.value.replace(/<[^>]*>/g, '').replace(/&nbsp;/gi, ' ').trim().length === 0
})

// Нечего отправлять, если нет ни текста, ни вложений
const isEmpty = computed<boolean>(() => isTextEmpty.value && files.value.length === 0)

const triggerFilePicker = () => {
    fileInput.value?.click()
}

const onFilesPicked = (event: Event) => {
    const input = event.target as HTMLInputElement
    for (const file of Array.from(input.files ?? [])) {
        if (files.value.length >= MAX_FILES) {
            toast.error(`Можно прикрепить не более ${MAX_FILES} файлов`)
            break
        }
        if (file.size > MAX_SIZE_BYTES) {
            toast.error(`Файл «${file.name}» превышает ${MAX_SIZE_MB} МБ`)
            continue
        }
        files.value.push(file)
    }
    // Сбрасываем значение, чтобы можно было выбрать тот же файл снова
    input.value = ''
}

const removeFile = (index: number) => {
    files.value.splice(index, 1)
}

const handleSend = async () => {
    if (isEmpty.value || isSending.value) {
        return
    }

    isSending.value = true
    try {
        // Если есть только вложения — текст не отправляем (иначе уехал бы пустой «<p></p>»)
        const text = isTextEmpty.value ? '' : newMessage.value
        const message = await TasksFeature.sendMessage(props.taskId, text, files.value)
        emit('sent', message)
        newMessage.value = ''
        files.value = []
        toast.success('Сообщение отправлено')
    } catch {
        // Тост об ошибке покажет интерсептор; введённый текст и файлы не сбрасываем
    } finally {
        isSending.value = false
    }
}
</script>
