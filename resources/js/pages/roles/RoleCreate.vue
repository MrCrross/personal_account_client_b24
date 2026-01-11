<template>
    <AppLayout>
        <Head title="Создание роли"/>

        <AdminLayout>
            <div class="lg:w-1/2">
                <RoleForm/>
            </div>
        </AdminLayout>
    </AppLayout>
</template>
<script setup lang="ts">
import {RoleForm} from "@/components";
import {AppLayout, AdminLayout} from "@/layouts";
import {Head} from "@inertiajs/vue3";
import {useBreadcrumbStore} from "@/store";

const breadcrumbsStore = useBreadcrumbStore();
breadcrumbsStore.addBreadcrumb({
    title: 'Создание роли',
    href: '/admin/roles/create',
})
</script>
<style scoped>

</style>
