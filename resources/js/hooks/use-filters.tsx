import { router } from "@inertiajs/react";

type FilterPrimitive = string | number | boolean | null;

type FilterValue =
    | FilterPrimitive
    | FilterPrimitive[];

type FilterParams = Record<string, FilterValue>;

export function useFilters<T extends FilterParams>(
    filters: T,
    route: string,
) {
    const apply = (newFilters: Partial<T>) => {
        router.get(
            route,
            {
                ...filters,
                ...newFilters,
                page: 1,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    return { apply };
}
