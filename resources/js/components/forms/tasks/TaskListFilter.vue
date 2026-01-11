<template>
    <form
        @submit.prevent="filterSubmit"
    >
        <Card class="w-full border-2 border-radius">
            <CardHeader>
                <CardTitle class="text-lg">Фильтры</CardTitle>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
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
                            <DateRangePicker v-model="filterForm.dateRange"/>
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
    TaskStatusSelect, InputPhone, InputList
} from "@/components";
import {useDateFormat} from "@/composables";
import {DateRangePickerInterface, SelectOption, TaskQueryParams} from "@/types";
import {TaskPriorities} from "@/features";

interface Props {
    load?: (page: number, query: TaskQueryParams) => void
}
const props = defineProps<Props>()

interface Form {
    problemType: SelectOption | null,
    status: SelectOption | null,
    priority: SelectOption | null,
    phone: string,
    dateRange: DateRangePickerInterface | undefined
}

const filterForm = ref<Form>({
    problemType: null,
    status: null,
    priority: null,
    phone: '',
    dateRange: undefined,
    term: '',
});

const filterSubmit = async () => {
    const dateFormat = useDateFormat();
    const filterValue = filterForm.value;
    const formData = {
        problem_type_id: filterValue.problemType?.value,
        status_id: filterValue.status?.value,
        priority_id: filterValue.priority?.value,
        date_start: null,
        date_end: null,
        person_phone: filterValue.phone || null
    };
    if (filterValue.dateRange?.start) {
        formData.date_start = dateFormat.formatFromCalendar(filterValue.dateRange.start)
    }
    if (filterValue.dateRange?.end) {
        formData.date_end = dateFormat.formatFromCalendar(filterValue.dateRange.end)
    }
    if (props.load) {
        props.load(1, formData)
    }
}
</script>

<style scoped>

</style>
