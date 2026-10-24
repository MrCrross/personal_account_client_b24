import ApiService from "@/services/service";
import {PermissionsResponse, RolesResponse, Role, RoleListResponse, RoleQueryParams} from "@/types";
import {AxiosResponse} from "axios";

export default {
    getAll: async (): Promise<AxiosResponse<RolesResponse>> => {
        return await ApiService.get('/api/roles/all');
    },
    getPermissions: async (): Promise<AxiosResponse<PermissionsResponse>> => {
        return await ApiService.get('/api/roles/permissions');
    },
    getList: async (queryParams?: RoleQueryParams): Promise<AxiosResponse<RoleListResponse>> => {
        return await ApiService.get('/api/roles', queryParams);
    },
    getById: async (roleId: number): Promise<AxiosResponse<Role>> => {
        return await ApiService.get('/api/roles/' + roleId);
    },
    create: async (data: string): Promise<AxiosResponse<Role>> => {
        return await ApiService.post('/api/roles', data);
    },
    edit: async (roleId: number, data: string): Promise<AxiosResponse<Role>> => {
        return await ApiService.patch('/api/roles/' + roleId, data);
    },
}
