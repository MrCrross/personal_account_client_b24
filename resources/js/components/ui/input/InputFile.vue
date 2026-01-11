<template>
    <div :class="cn('space-y-4', props.class)">
        <!-- Drop zone -->
        <Card
            :class="cn(
        'border-2 border-dashed cursor-pointer transition-colors',
        'hover:border-primary hover:bg-primary/5',
        isDragging && !props.disabled ? 'border-primary bg-primary/10' : 'border-muted',
        props.disabled ? 'cursor-not-allowed opacity-50' : ''
      )"
            @drop="handleDrop"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @click="triggerFileInput"
        >
            <CardContent class="gap-0 p-2 text-center">
                <div class="flex flex-col items-center justify-center space-y-3">
                    <Upload class="w-8 h-8 text-muted-foreground" />
                    <div>
                        <p class="font-medium text-sm">
                            {{ label || 'Перетащите файлы сюда или нажмите для выбора' }}
                        </p>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ description || `Поддерживаются: изображения, видео, документы Office (до ${props.maxSize || MAX_SIZE_MB_DEFAULT}MB)` }}
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        class="mt-2"
                        :disabled="props.disabled"
                        @click.stop="triggerFileInput"
                    >
                        Выбрать файлы
                    </Button>
                </div>

                <!-- Нативный input вместо компонента Input -->
                <input
                    ref="inputRef"
                    type="file"
                    :multiple="multiple"
                    :accept="accept || ACCEPTED_TYPES.all"
                    class="hidden"
                    :disabled="props.disabled"
                    @change="onInputChange"
                />
            </CardContent>
        </Card>

        <!-- File badges grid (Gmail style) -->
        <div v-if="files.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
            <div
                v-for="(file, index) in files"
                :key="file.id"
                class="relative group"
            >
                <Badge
                    :variant="getFileVariant(file)"
                    class="w-full justify-between pr-6 py-1.5 transition-all group-hover:opacity-80"
                >
                    <div class="flex flex-row items-center gap-1.5 min-w-0 flex-1">
                        <component
                            :is="getFileIcon(file)"
                            class="w-3 h-3 flex-shrink-0"
                        />
                        <p class="truncate text-xs">
                            {{ file.name }}
                        </p>
                        <p v-if="file.size > 0" class="text-xs opacity-70 text-nowrap">
                            {{ formatFileSize(file.size) }}
                        </p>
                    </div>
                </Badge>

                <Button
                    v-if="!props.disabled"
                    variant="ghost"
                    size="icon"
                    class="absolute right-1 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity h-5 w-5"
                    @click.stop="removeFile(index)"
                >
                    <X class="w-3 h-3" />
                </Button>
            </div>
        </div>

        <!-- Max files info -->
        <div v-if="props.maxFiles" class="text-xs text-muted-foreground">
            Загружено: {{ files.length }} / {{ props.maxFiles }} файлов
        </div>
    </div>
</template>

<script setup lang="ts">
import type {HTMLAttributes} from 'vue'
import {cn} from '@/lib/utils'
import {ref, watch} from 'vue'
import {Card, CardContent, Badge, Button} from '@/components'
import {Upload, FileText, Image, Video, File, X} from 'lucide-vue-next'
import { useToast } from '@/composables'

interface FileWithPreview {
    id: string
    name: string
    size: number
    type: string
    originalFile?: File
}

const props = defineProps<{
    defaultValue?: File[] | string[]
    modelValue?: File[] | string[]
    class?: HTMLAttributes['class']
    multiple?: boolean
    accept?: string
    maxSize?: number // in MB
    maxFiles?: number
    label?: string
    description?: string
    disabled?: boolean
}>()

const emits = defineEmits<{
    (e: 'update:modelValue', payload: File[] | string[]): void
    (e: 'error', payload: string): void
}>()

const files = ref<FileWithPreview[]>([])
const isDragging = ref(false)
const inputRef = ref<HTMLInputElement>()
const { error: showError } = useToast()

// Поддерживаемые форматы
const ACCEPTED_TYPES = {
    images: 'image/*',
    videos: 'video/*',
    documents: '.doc,.docx,.xls,.xlsx,.ppt,.pptx,.pdf,.txt',
    all: 'image/*,video/*,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.pdf,.txt'
}

const BYTE = 1024
const MB_IN_BYTE = 1048576
const MAX_SIZE_MB_DEFAULT = 10

const MAX_SIZE = props.maxSize ? props.maxSize * MB_IN_BYTE : MAX_SIZE_MB_DEFAULT * MB_IN_BYTE

const getImgId = (): string => {
    return Math.random().toString(36).slice(2, 9);
}

const getFileIcon = (file: FileWithPreview) => {
    if (file.type.startsWith('image/')) return Image
    if (file.type.startsWith('video/')) return Video
    if (file.type.includes('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) return FileText
    if (file.type.includes('excel') || file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) return FileText
    if (file.type.includes('powerpoint') || file.name.endsWith('.ppt') || file.name.endsWith('.pptx')) return FileText
    if (file.name.endsWith('.pdf')) return FileText

    return File
}

const getFileVariant = (file: FileWithPreview): any => {
    if (file.type.startsWith('image/')) return 'info'
    if (file.type.startsWith('video/')) return 'warning'
    if (file.type.includes('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) return 'success'
    if (file.type.includes('excel') || file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) return 'destructive'
    if (file.type.includes('powerpoint') || file.name.endsWith('.ppt') || file.name.endsWith('.pptx')) return 'default'
    if (file.name.endsWith('.pdf')) return 'secondary'

    return 'outline'
}

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 B'

    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(BYTE))
    return (bytes / Math.pow(BYTE, i)).toFixed(i > 0 ? 1 : 0) + ' ' + sizes[i]
}

const validateFile = (file: File): boolean => {
    if (file.size > MAX_SIZE) {
        const errorMessage = `Файл "${file.name}" превышает максимальный размер ${props.maxSize || MAX_SIZE_MB_DEFAULT}MB`
        emits('error', errorMessage)
        showError(errorMessage)
        return false
    }

    const allowedTypes = props.accept ? props.accept.split(',') : ACCEPTED_TYPES.all.split(',')
    const isValidType = allowedTypes.some(type => {
        if (type.includes('*')) {
            const category = type.split('/')[0]
            return file.type.startsWith(category)
        }
        return file.name.toLowerCase().endsWith(type.replace('.', ''))
    })

    if (!isValidType) {
        const errorMessage = `Файл "${file.name}" имеет неподдерживаемый формат`
        emits('error', errorMessage)
        showError(errorMessage)
        return false
    }

    if (props.maxFiles && files.value.length >= props.maxFiles) {
        const errorMessage = `Максимальное количество файлов: ${props.maxFiles}`
        emits('error', errorMessage)
        showError(errorMessage)
        return false
    }

    return true
}

const handleFiles = (newFiles: FileList | null) => {
    if (!newFiles) return

    const fileArray = Array.from(newFiles)
    const validFiles: FileWithPreview[] = []

    for (const file of fileArray) {
        if (validateFile(file)) {
            const fileWithPreview: FileWithPreview = {
                id: getImgId(),
                name: file.name,
                size: file.size,
                type: file.type,
                originalFile: file
            }
            validFiles.push(fileWithPreview)
        }
    }

    if (props.multiple) {
        files.value = [...files.value, ...validFiles]
    } else {
        files.value = validFiles.slice(0, 1)
    }

    emits('update:modelValue', files.value.map(f => f.originalFile || f))
}

const handleDrop = (e: DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    isDragging.value = false
    if (!props.disabled) {
        handleFiles(e.dataTransfer?.files || null)
    }
}

const handleDragOver = (e: DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    if (!props.disabled) {
        isDragging.value = true
    }
}

const handleDragLeave = (e: DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    isDragging.value = false
}

const removeFile = (index: number) => {
    if (props.disabled) return

    files.value.splice(index, 1)
    emits('update:modelValue', files.value.map(f => f.originalFile || f))
}

const triggerFileInput = () => {
    if (!props.disabled && inputRef.value) {
        inputRef.value.click()
    }
}

const onInputChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    handleFiles(target.files)
    // Сбрасываем значение input чтобы можно было выбрать тот же файл снова
    target.value = ''
}

// Преобразуем входные значения в единый формат
const normalizeFiles = (inputFiles: File[] | string[] | undefined): FileWithPreview[] => {
    if (!inputFiles) return []

    return inputFiles.map(item => {
        if (typeof item === 'string') {
            // Для строк создаем базовый объект (предполагаем, что это URL или путь)
            const fileName = item.split('/').pop() || item // Извлекаем имя файла из URL
            return {
                id: getImgId(),
                name: fileName,
                size: 0, // Для строк размер неизвестен
                type: 'unknown'
            }
        } else {
            // Для File объектов
            return {
                id: getImgId(),
                name: item.name,
                size: item.size,
                type: item.type,
                originalFile: item
            }
        }
    })
}

watch(() => props.defaultValue, (newValue) => {
    if (newValue) {
        files.value = normalizeFiles(newValue)
    }
}, { immediate: true })

watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        files.value = normalizeFiles(newValue)
    }
}, { immediate: true })

defineExpose({triggerFileInput})
</script>
