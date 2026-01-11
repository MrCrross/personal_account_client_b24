<template>
    <AppLayout>
        <Head title="Роли"/>

        <AdminLayout>
            <RoleTable/>
        </AdminLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {RoleTable} from '@/components'
import {AppLayout, AdminLayout} from "@/layouts";
import {useBreadcrumbStore} from "@/store";

const breadcrumbsStore = useBreadcrumbStore();
breadcrumbsStore.addBreadcrumb({
    title: 'Роли',
    href: '/admin/roles',
})
</script>
