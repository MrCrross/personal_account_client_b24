<script setup lang="ts">
import {
    DateRange,
    DateRangePickerArrow,
    DateRangePickerCalendar,
    DateRangePickerCell,
    DateRangePickerCellTrigger,
    DateRangePickerContent,
    DateRangePickerField,
    DateRangePickerGrid,
    DateRangePickerGridBody,
    DateRangePickerGridHead,
    DateRangePickerGridRow,
    DateRangePickerHeadCell,
    DateRangePickerHeader,
    DateRangePickerHeading,
    DateRangePickerInput,
    DateRangePickerNext,
    DateRangePickerPrev,
    DateRangePickerRoot,
    DateRangePickerTrigger
} from 'reka-ui'
import { Calendar, ChevronLeft, ChevronRight } from 'lucide-vue-next'

interface Props {
    modelValue?: DateRange,
    placeholder?: string,
    disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: undefined,
    placeholder: 'Выберите период...',
    disabled: false
})

const emit = defineEmits<{
    'update:modelValue': [value: DateRange | undefined]
}>()

const handleUpdate = (value: DateRange | undefined) => {
    emit('update:modelValue', value)
}
</script>

<template>
    <DateRangePickerRoot
        :model-value="modelValue"
        locale="ru"
        :week-starts-on="1"
        :disabled="disabled"
        @update:model-value="handleUpdate"
    >
        <DateRangePickerField
            v-slot="{ segments }"
            class="flex h-10 w-full items-center rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        >
            <template v-for="item in segments.start" :key="item.part">
                <DateRangePickerInput
                    v-if="item.part === 'literal'"
                    :part="item.part"
                    type="start"
                    class="literal"
                >
                    {{ item.value }}
                </DateRangePickerInput>
                <DateRangePickerInput
                    v-else
                    :part="item.part"
                    type="start"
                    class="rounded-md p-0.5 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 data-[placeholder]:text-muted-foreground"
                >
                    {{ item.value }}
                </DateRangePickerInput>
            </template>

            <span class="mx-2 text-muted-foreground">-</span>

            <template v-for="item in segments.end" :key="item.part">
                <DateRangePickerInput
                    v-if="item.part === 'literal'"
                    :part="item.part"
                    type="end"
                    class="literal"
                >
                    {{ item.value }}
                </DateRangePickerInput>
                <DateRangePickerInput
                    v-else
                    :part="item.part"
                    type="end"
                    class="rounded-md p-0.5 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 data-[placeholder]:text-muted-foreground"
                >
                    {{ item.value }}
                </DateRangePickerInput>
            </template>

            <DateRangePickerTrigger
                class="ml-auto focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 rounded-md p-1 hover:bg-accent"
                :disabled="disabled"
            >
                <Calendar class="w-4 h-4 opacity-50" />
            </DateRangePickerTrigger>
        </DateRangePickerField>

        <DateRangePickerContent
            :side-offset="4"
            class="z-50 rounded-md border bg-popover text-popover-foreground shadow-md will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade"
        >
            <DateRangePickerArrow class="fill-popover stroke-border" />

            <DateRangePickerCalendar
                v-slot="{ weekDays, grid }"
                class="p-4"
            >
                <DateRangePickerHeader class="flex items-center justify-between mb-4">
                    <DateRangePickerPrev
                        class="inline-flex items-center justify-center rounded-md w-7 h-7 hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    >
                        <ChevronLeft class="w-4 h-4" />
                    </DateRangePickerPrev>

                    <DateRangePickerHeading class="text-sm font-medium" />

                    <DateRangePickerNext
                        class="inline-flex items-center justify-center rounded-md w-7 h-7 hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    >
                        <ChevronRight class="w-4 h-4" />
                    </DateRangePickerNext>
                </DateRangePickerHeader>

                <div class="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                    <DateRangePickerGrid
                        v-for="month in grid"
                        :key="month.value.toString()"
                        class="w-full border-collapse space-y-1"
                    >
                        <DateRangePickerGridHead>
                            <DateRangePickerGridRow class="flex w-full justify-between mb-2">
                                <DateRangePickerHeadCell
                                    v-for="day in weekDays"
                                    :key="day"
                                    class="w-8 text-xs font-normal text-muted-foreground text-center"
                                >
                                    {{ day }}
                                </DateRangePickerHeadCell>
                            </DateRangePickerGridRow>
                        </DateRangePickerGridHead>

                        <DateRangePickerGridBody>
                            <DateRangePickerGridRow
                                v-for="(weekDates, index) in month.rows"
                                :key="`weekDate-${index}`"
                                class="flex w-full justify-between mb-1"
                            >
                                <DateRangePickerCell
                                    v-for="weekDate in weekDates"
                                    :key="weekDate.toString()"
                                    :date="weekDate"
                                    class="relative calendar-cell"
                                >
                                    <DateRangePickerCellTrigger
                                        :day="weekDate"
                                        :month="month.value"
                                        class="relative flex items-center justify-center rounded-full w-8 h-8 text-sm font-normal outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover:bg-accent hover:text-accent-foreground data-[outside-month]:text-muted-foreground data-[selected]:bg-primary data-[selected]:text-primary-foreground data-[highlighted]:bg-accent data-[highlighted]:text-accent-foreground data-[unavailable]:pointer-events-none data-[unavailable]:text-muted-foreground data-[unavailable]:line-through data-[today]:before:content-[''] data-[today]:before:absolute data-[today]:before:top-0.5 data-[today]:before:right-0.5 data-[today]:before:w-1 data-[today]:before:h-1 data-[today]:before:rounded-full data-[today]:before:bg-primary"
                                    />
                                </DateRangePickerCell>
                            </DateRangePickerGridRow>
                        </DateRangePickerGridBody>
                    </DateRangePickerGrid>
                </div>
            </DateRangePickerCalendar>
        </DateRangePickerContent>
    </DateRangePickerRoot>
</template>

<style scoped>
/* Более конкретные селекторы для подсветки диапазона */
:deep(button[data-in-range]) {
    background-color: hsl(var(--accent)) !important;
    color: hsl(var(--accent-foreground)) !important;
}

:deep(button[data-range-start]) {
    background-color: hsl(var(--primary)) !important;
    color: hsl(var(--primary-foreground)) !important;
    border-radius: 50% !important;
}

:deep(button[data-range-end]) {
    background-color: hsl(var(--primary)) !important;
    color: hsl(var(--primary-foreground)) !important;
    border-radius: 50% !important;
}

:deep(button[data-selected]:not([data-in-range])) {
    background-color: hsl(var(--primary)) !important;
    color: hsl(var(--primary-foreground)) !important;
    border-radius: 50% !important;
}

/* Стили для непрерывного фона диапазона */
:deep(.calendar-cell[data-in-range]) {
    position: relative;
}

:deep(.calendar-cell[data-in-range]:not([data-range-start]):not([data-range-end]))::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 32px;
    background-color: hsl(var(--accent));
    transform: translateY(-50%);
    z-index: -1;
}

:deep(.calendar-cell[data-range-start])::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    right: 0;
    height: 32px;
    background-color: hsl(var(--accent));
    transform: translateY(-50%);
    z-index: -1;
}

:deep(.calendar-cell[data-range-end])::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 50%;
    height: 32px;
    background-color: hsl(var(--accent));
    transform: translateY(-50%);
    z-index: -1;
}
</style>
