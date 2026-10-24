<template>
    <AppLayout>
        <Head :title="user ? user.name : 'Пользователь'"/>

        <AdminLayout>
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="list().url">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    К пользователям
                </Link>
            </Button>

            <div v-if="isLoading" class="flex justify-center py-12">
                <LoaderCircle class="h-6 w-6 animate-spin" />
            </div>

            <UserDetails v-else-if="user" :user="user" @edit="openEditModal" />

            <div v-else class="py-12 text-center">
                <Label>Пользователь не найден.</Label>
            </div>
        </AdminLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import {AppLayout, AdminLayout} from "@/layouts";
import {Head, Link} from "@inertiajs/vue3";
import {ArrowLeft, LoaderCircle} from "@lucide/vue";
import {onMounted, ref} from "vue";
import {Button, Label, UserDetails, UserForm} from "@/components";
import {list, view} from "@/routes/users";
import {Users} from "@/features";
import {useBreadcrumbStore, useModalStore} from "@/store";
import type {User} from "@/types";

interface Props {
    userId: number
}
const props = defineProps<Props>()

const modalStore = useModalStore()
const breadcrumbsStore = useBreadcrumbStore()
breadcrumbsStore.setBreadcrumbs([
    {title: 'Администрирование', href: '/admin'},
    {title: 'Пользователи', href: list().url},
    {title: 'Пользователь', href: view(props.userId).url},
])

const user = ref<User | null>(null)
const isLoading = ref<boolean>(true)

onMounted(async () => {
    await loadUser()
})

const loadUser = async () => {
    isLoading.value = true
    try {
        user.value = await Users.getById(props.userId)
        breadcrumbsStore.updateLastBreadcrumb({title: user.value.name, href: view(props.userId).url})
    } catch {
        // Тост покажет интерсептор; оставляем пустое состояние
        user.value = null
    } finally {
        isLoading.value = false
    }
}

const openEditModal = () => {
    modalStore.openModal(UserForm, {
        title: 'Редактирование пользователя',
        componentProps: {
            user: user.value,
            onSaved: handleUserSaved,
        }
    })
}

const handleUserSaved = () => {
    modalStore.closeModal()
    // Перечитываем целиком: в ответе на сохранение могут не быть подгружены группа и компания
    loadUser()
}
</script>
