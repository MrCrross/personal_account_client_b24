<template>
    <button
        @click="toggleTheme"
        class="relative p-2 rounded-md transition-all duration-300 hover:bg-primary-foreground cursor-pointer"
        :aria-label="appearance === 'dark' ? 'Переключить на светлую тему' : 'Переключить на темную тему'"
    >
        <!-- Солнышко (светлая тема) -->
        <SunIcon
            v-if="appearance === 'light'"
            class="w-5 h-5 transition-all duration-300"
        />

        <!-- Луна (темная тема) -->
        <MoonIcon
            v-else
            class="w-5 h-5 transition-all duration-300"
        />

        <!-- Анимация переключения -->
        <span
            class="absolute inset-0 flex items-center justify-center opacity-0 scale-150"
            :class="{
                'animate-ping-once': isAnimating
            }"
        />
    </button>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { SunIcon, MoonIcon } from '@lucide/vue';
import { useAppearance } from '@/composables/utils/useAppearance';
import { Appearance } from '@/types';

const { appearance, updateAppearance } = useAppearance();
const isAnimating = ref(false);

const toggleTheme = () => {
    isAnimating.value = true;
    const nextTheme: Appearance = appearance.value === 'dark' ? 'light' : 'dark';
    updateAppearance(nextTheme);

    setTimeout(() => {
        isAnimating.value = false;
    }, 300);
};
</script>

<style scoped>
@keyframes ping-once {
    0% {
        transform: scale(1);
        opacity: 0.7;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-ping-once {
    animation: ping-once 0.3s ease-out;
}
</style>
