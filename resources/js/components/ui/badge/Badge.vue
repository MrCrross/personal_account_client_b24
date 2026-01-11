<script setup lang="ts">
import { computed } from 'vue'
import {BadgeProps, BadgeVariant} from "@/types";

const props = withDefaults(defineProps<BadgeProps>(), {
    variant: 'default'
})

const variantClasses = computed(() => {
    const baseClasses = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2'

    const variants: Record<BadgeVariant, string> = {
        default: 'border-transparent bg-primary text-primary-foreground hover:bg-primary/80',
        secondary: 'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80',
        destructive: 'border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80',
        outline: 'border-gray-300 bg-transparent text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800',
        success: 'border-transparent bg-green-500 text-white hover:bg-green-600',
        warning: 'border-transparent bg-yellow-500 text-white hover:bg-yellow-600',
        info: 'border-transparent bg-blue-400 text-white hover:bg-blue-500',
        pending: 'border-transparent bg-orange-500 text-white hover:bg-orange-600'
    }

    return `${baseClasses} ${variants[props.variant]}`
})
</script>

<template>
  <span :class="[variantClasses, props.class]">
    <slot />
  </span>
</template>
