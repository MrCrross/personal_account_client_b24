<template>
    <RoleListFilter :load="loadRoles"/>
    <LoaderCircle v-if="isLoading" class="h-8 w-8 animate-spin mx-auto my-8" />
    <div v-else-if="roles.length">
        <div class="flex flex-row items-start justify-between gap-2 mb-2">
            <PerPageSelector
                v-model="perPage"
                @change="handlePerPageChange"
            />
        </div>
        <DataTable
            :columns="columns"
            :data="roles"
            :selectable="true"
            class="border rounded-lg"
            @row-click="openCard"
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
                @click="loadRoles()"
                :disabled="isLoadingMore"
                class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
                <LoaderCircle v-if="isLoadingMore" class="h-4 w-4 animate-spin" />
                <span>Загрузить еще</span>
            </Button>
        </div>
    </div>
    <div v-else class="text-center py-12 text-muted-foreground">
        <p class="text-lg font-medium">Данные отсутствуют</p>
        <p v-if="hasActiveFilters" class="text-sm">Попробуйте изменить параметры фильтра</p>
    </div>
</template>

<script setup lang="ts">
import {Badge, Button, DataTable, PerPageSelector, RoleForm, RoleListFilter} from "@/components";
import {LoaderCircle, PencilIcon} from 'lucide-vue-next'
import {Role, RoleQueryParams, SelectOption, TableColumn} from "@/types";
import {computed, onMounted, ref} from "vue";
import {Roles} from "@/features";
import {useModalStore} from "@/store";
import {router} from "@inertiajs/vue3";
import {view} from "@/routes/roles";
import {useDateFormat, usePerPage} from "@/composables";

const formatDate = useDateFormat()
const modalStore = useModalStore()

const isLoading = ref(true)
const isLoadingMore = ref(false)
const loadMore = ref(false)
const page = ref<number>(1)
const perPage = ref<SelectOption>(usePerPage().read())
const roles = ref<Role[]>([])
// Текущие фильтры — переиспользуются при смене per_page и подгрузке следующих страниц
const activeQuery = ref<RoleQueryParams>({})
const hasActiveFilters = computed<boolean>(() => Object.keys(activeQuery.value).length > 0)

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
    await loadRoles(1)
})

const handlePerPageChange = () => {
    loadRoles(1)
}

const loadRoles = async (pageNumber?: number, query?: RoleQueryParams) => {
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

    const response = await Roles.getList({
        ...activeQuery.value,
        page: page.value,
        per_page: perPage.value ? Number(perPage.value.value) : undefined,
    })

    roles.value = isReset ? response.data : [...roles.value, ...response.data]
    loadMore.value = response.has_more

    isLoading.value = false
    isLoadingMore.value = false
}

const openEditModal = (role: Role) => {
    modalStore.openModal(RoleForm, {
        title: 'Редактирование роли',
        componentProps: {
            role,
            onSaved: handleRoleSaved
        }
    })
}

const openCard = (role: Role) => {
    router.visit(view(role.id).url)
}

const handleRoleSaved = (updatedData: Role) => {
    modalStore.closeModal()

    if (!updatedData || !updatedData.id) {
        loadRoles(1)
        return
    }

    const index = roles.value.findIndex(r => r.id === updatedData.id)
    if (index > -1) {
        roles.value[index] = updatedData
    } else {
        roles.value.push(updatedData)
    }
}
</script>

<style scoped>

</style>
