<template>
    <form @submit.prevent="taskEditSubmit" class="flex flex-col gap-4">
        <div class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Название обращения<RequiredMark /></Label>
            <Input v-model="form.name"
                   placeholder="Название..."
                   type="text"
                   maxlength="255"
                   required
            />
        </div>
        <div class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Приоритет<RequiredMark /></Label>
            <InputList v-model="form.priority"
                       :data="priorities"
                       :clearable="false"
                       placeholder="Выберите приоритет..."
            />
        </div>
        <div class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Описание проблемы<RequiredMark /></Label>
            <Editor v-model="form.description" placeholder="Опишите проблему..." />
        </div>
        <p v-if="task.description_files.length" class="text-xs text-muted-foreground">
            Прикреплённые к описанию файлы сохранятся.
        </p>
        <div class="flex justify-end gap-2">
            <Button type="button" variant="secondary" :disabled="isSubmit" @click="emit('cancel')">
                Отмена
            </Button>
            <Button type="submit" variant="default" :disabled="isSubmit || !canSubmit">
                <LoaderCircle v-if="isSubmit" class="h-4 w-4 animate-spin" />
                Сохранить
            </Button>
        </div>
    </form>
</template>

<script setup lang="ts">
import {Button, Editor, Input, InputList, Label, RequiredMark} from "@/components";
import {LoaderCircle} from "@lucide/vue";
import {computed, onMounted, ref} from "vue";
import {TaskPriorities, Tasks} from "@/features";
import type {SelectOption, Task} from "@/types";
import {useBBCode, useToast} from "@/composables";

interface Props {
    task: Task
}
interface Emits {
    (e: 'saved'): void
    (e: 'cancel'): void
}
const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const toast = useToast()
const bbCodeConvert = useBBCode()

interface TaskEditForm {
    name: string
    description: string
    priority: SelectOption<number> | null
}

// Описание хранится в Битриксе в BBCode — редактор работает с HTML
const form = ref<TaskEditForm>({
    name: props.task.title,
    description: bbCodeConvert.convertToHTML(props.task.description || ''),
    priority: {value: props.task.priority.id, label: props.task.priority.name},
})
const priorities = ref<SelectOption<number>[]>([])
const isSubmit = ref<boolean>(false)

const canSubmit = computed<boolean>(() =>
    form.value.name.trim() !== '' && form.value.priority !== null
)

onMounted(async () => {
    priorities.value = await TaskPriorities.getOptions()
})

const taskEditSubmit = async () => {
    if (!form.value.priority) return

    isSubmit.value = true
    try {
        await Tasks.update(props.task.id, {
            name: form.value.name.trim(),
            description: bbCodeConvert.convertToBBCode(form.value.description || ''),
            priority_id: form.value.priority.value,
        })
        toast.success('Изменения сохранены')
        emit('saved')
    } catch {
        // Ошибку уже показал интерсептор axios — форму оставляем открытой с введёнными данными
    } finally {
        isSubmit.value = false
    }
}
</script>
