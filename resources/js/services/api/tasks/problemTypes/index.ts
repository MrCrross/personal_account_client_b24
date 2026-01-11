import {AxiosResponse} from "axios";
import ApiService from "@/services/service";
import type {ProblemTypeResponse} from "@/types";

export default {
    getAll(): Promise<AxiosResponse<ProblemTypeResponse>> {
        return ApiService.get('/api/tasks/problem_types');
    },
    getAllWithProblems(): Promise<AxiosResponse<ProblemTypeResponse>> {
        return ApiService.get('/api/tasks/problem_types', {with_problems: 1});
    },
};
