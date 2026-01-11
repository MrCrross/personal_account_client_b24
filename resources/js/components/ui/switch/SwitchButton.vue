<template>
    <div
        class="inline-flex rounded-lg border"
        :class="[
      // Базовые классы для светлой темы
      'bg-white border-gray-200',
      // Темная тема
      'dark:bg-gray-800 dark:border-gray-700',
      // Disabled state
      { 'opacity-50 cursor-not-allowed': disabled }
    ]"
        role="group"
    >
        <button
            v-for="option in options"
            :key="option.value"
            :title="option.title"
            :disabled="disabled"
            class="flex items-center justify-center rounded-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
            :class="[
        sizeClasses,
        buttonClass,
        // Активное состояние - светлая тема
        isActive(option)
          ? [
              activeClass || 'bg-primary text-primary-foreground shadow-sm',
            ]
          : [
              inactiveClass || 'text-gray-600 hover:bg-gray-100',
              'dark:text-gray-300 dark:hover:bg-gray-700'
            ],
        // Focus styles
        'focus:ring-primary dark:focus:ring-primary-foreground focus:ring-offset-white dark:focus:ring-offset-gray-800'
      ]"
            @click="handleClick(option)"
        >
            <!-- Иконка -->
            <component
                v-if="option.icon"
                :is="option.icon"
                class="flex-shrink-0"
                :class="{
          'h-4 w-4': size === 'sm',
          'h-5 w-5': size === 'md',
          'h-6 w-6': size === 'lg'
        }"
            />

            <!-- Текст (если есть иконка, добавляем отступ) -->
            <span
                v-if="option.text"
                class="whitespace-nowrap"
                :class="{
          'ml-2': option.icon,
          'ml-0': !option.icon
        }"
            >
        {{ option.text }}
      </span>
        </button>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

export interface SwitchOption {
    value: string | number
    icon?: any // Component
    text?: string
    title?: string // Tooltip
}

interface Props {
    modelValue?: string | number
    options: SwitchOption[]
    size?: 'sm' | 'md' | 'lg'
    disabled?: boolean
    buttonClass?: string
    activeClass?: string
    inactiveClass?: string
}

interface Emits {
    (e: 'update:modelValue', value: string | number): void
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    size: 'md',
    disabled: false,
    showLabels: true,
    buttonClass: '',
    activeClass: '',
    inactiveClass: ''
})

const emit = defineEmits<Emits>()

const sizeClasses = computed(() => {
    const sizes = {
        sm: 'p-1 text-xs',
        md: 'p-2 text-sm',
        lg: 'p-3 text-base'
    }
    return sizes[props.size]
})

const handleClick = (option: SwitchOption) => {
    if (!props.disabled) {
        emit('update:modelValue', option.value)
    }
}

const isActive = (option: SwitchOption) => {
    return props.modelValue === option.value
}
</script>
