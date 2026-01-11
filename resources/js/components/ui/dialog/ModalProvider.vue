<script setup lang="ts">
import { Modal } from '@/components'
import { useModalStore } from '@/store'
import { computed } from "vue";

const modalStore = useModalStore()

const isModalOpen = computed(() => modalStore.isOpen)

const handleOpenChange = (open: boolean) => {
    modalStore.isOpen = open
    if (!open) {
        modalStore.closeModal()
    }
}
</script>

<template>
    <Modal
        :is-open="isModalOpen"
        :title="modalStore.modalOptions.title"
        :description="modalStore.modalOptions.description"
        :size="modalStore.modalOptions.size"
        :component="modalStore.modalComponent"
        :component-props="modalStore.modalOptions.componentProps"
        @update:is-open="handleOpenChange"
        @close="modalStore.closeModal"
    />
</template>
