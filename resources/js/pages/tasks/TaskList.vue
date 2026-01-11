<template>
    <AppLayout>
        <Head title="Мои обращения"/>

        <TasksLayout>
            <TaskListFilter :load="loadTasks"/>
            <LoaderCircle v-if="isLoading" class="h-4 w-4 animate-spin" />

            <div v-else-if="tasks.length > 0">
                <div class="flex flex-row items-start justify-between gap-2 mb-2">
                    <PerPageSelector
                        v-model="perPage"
                        @change="handlePerPageChange"
                    />
                    <SwitchTypeView
                        v-model="typeView"
                    />
                </div>
                <div v-if="typeView === 'card'" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <TaskCard
                        v-for="task in tasks"
                        :key="task.id"
                        :task="task"
                    />
                </div>
                <TaskTable
                    v-else-if="typeView === 'table'"
                    :tasks="tasks"
                />
                <div
                    v-if="loadMore && perPage?.value < tasks.length"
                    class="flex justify-center mt-4"
                >
                    <Button
                        @click="loadTasks()"
                        :disabled="isLoadingMore"
                        class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <LoaderCircle v-if="isLoadingMore" class="h-4 w-4 animate-spin" />
                        <span>Загрузить еще</span>
                    </Button>
                </div>
            </div>

            <div v-else class="grid gap-4">
                <Label>Обращения не найдены.</Label>
            </div>
        </TasksLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import {AppLayout, TasksLayout} from "@/layouts";
import {Head} from "@inertiajs/vue3";
import {LoaderCircle} from "lucide-vue-next";
import {onMounted, Ref, ref} from "vue";
import {
    TaskCard,
    Label,
    SwitchTypeView,
    TaskTable,
    Button,
    TaskListFilter,
    PerPageSelector
} from "@/components";
import {Tasks as TasksFeature} from "@/features";
import {SelectOption, Task, TaskQueryParams, TypeViewData} from "@/types";

const typeView: Ref<TypeViewData> = ref('card')
const isLoading = ref(true);
const loadMore = ref(true);
const isLoadingMore = ref(false)
const tasks: Ref<Task[]> = ref([])
const page = ref<number>(1)
const perPage = ref<SelectOption>()

onMounted( async () => {
    tasks.value = await TasksFeature.getList()
    isLoading.value = false
})
const handlePerPageChange = () => {
    page.value = 0
    loadTasks()
}

const loadTasks = async (pageNumber?: number, query?: TaskQueryParams) => {
    isLoading.value = true
    isLoadingMore.value = true
    if (!query) {
        query = {}
    }
    if (!pageNumber) {
        page.value++;
    }
    if (pageNumber === 0 || page.value === 1) {
        tasks.value = []
    }
    query.page = pageNumber || page.value
    query.per_page = perPage.value?.value;
    const data = await TasksFeature.getList(query)
    if (data.length === 0 || data.length < perPage.value?.value) {
        loadMore.value = false
    }
    tasks.value = [...tasks.value, ...data]
    isLoading.value = false
    isLoadingMore.value = false
}
</script>

<style scoped>

</style>
