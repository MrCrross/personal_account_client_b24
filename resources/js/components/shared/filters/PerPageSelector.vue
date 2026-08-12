<template>
    <div class="w-20">
        <InputList
            :model-value="modelValue"
            :data="perPageOptions"
            :clearable="false"
            @update:model-value="handleInput"
        />
    </div>
</template>

<script setup lang="ts">
import { InputList, type SelectOption } from '@/components'
import { usePerPage } from '@/composables'

interface Props {
    modelValue?: SelectOption
}

interface Emits {
    (e: 'update:modelValue', value: SelectOption): void
    (e: 'change', value: SelectOption): void
}

defineProps<Props>()
const emit = defineEmits<Emits>()

const { perPageOptions, save } = usePerPage()

// Обработчик изменения значения пользователем
const handleInput = (value: SelectOption | null) => {
    if (!value) {
        return
    }
    save(value)
    emit('update:modelValue', value)
    emit('change', value)
}
</script>
