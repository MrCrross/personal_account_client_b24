<script setup lang="ts">
import AppContent from '@/components/shared/app/AppContent.vue';
import AppShell from '@/components/shared/menu/AppShell.vue';
import AppSidebar from '@/components/shared/menu/AppSidebar.vue';
import AppSidebarHeader from '@/components/shared/menu/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
