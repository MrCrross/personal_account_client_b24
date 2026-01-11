<template>
    <div
        :class="cn(
      'border border-input flex flex-col w-full rounded-md bg-transparent shadow-xs transition-colors',
      'focus-within:border-ring focus-within:ring-ring/50 focus-within:ring-[3px]',
      props.class,
    )"
    >
        <!-- Кастомная панель инструментов -->
        <div :class="cn('flex items-center gap-1 p-2 border-b border-input')">
            <!-- Заголовки -->
            <div class="flex items-center gap-1">
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground',
            currentHeader === 1 && 'bg-accent text-accent-foreground'
          )"
                    @click="setHeader(1)"
                    title="Заголовок 1"
                    type="button"
                >
                    <Heading1 :size="16"/>
                </button>
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground',
            currentHeader === 2 && 'bg-accent text-accent-foreground'
          )"
                    @click="setHeader(2)"
                    title="Заголовок 2"
                    type="button"
                >
                    <Heading2 :size="16"/>
                </button>
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground',
            currentHeader === 3 && 'bg-accent text-accent-foreground'
          )"
                    @click="setHeader(3)"
                    title="Заголовок 3"
                    type="button"
                >
                    <Heading3 :size="16"/>
                </button>
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground',
            currentHeader === false && 'bg-accent text-accent-foreground'
          )"
                    @click="editor?.chain().focus().setParagraph().run()"
                    title="Обычный текст"
                    type="button"
                >
                    <Pilcrow :size="16"/>
                </button>
            </div>

            <div class="w-px h-6 bg-input mx-2"/>

            <!-- Форматирование текста -->
            <div class="flex items-center gap-1">
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground',
            isBold && 'bg-accent text-accent-foreground'
          )"
                    @click="toggleBold"
                    title="Жирный"
                    type="button"
                >
                    <Bold :size="16"/>
                </button>
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground',
            isItalic && 'bg-accent text-accent-foreground'
          )"
                    @click="toggleItalic"
                    title="Наклонный"
                    type="button"
                >
                    <Italic :size="16"/>
                </button>
            </div>

            <div class="w-px h-6 bg-input mx-2"/>

            <!-- Списки -->
            <div class="flex items-center gap-1">
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground',
            isOrderedList && 'bg-accent text-accent-foreground'
          )"
                    @click="toggleOrderedList"
                    title="Нумерованный список"
                    type="button"
                >
                    <ListOrdered :size="16"/>
                </button>
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground',
            isBulletList && 'bg-accent text-accent-foreground'
          )"
                    @click="toggleBulletList"
                    title="Маркированный список"
                    type="button"
                >
                    <List :size="16"/>
                </button>
            </div>

            <div class="w-px h-6 bg-input mx-2"/>

            <!-- Очистка -->
            <div class="flex items-center gap-1">
                <button
                    :class="cn(
            'w-8 h-8 p-1.5 rounded-md flex items-center justify-center border-none bg-transparent cursor-pointer transition-colors',
            'text-foreground hover:bg-accent hover:text-accent-foreground'
          )"
                    @click="clearFormatting"
                    title="Очистить форматирование"
                    type="button"
                >
                    <Eraser :size="16"/>
                </button>
            </div>
        </div>

        <!-- Редактор -->
        <EditorContent :editor="editor" class="min-h-[120px]"/>
    </div>
</template>
<script setup lang="ts">
import {onBeforeUnmount, ref, watch} from 'vue'
import type {HTMLAttributes} from 'vue'
import {useEditor, EditorContent} from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import {cn} from '@/lib/utils'
import {
    Bold,
    Italic,
    List,
    ListOrdered,
    Heading1,
    Heading2,
    Heading3,
    Eraser,
    Pilcrow
} from 'lucide-vue-next'

const props = defineProps<{
    defaultValue?: string
    modelValue?: string
    class?: HTMLAttributes['class']
    placeholder?: string
}>()

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string): void
}>()

// Реактивные состояния для кнопок
const isBold = ref(false)
const isItalic = ref(false)
const isOrderedList = ref(false)
const isBulletList = ref(false)
const currentHeader = ref<number | false>(false)

const editor = useEditor({
    content: props.modelValue || props.defaultValue || '',
    extensions: [
        StarterKit,
        Placeholder.configure({
            placeholder: props.placeholder || 'Введите текст...',
        }),
    ],
    editorProps: {
        attributes: {
            class: 'focus:outline-none min-h-[120px] p-3',
        },
    },
    onUpdate: ({editor}) => {
        const html = editor.getHTML()
        emits('update:modelValue', html)
        updateButtonStates()
    },
    onCreate: () => {
        updateButtonStates()
    },
    onSelectionUpdate: () => {
        updateButtonStates()
    },
})

const updateButtonStates = () => {
    if (!editor.value) return

    isBold.value = editor.value.isActive('bold')
    isItalic.value = editor.value.isActive('italic')
    isOrderedList.value = editor.value.isActive('orderedList')
    isBulletList.value = editor.value.isActive('bulletList')

    // Проверяем заголовки
    if (editor.value.isActive('heading', {level: 1})) {
        currentHeader.value = 1
    } else if (editor.value.isActive('heading', {level: 2})) {
        currentHeader.value = 2
    } else if (editor.value.isActive('heading', {level: 3})) {
        currentHeader.value = 3
    } else {
        currentHeader.value = false
    }
}

// Методы для форматирования
const toggleBold = () => {
    editor.value?.chain().focus().toggleBold().run()
}

const toggleItalic = () => {
    editor.value?.chain().focus().toggleItalic().run()
}

const toggleOrderedList = () => {
    editor.value?.chain().focus().toggleOrderedList().run()
}

const toggleBulletList = () => {
    editor.value?.chain().focus().toggleBulletList().run()
}

const setHeader = (level: number) => {
    if (currentHeader.value === level) {
        editor.value?.chain().focus().setParagraph().run()
    } else {
        editor.value?.chain().focus().setHeading({level: level as 1 | 2 | 3}).run()
    }
}

const clearFormatting = () => {
    editor.value?.chain().focus().clearNodes().unsetAllMarks().run()
}

// Синхронизация с внешними изменениями modelValue
watch(() => props.modelValue, (newValue) => {
    if (!editor.value) return

    const currentContent = editor.value.getHTML()
    if (newValue !== currentContent) {
        editor.value.commands.setContent(newValue || '', false)
        updateButtonStates()
    }
})

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy()
    }
})
</script>

<style>
/* Стили для TipTap */
.ProseMirror {
    padding: 12px;
    outline: none;
    min-height: 120px;
}

.ProseMirror p {
    margin: 0;
    padding: 0;
}

.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: hsl(var(--muted-foreground));
    pointer-events: none;
    height: 0;
}

.ProseMirror h1 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.ProseMirror h2 {
    font-size: 1.25rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.ProseMirror h3 {
    font-size: 1.125rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.ProseMirror ul, .ProseMirror ol {
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}

.ProseMirror ul {
    list-style-type: disc;
}

.ProseMirror ol {
    list-style-type: decimal;
}

.ProseMirror li {
    margin: 0.25rem 0;
}

.ProseMirror strong {
    font-weight: bold;
}

.ProseMirror em {
    font-style: italic;
}
</style>
