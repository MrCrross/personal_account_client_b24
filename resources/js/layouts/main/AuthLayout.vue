<script setup lang="ts">
import {AuthSimpleLayout as AuthLayout} from '@/layouts';
import {ToastContainer, ModalProvider} from "@/components";

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description">
        <slot />
        <ToastContainer />
        <ModalProvider />
    </AuthLayout>
</template>
