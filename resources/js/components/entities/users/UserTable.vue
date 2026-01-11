<template>
    <LoaderCircle v-if="isLoading" class="h-8 w-8 animate-spin mx-auto my-8" />
    <div v-else-if="users.length">
        <div class="flex flex-row items-start justify-between gap-2 mb-2">
            <PerPageSelector
                v-model="perPage"
                @change="handlePerPageChange"
            />
        </div>
        <DataTable
            :columns="columns"
            :data="users"
            :selectable="true"
            class="border rounded-lg"
        >
            <template #cell-roles="{ item }">
                <Badge
                    variant="default"
                    v-for="role in item.roles"
                    :key="role.id"
                >
                    {{role.title}}
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
    </div>

    <div v-else class="text-center py-12 text-muted-foreground">
        <UsersIcon class="h-12 w-12 mx-auto mb-4" />
        <p class="text-lg font-medium">Пользователи не найдены</p>
        <p class="text-sm">Начните с добавления первого пользователя</p>
    </div>
    <div
        v-if="loadMore"
        class="flex justify-center mt-4"
    >
        <Button
            @click="loadUsers()"
            :disabled="isLoadingMore"
            class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
        >
            <LoaderCircle v-if="isLoadingMore" class="h-4 w-4 animate-spin" />
            <span>Загрузить еще</span>
        </Button>
    </div>
</template>

<script setup lang="ts">
import {Badge, Button, DataTable, PerPageSelector, UserForm} from "@/components";
import {LoaderCircle, PencilIcon, UsersIcon} from 'lucide-vue-next'
import {SelectOption, TableColumn, User, UserQueryParams} from "@/types";
import { onMounted, ref} from "vue";
import {Users} from "@/features";
import {useModalStore} from "@/store";
import {useDateFormat} from "@/composables";

const formatDate = useDateFormat()
const modalStore = useModalStore()

const isLoading = ref(true)
const isLoadingMore = ref(false)
const loadMore = ref(true);
const page = ref<number>(0)
const perPage = ref<SelectOption>()
const users = ref([])

const columns: TableColumn[] = [
    {
        key: 'id',
        label: 'ID',
        sortable: true,
        width: '80px'
    },
    {
        key: 'name',
        label: 'ФИО',
        sortable: true
    },
    {
        key: 'email',
        label: 'Email',
        sortable: true
    },
    {
        key: 'roles',
        label: 'Роли',
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
    await loadUsers(0)
    isLoading.value = false
})

const handlePerPageChange = () => {
    page.value = 0
    loadUsers()
}

const loadUsers = async (pageNumber?: number, query?: UserQueryParams) => {
    isLoading.value = true
    isLoadingMore.value = true
    if (!query) {
        query = {}
    }
    if (!pageNumber) {
        page.value++;
    }
    if (pageNumber === 0 || page.value === 1) {
        users.value = []
    }
    query.page = pageNumber || page.value
    query.per_page = perPage.value?.value;
    const data = await Users.getList(query)
    if (data.length === 0 || data.length < perPage.value?.value) {
        loadMore.value = false
    }
    users.value = [...users.value, ...data]
    isLoading.value = false
    isLoadingMore.value = false
}

const openEditModal = (user: any) => {
    modalStore.openModal(UserForm, {
        title:'Редактирование пользователя',
        componentProps: {
            user,
            onSaved: handleUserSaved
        }
    })
}

const handleUserSaved = (updatedUser: User) => {
    modalStore.closeModal()

    if (updatedUser && updatedUser.id) {
        // Обновляем существующего пользователя или добавляем нового
        const index = users.value.findIndex(u => u.id === updatedUser.id)
        if (index > -1) {
            // Обновляем существующего
            users.value[index] = updatedUser
        } else {
            // Добавляем нового в начало
            users.value.push(updatedUser)
        }
    } else {
        isLoading.value = true
        // Если что-то пошло не так, перезагружаем весь список
        loadUsers()
    }
}
</script>

<style scoped>

</style>
