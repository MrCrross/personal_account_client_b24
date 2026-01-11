<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid gap-4">
            <div class="space-y-2">
                <Label for="name">Техническое название <RequiredMark /></Label>
                <Input
                    id="name"
                    v-model="roleForm.name"
                    pattern="[a-z0-9_]*"
                    placeholder="Введите техническое название [a-z_](слитно, слова через нижнее подчеркивание)"
                />
            </div>

            <div class="space-y-2">
                <Label for="title">Название <RequiredMark /></Label>
                <Input
                    id="title"
                    v-model="roleForm.title"
                    type="text"
                    pattern="[а-яА-ЯёЁa-zA-Z0-9 ]*"
                    placeholder="Введите название"
                />
            </div>

            <div class="space-y-2">
                <Label for="guard_name">Уровень ответственности роли</Label>
                <InputList id="guard_name"
                           v-model="roleForm.guard_name"
                           :data="guardOptions"
                />
            </div>

            <div class="space-y-2">
                <Label>Права доступа</Label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div
                        v-for="permission in permissions"
                        :key="permission.id"
                        class="flex items-center space-x-2 p-2 border rounded-md hover:bg-muted/50 cursor-pointer"
                        :class="{ 'bg-primary/10 border-primary': roleForm.permissions?.includes(permission.id) }"
                    >
                        <Label class="cursor-pointer flex-1">
                            <Checkbox
                                :model-value="roleForm.permissions?.includes(permission.id)"
                                @update:model-value="togglePermission(permission.id)"
                            />
                            {{ `${permission.title} (${permission.name} - ${permission.guard_name})` }}
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
import {Button, Checkbox, Input, RequiredMark, Label, InputList} from "@/components";
import {computed, onMounted, Ref, ref, watch} from "vue";
import {Permission, Role, RoleForm, SelectOption} from "@/types";
import {Roles} from "@/features";
import {useToast} from "@/composables";

const guardOptions: SelectOption[] = [
    {
        value: 'api',
        label: 'API',
    },
    {
        value: 'web',
        label: 'Страницы',
    },
]

const roleForm = ref<RoleForm>({
    name: '',
    title: '',
    guard_name: guardOptions[1],
    permissions: []
})
interface Props {
    role?: Role
}

const emit = defineEmits(['saved', 'cancel'])
const props = defineProps<Props>()
const isEdit = computed(() => !!props.role)
const permissionsAll: Ref<Permission[]> = ref([])
const permissions: Ref<Permission[]> = ref([])
const isSubmitting = ref(false)

watch(() => roleForm.value.guard_name, (newGuardName, oldGuardName) => {
    if (newGuardName !== oldGuardName) {
        if (newGuardName?.value) {
            updatePermissions(newGuardName.value)
        }else {
            updatePermissions('')
        }

    }
})

onMounted(async () => {
    permissionsAll.value = await Roles.getPermissions()
    updatePermissions(roleForm.value.guard_name?.value || '');
    if (isEdit.value && props?.role) {
        const role = props.role
        roleForm.value = {
            name: role.name,
            title: role.title,
            guard_name: guardOptions.filter((item) => item.value === role.guard_name)[0],
            permissions: role?.permissions?.map((permission: Permission) => permission.id) || []
        }
    }
})
const togglePermission = (permissionId: number) => {
    const index = roleForm.value.permissions?.indexOf(permissionId)
    if (typeof index !== "undefined") {
        if (index > -1) {
            roleForm.value.permissions?.splice(index, 1)
        } else {
            roleForm.value.permissions?.push(permissionId)
        }
    }
}

const updatePermissions = (guardName: string) => {
    permissions.value = permissionsAll.value.filter((permission: Permission) => permission.guard_name === guardName)
    roleForm.value.permissions = []
}

const submitForm = async () => {
    const toast = useToast();
    if (isEdit.value && props?.role) {
        const role = await Roles.update(props.role.id, roleForm.value)
        toast.success('Роль успешно изменена')
        emit('saved', role)
    } else {
        await Roles.store(roleForm.value)
        toast.success('Роль успешно создана')
    }
}
</script>

<style scoped>

</style>
