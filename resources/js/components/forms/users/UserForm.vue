<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid gap-4">
            <div class="space-y-2">
                <Label for="name">ФИО <RequiredMark /></Label>
                <Input
                    id="name"
                    v-model="userForm.name"
                    placeholder="Введите ФИО пользователя"
                />
            </div>

            <div class="space-y-2">
                <Label for="email">Email <RequiredMark /></Label>
                <Input
                    id="email"
                    v-model="userForm.email"
                    type="email"
                    placeholder="Введите email"
                />
            </div>

            <div class="space-y-2">
                <Label for="password">{{ isEdit ? 'Новый пароль' : 'Пароль' }} <RequiredMark /></Label>
                <Input
                    id="password"
                    v-model="userForm.password"
                    type="password"
                    :placeholder="isEdit ? 'Оставьте пустым, если не меняется' : 'Введите пароль'"
                />
            </div>

            <div class="space-y-2">
                <Label for="password">Группа в Bitrix24 для создания задач <RequiredMark /></Label>
                <Autocomplete
                    v-model="userForm.b_group_id"
                    placeholder="Поиск группы..."
                    :api-feature="BGroups"
                    :value="props?.user?.b_group"
                />
            </div>

            <div class="space-y-2">
                <Label for="password">Компания в Bitrix24 <RequiredMark /></Label>
                <Autocomplete
                    v-model="userForm.b_company_id"
                    placeholder="Поиск компании..."
                    :api-feature="BCompanies"
                    :value="props?.user?.b_company"
                />
            </div>

            <div class="space-y-2">
                <Label>Роли</Label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div
                        v-for="role in roles"
                        :key="role.id"
                        class="flex items-center space-x-2 p-2 border rounded-md hover:bg-muted/50 cursor-pointer"
                        :class="{ 'bg-primary/10 border-primary': userForm.roles?.includes(role.id) }"
                    >
                        <Label class="cursor-pointer flex-1">
                            <Checkbox
                                :model-value="userForm.roles?.includes(role.id)"
                                @update:model-value="toggleRole(role.id)"
                            />
                            {{ `${role.title} (${role.name} - ${role.guard_name})` }}
                        </Label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 pt-4">
            <Button type="button" variant="outline" @click="$emit('cancel')">
                Отмена
            </Button>
            <Button type="submit" :disabled="isSubmitting">
                <LoaderCircle v-if="isSubmitting" class="h-4 w-4 animate-spin mr-2" />
                {{ isEdit ? 'Обновить' : 'Создать' }}
            </Button>
        </div>
    </form>
</template>

<script setup lang="ts">
import {LoaderCircle} from 'lucide-vue-next'
import {Autocomplete, Button, Checkbox, Input, RequiredMark, Label} from "@/components";
import {computed, onMounted, Ref, ref} from "vue";
import {Role, User, UserCreateForm} from "@/types";
import {BGroups, BCompanies, Roles, Users} from "@/features";
import {useToast} from "@/composables";

const getEmptyForm = (): UserCreateForm => ({
    name: '',
    email: '',
    password: '',
    b_group_id: 0,
    b_company_id: 0,
    roles: []
});

const userForm = ref<UserCreateForm>(getEmptyForm())
interface Props {
    user?: User
}
const emit = defineEmits(['saved', 'cancel'])
const props = defineProps<Props>()
const isEdit = computed(() => !!props.user)
const roles: Ref<Role[]> = ref([])
const isSubmitting = ref(false)

onMounted(async () => {
    roles.value = await Roles.getAll()
    if (isEdit.value && props?.user) {
        const user = props.user
        userForm.value = {
            name: user.name,
            email: user.email,
            password: '',
            b_group_id: Number(user.b_group.value),
            b_company_id: Number(user.b_company.value),
            roles: user?.roles?.map((role: Role) => role.id) || []
        }
    }
})
const toggleRole = (roleId: number) => {
    const index = userForm.value.roles?.indexOf(roleId)
    if (typeof index !== "undefined") {
        if (index > -1) {
            userForm.value.roles?.splice(index, 1)
        } else {
            userForm.value.roles?.push(roleId)
        }
    }
}

const submitForm = async () => {
    const toast = useToast();
    if (isEdit.value && props?.user) {
        const user = await Users.update(props.user.id, userForm.value)
        toast.success('Пользователь успешно изменен')
        emit('saved', user)
    } else {
        await Users.store(userForm.value)
        toast.success('Пользователь успешно создан')
        userForm.value = getEmptyForm()
    }
}
</script>

<style scoped>

</style>
