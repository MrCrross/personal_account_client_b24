<template>
    <AppLayout>
        <Head title="Пользователи"/>

        <AdminLayout>
            <UserTable/>
        </AdminLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {UserTable} from '@/components'
import {AppLayout, AdminLayout} from "@/layouts";
import {useBreadcrumbStore,} from "@/store";

const breadcrumbsStore = useBreadcrumbStore();
breadcrumbsStore.updateLastBreadcrumb({
    title: 'Пользователи',
    href: '/admin/users',
})
</script>
