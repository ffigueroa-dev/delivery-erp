import { useEffect, useState } from 'react';
import { useFilters } from '@/hooks/use-filters';
import { OrderFilters } from '../../types/Filter';
import * as ordersRoute from '@/routes/orders';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import { SelectOption, UUID } from '@/types/common';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { SelectField } from '@/components/form/select-field';
import { MultiSelectField } from '@/components/form/multi-select-field';

type OrderFilterProps = {
    filters: OrderFilters;
    dropdowns: {
        clients: SelectOption<UUID>[];
        deliveries: SelectOption<UUID>[];
        products: SelectOption<UUID>[];
    };
    isOpen: boolean;
    setIsOpen: (open: boolean) => void;
};

const DEFAULT_PER_PAGE = 50;

export const OrderFilter = ({
    filters,
    dropdowns,
    isOpen,
    setIsOpen,
}: OrderFilterProps) => {
    const { apply } = useFilters<OrderFilters>(
        filters,
        ordersRoute.index().url,
    );

    const [form, setForm] = useState<OrderFilters>(filters);

    useEffect(() => {
        if (isOpen) {
            setForm(filters);
        }
    }, [isOpen, filters]);

    const updateFilter = <K extends keyof OrderFilters>(
        key: K,
        value: OrderFilters[K],
    ) => {
        setForm((current) => ({
            ...current,
            [key]: value,
        }));
    };

    const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        // El backend requiere ambas fechas si se especifica alguna.
        if (Boolean(form.date_from) !== Boolean(form.date_to)) {
            return;
        }

        apply({
            ...form,
            date_from: form.date_from || undefined,
            date_to: form.date_to || undefined,
            status: form.status || undefined,
            sort: form.sort || undefined,
            delivery_id: form.delivery_id || undefined,
            per_page: Number(form.per_page) || DEFAULT_PER_PAGE,
        });

        setIsOpen(false);
    };

    const handleClear = () => {
        const clearedFilters: OrderFilters = {
            per_page: DEFAULT_PER_PAGE,
            status: undefined,
            date_from: undefined,
            date_to: undefined,
            sort: undefined,
            delivery_id: undefined,
            client_id: undefined,
        };

        setForm(clearedFilters);
        apply(clearedFilters);
        setIsOpen(false);
    };

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Filtrar pedidos</DialogTitle>
                    <DialogDescription>
                        Seleccioná los criterios para filtrar los pedidos.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {/* Cliente */}
                        <SelectField
                            id="client_id"
                            label="Cliente"
                            selectLabel="Clientes"
                            placeholder="Todos los clientes"
                            value={form.client_id ?? ''}
                            onChange={(value: UUID | undefined) =>
                                updateFilter(
                                    'client_id',
                                    value ? (value as UUID) : undefined,
                                )
                            }
                            dropdown={[
                                {
                                    value: '' as UUID,
                                    label: 'Todos los clientes',
                                },
                                ...dropdowns.clients,
                            ]}
                        />
                        {/* Delivery */}
                        <SelectField
                            id="delivery_id"
                            label="Deliveries"
                            selectLabel="Deliveries"
                            placeholder="Todos los deliveries"
                            value={form.delivery_id ?? ''}
                            onChange={(value: UUID | undefined) =>
                                updateFilter(
                                    'delivery_id',
                                    value ? (value as UUID) : undefined,
                                )
                            }
                            dropdown={[
                                {
                                    value: '' as UUID,
                                    label: 'Todos los deliveries',
                                },
                                ...dropdowns.deliveries,
                            ]}
                        />
                        {/* Products */}
                        <MultiSelectField
                            id="product_ids"
                            label="Productos"
                            selectLabel="Productos"
                            placeholder="Todos los productos"
                            value={form.product_ids ?? []}
                            onChange={(value: UUID[]) =>
                                updateFilter(
                                    'product_ids',
                                    value.length > 0 ? value : undefined,
                                )
                            }
                            dropdown={dropdowns.products}
                        />
                        {/* Estado */}
                        <div className="space-y-2">
                            <Label htmlFor="status">Estado</Label>
                            <Select
                                value={form.status ?? 'all'}
                                onValueChange={(value) =>
                                    updateFilter(
                                        'status',
                                        value === 'all'
                                            ? undefined
                                            : (value as OrderFilters['status']),
                                    )
                                }
                            >
                                <SelectTrigger id="status">
                                    <SelectValue placeholder="Todos los estados" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">
                                        Todos los estados
                                    </SelectItem>
                                    <SelectItem value="pending">
                                        Pendiente
                                    </SelectItem>
                                    <SelectItem value="delivered">
                                        Entregado
                                    </SelectItem>
                                    <SelectItem value="cancelled">
                                        Cancelado
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        {/* Fecha desde */}
                        <div className="space-y-2">
                            <Label htmlFor="date_from">Desde</Label>
                            <Input
                                id="date_from"
                                type="date"
                                value={form.date_from ?? ''}
                                max={form.date_to || undefined}
                                maxLength={10}
                                onChange={(event) =>
                                    updateFilter(
                                        'date_from',
                                        event.target.value || undefined,
                                    )
                                }
                            />
                        </div>

                        {/* Fecha hasta */}
                        <div className="space-y-2">
                            <Label htmlFor="date_to">Hasta</Label>
                            <Input
                                id="date_to"
                                type="date"
                                value={form.date_to ?? ''}
                                min={form.date_from || undefined}
                                max={new Date().toLocaleDateString('en-CA')}
                                onChange={(event) =>
                                    updateFilter(
                                        'date_to',
                                        event.target.value || undefined,
                                    )
                                }
                            />
                        </div>

                        {/* Ordenamiento */}
                        <div className="space-y-2">
                            <Label htmlFor="sort">Ordenar por fecha</Label>
                            <Select
                                value={form.sort ?? '-created_at'}
                                onValueChange={(value) =>
                                    updateFilter(
                                        'sort',
                                        value as OrderFilters['sort'],
                                    )
                                }
                            >
                                <SelectTrigger id="sort">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="-created_at">
                                        Más recientes primero
                                    </SelectItem>
                                    <SelectItem value="created_at">
                                        Más antiguos primero
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        {/* Cantidad por página */}
                        <div className="space-y-2">
                            <Label htmlFor="per_page">
                                Resultados por página
                            </Label>
                            <Select
                                value={String(
                                    form.per_page ?? DEFAULT_PER_PAGE,
                                )}
                                onValueChange={(value) =>
                                    updateFilter('per_page', Number(value))
                                }
                            >
                                <SelectTrigger id="per_page">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="10">10</SelectItem>
                                    <SelectItem value="25">25</SelectItem>
                                    <SelectItem value="50">50</SelectItem>
                                    <SelectItem value="100">100</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        {/* TODO: Agregar filtro de delivery usando dropdowns.deliveries */}
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setIsOpen(false)}
                        >
                            Cancelar
                        </Button>

                        <Button
                            type="button"
                            variant="outline"
                            onClick={handleClear}
                        >
                            Limpiar filtros
                        </Button>

                        <Button type="submit">Aplicar filtros</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
};
