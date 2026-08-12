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

// Вложение из Битрикс-Диска (маркер [DISK FILE ID=nXXX] в тексте), уже разрешённое бэком
export interface TaskDiskFile {
    id: number
    name: string
    is_image: boolean
    url: string
}

// Тип результата задачи. Текст — без маркеров вложений, сами файлы вынесены в files (как у описания)
export interface TaskResult {
    id: number
    text: string
    files: TaskDiskFile[]
    creator: TaskUser
    created_at: string
}

// Автор сообщения переписки
export interface TaskMessageAuthor {
    id: number
    name: string
}

// Сообщение переписки в обращении (хранится локально, копия уходит в Битрикс)
export interface TaskMessage {
    id: number
    text: string
    // Вложения резолвятся бэком из Битрикс-Диска (как description_files)
    files: TaskDiskFile[]
    author: TaskMessageAuthor
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
    description_files: TaskDiskFile[]
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
    messages: TaskMessage[]
    creator: Creator
}

// Параметры запроса — имена полей соответствуют тому, что читает бэкенд (TasksListFilterRequest)
export interface TaskQueryParams {
    page?: number
    per_page?: number
    search?: string
    problem_type_id?: number
    status_id?: number
    priority_id?: number
    person_phone?: string
    date_start?: string
    date_end?: string
}

export interface TasksResponse {
    data: Task[]
    has_more: boolean
    current_page: number
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
    number: string,
    cost: string,
    contractor: string,
}
interface TaskStoreFormEquipment {
    number: string,
    model: string,
}
interface TaskStoreFormOneC {
    conf: string,
    version: string,
    link: string,
}
interface TaskStoreFormSite {
    os: string,
    browser: string,
    link: string,
}
export interface TaskStoreForm {
    name: string
    description: string
    person: TaskStoreFormPerson
    problemType: SelectOption<number> | null
    problem: SelectOption<number> | null
    priority: SelectOption<number> | null
    files?: File[]
    document: TaskStoreFormDocument,
    equipment: TaskStoreFormEquipment,
    oneC: TaskStoreFormOneC,
    site: TaskStoreFormSite,
}
