import { SelectOption } from "@/types";

export interface Role {
    id: number
    name: string
    title: string
    guard_name: string
    created_at: string
    permissions?: Permission[]
}

export interface Permission {
    id: number
    name: string
    title: string
    guard_name: string
    created_at?: string
}

// Форма для UI. Поля транслируются в payload бэкенда внутри features/roles.
export interface RoleForm {
    name: string
    title: string
    guard_name: SelectOption<string> | null
    permissions: number[]
}

export type RolesResponse = {
    data: Role[]
}

export type PermissionsResponse = {
    data: Permission[]
}

export type RoleListResponse = {
    data: Role[]
    has_more: boolean
    current_page: number
}

export interface RoleQueryParams {
    page?: number
    per_page?: number
    search?: string
    guard_name?: string
    permission_id?: number
}
