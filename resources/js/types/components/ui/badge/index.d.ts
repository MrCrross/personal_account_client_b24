import type {HTMLAttributes} from "vue";

export type BadgeVariant =
    | 'default'
    | 'secondary'
    | 'destructive'
    | 'outline'
    | 'success'
    | 'warning'
    | 'info'
    | 'pending'

export interface BadgeProps {
    variant?: BadgeVariant
    icon?: boolean
    class?: HTMLAttributes['class']
}
