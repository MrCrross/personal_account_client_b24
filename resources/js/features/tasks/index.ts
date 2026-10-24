import {API} from "@/services";
import {Task, TaskMessage, TaskQueryParams, TasksResponse, TaskStore, TaskUpdate} from "@/types";

export const Tasks = {
    getList: async (queryParams?: TaskQueryParams): Promise<TasksResponse> => {
        const response = await API.tasks.tasks.getList(queryParams);

        return response.data;
    },
    getById: async (id: number): Promise<Task> => {
        const response = await API.tasks.tasks.getById(id);

        return response.data;
    },
    store: async (formData: FormData) => {
        await API.tasks.tasks.store(formData);
    },
    update: async (id: number, data: TaskUpdate) => {
        await API.tasks.tasks.update(id, data);
    },
    sendMessage: async (id: number, text: string, files: File[] = []): Promise<TaskMessage> => {
        const formData = new FormData();
        formData.append('text', text);
        files.forEach((file, index) => {
            formData.append(`files[${index}]`, file);
        });

        const response = await API.tasks.tasks.sendMessage(id, formData);

        return response.data;
    },
    collectFormDataTaskCreate: (data: TaskStore): FormData => {
        const formData = new FormData();
        // Обязательные поля
        formData.append('name', data.name);
        formData.append('description', data.description);
        formData.append('person', data.person);
        formData.append('person_phone', data.person_phone);
        formData.append('problem_type_id', data.problem_type_id.toString());
        formData.append('priority_id', data.priority_id.toString());
        // Опциональные поля
        if (data.problem_id !== null && data.problem_id !== undefined) {
            formData.append('problem_id', data.problem_id.toString());
        }
        if (data.document_number) {
            formData.append('document_number', data.document_number);
        }
        if (data.document_cost !== null && data.document_cost !== undefined) {
            formData.append('document_cost', data.document_cost.toString());
        }
        if (data.document_contractor) {
            formData.append('document_contractor', data.document_contractor);
        }
        if (data.equipment_number) {
            formData.append('equipment_number', data.equipment_number);
        }
        if (data.equipment_model) {
            formData.append('equipment_model', data.equipment_model);
        }
        if (data.one_c_config) {
            formData.append('one_c_config', data.one_c_config);
        }
        if (data.one_c_version) {
            formData.append('one_c_version', data.one_c_version);
        }
        if (data.one_c_link) {
            formData.append('one_c_link', data.one_c_link);
        }
        if (data.site_link) {
            formData.append('site_link', data.site_link);
        }
        if (data.site_browser) {
            formData.append('site_browser', data.site_browser);
        }
        if (data.site_os) {
            formData.append('site_os', data.site_os);
        }
        // Файлы
        if (data.files && data.files.length > 0) {
            data.files.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
            });
        }

        return formData;
    }
}

export * from './problemTypes'
export * from './priorities'
