<template>
    <Card class="p-4 gap-3">
        <div class="md:flex justify-between items-start">
            <div>
                <h3 class="font-semibold text-lg">{{ task.title }}</h3>
                <p class="text-sm text-gray-500">#{{ task.id }}</p>
                <p class="text-sm text-gray-500" v-if="task.creator.id !== auth.user.id">Создатель: {{ task.creator.name }}</p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:justify-between md:items-start w-full gap-2">
            <Badge :variant="task.status.variant || 'default'">
                {{ task.status?.name }}
            </Badge>
            <Button type="button" variant="outline" size="sm" class="cursor-pointer" @click="handleViewDetails">
                <Eye class="w-4 h-4 mr-2"/>
                Подробнее
            </Button>
        </div>

        <EditorReadonly :default-value="bbCodeConvert.convertToHTML((task.description === null || task.description === '') ? '[P]Нет описания[/P]' : task.description).slice(0, 255)" class="text-sm text-gray-600"/>

        <div class="md:grid grid-cols-2 gap-2 text-sm">
            <div>
                <span class="font-medium">Приоритет:</span>
                <p><BadgePriority :priority="task.priority" /></p>
            </div>
            <div>
                <span class="font-medium">Тип проблемы:</span>
                <p>{{ task.problem_type.name }}</p>
            </div>
            <div v-if="task.responsible">
                <span class="font-medium">Ответственный:</span>
                <p>{{ task.responsible.last_name }} {{ task.responsible.name }}</p>
            </div>
            <div>
                <span class="font-medium">Дедлайн:</span>
                <p>{{ task.deadline ? dateFormat.formatDate(task.deadline) : 'Не указан' }}</p>
            </div>
        </div>
    </Card>
</template>

<script setup lang="ts">
import type {Task} from '@/types';
import {useBBCode, useDateFormat} from "@/composables";
import {Badge, BadgePriority, Button, Card, EditorReadonly} from "@/components";
import {Eye} from "lucide-vue-next";
import {router, usePage} from "@inertiajs/vue3";
import {show} from "@/routes/tasks";
import {computed} from "vue";

const bbCodeConvert = useBBCode()
const dateFormat = useDateFormat();
const page = usePage();
const auth = computed(() => page.props.auth);

const props = defineProps<{
    task: Task
}>()

const handleViewDetails = () => {
    router.visit(show(props.task.id).url)
}
</script>
