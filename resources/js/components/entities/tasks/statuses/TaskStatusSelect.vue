<template>
    <div class="relative">
        <BaseSelect
            :model-value="modelValue"
            :options="options"
            :placeholder="placeholder"
            :disabled="disabled || isLoading"
            :searchable="true"
            @update:model-value="emit('update:modelValue', $event)"
        />
    </div>
</template>

<script setup lang="ts">
import {ref, onMounted,} from 'vue'
import {BaseSelect} from '@/components'
import { API } from '@/services'
import type {ProblemType} from "@/types";

export interface SelectOption {
    value: number | string
    label: string
}

interface Props {
    modelValue?: number | string | null
    placeholder?: string
    disabled?: boolean
}

interface Emits {
    (e: 'update:modelValue', value: number | string | null): void
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    placeholder: 'Выберите статус...',
    disabled: false
})

const emit = defineEmits<Emits>()

const options = ref<SelectOption[]>([])
const isLoading = ref(false)

// Загрузка типов проблем
const loadTaskStatuses = async () => {
    try {
        isLoading.value = true
        const response = await API.tasks.statuses.getAll()
        options.value = (response.data.data || []).map((item: ProblemType) => ({
            value: item.id,
            label: item.name
        }))
    } catch (error) {
        console.error('Ошибка загрузки статуса:', error)
        options.value = []
    } finally {
        isLoading.value = false
    }
}

// Загружаем данные при монтировании
onMounted(() => {
    loadTaskStatuses()
})
</script>

<style scoped>

</style>
