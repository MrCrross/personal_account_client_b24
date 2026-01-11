<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogOverlay } from '@/components'
import { type Component, computed } from 'vue'
import {cn} from "@/lib/utils";
import {ModalProps} from "@/types";

const props = withDefaults(defineProps<ModalProps>(), {
    isOpen: false,
    size: 'md',
    component: undefined, // Значение по умолчанию
    componentProps: () => ({})
})

const emit = defineEmits<{
    (e: 'update:isOpen', value: boolean): void
    (e: 'close'): void
}>()

const slots = defineSlots<{
    default?: any
    header?: any
    footer?: any
}>()

const sizeClasses = computed(() => {
    const sizes = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        '2xl': 'max-w-2xl',
        '4xl': 'max-w-4xl',
        '6xl': 'max-w-6xl',
        full: 'max-w-[95vw]'
    }
    return sizes[props.size]
})

const handleOpenChange = (open: boolean) => {
    emit('update:isOpen', open)
    if (!open) {
        emit('close')
    }
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="handleOpenChange">
        <DialogOverlay class="bg-transparent" />

        <DialogContent :class="cn('max-h-[90vh] overflow-y-auto', sizeClasses)">
            <!-- Хедер -->
            <DialogHeader v-if="title || description || slots.header">
                <slot name="header">
                    <DialogTitle v-if="title">{{ title }}</DialogTitle>
                    <DialogDescription v-if="description">{{ description }}</DialogDescription>
                </slot>
            </DialogHeader>

            <!-- Тело модалки -->
            <div class="flex-1 py-4">
                <!-- Если передан компонент -->
                <component
                    v-if="component"
                    :is="component"
                    v-bind="componentProps"
                    @close="() => emit('update:isOpen', false)"
                />

                <!-- Если используется слот -->
                <slot v-else />
            </div>

            <!-- Футер (опционально) -->
            <div v-if="slots.footer" class="flex justify-end gap-2 pt-4 border-t">
                <slot name="footer" />
            </div>
        </DialogContent>
    </Dialog>
</template>
