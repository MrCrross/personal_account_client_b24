<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <!-- width/height — реальные пропорции файла (квадрат), итоговый размер задаёт родитель через class -->
    <img
        src="/src/img/logo.png"
        :class="cn('object-contain', className)"
        v-bind="$attrs"
        alt="Логотип"
        width="166"
        height="166"
    >
</template>
