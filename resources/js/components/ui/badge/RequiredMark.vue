<template>
  <span
      :class="cn([sizeClasses[size], colorClasses[color], ['font-bold cursor-help']])"
      :title="title"
  >*</span>
</template>

<script setup lang="ts">
import {cn} from "@/lib/utils";

interface Props {
    title?: string
    size?: 'sm' | 'md' | 'lg' | 'xl'
    color?: 'red' | 'orange' | 'blue' | 'purple' | 'green' | 'gray'
}

withDefaults(defineProps<Props>(), {
    title: 'Обязательное поле',
    size: 'md',
    color: 'red'
})

// Классы для размеров
const sizeClasses = {
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
    xl: 'text-xl'
}

// Классы для цветов
const colorClasses = {
    red: 'text-red-600 dark:text-red-400',
    orange: 'text-orange-500 dark:text-orange-400',
    blue: 'text-blue-600 dark:text-blue-400',
    purple: 'text-purple-600 dark:text-purple-400',
    green: 'text-green-600 dark:text-green-400',
    gray: 'text-gray-500 dark:text-gray-400'
}
</script>
