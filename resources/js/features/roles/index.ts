import {API} from "@/services";
import {Permission, Role, RoleForm} from "@/types";

// Преобразование формы из UI в payload для бэкенда.
// Не мутируем входной объект (он реактивен в компоненте).
const buildPayload = (data: RoleForm): Record<string, unknown> => ({
    name: data.name,
    title: data.title,
    guard_name: data.guard_name?.value ?? '',
    permissions: data.permissions.length > 0 ? data.permissions : null,
})

export const Roles = {
    getAll: async (): Promise<Role[]> => {
        const response = await API.roles.getAll();

        return response.data.data;
    },
    getPermissions: async (): Promise<Permission[]> => {
        const response = await API.roles.getPermissions();

        return response.data.data;
    },
    getList: async (): Promise<Role[]> => {
        const response = await API.roles.getList();

        return response.data.data;
    },
    store: async (data: RoleForm): Promise<Role> => {
        const jsonData = JSON.stringify(buildPayload(data));
        const response = await API.roles.create(jsonData);

        return response.data;
    },
    update: async (roleId: number, data: RoleForm): Promise<Role> => {
        const payload = {...buildPayload(data), _method: 'PATCH'};
        const jsonData = JSON.stringify(payload);
        const response = await API.roles.edit(roleId, jsonData);

        return response.data;
    },
}
