import {AxiosResponse} from "axios";
import {APIResponse, TaskStatusResponse} from "@/types";
import ApiService from "@/services/service";

export default {
    getAll(): Promise<AxiosResponse<APIResponse<TaskStatusResponse>>> {
        return ApiService.get('/api/tasks/statuses');
    },
};
