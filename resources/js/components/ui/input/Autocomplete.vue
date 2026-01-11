<template>
    <div class="autocomplete-container relative">
        <div class="relative">
            <Input
                v-model="searchQuery"
                :placeholder="placeholder"
                :disabled="disabled"
                class="w-full pr-8"
                @focus="isOpen = true"
                @blur="handleBlur"
                @keydown.esc="isOpen = false"
                @keydown.down="highlightNext"
                @keydown.up="highlightPrev"
                @keydown.enter="selectHighlighted"
            />

            <!-- Индикатор загрузки -->
            <div
                v-if="isLoading"
                class="absolute right-2 top-1/2 transform -translate-y-1/2"
            >
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
            </div>

            <!-- Кнопка очистки -->
            <Button
                v-else-if="searchQuery && !isLoading"
                variant="ghost"
                size="sm"
                class="absolute right-1 top-1/2 transform -translate-y-1/2 h-6 w-6 p-0"
                @click="clearSelection"
            >
                ×
            </Button>
        </div>

        <!-- Dropdown с опциями -->
        <div
            v-if="isOpen && filteredOptions.length > 0"
            class="absolute z-50 w-full mt-1 bg-secondary border border-border rounded-md shadow-lg max-h-60 overflow-y-auto"
        >
            <ul class="py-1">
                <li
                    v-for="(option, index) in filteredOptions"
                    :key="option.value"
                    :class="[
                        'px-3 py-2 cursor-pointer transition-colors',
                        highlightedIndex === index
                            ? 'bg-primary text-primary-foreground'
                            : 'hover:bg-secondary'
                    ]"
                    @mousedown="selectOption(option)"
                    @mouseenter="highlightedIndex = index"
                >
                    {{ option.label }}
                </li>
            </ul>
        </div>

        <!-- Сообщение, если ничего не найдено -->
        <div
            v-if="isOpen && !isLoading && filteredOptions.length === 0 && searchQuery"
            class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg"
        >
            <div class="p-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                Ничего не найдено
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, computed } from 'vue'
import { Input, Button } from '@/components'
import ApiService from "@/services/service";
import {Autocomplete} from "@/types";

const MIN_QUERY_LENGTH = 2;
export interface Option {
    value: number | string
    label: string
    [key: string]: any
}
interface ApiFeature {
    autocomplete(term: string): Promise<Autocomplete[]>
}

interface Props {
    modelValue?: number | string | null
    placeholder?: string
    disabled?: boolean
    debounceTime?: number
    apiEndpoint?: string // Опционально: кастомный API endpoint
    apiFeature?: ApiFeature
    value?: Option
}

interface Emits {
    (e: 'update:modelValue', value: number | string | null): void
    (e: 'selected', option: Option): void // Новое событие при выборе
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    placeholder: 'Выберите значение...',
    disabled: false,
    debounceTime: 500,
    apiEndpoint: '',
})

const emit = defineEmits<Emits>()

const searchQuery = ref('')
const isOpen = ref(false)
const highlightedIndex = ref(-1)
const isLoading = ref(false)
const options = ref<Option[]>([])
const searchTimeout = ref<number | null>(null)
const lastSearchQuery = ref<string | null>('')
const isSelecting = ref(false)

// Фильтрованные опции (можно добавить локальную фильтрацию)
const filteredOptions = computed(() => {
    return options.value
})

const debounce = (func: Function, delay: number) => {
    return (...args: any[]) => {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        searchTimeout.value = setTimeout(() => func(...args), delay)
    }
}

// Debounced версия функции поиска
const debouncedSearch = debounce((query: string) => {
    if (query && query.length >= MIN_QUERY_LENGTH && query !== lastSearchQuery.value) {
        lastSearchQuery.value = query
        fetchOptions(query)
    }
}, props.debounceTime)

// Поиск при изменении запроса
watch(searchQuery, (newQuery) => {
    if (isSelecting.value) {
        isSelecting.value = false
        return
    }
    highlightedIndex.value = -1

    if (newQuery && newQuery.length >= MIN_QUERY_LENGTH) {
        // Используем debounced функцию
        debouncedSearch(newQuery)
    } else if (!newQuery || newQuery.length === 0) {
        // Очищаем при пустом запросе
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
            searchTimeout.value = null
        }
        options.value = []
        isOpen.value = false
        emit('update:modelValue', null)
        lastSearchQuery.value = ''
    }
})

// Запрос к API
const fetchOptions = async (query: string) => {
    if (!query || query.length < 2) return

    isLoading.value = true
    isOpen.value = true

    try {
        let responseData = []
        // Используем стандартный API для групп или кастомный endpoint
        if (props.apiEndpoint !== '') {
            const response = await ApiService.get(props.apiEndpoint, { term: query });
            responseData = response.data.data;
        } else if (props.apiFeature) {
            responseData = await props.apiFeature.autocomplete(query);
        }

        options.value = responseData
    } catch (error) {
        options.value = []
    } finally {
        isLoading.value = false
    }
}

// Навигация стрелками
const highlightNext = () => {
    if (filteredOptions.value.length > 0) {
        highlightedIndex.value = Math.min(highlightedIndex.value + 1, filteredOptions.value.length - 1)
    }
}

const highlightPrev = () => {
    if (filteredOptions.value.length > 0) {
        highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0)
    }
}

// Выбор выделенного элемента Enter
const selectHighlighted = () => {
    if (highlightedIndex.value >= 0 && filteredOptions.value[highlightedIndex.value]) {
        selectOption(filteredOptions.value[highlightedIndex.value])
    }
}

// Обработчик выбора опции
const selectOption = (option: Option) => {
    isSelecting.value = true
    searchQuery.value = option.label
    emit('update:modelValue', option.value)
    emit('selected', option)
    isOpen.value = false
    highlightedIndex.value = -1

    // Очищаем таймер если он есть
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
        searchTimeout.value = null
    }

    // Сбрасываем флаг после небольшой задержки
    setTimeout(() => {
        isSelecting.value = false
    }, 100)
}

// Очистка выбора
const clearSelection = () => {
    isSelecting.value = true
    searchQuery.value = ''
    options.value = []
    emit('update:modelValue', null)
    isOpen.value = false
    highlightedIndex.value = -1

    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
        searchTimeout.value = null
    }

    setTimeout(() => {
        isSelecting.value = false
    }, 100)
}

// Обработчик потери фокуса с задержкой
const handleBlur = () => {
    setTimeout(() => {
        isOpen.value = false
    }, 200)
}

// Закрытие dropdown при клике вне компонента
const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement
    if (!target.closest('.autocomplete-container')) {
        isOpen.value = false
    }
}

onMounted(async () => {
    document.addEventListener('click', handleClickOutside)
    if (props.value && props.value.label) {
        options.value = [props.value]
        selectOption(options.value[0])
        emit('update:modelValue', props.value.value)
    }
})

// Очистка
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }
})
</script>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
