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
                    v-if="loadMore"
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
import {LoaderCircle} from "@lucide/vue";
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
import {usePerPage} from "@/composables";
import {SelectOption, Task, TaskQueryParams, TypeViewData} from "@/types";

const typeView: Ref<TypeViewData> = ref('card')
const isLoading = ref(true);
const loadMore = ref(false);
const isLoadingMore = ref(false)
const tasks: Ref<Task[]> = ref([])
const page = ref<number>(1)
// Значение читается из localStorage синхронно, чтобы первый запрос уже ушёл с нужным per_page
const perPage = ref<SelectOption>(usePerPage().read())
// Текущие фильтры — переиспользуются при смене per_page и подгрузке следующих страниц
const activeQuery = ref<TaskQueryParams>({})

onMounted(async () => {
    await loadTasks(1)
})

const handlePerPageChange = () => {
    loadTasks(1)
}

const loadTasks = async (pageNumber?: number, query?: TaskQueryParams) => {
    // pageNumber задан (первый запрос, фильтр, смена per_page) → сброс списка;
    // pageNumber не задан (кнопка «Загрузить еще») → подгрузка следующей страницы
    const isReset = pageNumber !== undefined

    if (query !== undefined) {
        activeQuery.value = query
    }

    if (isReset) {
        page.value = pageNumber || 1
        loadMore.value = false
        isLoading.value = true
    } else {
        page.value++
        isLoadingMore.value = true
    }

    const perPageValue = perPage.value ? Number(perPage.value.value) : undefined
    const response = await TasksFeature.getList({
        ...activeQuery.value,
        page: page.value,
        per_page: perPageValue,
    })

    tasks.value = isReset ? response.data : [...tasks.value, ...response.data]
    loadMore.value = response.has_more

    isLoading.value = false
    isLoadingMore.value = false
}
</script>

<style scoped>

</style>
