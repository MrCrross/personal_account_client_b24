<!-- components/Toast/ToastItem.vue -->
<script setup lang="ts">
import { ToastClose, ToastDescription, ToastRoot, ToastTitle } from 'reka-ui'
import { CheckCircle, Info, AlertCircle, AlertTriangle } from 'lucide-vue-next'
import type { ToastType } from '@/composables/components/useToast'

const ToastTypeClasses: Record<ToastType, string> = {
    'success': 'border-l-4 border-l-green-500 dark:border-l-green-400',
    'info': 'border-l-4 border-l-blue-500 dark:border-l-blue-400',
    'error': 'border-l-4 border-l-red-500 dark:border-l-red-400',
    'warning': 'border-l-4 border-l-yellow-500 dark:border-l-yellow-400'
}

const ToastTypeIcons = {
    'success': CheckCircle,
    'info': Info,
    'error': AlertCircle,
    'warning': AlertTriangle
}

const ToastTypeIconColors: Record<ToastType, string> = {
    'success': 'text-green-500 dark:text-green-400',
    'info': 'text-blue-500 dark:text-blue-400',
    'error': 'text-red-500 dark:text-red-400',
    'warning': 'text-yellow-500 dark:text-yellow-400'
}

const props = defineProps<{
    id: number
    title?: string
    content: string
    type: ToastType
    duration?: number
    closeable?: boolean
    position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left'
    icon?: string
}>()

const emit = defineEmits<{
    (e: 'close', id: number): void
}>()

const handleClose = () => {
    emit('close', props.id)
}

const IconComponent = ToastTypeIcons[props.type]
</script>

<template>
    <ToastRoot
        :duration="duration || 3000"
        :class="[
      'w-full p-4 rounded-r-lg shadow-lg bg-background dark:bg-background-dark',
      'text-foreground dark:text-foreground-dark',
      'transition-all duration-300 ease-in-out my-2',
      ToastTypeClasses[type]
    ]"
        @escape-key-down="handleClose"
    >
        <div class="flex items-start gap-3">
            <component
                :is="IconComponent"
                :class="[
          'w-5 h-5 mt-0.5 flex-shrink-0',
          ToastTypeIconColors[type]
        ]"
            />

            <div class="flex-1 min-w-0">
                <ToastTitle v-if="title" class="font-semibold text-sm mb-1">
                    {{ title }}
                </ToastTitle>
                <ToastDescription class="text-sm text-muted-foreground dark:text-muted-foreground-dark">
                    {{ content }}
                </ToastDescription>
            </div>

            <ToastClose
                v-if="closeable"
                class="ml-2 flex-shrink-0 rounded-lg hover:bg-accent dark:hover:bg-accent-dark p-1.5 transition-colors text-muted-foreground dark:text-muted-foreground-dark hover:text-foreground dark:hover:text-foreground-dark"
                aria-label="Close"
                @click="handleClose"
            >
                <span aria-hidden="true" class="text-lg font-semibold">×</span>
            </ToastClose>
        </div>
    </ToastRoot>
</template>
