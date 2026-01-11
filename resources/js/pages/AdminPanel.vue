<template>
    <AppLayout>
        <Head title="Администрирование"/>

        <AdminLayout>
            <Card class="p-4 gap-3">
                <CardHeader>
                    <CardTitle class="text-lg">Вы, Администратор, Поздравляю!</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div>
                        <p class="text-foreground">Перейдите в один из разделов для менеджмента.</p>
                    </div>
                </CardContent>
            </Card>
        </AdminLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import {AppLayout, AdminLayout} from '@/layouts';
import {Head} from '@inertiajs/vue3';
import {Card, CardContent, CardHeader, CardTitle} from "@/components";
</script>
