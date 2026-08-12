import {AxiosResponse} from "axios";
import {TaskStatusResponse} from "@/types";
import ApiService from "@/services/service";

export default {
    getAll(): Promise<AxiosResponse<TaskStatusResponse>> {
        return ApiService.get('/api/tasks/statuses');
    },
};
