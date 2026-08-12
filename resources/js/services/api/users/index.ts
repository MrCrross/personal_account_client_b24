import ApiService from "@/services/service";
import {update} from "@/routes/theme";
import {ThemeFormInput, User, UserListResponse, UserQueryParams} from "@/types";
import {AxiosResponse} from "axios";

export default {
    getList: async (queryParams?: UserQueryParams): Promise<AxiosResponse<UserListResponse>> => {
        return await ApiService.get('/api/users', queryParams);
    },
    getById: async (userId: number): Promise<AxiosResponse<User>> => {
        return await ApiService.get('/api/users/' + userId);
    },
    create: async (data: string): Promise<AxiosResponse<User>> => {
        return await ApiService.post('/api/users', data);
    },
    edit: async (userId: number, data: string): Promise<AxiosResponse<User>> => {
        return await ApiService.patch('/api/users/' + userId, data);
    },
    updateTheme: function (input: ThemeFormInput) {
        ApiService.post(update().url, input);
    },
}
