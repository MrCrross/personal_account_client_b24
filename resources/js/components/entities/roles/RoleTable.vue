<template>
    <LoaderCircle v-if="isLoading" class="h-8 w-8 animate-spin mx-auto my-8" />
    <DataTable
        v-else-if="roles.length"
        :columns="columns"
        :data="roles"
        :selectable="true"
        class="border rounded-lg"
    >
        <template #cell-permissions="{ item }">
            <Badge
                variant="default"
                v-for="permission in item.permissions"
                :key="permission.id"
            >
                {{permission.title}}
            </Badge>
        </template>
        <template #cell-created_at="{ item }">
            <span class="text-sm">
                {{ formatDate.formatDateTime(item.created_at) }}
            </span>
        </template>

        <!-- Слот для действий -->
        <template #cell-actions="{ item }">
            <div class="flex gap-2 justify-end">
                <Button
                    variant="ghost"
                    size="sm"
                    class="h-8 w-8 p-0"
                    @click="openEditModal(item)"
                >
                    <PencilIcon class="w-4 h-4" />
                </Button>
            </div>
        </template>
    </DataTable>
    <div v-else class="text-center py-12 text-muted-foreground">
        <p class="text-lg font-medium">Данные отсутствуют</p>
    </div>
</template>

<script setup lang="ts">
import {Badge, Button, DataTable, RoleForm} from "@/components";
import {LoaderCircle, PencilIcon} from 'lucide-vue-next'
import {Role, TableColumn} from "@/types";
import { onMounted, ref} from "vue";
import {Roles} from "@/features";
import {useModalStore} from "@/store";
import {useDateFormat} from "@/composables";

const formatDate = useDateFormat()
const modalStore = useModalStore()

const isLoading = ref(true)
const roles = ref([])

const columns: TableColumn[] = [
    {
        key: 'id',
        label: 'ID',
        sortable: true,
        width: '80px'
    },
    {
        key: 'title',
        label: 'Название',
        sortable: true
    },
    {
        key: 'name',
        label: 'Техническое название',
        sortable: true
    },
    {
        key: 'guard_name',
        label: 'Уровень ответственности',
        sortable: true,
        headerClass: 'truncate max-w-1/12',
    },
    {
        key: 'permissions',
        label: 'Права доступа',
        slot: true,
        cellClass: 'flex gap-1 flex-wrap'
    },
    {
        key: 'created_at',
        label: 'Дата создания',
        sortable: true,
        align: 'center'
    },
    {
        key: 'actions',
        label: 'Действия',
        align: 'center'
    },
]
onMounted(async () => {
    await loadRoles()
})

const loadRoles = async () => {
    roles.value = await Roles.getList()
    isLoading.value = false
}

const openEditModal = (role: any) => {
    modalStore.openModal(RoleForm, {
        title: 'Редактирование роли',
        componentProps: {
            role,
            onSaved: handleUserSaved
        }
    })
}

const handleUserSaved = (updatedData: Role) => {
    modalStore.closeModal()

    if (updatedData && updatedData.id) {
        // Обновляем существующего пользователя или добавляем нового
        const index = roles.value.findIndex(r => r.id === updatedData.id)
        if (index > -1) {
            // Обновляем существующего
            roles.value[index] = updatedData
        } else {
            // Добавляем нового в начало
            roles.value.push(updatedData)
        }
    } else {
        isLoading.value = true
        // Если что-то пошло не так, перезагружаем весь список
        loadRoles()
    }
}
</script>

<style scoped>

</style>
