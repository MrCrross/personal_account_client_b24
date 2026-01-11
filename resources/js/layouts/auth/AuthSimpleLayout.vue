<script setup lang="ts">
import AppLogoIcon from '@/components/shared/menu/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-gray-background p-6 md:p-10">
        <div class="w-full max-w-sm bg-background border-gray-background rounded-2xl p-4">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="home()" class="flex flex-col items-center gap-2 font-medium">
                        <div class="mb-1 flex h-12 w-24 items-center justify-center rounded-md">
                            <AppLogoIcon class="fill-current text-[var(--foreground)] dark:text-white" />
                        </div>
                        <span v-if="title" class="sr-only">{{ title }}</span>
                    </Link>
                    <div v-if="title && description" class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">{{ description }}</p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
