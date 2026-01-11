<template>
    <DataTable
        v-if="tasks.length"
        :columns="columns"
        :data="tasks"
        :selectable="true"
    >
        <template #cell-status="{ item }">
            <Badge :variant="item.status.variant || 'default'">
                {{ item.status?.name }}
            </Badge>
        </template>

        <template #cell-priority="{ item }">
            <BadgePriority :priority="item.priority" />
        </template>

        <template #cell-problem_type="{ item }">
            {{ item.problem_type.name }}
        </template>

        <template #cell-responsible="{ item }">
            {{ item.responsible.last_name }} {{ item.responsible.name }}
        </template>

        <template #cell-deadline="{ item }">
            <span class="text-sm">
                {{ formatDate.formatDate(item.deadline) }}
            </span>
        </template>

        <template #cell-created_at="{ item }">
            <span class="text-sm">
                {{ formatDate.formatDateTime(item.created_at) }}
            </span>
        </template>

        <!-- Слот для действий -->
        <template #cell-actions="{ item }">
            <div class="flex gap-2 justify-end">
                <Button
                    variant="ghost"
                    size="sm"
                    class="h-8 w-8 p-0"
                    @click="openViewModal(item)"
                    title="Подробнее"
                >
                    <Eye class="w-4 h-4" title="Подробнее" />
                </Button>
            </div>
        </template>
    </DataTable>
    <div v-else class="text-center py-12 text-muted-foreground">
        <p class="text-lg font-medium">Обращения не найдены</p>
    </div>
</template>

<script setup lang="ts">
import {Badge, BadgePriority, Button, DataTable, TaskDetailsModal} from "@/components";
import {Eye} from 'lucide-vue-next'
import {Task, TableColumn} from "@/types";
import {useModalStore} from "@/store";
import {useDateFormat} from "@/composables";

const formatDate = useDateFormat()
const modalStore = useModalStore()

interface Props {
    tasks: Task[]
}
defineProps<Props>()

const columns: TableColumn[] = [
    { key: 'id', label: 'ID', sortable: true, width: '80px' },
    { key: 'title', label: 'Название', sortable: true },
    { key: 'status', label: 'Статус', sortable: true },
    { key: 'priority', label: 'Приоритет', slot: true, },
    { key: 'problem_type', label: 'Тип проблемы', slot: true, },
    { key: 'responsible', label: 'Ответственный', slot: true, },
    { key: 'created_at', label: 'Дата создания', sortable: true, align: 'center' },
    { key: 'deadline', label: 'Дедлайн', slot: true, },
    { key: 'actions', label: '', align: 'center' },
];

const openViewModal = (task: any) => {
    modalStore.openModal(TaskDetailsModal, {
        title: 'Детали задачи',
        size: '4xl',
        componentProps: {
            task
        }
    })
}
</script>

<style scoped>

</style>
