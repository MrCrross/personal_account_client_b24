<template>
    <form @submit.prevent="taskCreateSubmit"
          class="container w-full flex flex-col gap-2 lg:w-1/2"
    >
        <div class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Название обращения<RequiredMark /></Label>
            <Input v-model="formTaskCreate.name"
                   placeholder="Название..."
                   type="text"
                   pattern="[а-яА-ЯёЁa-zA-Z 0-9]*"
                   required
            />
        </div>
        <div class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Контактное лицо<RequiredMark /></Label>
            <Input v-model="formTaskCreate.person.name"
                   placeholder="ФИО"
                   type="text"
                   pattern="[а-яА-ЯёЁa-zA-Z ]*"
                   required
            />
        </div>
        <div class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Номер телефона<RequiredMark /></Label>
            <InputPhone v-model="formTaskCreate.person.phone" required/>
        </div>
        <div class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Приоритет<RequiredMark /></Label>
            <InputList v-model="formTaskCreate.priority"
                       :data="priorities"
                       placeholder="Выберите приоритет..."
                       required
            />
        </div>
        <div v-if="problemTypes" class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Тип обращения<RequiredMark /></Label>
            <InputList v-model="formTaskCreate.problemType"
                       :data="problemTypes"
                       placeholder="Выберите тип обращения..."
                       required
            />
        </div>
        <div v-if="formTaskCreate.problemType?.value && selectedProblemType?.problems?.length" class="flex flex-col gap-2">
            <Label class="text-sm font-medium">Обращение по<RequiredMark /></Label>
            <InputList v-model="formTaskCreate.problem"
                       :data="selectedProblemType.problems"
                       :key="formTaskCreate.problemType?.value"
                       required
                       placeholder="Выберите возможную причину обращения..."
            />
        </div>
        <div v-if="formTaskCreate.problemType?.value === PROBLEM_TYPES_NAME.DOCUMENT" class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Номер договора</Label>
                <Input v-model="formTaskCreate.document.number"
                       placeholder="Номер договора"
                       type="text"
                       required
                />
            </div>
<!--            <div class="flex flex-col gap-2">-->
<!--                <Label class="text-sm font-medium">Сумма договора</Label>-->
<!--                <Input v-model="formTaskCreate.document.cost"-->
<!--                       placeholder="Сумма договора"-->
<!--                       min="0"-->
<!--                       step="0.01"-->
<!--                       type="number"/>-->
<!--            </div>-->
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Контрагент</Label>
                <Input v-model="formTaskCreate.document.contractor"
                       placeholder="Контрагент"
                       type="text"
                       required
                />
            </div>
        </div>
        <div v-if="formTaskCreate.problemType?.value === PROBLEM_TYPES_NAME.EQUIPMENT" class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Модель устройства (оргтехника)</Label>
                <Input v-model="formTaskCreate.equipment.model"
                       placeholder="Модель устройства"
                       type="text"/>
            </div>
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Номер устройства<RequiredMark /></Label>
                <Input v-model="formTaskCreate.equipment.number"
                       placeholder="Номер устройства с наклейки"
                       type="text"
                       required
                />
            </div>
        </div>
        <div v-if="formTaskCreate.problemType?.value === PROBLEM_TYPES_NAME.ONE_C" class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Конфигурация</Label>
                <Input v-model="formTaskCreate.oneC.conf"
                       placeholder="Конфигурация сервера"
                       type="text"/>
            </div>
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Версия 1С</Label>
                <Input v-model="formTaskCreate.oneC.version"
                       placeholder="Версия 1С"
                       type="text"/>
            </div>
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Ссылка на обработку</Label>
                <Input v-model="formTaskCreate.oneC.link"
                       placeholder="Ссылка на обработку"
                       type="text"/>
            </div>
        </div>
        <div v-if="[PROBLEM_TYPES_NAME.SITE, PROBLEM_TYPES_NAME.BITRIX].includes(formTaskCreate.problemType?.value)" class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Ссылка на сайт/страницу<RequiredMark /></Label>
                <Input v-model="formTaskCreate.site.link"
                       placeholder="Ссылка на сайт/страницу"
                       type="text"
                       required
                />
            </div>
        </div>
        <div v-if="formTaskCreate.problemType?.value === PROBLEM_TYPES_NAME.SITE" class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Браузер</Label>
                <Input v-model="formTaskCreate.site.browser"
                       placeholder="Название браузера"
                       type="text"/>
            </div>
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Операционная система</Label>
                <Input v-model="formTaskCreate.site.os"
                       placeholder="Название операционной системы"
                       type="text"/>
            </div>
        </div>
        <div v-if="formTaskCreate.problemType?.value" class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Описание проблемы<RequiredMark /></Label>
                <Editor v-model="formTaskCreate.description"
                       placeholder="Название..."
                        required
                />
            </div>
            <div class="flex flex-col gap-2">
                <Label class="text-sm font-medium">Загружаемые файлы</Label>
                <InputFile v-model="formTaskCreate.files"
                           multiple
                           :max-size="15"
                           :max-files="5"
                           label="Загрузите файлы"
                />
            </div>
        </div>
        <Button type="submit" variant="default" :disabled="isSubmit">
            Отправить
        </Button>
        <ConsentPersonalData button-name="Отправить"/>
    </form>
</template>

<script setup lang="ts">
import {
    Button,
    Input,
    InputFile,
    InputPhone,
    Label,
    InputList,
    Editor,
    RequiredMark,
    ConsentPersonalData
} from "@/components";
import {computed, onMounted, ref, watch} from "vue";
import {TaskPriorities, TaskProblemTypes, Tasks} from "@/features";
import type {TaskStoreForm, TaskStore} from "@/types";
import {useBBCode, useToast} from "@/composables";

const bbCodeConvert = useBBCode()
const getEmptyForm = (): TaskStoreForm => ({
    name: '',
    description: '',
    person: {
        name: '',
        phone: '',
    },
    problemType: null,
    problem: null,
    priority: null,
    document: {
        number: '',
        cost: '',
        contractor: '',
    },
    equipment: {
        number: '',
        model: '',
    },
    oneC: {
        conf: '',
        version: '',
        link: '',
    },
    site: {
        link: '',
        browser: '',
        os: ''
    },
    files: [],
})
const formTaskCreate = ref<TaskStoreForm>(getEmptyForm())

const PROBLEM_TYPES_NAME = {
    DOCUMENT: 1,
    EQUIPMENT: 2,
    ONE_C: 3,
    SITE: 4,
    BITRIX: 5,
}

const problemTypes = ref()
const priorities = ref()
const isSubmit = ref(false)

const selectedProblemType = computed(() => {
    if (!problemTypes.value || formTaskCreate.value.problemType === null) return null
    return problemTypes.value.find((type: any) => type.id === formTaskCreate.value.problemType?.value)
})

watch(() => formTaskCreate.value.problemType, () => {
    const emptyForm = getEmptyForm();
    formTaskCreate.value.problem = emptyForm.problem // Сбрасываем проблему при изменении типа
    formTaskCreate.value.document = emptyForm.document
    formTaskCreate.value.equipment = emptyForm.equipment
    formTaskCreate.value.oneC = emptyForm.oneC
    formTaskCreate.value.site = emptyForm.site
})

onMounted(async () => {
    problemTypes.value = await TaskProblemTypes.getAll()
    priorities.value = await TaskPriorities.getOptions()
})

const taskCreateSubmit = async () => {
    isSubmit.value = true
    const toast = useToast()
    try {
        const form = formTaskCreate.value;
        // Подготавливаем данные в формате TaskStore
        const taskData: TaskStore = {
            name: form.name,
            description: bbCodeConvert.convertToBBCode(form.description || ''),
            person: form.person.name,
            person_phone: form.person.phone,
            problem_type_id: form.problemType?.value || 0,
            problem_id: form.problem?.value || null,
            priority_id: form.priority?.value || 0,
            files: form.files,
            document_number: form.document.number,
            document_cost: form.document.cost ? Number(form.document.cost) : null,
            document_contractor: form.document.contractor,
            equipment_number: form.equipment.number,
            equipment_model: form.equipment.model,
            one_c_config: form.oneC.conf,
            one_c_version: form.oneC.version,
            one_c_link: form.oneC.link,
            site_link: form.site.link,
            site_browser: form.site.browser,
            site_os: form.site.os
        };
        const formData = Tasks.collectFormDataTaskCreate(taskData);
        await Tasks.store(formData);
        toast.success('Обращение создано успешно')
        formTaskCreate.value = getEmptyForm()
        isSubmit.value = false
    } catch (error) {
        console.error('Ошибка:', error);
        // Можно добавить обработку ошибок, например, показать уведомление
        toast.error('Произошла ошибка')
    }
}
</script>

<style scoped>

</style>
