<template>
    <Card class="gap-4">
        <CardHeader class="flex flex-row items-start justify-between gap-4">
            <div class="space-y-1">
                <CardTitle class="text-2xl">{{ user.name }}</CardTitle>
                <p class="text-sm text-muted-foreground">#{{ user.id }}</p>
            </div>
            <Button variant="outline" size="sm" @click="emit('edit')">
                <PencilIcon class="mr-2 h-4 w-4" />
                Редактировать
            </Button>
        </CardHeader>
        <CardContent class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <Label class="text-xs text-muted-foreground">Email</Label>
                <p class="text-sm text-foreground">{{ user.email }}</p>
            </div>
            <div>
                <Label class="text-xs text-muted-foreground">Дата создания</Label>
                <p class="text-sm text-foreground">{{ dateFormat.formatDateTime(user.created_at ?? null) }}</p>
            </div>
            <div>
                <Label class="text-xs text-muted-foreground">Группа в Bitrix24</Label>
                <p class="text-sm text-foreground">{{ user.b_group.label || `#${user.b_group.value}` }}</p>
            </div>
            <div>
                <Label class="text-xs text-muted-foreground">Компания в Bitrix24</Label>
                <p class="text-sm text-foreground">{{ user.b_company.label || `#${user.b_company.value}` }}</p>
            </div>
            <div>
                <Label class="text-xs text-muted-foreground">Telegram chat ID</Label>
                <p class="text-sm text-foreground">{{ user.telegram_chat_id || 'Не указано' }}</p>
            </div>
            <div>
                <Label class="text-xs text-muted-foreground">Роли</Label>
                <div v-if="user.roles?.length" class="mt-1 flex flex-wrap gap-1">
                    <Badge v-for="role in user.roles" :key="role.id" variant="default">
                        {{ role.title }}
                    </Badge>
                </div>
                <p v-else class="text-sm text-foreground">Нет ролей</p>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import {Badge, Button, Card, CardContent, CardHeader, CardTitle, Label} from "@/components";
import {PencilIcon} from "@lucide/vue";
import {useDateFormat} from "@/composables";
import type {User} from "@/types";

interface Props {
    user: User
}
interface Emits {
    (e: 'edit'): void
}
defineProps<Props>()
const emit = defineEmits<Emits>()

const dateFormat = useDateFormat()
</script>
