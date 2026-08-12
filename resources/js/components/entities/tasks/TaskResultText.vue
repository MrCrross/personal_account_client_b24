<template>
    <div class="text-foreground">
        <!-- Текст результата приходит из Битрикса как HTML — санитизируем и отдаём в readonly-редактор.
             Маркеры [DISK FILE] вырезаны на бэке, сами файлы вынесены отдельным блоком ниже. -->
        <EditorReadonly v-if="text" :default-value="sanitizeHtml(text)" />
        <div v-if="files.length" class="mt-3 space-y-2">
            <DiskFile v-for="file in files" :key="file.id" :file="file" />
        </div>
    </div>
</template>

<script setup lang="ts">
import type {TaskDiskFile} from "@/types";
import {EditorReadonly} from "@/components";
import {sanitizeHtml} from "@/lib/sanitizeHtml";
import DiskFile from "./DiskFile.vue";

defineProps<{
    text: string
    files: TaskDiskFile[]
}>()
</script>
