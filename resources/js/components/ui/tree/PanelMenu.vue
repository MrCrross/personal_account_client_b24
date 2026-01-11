<template>
    <TreeRoot
        v-slot="{ flattenItems }"
        :class="cn('bg-background border rounded-lg shadow-sm p-2 text-sm font-medium', containerClass)"
        :items="items"
        :get-key="getItemKey"
        :default-expanded="defaultExpanded"
    >
        <!-- Header slot -->
        <slot name="header" v-if="hasHeaderSlot"/>
        <h2 v-else-if="title" class="font-semibold text-sm text-muted-foreground px-2 pt-1 pb-3">
            {{ title }}
        </h2>

        <!-- Menu items -->
        <TreeItem
            v-for="item in flattenItems"
            v-slot="{ isExpanded }"
            :key="item._id"
            :style="{ 'padding-left': `${item.level * 0.75}rem` }"
            v-bind="item.bind"
            :class="
        cn(
          'flex items-center py-2 px-3 my-0.5 rounded-md outline-none transition-colors',
          'focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background',
          'hover:bg-accent hover:text-accent-foreground',
          'data-[selected]:bg-primary data-[selected]:text-primary-foreground',
          itemClass,
          item.value.disabled && 'opacity-50 cursor-not-allowed'
        )
      "
        >
            <!-- Wrap content in Link for Inertia navigation -->
            <Link
                v-if="shouldUseInertiaLink(item)"
                :href="item.value.href"
                :class="cn('flex items-center flex-1', item.value.disabled && 'pointer-events-none')"
                :preserve-state="true"
                :preserve-scroll="true"
                :only="item.value.only"
            >
                <PanelMenuItem :item="item" :is-expanded="isExpanded"/>
            </Link>

            <!-- Regular content for items without href or with target="_blank" -->
            <div
                v-else
                :class="cn('flex items-center flex-1', item.value.disabled && 'pointer-events-none')"
                @click="!item.value.disabled && handleItemClick(item)"
            >
                <PanelMenuItem :item="item" :is-expanded="isExpanded"/>
            </div>
        </TreeItem>
    </TreeRoot>
</template>

<script setup lang="ts">
import {computed} from 'vue'
import {useSlots} from 'vue'
import {Link} from '@inertiajs/vue3'
import {TreeItem, TreeRoot} from 'reka-ui'
import {cn} from '@/lib/utils'
import type {PanelMenuItemType} from '@/types'
import {PanelMenuItem} from "@/components";

interface Props {
    items: PanelMenuItemType[]
    title?: string
    defaultExpanded?: string[]
    containerClass?: string
    itemClass?: string
    getItemKey?: (item: PanelMenuItemType) => string
    getItemLabel?: (item: any) => string
}

const props = withDefaults(defineProps<Props>(), {
    defaultExpanded: () => [],
    getItemKey: (item: PanelMenuItemType) => item.id || item.label,
    getItemLabel: (item: any) => item.value.label
})

const emit = defineEmits<{
    (e: 'item-click', item: PanelMenuItemType): void
}>()

const slots = useSlots()
const hasHeaderSlot = computed(() => !!slots.header)

const shouldUseInertiaLink = (item: any) => {
    const menuItem = item.value as PanelMenuItemType
    return menuItem.href && !menuItem.disabled && menuItem.target !== '_blank' && !item.hasChildren
}

const handleItemClick = (item: any) => {
    const menuItem = item.value as PanelMenuItemType

    emit('item-click', menuItem)

    // Handle external links
    if (menuItem.href && menuItem.target === '_blank') {
        window.open(menuItem.href, '_blank', 'noopener,noreferrer')
    }
}
</script>

<style scoped>
/* Smooth transitions */
.tree-item {
    transition: all 0.2s ease;
}

/* Improve focus styles */
:focus-visible {
    outline: 2px solid hsl(var(--primary));
    outline-offset: 2px;
}

/* Cursor pointer for clickable items */
:deep(.tree-item:not(.opacity-50)) {
    cursor: pointer;
}
</style>
