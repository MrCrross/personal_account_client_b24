import {AxiosResponse} from "axios";
import {TaskQueryParams, TasksResponse} from "@/types";
import ApiService from "@/services/service";

export default {
    getList(queryParams?: TaskQueryParams): Promise<AxiosResponse<TasksResponse>> {
        return ApiService.get('/api/tasks', queryParams);
    },
    store(data: FormData): Promise<AxiosResponse<null>> {
        return ApiService.post('/api/tasks', data);
    },
};
