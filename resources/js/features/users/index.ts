import {API} from "@/services";
import {User, UserCreateForm, UserEditForm, UserListResponse, UserQueryParams} from "@/types";

export const Users = {
    getList: async (queryParams?: UserQueryParams): Promise<UserListResponse> => {
        const response = await API.users.getList(queryParams);

        return response.data;
    },
    store: async (data: UserCreateForm): Promise<User> => {
        if (!data.roles) {
            data.roles = null
        }
        const jsonData = JSON.stringify(data);
        const response = await API.users.create(jsonData);

        return response.data;
    },
    update: async (userId: number, data: UserEditForm ): Promise<User> => {
        data._method = 'PATCH'
        const jsonData = JSON.stringify(data);
        const response = await API.users.edit(userId, jsonData);

        return response.data;
    },
}
