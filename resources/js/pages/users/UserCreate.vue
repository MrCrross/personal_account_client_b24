<template>
    <AppLayout>
        <Head title="Создание пользователя"/>

        <AdminLayout>
            <div class="lg:w-1/2">
                <UserForm/>
            </div>
        </AdminLayout>
    </AppLayout>
</template>
<script setup lang="ts">
import {UserForm} from "@/components";
import {AppLayout, AdminLayout} from "@/layouts";
import {Head} from "@inertiajs/vue3";
import {useBreadcrumbStore} from "@/store";

const breadcrumbsStore = useBreadcrumbStore();
breadcrumbsStore.updateLastBreadcrumb({
    title: 'Создание пользователя',
    href: '/admin/users/create',
})
</script>
<style scoped>

</style>
