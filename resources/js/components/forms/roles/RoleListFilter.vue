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
                            <Label class="text-sm font-medium">Поиск (название или техническое название)</Label>
                            <Input v-model="filterForm.search" placeholder="Например, Администратор или admin"/>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Уровень ответственности</Label>
                            <InputList v-model="filterForm.guard" :data="guardOptions"/>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Право доступа</Label>
                            <InputList v-model="filterForm.permission" :data="permissionOptions"/>
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
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    Input,
    InputList,
    Label
} from "@/components";
import {RoleQueryParams, SelectOption} from "@/types";
import {Roles} from "@/features";

interface Props {
    load?: (page: number, query: RoleQueryParams) => void
}
const props = defineProps<Props>()

interface Form {
    search: string,
    guard: SelectOption | null,
    permission: SelectOption | null,
}

const guardOptions: SelectOption<string>[] = [
    {value: 'api', label: 'API'},
    {value: 'web', label: 'Страницы'},
]

const filterForm = ref<Form>({
    search: '',
    guard: null,
    permission: null,
});
const permissionOptions = ref<SelectOption<number>[]>([])

onMounted(async () => {
    const permissions = await Roles.getPermissions()
    permissionOptions.value = permissions.map(permission => ({
        value: permission.id,
        label: `${permission.title} (${permission.guard_name})`,
    }))
})

const filterSubmit = async () => {
    const filterValue = filterForm.value;
    const formData: RoleQueryParams = {}

    const trimmedSearch = filterValue.search.trim()
    if (trimmedSearch) {
        formData.search = trimmedSearch
    }
    if (filterValue.guard) {
        formData.guard_name = String(filterValue.guard.value)
    }
    if (filterValue.permission) {
        formData.permission_id = Number(filterValue.permission.value)
    }

    props.load?.(1, formData)
}
</script>

<style scoped>

</style>
