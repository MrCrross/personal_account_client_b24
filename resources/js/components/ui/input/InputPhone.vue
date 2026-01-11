<template>
    <Input
        :model-value="formattedValue"
        @input="handleInput"
        @blur="handleBlur"
        :placeholder="placeholder"
        type="tel"
        :class="[
            'phone-input',
            '',
            { 'border-destructive': showError }
        ]"
    />
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Input } from "@/components";

const props = withDefaults(defineProps<{
    modelValue?: string;
    placeholder?: string;
}>(), {
    modelValue: '',
    placeholder: '+7 (___) ___-__-__'
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const CLEAN_PHONE_LENGTH = 11;
const FORMATTED_PHONE_LENGTH = 18;
const isFocused = ref(false);

// Чистый номер (только цифры)
const cleanValue = computed(() => {
    return props.modelValue.replace(/\D/g, '');
});

// Показывать ошибку если номер неполный или слишком длинный
const showError = computed(() => {
    return cleanValue.value.length > 0 && cleanValue.value.length !== CLEAN_PHONE_LENGTH;
});

// Форматированное значение для отображения
const formattedValue = computed(() => {
    const digits = cleanValue.value;

    // Если цифр нет, показываем пустую строку
    if (!digits) return '';

    // Убираем первую 7 если она есть, так как мы добавляем её вручную
    const formattedDigits = digits.startsWith('7') ? digits.slice(1) : digits;

    // Форматируем по маске: +7 (XXX) XXX-XX-XX
    const match = formattedDigits.match(/^(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})$/);
    if (!match) return '';

    let result = '+7';
    if (match[1]) result += ` (${match[1]}`;
    if (match[2]) result += `) ${match[2]}`;
    if (match[3]) result += `-${match[3]}`;
    if (match[4]) result += `-${match[4]}`;

    return result;
});

// Обработка ввода - просто извлекаем цифры
const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const inputValue = target.value;

    // Извлекаем только цифры и обрезаем до 11
    const digits = inputValue.replace(/\D/g, '').slice(0, CLEAN_PHONE_LENGTH);
    target.value = inputValue.slice(0, FORMATTED_PHONE_LENGTH)

    // Отправляем только цифры наружу
    emit('update:modelValue', digits);
};

const handleBlur = () => {
    isFocused.value = false;

    // При потере фокуса автоматически добавляем 7 если номер начинается не с 7
    const digits = cleanValue.value;
    if (digits.length > 0 && digits.length <= 10 && !digits.startsWith('7')) {
        const correctedDigits = '7' + digits;
        emit('update:modelValue', correctedDigits.slice(0, CLEAN_PHONE_LENGTH));
    }
};

</script>

<style scoped>
.phone-input :deep(input) {
    letter-spacing: 0.5px;
    font-variant-numeric: tabular-nums;
    font-family: monospace;
}
</style>
