<template>
    <AppLayout>
        <Head title="Создать обращение"/>

        <TasksLayout>
            <div class="space-y-6">
                <HeadingSmall title="Обращения" description="Создание нового обращения" />
                <TaskCreateForm />
            </div>
        </TasksLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import {Head} from "@inertiajs/vue3";
import {AppLayout, TasksLayout} from "@/layouts";
import {HeadingSmall, TaskCreateForm} from "@/components";
</script>

<style scoped>

</style>
