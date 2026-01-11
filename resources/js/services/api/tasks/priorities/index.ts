import {AxiosResponse} from "axios";
import ApiService from "@/services/service";
import {TaskPriorityResponse} from "@/types";

export default {
    getAll(): Promise<AxiosResponse<TaskPriorityResponse>> {
        return ApiService.get('/api/tasks/priorities');
    },
};
