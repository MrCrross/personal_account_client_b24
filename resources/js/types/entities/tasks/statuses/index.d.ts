import {BadgeVariant} from "@/components";

export type TaskStatusResponse = {
    data: TaskStatus[]
}

export interface TaskStatus {
    id: number
    name: string | null,
    variant: BadgeVariant | null
}
