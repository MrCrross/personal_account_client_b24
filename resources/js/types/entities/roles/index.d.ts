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

export interface RoleForm {
    name: string
    title: string
    guard_name: string
    permissions: number[] | null
}

export type RolesResponse = {
    data: Role[]
}

export type PermissionsResponse = {
    data: Permission[]
}
