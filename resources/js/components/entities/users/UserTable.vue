<template>
    <UserListFilter :load="loadUsers"/>
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
            @row-click="openCard"
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
                        @click.stop="openEditModal(item)"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </Button>
                </div>
            </template>
        </DataTable>
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
    </div>

    <div v-else class="text-center py-12 text-muted-foreground">
        <UsersIcon class="h-12 w-12 mx-auto mb-4" />
        <p class="text-lg font-medium">Пользователи не найдены</p>
        <p v-if="hasActiveFilters" class="text-sm">Попробуйте изменить параметры фильтра</p>
        <p v-else class="text-sm">Начните с добавления первого пользователя</p>
    </div>
</template>

<script setup lang="ts">
import {Badge, Button, DataTable, PerPageSelector, UserForm, UserListFilter} from "@/components";
import {LoaderCircle, PencilIcon, UsersIcon} from '@lucide/vue'
import {SelectOption, TableColumn, User, UserQueryParams} from "@/types";
import {computed, onMounted, ref} from "vue";
import {Users} from "@/features";
import {useModalStore, } from "@/store";
import {router} from "@inertiajs/vue3";
import {view} from "@/routes/users";
import {useDateFormat, usePerPage} from "@/composables";

const formatDate = useDateFormat()
const modalStore = useModalStore()

const isLoading = ref(true)
const isLoadingMore = ref(false)
const loadMore = ref(false)
const page = ref<number>(1)
const perPage = ref<SelectOption>(usePerPage().read())
const users = ref<User[]>([])
// Текущие фильтры — переиспользуются при смене per_page и подгрузке следующих страниц
const activeQuery = ref<UserQueryParams>({})
const hasActiveFilters = computed<boolean>(() => Object.keys(activeQuery.value).length > 0)

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
    await loadUsers(1)
})

const handlePerPageChange = () => {
    loadUsers(1)
}

const loadUsers = async (pageNumber?: number, query?: UserQueryParams) => {
    // pageNumber задан (первый запрос, фильтр, смена per_page) → сброс списка;
    // pageNumber не задан (кнопка «Загрузить еще») → подгрузка следующей страницы
    const isReset = pageNumber !== undefined

    if (query !== undefined) {
        activeQuery.value = query
    }

    if (isReset) {
        page.value = pageNumber || 1
        loadMore.value = false
        isLoading.value = true
    } else {
        page.value++
        isLoadingMore.value = true
    }

    const response = await Users.getList({
        ...activeQuery.value,
        page: page.value,
        per_page: perPage.value ? Number(perPage.value.value) : undefined,
    })

    users.value = isReset ? response.data : [...users.value, ...response.data]
    loadMore.value = response.has_more

    isLoading.value = false
    isLoadingMore.value = false
}

const openEditModal = (user: User) => {
    modalStore.openModal(UserForm, {
        title: 'Редактирование пользователя',
        componentProps: {
            user,
            onSaved: handleUserSaved
        }
    })
}

const openCard = (user: User) => {
    router.visit(view(user.id).url)
}

const handleUserSaved = (updatedUser: User) => {
    modalStore.closeModal()

    if (!updatedUser || !updatedUser.id) {
        loadUsers(1)
        return
    }

    const index = users.value.findIndex(u => u.id === updatedUser.id)
    if (index > -1) {
        users.value[index] = updatedUser
    } else {
        users.value.push(updatedUser)
    }
}
</script>

<style scoped>

</style>
