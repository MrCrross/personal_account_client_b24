<script setup lang="ts">
import {ref, onMounted, watch} from 'vue'
import {BaseSelect} from '@/components'
import type {ProblemType} from "@/types";
import {TaskProblemTypes} from "@/features";
import {SelectOption} from "@/types";

interface Props {
    modelValue?: number | string | null
    placeholder?: string
    disabled?: boolean
    data?: ProblemType[] | undefined
}

interface Emits {
    (e: 'update:modelValue', value: number | string | null): void
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    placeholder: 'Выберите тип проблемы...',
    disabled: false
})

const emit = defineEmits<Emits>()

const options = ref<SelectOption[]>([])
const isLoading = ref(false)

// Загрузка типов проблем
const loadProblemTypes = async () => {
    try {
        isLoading.value = true
        options.value = await TaskProblemTypes.getOptions();
    } catch (error) {
        console.error('Ошибка загрузки типов проблем:', error)
        options.value = []
    } finally {
        isLoading.value = false
    }
}

// Загружаем данные при монтировании
onMounted(() => {
    loadProblemTypes()
})

// Можно добавить обновление при изменении пропсов, если нужно
watch(() => props.disabled, (newValue) => {
    if (!newValue && options.value.length === 0) {
        loadProblemTypes()
    }
})
</script>

<template>
    <div class="relative">
        <BaseSelect
            :model-value="modelValue"
            :options="options"
            :placeholder="placeholder"
            :disabled="disabled || isLoading"
            @update:model-value="emit('update:modelValue', $event)"
        />
    </div>
</template>

<style scoped>

</style>
