import { SelectOption, UUID } from "@/types/common";

export const ClientTypeEnum = {
    COMMERCIAL: 'commercial',
    RETAIL: 'retail',
} as const;

export type ClientType =
    (typeof ClientTypeEnum)[keyof typeof ClientTypeEnum];

export interface Client {
    id: UUID;
    name: string;
    phone: string | null;
    type: ClientType;
    full_address: string;
    address_reference: string | null;
    active: boolean;
}

export type ClientTypeOption = SelectOption<ClientType>;
