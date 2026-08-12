<template>
    <div class="space-y-6">
        <!-- Заголовок -->
        <div>
            <h2 class="text-2xl font-bold text-foreground">{{ task.title }}</h2>
            <p class="text-sm text-muted-foreground">
                #{{ task.id }}
                <span v-if="isAdmin"> · Битрикс #{{ task.b_id }}</span>
            </p>
            <p class="text-sm text-muted-foreground" v-if="task.creator.id !== auth.user.id">
                Создатель: {{ task.creator.name }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Центральная область: описание, результаты, чат -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Описание -->
                <Card class="gap-4">
                    <CardHeader>
                        <CardTitle class="text-lg">Описание задачи</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <EditorReadonly :default-value="bbCodeConvert.convertToHTML(task.description || 'Нет описания')" class="text-foreground whitespace-pre-wrap"/>
                        <div v-if="task.description_files.length" class="mt-3 space-y-2">
                            <DiskFile v-for="file in task.description_files" :key="file.id" :file="file" />
                        </div>
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
                            <TaskResultText :text="result.text" :files="result.files" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Чат -->
                <TaskChat
                    :task-id="task.id"
                    :messages="task.messages"
                    @sent="emit('sent', $event)"
                />
            </div>

            <!-- Боковая область: статус и метаданные обращения (без заголовков).
                 self-start, чтобы колонка не растягивалась по высоте грида и работал sticky -->
            <aside class="lg:col-span-1 lg:sticky lg:top-6 lg:self-start">
                <Card>
                    <CardContent class="space-y-4">
                        <!-- Статус и приоритет -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <Label class="text-sm text-muted-foreground">Статус</Label>
                                <Badge :variant="task.status.variant || 'default'">
                                    {{ task.status?.name }}
                                </Badge>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <Label class="text-sm text-muted-foreground">Приоритет</Label>
                                <BadgePriority :priority="task.priority" />
                            </div>
                        </div>

                        <!-- Контактная информация -->
                        <div class="space-y-3 border-t pt-4">
                            <div>
                                <Label class="text-xs text-muted-foreground">Контактное лицо</Label>
                                <p class="text-sm text-foreground">{{ task.person }}</p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">Телефон</Label>
                                <p class="text-sm text-foreground">{{ task.person_phone }}</p>
                            </div>
                        </div>

                        <!-- Ответственность -->
                        <div class="space-y-3 border-t pt-4">
                            <div>
                                <Label class="text-xs text-muted-foreground">Ответственный</Label>
                                <p class="text-sm text-foreground">
                                    {{ task.responsible.last_name }} {{ task.responsible.name }}
                                </p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">Группа</Label>
                                <p class="text-sm text-foreground">{{ task.group.name }}</p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">Тип проблемы</Label>
                                <p class="text-sm text-foreground">{{ task.problem_type.name }}</p>
                            </div>
                        </div>

                        <!-- Сроки выполнения -->
                        <div class="space-y-3 border-t pt-4">
                            <div>
                                <Label class="text-xs text-muted-foreground">Начало</Label>
                                <p class="text-sm text-foreground">{{ dateFormat.formatDateTime(task.date_start) }}</p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">План начала</Label>
                                <p class="text-sm text-foreground">{{ dateFormat.formatDateTime(task.start_date_plan) }}</p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">Дедлайн</Label>
                                <p class="text-sm text-foreground">{{ dateFormat.formatDateTime(task.deadline) }}</p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">Завершено</Label>
                                <p class="text-sm text-foreground">{{ dateFormat.formatDateTime(task.closed_date) }}</p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">План завершения</Label>
                                <p class="text-sm text-foreground">{{ dateFormat.formatDateTime(task.end_date_plan) }}</p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">Изменено</Label>
                                <p class="text-sm text-foreground">{{ dateFormat.formatDateTime(task.changed_date) }}</p>
                            </div>
                        </div>

                        <!-- Временные затраты -->
                        <div class="space-y-3 border-t pt-4">
                            <div>
                                <Label class="text-xs text-muted-foreground">Плановая длительность</Label>
                                <p class="text-sm text-foreground">{{ dateFormat.formatDuration(task.duration_plan) }}</p>
                            </div>
                            <div>
                                <Label class="text-xs text-muted-foreground">Фактическая длительность</Label>
                                <p class="text-sm text-foreground">{{ dateFormat.formatDuration(task.duration_fact) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </aside>
        </div>
    </div>
</template>

<script setup lang="ts">
import type {Task, TaskMessage} from '@/types'
import {Badge, BadgePriority, Card, CardContent, CardHeader, CardTitle, DiskFile, EditorReadonly, Label, TaskChat, TaskResultText} from "@/components";
import {useBBCode, useDateFormat} from "@/composables";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const bbCodeConvert = useBBCode()
const page = usePage();
const auth = computed(() => page.props.auth);
const isAdmin = computed(() => auth.value.permissions.includes('tasks_all_users'));

defineProps<{
    task: Task
}>()

const emit = defineEmits<{
    (e: 'sent', message: TaskMessage): void
}>()

const dateFormat = useDateFormat();
</script>
