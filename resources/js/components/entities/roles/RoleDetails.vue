<template>
    <Card class="gap-4">
        <CardHeader class="flex flex-row items-start justify-between gap-4">
            <div class="space-y-1">
                <CardTitle class="text-2xl">{{ role.title }}</CardTitle>
                <p class="text-sm text-muted-foreground">#{{ role.id }} · {{ role.name }}</p>
            </div>
            <Button variant="outline" size="sm" @click="emit('edit')">
                <PencilIcon class="mr-2 h-4 w-4" />
                Редактировать
            </Button>
        </CardHeader>
        <CardContent class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <Label class="text-xs text-muted-foreground">Уровень ответственности</Label>
                    <p class="text-sm text-foreground">{{ guardLabel }}</p>
                </div>
                <div>
                    <Label class="text-xs text-muted-foreground">Дата создания</Label>
                    <p class="text-sm text-foreground">{{ dateFormat.formatDateTime(role.created_at) }}</p>
                </div>
            </div>
            <div class="border-t pt-4">
                <Label class="text-xs text-muted-foreground">
                    Права доступа ({{ role.permissions?.length ?? 0 }})
                </Label>
                <ul v-if="role.permissions?.length" class="mt-2 space-y-1">
                    <li
                        v-for="permission in role.permissions"
                        :key="permission.id"
                        class="flex flex-wrap items-center gap-2 text-sm"
                    >
                        <Badge variant="default">{{ permission.title }}</Badge>
                        <span class="text-muted-foreground">{{ permission.name }}</span>
                    </li>
                </ul>
                <p v-else class="text-sm text-foreground">Нет прав доступа</p>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import {computed} from "vue";
import {Badge, Button, Card, CardContent, CardHeader, CardTitle, Label} from "@/components";
import {PencilIcon} from "@lucide/vue";
import {useDateFormat} from "@/composables";
import type {Role} from "@/types";

interface Props {
    role: Role
}
interface Emits {
    (e: 'edit'): void
}
const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const dateFormat = useDateFormat()

// Подписи совпадают с вариантами в RoleForm
const GUARD_LABELS: Record<string, string> = {
    api: 'API',
    web: 'Страницы',
}
const guardLabel = computed<string>(() => GUARD_LABELS[props.role.guard_name] ?? props.role.guard_name)
</script>
