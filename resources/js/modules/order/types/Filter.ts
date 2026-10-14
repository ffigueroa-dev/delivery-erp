import { UUID } from "@/types/common";

export type OrderFilters = {
    per_page?: number;
    date_from?: string;
    date_to?: string;
    sort?: string;
    status?: string;
    delivery_id?: UUID;
    client_id?: UUID;
    product_ids?: UUID[];
};
