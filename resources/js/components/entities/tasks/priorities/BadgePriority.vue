<script setup lang="ts">
import {Badge, BadgeVariant} from "@/components";
import {TaskPriority} from "@/types";
import {onMounted, ref} from "vue";

interface Props {
    priority: TaskPriority;
}
const PRIORITY_VARIANTS: Record<number, BadgeVariant> = {
    0: 'default',
    1: 'success',
    2: 'destructive',
} as const;

type PriorityVariant = keyof typeof PRIORITY_VARIANTS;
const badgeVariant = ref<BadgeVariant>('default')

const props = defineProps<Props>()

onMounted(() => {
    if (props.priority.id in PRIORITY_VARIANTS) {
        badgeVariant.value = PRIORITY_VARIANTS[props.priority.id as PriorityVariant];
    } else {
        badgeVariant.value = 'default';
    }
})
</script>

<template>
    <Badge :variant="badgeVariant">
        {{priority.name}}
    </Badge>
</template>
