import type {SelectOption} from "@/types";
import {API} from "@/services";
import type {TaskPriority} from "@/types";
import {useLocalStorage} from "@/composables";

export const TaskPriorities = {
    getOptions: async (): Promise<SelectOption<number>[]> => {
        const local: string | null = useLocalStorage.get('tasks_priorities_options');
        if (local) {
            return JSON.parse(local);
        } else {
            const response = await API.tasks.priorities.getAll()
            const options = (response.data.data || []).map((item: TaskPriority) => ({
                value: item.id,
                label: item.name
            }));
            useLocalStorage.setJson('tasks_priorities_options', options);

            return options;
        }
    },
}
