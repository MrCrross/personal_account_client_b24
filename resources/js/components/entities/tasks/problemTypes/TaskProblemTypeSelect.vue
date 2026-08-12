<script setup lang="ts">
import {ref, onMounted, watch} from 'vue'
import {BaseSelect} from '@/components'
import {TaskProblemTypes} from "@/features"
import type {SelectOption} from "@/types"

interface Props {
    modelValue?: SelectOption | null
    placeholder?: string
    disabled?: boolean
}

interface Emits {
    (e: 'update:modelValue', value: SelectOption | null): void
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    placeholder: 'Выберите тип проблемы...',
    disabled: false
})

const emit = defineEmits<Emits>()

const options = ref<SelectOption[]>([])
const isLoading = ref(false)

const loadProblemTypes = async () => {
    try {
        isLoading.value = true
        options.value = await TaskProblemTypes.getOptions()
    } catch (error) {
        console.error('Ошибка загрузки типов проблем:', error)
        options.value = []
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    loadProblemTypes()
})

// Если родитель разблокировал селект и опций нет — подгружаем
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
