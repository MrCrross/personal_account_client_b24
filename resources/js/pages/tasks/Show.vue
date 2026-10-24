<template>
    <AppLayout>
        <Head :title="task ? task.title : 'Обращение'"/>

        <div class="space-y-6 px-4 py-6">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="list().url">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    К обращениям
                </Link>
            </Button>

            <div v-if="isLoading" class="flex justify-center py-12">
                <LoaderCircle class="h-6 w-6 animate-spin" />
            </div>

            <div v-else-if="task" class="space-y-6">
                <TaskDetails :task="task" @sent="handleMessageSent" @updated="refreshTask" />
            </div>

            <div v-else class="py-12 text-center">
                <Label>Обращение не найдено.</Label>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import {AppLayout} from "@/layouts";
import {Head, Link} from "@inertiajs/vue3";
import {ArrowLeft, LoaderCircle} from "@lucide/vue";
import {onMounted, ref, Ref} from "vue";
import {Button, Label, TaskDetails} from "@/components";
import {list} from "@/routes/tasks";
import {Tasks as TasksFeature} from "@/features";
import type {Task, TaskMessage} from "@/types";

interface Props {
    taskId: number
}
const props = defineProps<Props>()

const task: Ref<Task | null> = ref(null)
const isLoading = ref<boolean>(true)

onMounted(async () => {
    await loadTask()
})

const loadTask = async () => {
    isLoading.value = true
    try {
        task.value = await TasksFeature.getById(props.taskId)
    } catch {
        // Тост покажет интерсептор; оставляем пустое состояние
        task.value = null
    } finally {
        isLoading.value = false
    }
}

// После редактирования подтягиваем актуальные данные из Битрикса без спиннера на всю страницу
const refreshTask = async () => {
    try {
        task.value = await TasksFeature.getById(props.taskId)
    } catch {
        // Тост покажет интерсептор; оставляем прежние данные
    }
}

const handleMessageSent =(message: TaskMessage) => {
    if (task.value) {
        task.value.messages.push(message)
    }
}
</script>
