<script setup lang="ts">
import { computed, watch, ref} from 'vue'
import { BaseSelect, type SelectOption } from '@/components'

interface OptionData {
    id: number
    name: string
    disabled?: boolean
}

interface ApiFeature {
    getOptions(): Promise<OptionData[] | SelectOption[]>
}

interface Props {
    modelValue?: SelectOption | null
    placeholder?: string
    disabled?: boolean
    data?: OptionData[] | SelectOption[] | undefined
    searchable?: boolean
    clearable?: boolean
    apiFeature?: ApiFeature
}

interface Emits {
    (e: 'update:modelValue', value: SelectOption | null): void
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    placeholder: 'Выберите значение...',
    disabled: false,
    searchable: false,
    clearable: true
})

const emit = defineEmits<Emits>()
const internalOptions = ref<SelectOption[]>([])

// Нормализуем данные в единый формат
const options = computed<SelectOption[]>(() => {
    if (props.data && props.data.length > 0) {
        return normalizeOptions(props.data)
    }

    // Иначе используем данные из apiFeature
    return internalOptions.value
})

const normalizeOptions = (data: OptionData[] | SelectOption[]): SelectOption[] => {
    if (!data) return []

    return data.map(item => {
        // Если это OptionData (id/name)
        if ('id' in item && 'name' in item) {
            return {
                value: item.id,
                label: item.name,
                disabled: item.disabled
            }
        }

        // Если это уже SelectOption (value/label)
        return item
    })
}

const fetchOptions = async () => {
    if (!props.apiFeature) return

    const responseData = await props.apiFeature.getOptions()
    internalOptions.value = normalizeOptions(responseData)
    emit('loaded', internalOptions.value)
}

watch(() => props.apiFeature, (newApiFeature) => {
    if (newApiFeature) {
        fetchOptions()
    }
}, { immediate: true })

// Сбрасываем значение если его нет в новых опциях
watch(options, (newOptions) => {
    if (props.modelValue !== null && props.modelValue !== undefined) {
        const optionExists = newOptions.some((opt: SelectOption) =>
            opt.value === props.modelValue.value && !opt.disabled
        )

        if (!optionExists) {
            emit('update:modelValue', null)
        }
    }
})
</script>

<template>
    <BaseSelect
        :model-value="modelValue"
        :options="options"
        :placeholder="placeholder"
        :disabled="disabled"
        :searchable="searchable"
        :clearable="clearable"
        @update:model-value="emit('update:modelValue', $event)"
    />
</template>
