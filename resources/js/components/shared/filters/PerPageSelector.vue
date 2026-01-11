<template>
    <div class="w-20">
        <InputList
            :model-value="internalValue"
            :data="perPageOptions"
            :clearable="false"
            @update:model-value="handleInput"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { InputList, type SelectOption } from '@/components'

interface Props {
    modelValue?: SelectOption
}

interface Emits {
    (e: 'update:modelValue', value: SelectOption): void
    (e: 'change', value: SelectOption): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const STORAGE_KEY = 'per_page_selection'
const perPageOptions = [
    { value: 10, label: '10' },
    { value: 15, label: '15' },
    { value: 25, label: '25' },
    { value: 50, label: '50' },
    { value: 100, label: '100' },
]

const internalValue = ref<SelectOption>(perPageOptions[1])
const isInitialized = ref(false)

// Загрузка значения из localStorage
const loadFromStorage = (): SelectOption | null => {
    try {
        const stored = localStorage.getItem(STORAGE_KEY)
        if (stored) {
            const parsed = JSON.parse(stored)
            // Проверяем, что значение есть в доступных опциях
            const foundOption = perPageOptions.find(opt => opt.value === parsed.value)
            return foundOption || null
        }
    } catch (error) {
        console.warn('Failed to load perPage from localStorage:', error)
    }

    return null
}

// Сохранение значения в localStorage
const saveToStorage = (value: SelectOption) => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(value))
    } catch (error) {
        console.warn('Failed to save perPage to localStorage:', error)
    }
}

// Инициализация значения
const initializeValue = () => {
    const storedValue = loadFromStorage()

    if (storedValue) {
        internalValue.value = storedValue
    } else {
        internalValue.value = perPageOptions[1]
    }

    emit('update:modelValue', internalValue.value)
    isInitialized.value=true
}

// Обработчик изменения значения
const handleInput = (value: SelectOption | null) => {
    if (value) {
        internalValue.value = value
        saveToStorage(value)
        emit('update:modelValue', value)
        if (isInitialized.value) {
            emit('change', value)
        }
    }
}

// Синхронизация с v-model
watch(() => props.modelValue, (newValue) => {
    if (newValue && newValue.value !== internalValue.value?.value) {
        internalValue.value = newValue
        saveToStorage(newValue)
    }
})

// Инициализация при монтировании
onMounted(() => {
    initializeValue()
})
</script>
