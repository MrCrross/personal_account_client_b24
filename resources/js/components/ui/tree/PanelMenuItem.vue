<template>
    <!-- Icon - render any component passed as icon -->
    <component
        :is="item.value.icon"
        v-if="item.value.icon"
        class="h-4 w-4 flex-shrink-0"
    />

    <!-- Content -->
    <div class="pl-2 flex-1 min-w-0">
        <span >{{ item.value.label }}</span>
    </div>

    <!-- Badge -->
    <Badge
        v-if="item.value.badge"
        variant="secondary"
        class="ml-2 text-xs"
    >
        {{ item.value.badge }}
    </Badge>

    <!-- Chevron for expandable items -->
    <ChevronRightIcon
        v-if="item.hasChildren"
        :class="[
      'h-3 w-3 ml-1 transition-transform flex-shrink-0',
      { 'rotate-90': isExpanded }
    ]"
    />
</template>

<script setup lang="ts">
import { Badge } from '@/components'
import { ChevronRightIcon } from '@lucide/vue'

interface Props {
    item: any
    isExpanded?: boolean
}

defineProps<Props>()
</script>
