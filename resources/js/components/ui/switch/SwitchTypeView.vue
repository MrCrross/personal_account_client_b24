<template>
    <SwitchButton
        :model-value="modelValue"
        :options="viewOptions"
        :size="size"
        :disabled="disabled"
        :show-labels="showLabels"
        active-class="bg-primary text-primary-foreground"
        inactive-class="text-muted-foreground hover:bg-muted"
        @update:model-value="handleViewChange"
    />
</template>

<!-- ViewSwitchButton.vue -->
<script setup lang="ts">
import { LayoutGrid, Table } from 'lucide-vue-next'
import {SwitchButton} from '@/components'
import {TypeViewData} from "@/types";

interface Props {
    modelValue?: TypeViewData
    size?: 'sm' | 'md' | 'lg'
    disabled?: boolean
    showLabels?: boolean
}

interface Emits {
    (e: 'update:modelValue', value: TypeViewData): void
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: 'card',
    size: 'md',
    disabled: false,
    showLabels: true
})

const emit = defineEmits<Emits>()

const viewOptions = [
    {
        value: 'card',
        icon: LayoutGrid,
        title: 'Показать в виде карточек'
    },
    {
        value: 'table',
        icon: Table,
        title: 'Показать в виде таблицы'
    }
]

const handleViewChange = (value: string | number) => {
    emit('update:modelValue', value as TypeViewData)
}
</script>
