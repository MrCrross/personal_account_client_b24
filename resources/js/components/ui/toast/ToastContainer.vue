<!-- components/Toast/ToastContainer.vue -->
<script setup lang="ts">
import ToastItem from './ToastItem.vue'
import { useToast } from '@/composables/components/useToast'
import { ToastProvider, ToastViewport } from 'reka-ui'

const { toasts, remove } = useToast()
</script>

<template>
    <ToastProvider>
        <div class="fixed z-50 pointer-events-none">
            <!-- Top Right -->
            <div class="fixed top-4 right-4 space-y-3 w-80 max-w-full">
                <ToastItem
                    v-for="toast in toasts.filter(t => t.position === 'top-right' || !t.position)"
                    :key="toast.id"
                    v-bind="toast"
                    @close="remove"
                />
            </div>

            <!-- Top Left -->
            <div class="fixed top-4 left-4 space-y-3 w-80 max-w-full">
                <ToastItem
                    v-for="toast in toasts.filter(t => t.position === 'top-left')"
                    :key="toast.id"
                    v-bind="toast"
                    @close="remove"
                />
            </div>

            <!-- Bottom Right -->
            <div class="fixed bottom-4 right-4 space-y-3 w-80 max-w-full">
                <ToastItem
                    v-for="toast in toasts.filter(t => t.position === 'bottom-right')"
                    :key="toast.id"
                    v-bind="toast"
                    @close="remove"
                />
            </div>

            <!-- Bottom Left -->
            <div class="fixed bottom-4 left-4 space-y-3 w-80 max-w-full">
                <ToastItem
                    v-for="toast in toasts.filter(t => t.position === 'bottom-left')"
                    :key="toast.id"
                    v-bind="toast"
                    @close="remove"
                />
            </div>
        </div>

        <!-- ToastViewport должен быть ОДИН на приложение -->
        <ToastViewport
            class="[--viewport-padding:_25px] fixed bottom-0 right-0 top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]"
        />
    </ToastProvider>
</template>
