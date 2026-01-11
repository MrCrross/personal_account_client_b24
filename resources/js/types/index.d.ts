import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User,
    permissions: string[],
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface PageData {
    component: string
    props: AppPageProps
    url: string
    version: string
    clearHistory: boolean
    encryptHistory: boolean
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export type Appearance = 'light' | 'dark' | 'system';

export type TypeViewData = 'card' | 'table';

export type BreadcrumbItemType = BreadcrumbItem;

export type DateRangeValue = {
    day: number,
    month: number,
    year: number
}

export type DateRange = {
    start: DateRangeValue,
    end: DateRangeValue,
}

export interface SelectOption {
    value: number | string
    label: string
}

export interface Autocomplete {
    value: number | string
    label: string
}

export interface AutocompleteResponse {
    data: Autocomplete[];
}

export * from './globals';
export * from './components/ui/badge';
export * from './components/ui/form';
export * from './components/ui/input';
export * from './components/ui/table';
export * from './components/ui/tree';
export * from './entities/api';
export * from './entities/auth';
export * from './entities/roles';
export * from './entities/tasks';
export * from './entities/tasks/statuses';
export * from './entities/tasks/problemTypes';
export * from './entities/tasks/priorities';
export * from './entities/users';
export * from './store/breadcrumbs';
export * from './store/modal';

