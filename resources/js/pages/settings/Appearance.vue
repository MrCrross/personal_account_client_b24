<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import {AppearanceTabs, HeadingSmall} from '@/components';
import { type BreadcrumbItem } from '@/types';
import { AppLayout, SettingsLayout } from '@/layouts';
import { appearance } from '@/routes';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Настройки темы',
        href: appearance().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Настройки темы" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Настройки темы" description="Изменить тему профиля" />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
