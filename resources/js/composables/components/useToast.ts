import { ref } from 'vue'

export type ToastType = 'success' | 'info' | 'warning' | 'error'

export interface ToastOptions {
    title?: string
    content: string
    type?: ToastType
    duration?: number
    position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left'
    icon?: string
    closeable?: boolean
}

export interface ToastItem extends ToastOptions {
    id: number
}

const toasts = ref<ToastItem[]>([])
let idCounter = 0

export const useToast = () => {
    const show = (options: ToastOptions | string) => {
        const toastOptions: ToastOptions = typeof options === 'string'
            ? { content: options }
            : options

        const toast: ToastItem = {
            id: idCounter++,
            type: 'info',
            duration: 3000,
            position: 'top-right',
            closeable: true,
            ...toastOptions
        }

        toasts.value.push(toast)

        if (toast.duration && toast.duration > 0) {
            setTimeout(() => {
                remove(toast.id)
            }, toast.duration)
        }

        return toast.id
    }

    const remove = (id: number) => {
        const index = toasts.value.findIndex(toast => toast.id === id)
        if (index !== -1) {
            toasts.value.splice(index, 1)
        }
    }

    const clearAll = () => {
        toasts.value = []
    }

    const success = (options: ToastOptions | string) => {
        return show({ ...typeof options === 'string' ? { content: options } : options, type: 'success' })
    }

    const error = (options: ToastOptions | string) => {
        return show({ ...typeof options === 'string' ? { content: options } : options, type: 'error' })
    }

    const warning = (options: ToastOptions | string) => {
        return show({ ...typeof options === 'string' ? { content: options } : options, type: 'warning' })
    }

    const info = (options: ToastOptions | string) => {
        return show({ ...typeof options === 'string' ? { content: options } : options, type: 'info' })
    }

    return {
        toasts,
        show,
        remove,
        clearAll,
        success,
        error,
        warning,
        info
    }
}
