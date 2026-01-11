import {API} from "@/services";
import {Permission, Role, RoleForm} from "@/types";

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
        data.guard_name = data.guard_name.value
        if (!data.permissions) {
            data.permissions = null
        }
        const jsonData = JSON.stringify(data);
        const response = await API.roles.create(jsonData);

        return response.data;
    },
    update: async (roleId: number, data: RoleForm ): Promise<Role> => {
        data.guard_name = data.guard_name.value
        data._method = 'PATCH'
        const jsonData = JSON.stringify(data);
        const response = await API.roles.edit(roleId, jsonData);

        return response.data;
    },
}
