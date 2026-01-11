<template>
    <div class="space-y-6">
        <!-- Заголовок и статус -->
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-foreground">{{ task.title }}</h2>
                <p class="text-sm text-muted-foreground">#: {{ task.id }}</p>
                <p class="text-sm text-gray-500" v-if="task.creator.id !== auth.user.id">Создатель: {{ task.creator.name }}</p>
            </div>
        </div>

        <div class="flex items-start gap-2 w-full">
            <div class="flex gap-2">
                <Label class="text-sm font-medium">Статус</Label>
                <Badge :variant="task.status.variant || 'default'">
                    {{ task.status?.name }}
                </Badge>
            </div>
            <div class="flex gap-2">
                <Label class="text-sm font-medium">Приоритет</Label>
                <BadgePriority :priority="task.priority" />
            </div>
        </div>

        <!-- Основная информация -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Контактная информация -->
            <Card class="gap-4">
                <CardHeader>
                    <CardTitle class="text-lg">Контактная информация</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div>
                        <Label class="text-sm font-medium">Контактное лицо</Label>
                        <p class="text-foreground">{{ task.person }}</p>
                    </div>
                    <div>
                        <Label class="text-sm font-medium">Телефон</Label>
                        <p class="text-foreground">{{ task.person_phone }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Ответственный и группа -->
            <Card class="gap-4">
                <CardHeader>
                    <CardTitle class="text-lg">Ответственность</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div>
                        <Label class="text-sm font-medium">Ответственный</Label>
                        <p class="text-foreground">
                            {{ task.responsible.last_name }} {{ task.responsible.name }}
                        </p>
                    </div>
                    <div>
                        <Label class="text-sm font-medium">Группа</Label>
                        <p class="text-foreground">{{ task.group.name }}</p>
                    </div>
                    <div>
                        <Label class="text-sm font-medium">Тип проблемы</Label>
                        <p class="text-foreground">{{ task.problem_type.name }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Даты -->
        <Card class="gap-4">
            <CardHeader>
                <CardTitle class="text-lg">Сроки выполнения</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div>
                            <Label class="text-sm font-medium">Начало</Label>
                            <p class="text-foreground">{{ dateFormat.formatDateTime(task.date_start) }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">План начала</Label>
                            <p class="text-foreground">{{ dateFormat.formatDateTime(task.start_date_plan) }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Дедлайн</Label>
                            <p class="text-foreground">{{ dateFormat.formatDateTime(task.deadline) }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <Label class="text-sm font-medium">Завершено</Label>
                            <p class="text-foreground">{{ dateFormat.formatDateTime(task.closed_date) }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">План завершения</Label>
                            <p class="text-foreground">{{ dateFormat.formatDateTime(task.end_date_plan) }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium">Изменено</Label>
                            <p class="text-foreground">{{ dateFormat.formatDateTime(task.changed_date) }}</p>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Длительность -->
        <Card class="gap-4">
            <CardHeader>
                <CardTitle class="text-lg">Временные затраты</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <Label class="text-sm font-medium">Плановая длительность</Label>
                        <p class="text-foreground">{{ dateFormat.formatDuration(task.duration_plan) }}</p>
                    </div>
                    <div>
                        <Label class="text-sm font-medium">Фактическая длительность</Label>
                        <p class="text-foreground">{{ dateFormat.formatDuration(task.duration_fact) }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Описание -->
        <Card class="gap-4">
            <CardHeader>
                <CardTitle class="text-lg">Описание задачи</CardTitle>
            </CardHeader>
            <CardContent>
                <EditorReadonly :default-value="bbCodeConvert.convertToHTML(task.description || 'Нет описания')" class="text-foreground whitespace-pre-wrap"/>
            </CardContent>
        </Card>

        <!-- Результаты -->
        <Card class="gap-4" v-if="task.results.length > 0">
            <CardHeader>
                <CardTitle class="text-lg">Результаты ({{ task.results.length }})</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div
                    v-for="result in task.results"
                    :key="result.id"
                    class="border rounded-lg p-4"
                >
                    <div class="flex justify-between items-start mb-2">
            <span class="text-sm font-medium">
              {{ result.creator.last_name }} {{ result.creator.name }}
            </span>
                        <span class="text-sm text-muted-foreground">
              {{ dateFormat.formatDate(result.created_at) }}
            </span>
                    </div>
                    <p class="text-foreground whitespace-pre-wrap">{{ result.text }}</p>
                </div>
            </CardContent>
        </Card>

        <!-- Кнопка закрытия -->
        <div class="flex justify-end">
            <Button @click="handleClose">
                Закрыть
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import type {Task} from '@/types'
import {Badge, BadgePriority, Button, Card, CardContent, CardHeader, CardTitle, EditorReadonly} from "@/components";
import {useBBCode, useDateFormat} from "@/composables";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const bbCodeConvert = useBBCode()
const page = usePage();
const auth = computed(() => page.props.auth);

export interface TaskDetailsModalProps {
    task: Task
}

defineProps<TaskDetailsModalProps>()

const dateFormat = useDateFormat();

const emit = defineEmits<{
    (e: 'close'): void
}>()

const handleClose = () => {
    emit('close')
}

</script>
