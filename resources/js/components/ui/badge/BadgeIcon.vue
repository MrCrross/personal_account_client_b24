<script setup lang="ts">
import { computed } from 'vue'
import {
    CheckCircle,
    XCircle,
    AlertCircle,
    Info,
    Clock
} from '@lucide/vue'
import {BadgeProps} from "@/types";
import {Badge} from "@/components";

const props = withDefaults(defineProps<BadgeProps>(), {
    variant: 'default',
    icon: false
})

const variantIcons = {
    success: CheckCircle,
    destructive: XCircle,
    warning: AlertCircle,
    info: Info,
    pending: Clock,
    default: null,
    secondary: null,
    outline: null
}

const IconComponent = computed(() => {
    return props.icon ? variantIcons[props.variant] : null
})
</script>

<template>
  <Badge :variant="variant">
    <component
        v-if="IconComponent"
        :is="IconComponent"
        class="w-3 h-3"
    />
    <slot />
  </Badge>
</template>
