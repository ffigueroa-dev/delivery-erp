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
        const params = {
            ...filters,
            ...newFilters,
            page: 1,
        };

        console.log('ROUTE:', route);
        console.log('PARAMS:', params);

        router.get(route, params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onStart: () => console.log('INERTIA: START'),
            onSuccess: () => console.log('INERTIA: SUCCESS'),
            onError: (errors) => console.log('INERTIA: ERROR', errors),
            onFinish: () => console.log('INERTIA: FINISH'),
        });
    };

    return { apply };
}
