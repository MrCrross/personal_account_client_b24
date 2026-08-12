<script setup lang="ts">
import {ref, onMounted} from 'vue'
import {BaseSelect} from '@/components'
import {API} from '@/services'
import type {SelectOption, TaskStatus} from "@/types"

interface Props {
    modelValue?: SelectOption | null
    placeholder?: string
    disabled?: boolean
}

interface Emits {
    (e: 'update:modelValue', value: SelectOption | null): void
}

withDefaults(defineProps<Props>(), {
    modelValue: null,
    placeholder: 'Выберите статус...',
    disabled: false
})

const emit = defineEmits<Emits>()

const options = ref<SelectOption[]>([])
const isLoading = ref(false)

const loadTaskStatuses = async () => {
    try {
        isLoading.value = true
        const response = await API.tasks.statuses.getAll()
        options.value = (response.data.data ?? []).map((item: TaskStatus) => ({
            value: item.id,
            label: item.name ?? '',
        }))
    } catch (error) {
        console.error('Ошибка загрузки статуса:', error)
        options.value = []
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    loadTaskStatuses()
})
</script>

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
