<template>
    <form
        @submit.prevent="filterSubmit"
    >
        <Card class="w-full border-2 border-radius">
            <CardHeader>
                <CardTitle class="text-lg">Фильтры</CardTitle>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                        <div class="md:col-span-2">
                            <Label class="text-sm font-medium">Поиск (номер заказа или название)</Label>
                            <Input v-model="filterForm.search" placeholder="Например, 123 или часть названия"/>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Тип обращения</Label>
                            <TaskProblemTypeSelect v-model="filterForm.problemType"/>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Статус</Label>
                            <TaskStatusSelect v-model="filterForm.status"/>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Приоритет</Label>
                            <InputList v-model="filterForm.priority" :api-feature="TaskPriorities"/>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Телефон</Label>
                            <InputPhone v-model="filterForm.phone" />
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Дата обращения</Label>
                            <DateRangePicker
                                :model-value="(filterForm.dateRange as DateRange | undefined)"
                                @update:model-value="(v: DateRange | undefined) => (filterForm.dateRange = v)"
                            />
                        </div>
                    </div>
                    <Button type="submit" variant="default">
                        Применить
                    </Button>
                </CardContent>
            </CardHeader>
        </Card>
    </form>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {
    Button,
    TaskProblemTypeSelect,
    Label,
    DateRangePicker,
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    TaskStatusSelect, InputPhone, InputList, Input
} from "@/components";
import {useDateFormat} from "@/composables";
import {SelectOption, TaskQueryParams} from "@/types";
import {TaskPriorities} from "@/features";
import {type DateRange} from "reka-ui";

interface Props {
    load?: (page: number, query: TaskQueryParams) => void
}
const props = defineProps<Props>()

interface Form {
    search: string,
    problemType: SelectOption | null,
    status: SelectOption | null,
    priority: SelectOption | null,
    phone: string,
    dateRange: DateRange | undefined
}

const filterForm = ref<Form>({
    search: '',
    problemType: null,
    status: null,
    priority: null,
    phone: '',
    dateRange: undefined,
});

const filterSubmit = async () => {
    const dateFormat = useDateFormat();
    const filterValue = filterForm.value;
    const formData: TaskQueryParams = {}

    const trimmedSearch = filterValue.search.trim()
    if (trimmedSearch) {
        formData.search = trimmedSearch
    }
    if (filterValue.problemType) {
        formData.problem_type_id = Number(filterValue.problemType.value)
    }
    if (filterValue.status) {
        formData.status_id = Number(filterValue.status.value)
    }
    if (filterValue.priority) {
        formData.priority_id = Number(filterValue.priority.value)
    }
    if (filterValue.phone) {
        formData.person_phone = filterValue.phone
    }
    if (filterValue.dateRange?.start) {
        formData.date_start = dateFormat.formatFromCalendar(filterValue.dateRange.start)
    }
    if (filterValue.dateRange?.end) {
        formData.date_end = dateFormat.formatFromCalendar(filterValue.dateRange.end)
    }

    props.load?.(1, formData)
}
</script>

<style scoped>

</style>
