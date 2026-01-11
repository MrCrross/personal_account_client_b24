import { defineStore } from 'pinia'
import {ref, type Component, markRaw} from 'vue'
import {ModalOptions} from "@/types";

export const useModalStore = defineStore('modal', () => {
    const isOpen = ref(false)
    const modalComponent = ref<Component | undefined>() // Изменим на undefined вместо null
    const modalOptions = ref<ModalOptions>({})

    const openModal = (component: Component, options: ModalOptions = {}) => {
        modalComponent.value = markRaw(component)
        modalOptions.value = {
            ...options,
            componentProps: options.componentProps || {}
        }
        isOpen.value = true
    }

    const closeModal = () => {
        isOpen.value = false
        setTimeout(() => {
            modalComponent.value = undefined // Изменим на undefined
            modalOptions.value = {}
        }, 300)
    }

    return {
        isOpen,
        modalComponent,
        modalOptions,
        openModal,
        closeModal
    }
})
