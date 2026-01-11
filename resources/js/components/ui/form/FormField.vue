<template>
    <div
        v-if="shouldShow"
        class="space-y-2"
        :class="field.props?.wrapperClass"
    >
        <Label :for="field.name" class="text-sm font-medium">
            {{ field.label }}
            <RequiredMark v-if="field.required" />
        </Label>

        <!-- Текстовые поля -->
        <Input
            v-if="['text', 'email', 'password', 'number'].includes(field.type)"
            :id="field.name"
            :model-value="modelValue"
            :type="field.type"
            :placeholder="field.placeholder"
            :required="field.required"
            :disabled="field.disabled"
            :pattern="field.pattern"
            :min="field.min"
            :max="field.max"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <!-- Phone -->
        <InputPhone
            v-else-if="field.type === 'phone'"
            :model-value="modelValue"
            :required="field.required"
            :disabled="field.disabled"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <!-- Textarea -->
        <textarea
            v-else-if="field.type === 'textarea'"
            :id="field.name"
            :value="modelValue"
            :placeholder="field.placeholder"
            :required="field.required"
            :disabled="field.disabled"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
            @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        />

        <!-- Select -->
        <InputList
            v-else-if="field.type === 'select'"
            :model-value="modelValue"
            :data="field.options"
            :placeholder="field.placeholder"
            :required="field.required"
            :disabled="field.disabled"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <!-- Autocomplete -->
        <Autocomplete
            v-else-if="field.type === 'autocomplete'"
            :model-value="modelValue"
            :placeholder="field.placeholder"
            :required="field.required"
            :disabled="field.disabled"
            :api-feature="field.apiFeature"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <!-- Checkbox -->
        <div v-else-if="field.type === 'checkbox'" class="flex items-center space-x-2">
            <Checkbox
                :id="field.name"
                :model-value="modelValue"
                :disabled="field.disabled"
                @update:model-value="$emit('update:modelValue', $event)"
            />
            <Label :for="field.name" class="cursor-pointer">
                {{ field.placeholder || field.label }}
            </Label>
        </div>

        <!-- File -->
        <InputFile
            v-else-if="field.type === 'file'"
            :model-value="modelValue"
            :multiple="field.multiple"
            :max-files="field.maxFiles"
            :max-size="field.maxSize"
            :disabled="field.disabled"
            :label="field.placeholder"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <!-- Editor -->
        <Editor
            v-else-if="field.type === 'editor'"
            :model-value="modelValue"
            :placeholder="field.placeholder"
            :required="field.required"
            :disabled="field.disabled"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <!-- Кастомный компонент -->
        <component
            v-else-if="field.type === 'custom' && field.component"
            :is="field.component"
            :model-value="modelValue"
            v-bind="field.props"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <!-- Ошибка неизвестного типа -->
        <div v-else class="text-red-500">
            Unknown field type: {{ field.type }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
    Input,
    InputPhone,
    InputList,
    Autocomplete,
    Checkbox,
    InputFile,
    Editor,
    Label,
    RequiredMark
} from '@/components'
import type { FormFieldConfig } from '@/types'

interface Props {
    field: FormFieldConfig
    modelValue: any
    formData?: Record<string, any>
}
interface Emits {
    (e: 'update:modelValue', value: any): void
}

const emit = defineEmits<Emits>()
const props = defineProps<Props>()

const shouldShow = computed(() => {
    if (!props.field.showIf) return true
    // Используем переданный formData или пустой объект
    return props.field.showIf(props.formData || {})
})
</script>
