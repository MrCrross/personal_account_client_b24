import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import {BreadcrumbItem} from "@/types";

export const useBreadcrumbStore = defineStore('breadcrumbs', () => {
    const items = ref<BreadcrumbItem[]>([])

    // Геттер для текущих breadcrumbs
    const breadcrumbs = computed(() => items.value)

    // Добавить breadcrumb
    const addBreadcrumb = (breadcrumb: BreadcrumbItem) => {
        // Проверяем, нет ли уже такого breadcrumb
        const existingIndex = items.value.findIndex(item =>
            item.title === breadcrumb.title && item.href === breadcrumb.href
        )

        if (existingIndex === -1) {
            items.value.push(breadcrumb)
        }
    }

    // Добавить несколько breadcrumbs
    const addBreadcrumbs = (newBreadcrumbs: BreadcrumbItem[]) => {
        newBreadcrumbs.forEach(breadcrumb => addBreadcrumb(breadcrumb))
    }

    // Обновить breadcrumbs (заменить полностью)
    const setBreadcrumbs = (newBreadcrumbs: BreadcrumbItem[]) => {
        items.value = newBreadcrumbs
    }

    // Удалить breadcrumb по индексу
    const removeBreadcrumb = (index: number) => {
        if (index >= 0 && index < items.value.length) {
            items.value.splice(index, 1)
        }
    }

    // Удалить breadcrumb по label
    const removeBreadcrumbByLabel = (label: string) => {
        const index = items.value.findIndex(item => item.title === label)
        if (index !== -1) {
            removeBreadcrumb(index)
        }
    }

    // Очистить все breadcrumbs
    const clearBreadcrumbs = () => {
        items.value = []
    }

    // Обновить последний breadcrumb
    const updateLastBreadcrumb = (breadcrumb: BreadcrumbItem) => {
        if (items.value.length > 0) {
            items.value[items.value.length - 1] = breadcrumb
        } else {
            addBreadcrumb(breadcrumb)
        }
    }

    return {
        breadcrumbs,
        addBreadcrumb,
        addBreadcrumbs,
        setBreadcrumbs,
        removeBreadcrumb,
        removeBreadcrumbByLabel,
        clearBreadcrumbs,
        updateLastBreadcrumb
    }
})
