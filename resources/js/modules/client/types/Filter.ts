import { ClientType } from "./Client";

export type ClientFilters = {
    per_page?: number;
    page?: number;
    active?: boolean;
    type?: ClientType;
    search?: string;
    phone?: string;
    address?: string;
};
