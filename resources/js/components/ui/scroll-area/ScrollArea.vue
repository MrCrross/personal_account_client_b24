<script setup lang="ts">
import {
    ScrollAreaRoot,
    ScrollAreaViewport,
    ScrollAreaScrollbar,
    ScrollAreaThumb,
    ScrollAreaCorner
} from 'reka-ui'
import { computed } from 'vue'

const props = defineProps<{
    class?: string
    viewportClass?: string
}>()

const viewportClasses = computed(() => [
    'h-full w-full rounded-[inherit]',
    props.viewportClass
])
</script>

<template>
    <ScrollAreaRoot
        class="relative overflow-hidden"
        :class="props.class"
    >
        <ScrollAreaViewport :class="viewportClasses">
            <slot />
        </ScrollAreaViewport>

        <!-- Вертикальный скроллбар -->
        <ScrollAreaScrollbar
            orientation="vertical"
            class="flex touch-none select-none p-0.5 transition-colors duration-150 ease-out hover:bg-muted/20 data-[orientation=vertical]:w-3"
        >
            <ScrollAreaThumb class="relative flex-1 rounded-full bg-border/60 transition-colors duration-150 hover:bg-border/80" />
        </ScrollAreaScrollbar>

        <!-- Горизонтальный скроллбар -->
        <ScrollAreaScrollbar
            orientation="horizontal"
            class="flex touch-none select-none p-0.5 transition-colors duration-150 ease-out hover:bg-muted/20 data-[orientation=horizontal]:h-3"
        >
            <ScrollAreaThumb class="relative flex-1 rounded-full bg-border/60 transition-colors duration-150 hover:bg-border/80" />
        </ScrollAreaScrollbar>

        <ScrollAreaCorner class="bg-border" />
    </ScrollAreaRoot>
</template>
