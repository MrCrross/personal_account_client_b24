<template>
    <div
        :class="cn(
            'rounded-md bg-transparent',
            props.class
        )"
    >
        <div
            class="readonly-content"
            v-html="content"
        />
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
    defaultValue?: string
    modelValue?: string
    class?: HTMLAttributes['class']
}>()

const content = computed(() => {
    return props.modelValue || props.defaultValue || ''
})
</script>

<style scoped>
.readonly-content {
    color: hsl(var(--foreground));
}

.readonly-content h1 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.readonly-content h2 {
    font-size: 1.25rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.readonly-content h3 {
    font-size: 1.125rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.readonly-content ul, .readonly-content ol {
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}

.readonly-content ul {
    list-style-type: disc;
}

.readonly-content ol {
    list-style-type: decimal;
}

.readonly-content li {
    margin: 0.25rem 0;
}

.readonly-content strong {
    font-weight: bold;
}

.readonly-content em {
    font-style: italic;
}

.readonly-content p {
    margin: 0.5rem 0;
}
</style>
