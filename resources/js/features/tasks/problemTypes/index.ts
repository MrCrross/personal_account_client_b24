import type {SelectOption} from "@/types";
import {API} from "@/services";
import type {ProblemType} from "@/types";
import {useLocalStorage} from "@/composables";

export const TaskProblemTypes = {
    getOptions: async (): Promise<SelectOption[]> => {
        const local: string | null = useLocalStorage.get('tasks_problem_types_options');
        if (local) {
            return JSON.parse(local);
        } else {
            const response = await API.tasks.problemTypes.getAll()
            const options = (response.data.data || []).map((item: ProblemType) => ({
                value: item.id,
                label: item.name
            }));
            useLocalStorage.setJson('tasks_problem_types_options', options);

            return options;
        }
    },
    getAll: async (): Promise<ProblemType[]> => {
        const local: string | null = useLocalStorage.get('tasks_problem_types');
        if (local) {
            return JSON.parse(local);
        } else {
            const response = await API.tasks.problemTypes.getAllWithProblems()
            const problemTypes = (response.data.data || []).map((item: ProblemType) => ({
                id: item.id,
                name: item.name,
                problems: item.problems,
            }));
            const options = (response.data.data || []).map((item: ProblemType) => ({
                value: item.id,
                label: item.name
            }));
            useLocalStorage.setJson('tasks_problem_types', problemTypes);
            useLocalStorage.setJson('tasks_problem_types_options', options);

            return problemTypes;
        }
    },
}
