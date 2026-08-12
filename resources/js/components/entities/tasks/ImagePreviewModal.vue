<template>
    <div class="space-y-3">
        <!-- Имя файла со скачиванием: иконка, по наведению раскрывается подпись «Скачать оригинал» -->
        <div class="flex items-center gap-2 pr-8">
            <p v-if="name" class="min-w-0 flex-1 truncate text-sm font-medium text-foreground">
                {{ name }}
            </p>
            <a
                :href="url"
                :download="name"
                title="Скачать оригинал"
                class="group text-primary inline-flex shrink-0 items-center text-sm"
            >
                <Download class="h-4 w-4 shrink-0" />
                <span
                    class="max-w-0 overflow-hidden whitespace-nowrap pl-1 underline opacity-0 transition-all duration-200 group-hover:max-w-[12rem] group-hover:opacity-100"
                >
                    Скачать оригинал
                </span>
            </a>
        </div>

        <div class="flex min-h-[200px] items-center justify-center">
            <LoaderCircle v-if="!loaded" class="h-8 w-8 animate-spin text-muted-foreground" />
            <img
                v-show="loaded"
                :src="url"
                :alt="name"
                class="mx-auto h-auto max-h-[80vh] w-auto max-w-full rounded-md"
                @load="loaded = true"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {Download, LoaderCircle} from "lucide-vue-next";

defineProps<{
    url: string
    name?: string
}>()

const loaded = ref<boolean>(false)
</script>
