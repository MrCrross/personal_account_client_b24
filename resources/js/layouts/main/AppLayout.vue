<script setup lang="ts">
import {AppHeaderLayout as AppLayout} from '@/layouts';
import {ToastContainer, ModalProvider, CookiesNotify} from "@/components";
import {useBreadcrumbStore} from "@/store";

const breadcrumbsStore = useBreadcrumbStore();
breadcrumbsStore.clearBreadcrumbs()
breadcrumbsStore.addBreadcrumb({
    title: 'Главная',
    href: '/',
})
</script>

<template>
    <AppLayout>
        <slot/>
        <ToastContainer/>
        <ModalProvider/>
        <CookiesNotify/>
    </AppLayout>
</template>
