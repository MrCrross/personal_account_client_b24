// Базовые типы пользователя
import {Creator, SelectOption, TaskPriority, TaskStatus} from "@/types";

export interface TaskUser {
    id: number
    name: string
    last_name: string
}

// Базовые типы группы
export interface TaskGroup {
    id: number
    name: string
}

// Тип проблемы
export interface TaskProblemType {
    id: number
    name: string
}

// Тип результата задачи
export interface TaskResult {
    id: number
    text: string
    creator: TaskUser
    created_at: string
}

// Основной тип задачи
export interface Task {
    id: number
    person: string
    person_phone: string
    b_id: number
    title: string
    description: string
    priority: TaskPriority
    status: TaskStatus
    group: TaskGroup
    responsible: TaskUser
    changed_date: string | null
    closed_date: string | null
    date_start: string | null
    deadline: string | null
    start_date_plan: string | null
    end_date_plan: string | null
    duration_plan: number | null
    duration_fact: number | null
    problem_type: TaskProblemType
    results: TaskResult[]
    creator: Creator
}

// Параметры запроса (если нужны)
export interface TaskQueryParams {
    page?: number
    per_page?: number
    status?: number
    priority?: number
    date_start?: string
    date_end?: string
    problem_type?: number
}

export interface TasksResponse {
    data: Task[]
}

export interface TaskStore {
    name: string
    description: string
    person: string
    person_phone: string
    problem_type_id: number
    problem_id: number|null
    priority_id: number
    files?: File[]
    document_number?: string
    document_cost?: number|null
    document_contractor?: string
    equipment_number?: string
    equipment_model?: string
    one_c_config?: string|null
    one_c_version?: string|null
    one_c_link?: string|null
    site_link?: string|null
    site_browser?: string|null
    site_os?: string|null
}

interface TaskStoreFormPerson {
    name: string
    phone: string
}

interface TaskStoreFormDocument {
    number: string | null,
    cost: string | null,
    contractor: string | null,
}
interface TaskStoreFormEquipment {
    number: string | null,
    model: string | null,
}
interface TaskStoreFormOneC {
    conf: string | null,
    version: string | null,
    link: string | null,
}
interface TaskStoreFormSite {
    os: string | null,
    browser: string | null,
    link: string | null,
}
export interface TaskStoreForm {
    name: string
    description: string
    person: TaskStoreFormPerson
    problemType: SelectOption | null
    problem: SelectOption | null
    priority: SelectOption | null
    files?: File[]
    document: TaskStoreFormDocument,
    equipment: TaskStoreFormEquipment,
    oneC: TaskStoreFormOneC,
    site: TaskStoreFormSite,
}
