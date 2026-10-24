<template>
    <div class="px-4 py-6">
        <Heading title="Администрирование" description="Раздел для управления данными проекта" />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <PanelMenu
                    title="Администрирование"
                    :items="sidebarNavItems"
                />
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl lg:max-w-4xl">
                <section class="space-y-8">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import {Separator, Heading, PanelMenu} from '@/components';
import {Users, ShieldUser, NotebookTabs, ListPlus} from "@lucide/vue";
import {onMounted, Ref, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import {useBreadcrumbStore} from "@/store";
import {PanelMenuItemType} from "@/types";

const page = usePage();
const sidebarNavItems: Ref<PanelMenuItemType[]> = ref([]);

onMounted(() => {
    if (page.props.auth.permissions.includes('users_module')) {
        sidebarNavItems.value.push({
            id: 'users',
            label: 'Пользователи',
            icon: Users,
            children: [
                {
                    id: 'users_list',
                    label: 'Список пользователей',
                    href: '/admin/users',
                    icon: NotebookTabs
                },
                {
                    id: 'users_create',
                    label: 'Создать пользователя',
                    href: '/admin/users/create',
                    icon: ListPlus
                },
            ],
        })
    }
    if (page.props.auth.permissions.includes('roles_module')) {
        sidebarNavItems.value.push({
            id: 'roles',
            label: 'Роли',
            icon: ShieldUser,
            children: [
                {
                    id: 'roles_list',
                    label: 'Список ролей',
                    href: '/admin/roles',
                    icon: NotebookTabs
                },
                {
                    id: 'roles_create',
                    label: 'Создать роль',
                    href: '/admin/roles/create',
                    icon: ListPlus
                },
            ],
        })
    }
})
const breadcrumbsStore = useBreadcrumbStore();
breadcrumbsStore.addBreadcrumb({
    title: 'Администрирование',
    href: '/admin',
})
</script>
