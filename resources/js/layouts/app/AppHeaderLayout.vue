<script setup lang="ts">
import {AppContent, AppHeader, AppShell} from '@/components';
</script>

<template>
    <AppShell class="flex-col">
        <AppHeader />
        <AppContent>
            <slot />
        </AppContent>
    </AppShell>
</template>
