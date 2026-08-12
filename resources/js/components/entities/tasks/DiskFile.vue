<template>
    <a
        v-if="!file.is_image"
        :href="file.url"
        :download="file.name"
        class="text-primary inline-flex items-center gap-1 text-sm underline"
    >
        <Paperclip class="h-4 w-4" />
        {{ file.name }}
    </a>
    <button
        v-else
        type="button"
        :title="`Открыть «${file.name}»`"
        class="block cursor-zoom-in border-none bg-transparent p-0"
        @click="openPreview"
    >
        <span
            v-if="!loaded && !failed"
            class="my-2 flex h-40 w-40 items-center justify-center rounded-md border"
        >
            <LoaderCircle class="h-6 w-6 animate-spin text-muted-foreground" />
        </span>
        <span
            v-else-if="failed"
            class="my-2 flex h-40 w-40 items-center justify-center rounded-md border p-2 text-center text-sm text-muted-foreground"
        >
            Не удалось загрузить
        </span>
        <img
            v-show="loaded"
            :src="file.url"
            :alt="file.name"
            class="my-2 max-h-64 max-w-full rounded-md border object-contain transition hover:opacity-90"
            @load="loaded = true"
            @error="failed = true"
        />
    </button>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {LoaderCircle, Paperclip} from "lucide-vue-next";
import type {TaskDiskFile} from "@/types";
import {useModalStore} from "@/store";
import ImagePreviewModal from "./ImagePreviewModal.vue";

const props = defineProps<{
    file: TaskDiskFile
}>()

const modalStore = useModalStore()

const loaded = ref<boolean>(false)
const failed = ref<boolean>(false)

const openPreview = () => {
    // Имя файла и кнопку скачивания рисует сама модалка (рядом, иконкой) — заголовок не дублируем
    modalStore.openModal(ImagePreviewModal, {
        size: 'full',
        componentProps: {
            url: props.file.url,
            name: props.file.name,
        },
    })
}
</script>
