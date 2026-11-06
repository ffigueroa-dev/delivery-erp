
import { useEffect, useState } from 'react';

import { useFilters } from '@/hooks/use-filters';


import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

import { ClientFilters } from '../../types/Filter';
import {ClientTypeEnum} from '../../types/Client';
import clients from '@/routes/clients';

type ClientFilterProps = {
    filters: ClientFilters;
    isOpen: boolean;
    setIsOpen: (open: boolean) => void;
};

const DEFAULT_PER_PAGE = 50;

type ActiveOption = 'active' | 'inactive' | 'all';

function getActiveOption(active?: boolean): ActiveOption {
    if (active === true) return 'active';
    if (active === false) return 'inactive';

    return 'all';
}

export function ClientFilter({
    filters,
    isOpen,
    setIsOpen,
}: ClientFilterProps) {
    const { apply } = useFilters<ClientFilters>(
        filters,
        clients.index().url,
    );

    const [form, setForm] = useState<ClientFilters>(filters);

    useEffect(() => {
        if (isOpen) {
            setForm(filters);
        }
    }, [isOpen, filters]);

    const updateFilter = <K extends keyof ClientFilters>(
        key: K,
        value: ClientFilters[K],
    ) => {
        setForm((current) => ({
            ...current,
            [key]: value,
        }));
    };

    const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        apply({
            ...form,
            active: form.active,
            type: form.type || undefined,
            phone: form.phone?.trim() || undefined,
            address: form.address?.trim() || undefined,
            search: form.search?.trim() || undefined,
            per_page: Number(form.per_page) || DEFAULT_PER_PAGE,
            
        });
        setIsOpen(false);
    };

    const handleClear = () => {
        const clearedFilters: ClientFilters = {
            per_page: DEFAULT_PER_PAGE,
            
            active: undefined,
            type: undefined,
            phone: undefined,
            address: undefined,
            search: undefined,
        };

        setForm(clearedFilters);
        apply(clearedFilters);
        setIsOpen(false);
    };

    const activeOption = getActiveOption(form.active);

    const selectActiveOption = (option: ActiveOption) => {
        updateFilter(
            'active',
            option === 'all' ? undefined : option === 'active',
        );
    };

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Filtrar clientes</DialogTitle>

                    <DialogDescription>
                        Seleccioná los criterios para filtrar los clientes.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {/* Estado */}
                        <div className="space-y-2">
                            <Label>Estado del cliente</Label>

                            <div className="space-y-3 rounded-md border p-3">
                                <label className="flex items-center gap-2">
                                    <Checkbox
                                        checked={activeOption === 'active'}
                                        onCheckedChange={() =>
                                            selectActiveOption('active')
                                        }
                                    />
                                    <span>Activos</span>
                                </label>

                                <label className="flex items-center gap-2">
                                    <Checkbox
                                        checked={activeOption === 'inactive'}
                                        onCheckedChange={() =>
                                            selectActiveOption('inactive')
                                        }
                                    />
                                    <span>Inactivos</span>
                                </label>

                                <label className="flex items-center gap-2">
                                    <Checkbox
                                        checked={activeOption === 'all'}
                                        onCheckedChange={() =>
                                            selectActiveOption('all')
                                        }
                                    />
                                    <span>Todos</span>
                                </label>
                            </div>
                        </div>

                        {/* Tipo */}
                        <div className="space-y-2">
                            <Label htmlFor="type">Tipo de cliente</Label>

                            <Select
                                value={form.type ?? 'all'}
                                onValueChange={(value) =>
                                    updateFilter(
                                        'type',
                                        value === 'all'
                                            ? undefined
                                            : (value as ClientFilters['type']),
                                    )
                                }
                            >
                                <SelectTrigger id="type">
                                    <SelectValue placeholder="Todos los tipos" />
                                </SelectTrigger>

                                <SelectContent>
                                    <SelectItem value="all">
                                        Todos los tipos
                                    </SelectItem>

                                    {Object.values(ClientTypeEnum).map((type) => (
                                        <SelectItem
                                            key={type}
                                            value={type}
                                        >
                                            {type}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        {/* Teléfono */}
                        <div className="space-y-2">
                            <Label htmlFor="phone">Teléfono</Label>

                            <Input
                                id="phone"
                                value={form.phone ?? ''}
                                onChange={(event) =>
                                    updateFilter(
                                        'phone',
                                        event.target.value || undefined,
                                    )
                                }
                                placeholder="Buscar por teléfono"
                            />
                        </div>

                        {/* Dirección */}
                        <div className="space-y-2">
                            <Label htmlFor="address">Dirección</Label>

                            <Input
                                id="address"
                                value={form.address ?? ''}
                                onChange={(event) =>
                                    updateFilter(
                                        'address',
                                        event.target.value || undefined,
                                    )
                                }
                                placeholder="Buscar por dirección"
                            />
                        </div>

                        {/* Resultados por página */}
                        <div className="space-y-2 sm:col-span-2">
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
                                    {[10, 25, 50, 100].map((amount) => (
                                        <SelectItem
                                            key={amount}
                                            value={String(amount)}
                                        >
                                            {amount}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
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

                        <Button type="submit">
                            Aplicar filtros
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
