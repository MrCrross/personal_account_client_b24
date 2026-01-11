<template>
    <form @submit.prevent="submitForm" class="space-y-6" :class="formClass">
        <div class="grid gap-4">
            <FormField
                v-for="field in fields"
                :key="field.name"
                :field="field"
                :model-value="formData[field.name]"
                :form-data="formData"
                @update:model-value="updateField(field.name, $event)"
            />
        </div>

        <div class="flex justify-end space-x-4 pt-4">
            <Button
                v-if="showCancelButton"
                type="button"
                variant="outline"
                @click="$emit('cancel')"
            >
                {{ cancelText }}
            </Button>
            <Button
                type="submit"
                :disabled="isSubmitting || disabled"
                :loading="isSubmitting"
            >
                {{ submitText }}
            </Button>
        </div>

        <slot name="footer" />
    </form>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { Button, FormField } from '@/components'
import { FormFieldConfig } from '@/types'

interface Props {
    fields: FormFieldConfig[]
    initialData?: Record<string, any>
    submitText?: string
    cancelText?: string
    showCancelButton?: boolean
    formClass?: string
    disabled?: boolean
}

interface Emits {
    (e: 'submit', data: any): void
    (e: 'cancel'): void
    (e: 'update:modelValue', data: any): void
}

const props = withDefaults(defineProps<Props>(), {
    submitText: 'Сохранить',
    cancelText: 'Отмена',
    showCancelButton: true,
    formClass: '',
    disabled: false
})

const emit = defineEmits<Emits>()

const formData = ref<Record<string, any>>({})
const isSubmitting = ref(false)

// Инициализация формы
const initializeForm = () => {
    const initialData: Record<string, any> = {}

    props.fields.forEach(field => {
        // Устанавливаем значение из initialData или дефолтное
        if (props.initialData && props.initialData[field.name] !== undefined) {
            initialData[field.name] = props.initialData[field.name]
        } else {
            // Дефолтные значения в зависимости от типа
            switch (field.type) {
                case 'checkbox':
                    initialData[field.name] = false
                    break
                case 'file':
                    initialData[field.name] = field.multiple ? [] : null
                    break
                case 'select':
                case 'autocomplete':
                    initialData[field.name] = null
                    break
                default:
                    initialData[field.name] = ''
            }
        }
    })

    formData.value = initialData
    emit('update:modelValue', formData.value)
}

// Обновление поля
const updateField = (fieldName: string, value: any) => {
    formData.value[fieldName] = value
    emit('update:modelValue', formData.value)
}

// Отправка формы
const submitForm = async () => {
    isSubmitting.value = true
    try {
        await emit('submit', formData.value)
    } finally {
        isSubmitting.value = false
    }
}

// Отслеживание изменений initialData
watch(() => props.initialData, (newData) => {
    if (newData) {
        Object.keys(newData).forEach(key => {
            if (formData.value[key] !== undefined) {
                formData.value[key] = newData[key]
            }
        })
    }
}, { deep: true })

// Инициализация при монтировании
initializeForm()
</script>
