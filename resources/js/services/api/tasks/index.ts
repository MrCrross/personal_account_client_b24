import {AxiosResponse} from "axios";
import {Task, TaskMessage, TaskQueryParams, TasksResponse, TaskUpdate} from "@/types";
import ApiService from "@/services/service";

export default {
    getList(queryParams?: TaskQueryParams): Promise<AxiosResponse<TasksResponse>> {
        return ApiService.get('/api/tasks', queryParams);
    },
    getById(id: number): Promise<AxiosResponse<Task>> {
        return ApiService.get(`/api/tasks/${id}`);
    },
    store(data: FormData): Promise<AxiosResponse<null>> {
        return ApiService.post('/api/tasks', data);
    },
    update(id: number, data: TaskUpdate): Promise<AxiosResponse<null>> {
        return ApiService.patch(`/api/tasks/${id}`, {
            name: data.name,
            description: data.description,
            priority_id: data.priority_id.toString(),
        });
    },
    sendMessage(id: number, data: FormData): Promise<AxiosResponse<TaskMessage>> {
        return ApiService.post(`/api/tasks/${id}/messages`, data);
    },
};
