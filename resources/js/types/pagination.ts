export type PaginationLinks = {
    active: boolean;
    label: string;
    page: number | null;
    url: string | null;
};

export type PaginatedResponse<T> = {
    data: T[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number | null;
        to: number | null;
        links: PaginationLinks[]
    }
};
