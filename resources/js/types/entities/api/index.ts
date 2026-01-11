export type APIResponse<T> = {
    error: boolean,
    data: T
}

export type APIValidationError = {
    errors: Record<string, string[]>,
    message: string,
    status: number
}

export type APIError = {
    message: string;
    errors: Record<string, string[]>;
    status?: number;
}

export type EntityLinks = {
    first: string;
    last: string | null;
    next: string | null;
    prev: string | null;
}

export type EntityMetaLinks = {
    current_page: number;
    current_page_url: string;
    from: number;
    path: string;
    per_page: number;
    to: number;
}

export interface EntityPage<T> {
    data: T,
    links: EntityLinks,
    meta: EntityMetaLinks
}
