import {Appearance, Autocomplete, Role} from "@/types";

export type AuthUser = {
    id: number,
    name: string,
    phone: string,
    telegram_chat_id?: number,
    b_group_id?: number,
    b_company_id?: number,
    email: string,
    theme: Appearance
}

export type ThemeFormInput = {
    theme: Appearance
}

export interface User {
    id: number,
    name: string,
    email: string,
    avatar?: string | null,
    b_group: Autocomplete,
    b_company: Autocomplete,
    telegram_chat_id?: string | null,
    theme?: Appearance,
    roles?: Role[],
    created_at?: string,
    updated_at?: string|null,
}

export type UserCreateForm = {
    name: string,
    email: string,
    password: string,
    b_group_id: number,
    b_company_id: number,
    telegram_chat_id?: string|null,
    roles: number[] | null
}

export type UserEditForm = {
    name: string,
    email: string,
    password?: string,
    b_group_id: number,
    telegram_chat_id?: string|null,
    roles: number[] | null
    _method?: 'PATCH'
}

export type UserListResponse = {
    data: User[]
    has_more: boolean
    current_page: number
}

export interface UserQueryParams {
    page?: number
    per_page?: number
    search?: string
    role_id?: number
    b_company_id?: number
    date_start?: string
    date_end?: string
}

export type Creator = {
    id: number
    name: string
}
