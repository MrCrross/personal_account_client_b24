<template>
    <AppLayout>
        <Head :title="role ? role.title : 'Роль'"/>

        <AdminLayout>
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="list().url">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    К ролям
                </Link>
            </Button>

            <div v-if="isLoading" class="flex justify-center py-12">
                <LoaderCircle class="h-6 w-6 animate-spin" />
            </div>

            <RoleDetails v-else-if="role" :role="role" @edit="openEditModal" />

            <div v-else class="py-12 text-center">
                <Label>Роль не найдена.</Label>
            </div>
        </AdminLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import {AppLayout, AdminLayout} from "@/layouts";
import {Head, Link} from "@inertiajs/vue3";
import {ArrowLeft, LoaderCircle} from "@lucide/vue";
import {onMounted, ref} from "vue";
import {Button, Label, RoleDetails, RoleForm} from "@/components";
import {list, view} from "@/routes/roles";
import {Roles} from "@/features";
import {useBreadcrumbStore, useModalStore} from "@/store";
import type {Role} from "@/types";

interface Props {
    roleId: number
}
const props = defineProps<Props>()

const modalStore = useModalStore()
const breadcrumbsStore = useBreadcrumbStore()
breadcrumbsStore.setBreadcrumbs([
    {title: 'Администрирование', href: '/admin'},
    {title: 'Роли', href: list().url},
    {title: 'Роль', href: view(props.roleId).url},
])

const role = ref<Role | null>(null)
const isLoading = ref<boolean>(true)

onMounted(async () => {
    await loadRole()
})

const loadRole = async () => {
    isLoading.value = true
    try {
        role.value = await Roles.getById(props.roleId)
        breadcrumbsStore.updateLastBreadcrumb({title: role.value.title, href: view(props.roleId).url})
    } catch {
        // Тост покажет интерсептор; оставляем пустое состояние
        role.value = null
    } finally {
        isLoading.value = false
    }
}

const openEditModal = () => {
    modalStore.openModal(RoleForm, {
        title: 'Редактирование роли',
        componentProps: {
            role: role.value,
            onSaved: handleRoleSaved,
        }
    })
}

const handleRoleSaved = () => {
    modalStore.closeModal()
    loadRole()
}
</script>
