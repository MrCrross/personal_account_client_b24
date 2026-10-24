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
                        :class="{ 'bg-primary/10 border-primary': roleForm.permissions.includes(permission.id) }"
                    >
                        <Label class="cursor-pointer flex-1">
                            <Checkbox
                                :model-value="roleForm.permissions.includes(permission.id)"
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
import {LoaderCircle} from '@lucide/vue'
import {Button, Checkbox, Input, RequiredMark, Label, InputList} from "@/components";
import {computed, onMounted, ref, watch} from "vue";
import type {Permission, Role, RoleForm, SelectOption} from "@/types";
import {Roles} from "@/features";
import {useToast} from "@/composables";

const guardOptions: SelectOption<string>[] = [
    {value: 'api', label: 'API'},
    {value: 'web', label: 'Страницы'},
]

interface Props {
    role?: Role
}
const props = defineProps<Props>()
const emit = defineEmits<{
    saved: [role: Role]
    cancel: []
}>()

const roleForm = ref<RoleForm>({
    name: '',
    title: '',
    guard_name: guardOptions[1],
    permissions: [],
})

const isEdit = computed(() => !!props.role)
const permissionsAll = ref<Permission[]>([])
const permissions = ref<Permission[]>([])
const isSubmitting = ref(false)

// При смене guard_name пересчитываем доступные права и оставляем из выбранных
// только те, что валидны для нового guard. Так инициализация формы при редактировании
// не стирает уже выбранные права (они валидны для текущего guard).
watch(() => roleForm.value.guard_name, (newGuard) => {
    filterPermissions(newGuard?.value ?? '')
    const validIds = new Set(permissions.value.map(p => p.id))
    roleForm.value.permissions = roleForm.value.permissions.filter(id => validIds.has(id))
})

const filterPermissions = (guardName: string) => {
    permissions.value = permissionsAll.value.filter(p => p.guard_name === guardName)
}

onMounted(async () => {
    permissionsAll.value = await Roles.getPermissions()
    if (isEdit.value && props.role) {
        const role = props.role
        roleForm.value = {
            name: role.name,
            title: role.title,
            guard_name: guardOptions.find(o => o.value === role.guard_name) ?? null,
            permissions: role.permissions?.map((p: Permission) => p.id) ?? [],
        }
    }
    filterPermissions(roleForm.value.guard_name?.value ?? '')
})

const togglePermission = (permissionId: number) => {
    const index = roleForm.value.permissions.indexOf(permissionId)
    if (index > -1) {
        roleForm.value.permissions.splice(index, 1)
    } else {
        roleForm.value.permissions.push(permissionId)
    }
}

const submitForm = async () => {
    const toast = useToast()
    isSubmitting.value = true
    try {
        const role = isEdit.value && props.role
            ? await Roles.update(props.role.id, roleForm.value)
            : await Roles.store(roleForm.value)
        toast.success(isEdit.value ? 'Роль успешно изменена' : 'Роль успешно создана')
        emit('saved', role)
    } catch (error) {
        console.error('Ошибка сохранения роли:', error)
    } finally {
        isSubmitting.value = false
    }
}
</script>
