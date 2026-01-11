import type {Component} from "vue";

export type ModalSize = 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '4xl' | '6xl' | 'full';

export interface ModalOptions {
    title?: string
    description?: string
    size?: ModalSize
    componentProps?: Record<string, any>
}

export interface ModalProps {
    isOpen: boolean
    title?: string
    description?: string
    size?: ModalSize
    component?: Component | undefined
    componentProps?: Record<string, any>
}
