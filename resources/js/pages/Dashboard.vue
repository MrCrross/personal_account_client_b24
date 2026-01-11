<script setup lang="ts">
import AppLayout from '@/layouts/main/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import {Head, Link} from '@inertiajs/vue3';
import {Card, CardContent, CardHeader, CardTitle} from "@/components";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Card class="p-4 gap-3">
                <CardHeader>
                    <CardTitle class="text-lg">Проект ещё в разработке!</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div>
                        <p class="text-foreground">Сейчас можете <Link class="italic text-blue-400" href="/tasks/create">подать обращение</Link> или <Link class="italic text-blue-400" href="/tasks">ознакомится с уже поданными.</Link></p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
