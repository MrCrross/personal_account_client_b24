<template>
    <div class="w-full">
        <!-- Table Container -->
        <div
            class="relative overflow-auto border rounded-lg"
            :class="cn([
        'bg-background border-border',
        containerClass
      ])"
        >
            <!-- Table -->
            <table class="w-full text-sm">
                <thead>
                <tr class="bg-muted/50">
                    <th
                        v-for="(column, index) in columns"
                        :key="column.key || index"
                        :class="cn([
                'px-4 py-3 text-left font-medium whitespace-nowrap',
                getAlignmentClass(column.align),
                column.headerClass,
                {
                  'cursor-pointer hover:bg-muted/70 transition-colors': column.sortable,
                  'sticky top-0': fixedHeader
                }
              ])"
                        @click="column.sortable && handleSort(column.key)"
                    >
                        <div class="flex items-center gap-2">
                            <span>{{ column.label }}</span>
                            <span
                                v-if="column.sortable"
                                class="flex flex-col"
                            >
                  <ArrowUpIcon
                      :class="cn([
                      'w-3 h-3',
                      {
                        'text-primary': sortBy === column.key && sortOrder === 'asc',
                        'text-muted-foreground/30': sortBy !== column.key || sortOrder !== 'asc'
                      }
                    ])"
                  />
                  <ArrowDownIcon
                      :class="cn([
                      'w-3 h-3 -mt-1',
                      {
                        'text-primary': sortBy === column.key && sortOrder === 'desc',
                        'text-muted-foreground/30': sortBy !== column.key || sortOrder !== 'desc'
                      }
                    ])"
                  />
                </span>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(item, rowIndex) in sortedData"
                    :key="getRowKey(item, rowIndex)"
                    :class="cn([
              'border-b border-border transition-colors',
              rowClass?.(item, rowIndex),
              {
                'hover:bg-muted/30': !rowClass,
                'cursor-pointer': selectable
              }
            ])"
                    @click="selectable && $emit('row-click', item)"
                >
                    <td
                        v-for="(column, colIndex) in columnsWithSlots"
                        :key="colIndex"
                        :class="cn([
        'px-4 py-3',
        getAlignmentClass(column.align),
        column.cellClass
    ])"
                    >
                        <slot
                            v-if="column.hasSlot"
                            :name="`cell-${column.key}`"
                            :item="item"
                            :value="item[column.key]"
                            :index="rowIndex"
                        />
                        <template v-else>
                            {{ item[column.key] }}
                        </template>
                    </td>
                </tr>
                </tbody>
            </table>

            <!-- Empty State -->
            <div
                v-if="!sortedData.length"
                class="flex flex-col items-center justify-center p-12 text-muted-foreground"
            >
                <div class="text-4xl mb-4">📭</div>
                <p class="text-lg font-medium mb-2">Нет данных</p>
                <p class="text-sm">Данные для отображения отсутствуют</p>
            </div>

            <!-- Loading State -->
            <LoaderCircle v-if="isLoading" class="h-4 w-4 animate-spin" />
        </div>
    </div>
</template>

<script setup lang="ts">
import {computed, ref, useSlots} from 'vue'
import {ArrowUpIcon, ArrowDownIcon, LoaderCircle} from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import {TableColumn} from "@/types";

const slots = useSlots()
interface Props {
    columns: TableColumn[]
    data: any[]
    defaultSort?: string
    defaultOrder?: 'asc' | 'desc'
    rowKey?: string
    isLoading?: boolean
    selectable?: boolean
    fixedHeader?: boolean
    containerClass?: string
    rowClass?: (item: any, index: number) => string
}

const props = withDefaults(defineProps<Props>(), {
    defaultOrder: 'asc',
    rowKey: 'id',
    fixedHeader: false
})

const emit = defineEmits<{
    'row-click': [item: any]
}>()

const sortBy = ref(props.defaultSort || props.columns[0]?.key)
const sortOrder = ref<'asc' | 'desc'>(props.defaultOrder)

const sortedData = computed(() => {
    if (!sortBy.value || !props.data.length) return props.data

    return [...props.data].sort((a, b) => {
        let valueA = a[sortBy.value]
        let valueB = b[sortBy.value]

        if (typeof valueA === 'string') {
            valueA = valueA.toLowerCase()
            valueB = valueB?.toString().toLowerCase()
        }

        if (valueA == null) return -1
        if (valueB == null) return 1

        if (valueA < valueB) return sortOrder.value === 'asc' ? -1 : 1
        if (valueA > valueB) return sortOrder.value === 'asc' ? 1 : -1
        return 0
    })
})

const getRowKey = (item: any, index: number) => {
    return item[props.rowKey] || index
}

const getAlignmentClass = (align?: string) => {
    switch (align) {
        case 'center': return 'text-center'
        case 'right': return 'text-right'
        default: return 'text-left'
    }
}

const handleSort = (columnKey: string) => {
    if (sortBy.value === columnKey) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = columnKey
        sortOrder.value = 'asc'
    }
}
const columnsWithSlots = computed(() => {
    return props.columns.map(column => ({
        ...column,
        hasSlot: column.slot || !!slots[`cell-${column.key}`]
    }))
})
</script>

<style scoped>
/* Естественная прокрутка с полупрозрачными скроллбарами */
.overflow-auto {
    scrollbar-width: thin;
    scrollbar-color: hsl(var(--muted-foreground) / 0.3) transparent;
}

.overflow-auto::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: hsl(var(--muted-foreground) / 0.3);
    border-radius: 4px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--muted-foreground) / 0.5);
}

/* Для темной темы */
.dark .overflow-auto {
    scrollbar-color: hsl(var(--muted-foreground) / 0.4) transparent;
}

.dark .overflow-auto::-webkit-scrollbar-thumb {
    background: hsl(var(--muted-foreground) / 0.4);
}

.dark .overflow-auto::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--muted-foreground) / 0.6);
}
</style>
