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
                            <Label class="text-sm font-medium">Поиск (ID, ФИО или email)</Label>
                            <Input v-model="filterForm.search" placeholder="Например, 12, Иванов или ivanov@mail.ru"/>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Роль</Label>
                            <InputList v-model="filterForm.role" :data="roleOptions"/>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Компания в Bitrix24</Label>
                            <Autocomplete
                                v-model="filterForm.companyId"
                                placeholder="Поиск компании..."
                                :api-feature="BCompanies"
                            />
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Дата создания</Label>
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
import {onMounted, ref} from "vue";
import {
    Autocomplete,
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    DateRangePicker,
    Input,
    InputList,
    Label
} from "@/components";
import {useDateFormat} from "@/composables";
import {SelectOption, UserQueryParams} from "@/types";
import {BCompanies, Roles} from "@/features";
import {type DateRange} from "reka-ui";

interface Props {
    load?: (page: number, query: UserQueryParams) => void
}
const props = defineProps<Props>()

interface Form {
    search: string,
    role: SelectOption | null,
    companyId: number | string | null,
    dateRange: DateRange | undefined
}

const filterForm = ref<Form>({
    search: '',
    role: null,
    companyId: null,
    dateRange: undefined,
});
const roleOptions = ref<SelectOption<number>[]>([])

onMounted(async () => {
    const roles = await Roles.getAll()
    roleOptions.value = roles.map(role => ({value: role.id, label: role.title}))
})

const filterSubmit = async () => {
    const dateFormat = useDateFormat();
    const filterValue = filterForm.value;
    const formData: UserQueryParams = {}

    const trimmedSearch = filterValue.search.trim()
    if (trimmedSearch) {
        formData.search = trimmedSearch
    }
    if (filterValue.role) {
        formData.role_id = Number(filterValue.role.value)
    }
    if (filterValue.companyId) {
        formData.b_company_id = Number(filterValue.companyId)
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
