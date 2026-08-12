<template>
    <VsSelect
        :model-value="modelValue"
        :options="options"
        :placeholder="placeholder"
        :disabled="disabled"
        :searchable="searchable"
        :clearable="clearable"
        @update:model-value="handleChange"
    />
</template>

<script setup lang="ts">
import VsSelect from 'vs-vue3-select'
import type { SelectOption } from '@/types'

interface Props {
    modelValue?: SelectOption | null
    options?: SelectOption[]
    placeholder?: string
    disabled?: boolean
    searchable?: boolean
    clearable?: boolean
    class?: string
}

interface Emits {
    (e: 'update:modelValue', value: SelectOption | null): void
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    options: () => [],
    placeholder: 'Выберите значение...',
    disabled: false,
    searchable: false,
    clearable: true
})

const emit = defineEmits<Emits>()

const handleChange = (option: SelectOption) => {
    if (option === null) {
        emit('update:modelValue', null)
        return
    }

    emit('update:modelValue', option)
}
</script>
