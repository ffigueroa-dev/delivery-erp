import { Check, ChevronsUpDown, X } from 'lucide-react';

import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Field, FieldLabel } from '@/components/ui/field';
import { SelectOption } from '@/types/common';
import InputError from '../input-error';
import { useState } from 'react';

type MultiSelectFieldProps<T extends string> = {
    id: string;
    label: string;
    selectLabel: string;
    value: T[];
    error?: string;
    placeholder: string;
    onChange: (value: T[]) => void;
    dropdown: SelectOption<T>[];
};

export function MultiSelectField<T extends string>({
    dropdown,
    id,
    label,
    onChange,
    placeholder,
    selectLabel,
    value,
    error,
}: MultiSelectFieldProps<T>) {
    const [open, setOpen] = useState(false);

    const toggleValue = (optionValue: T) => {
        const nextValue = value.includes(optionValue)
            ? value.filter((item) => item !== optionValue)
            : [...value, optionValue];

        onChange(nextValue);
    };

    const selectedOptions = dropdown.filter((option) =>
        value.includes(option.value),
    );

    return (
        <Field>
            <FieldLabel htmlFor={id}>{label}</FieldLabel>

            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <Button
                        id={id}
                        type="button"
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        aria-invalid={!!error}
                        className="w-full justify-between font-normal"
                    >
                        <span className="flex min-w-0 flex-1 flex-wrap gap-1">
                            {selectedOptions.length > 0 ? (
                                selectedOptions.map((option) => (
                                    <Badge
                                        key={option.value}
                                        variant="secondary"
                                    >
                                        {option.label}
                                    </Badge>
                                ))
                            ) : (
                                <span className="text-muted-foreground">
                                    {placeholder}
                                </span>
                            )}
                        </span>

                        <ChevronsUpDown className="ml-2 size-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>

                <PopoverContent
                    className="w-[var(--radix-popover-trigger-width)] p-0"
                    align="start"
                >
                    <Command>
                        <CommandInput placeholder="Buscar..." />
                        <CommandList>
                            <CommandEmpty>
                                No se encontraron resultados.
                            </CommandEmpty>

                            <CommandGroup heading={selectLabel}>
                                {dropdown.map((option) => {
                                    const selected = value.includes(
                                        option.value,
                                    );

                                    return (
                                        <CommandItem
                                            key={option.value}
                                            value={option.label}
                                            onSelect={() =>
                                                toggleValue(option.value)
                                            }
                                        >
                                            {option.label}

                                            <Check
                                                className={`ml-auto size-4 ${
                                                    selected
                                                        ? 'opacity-100'
                                                        : 'opacity-0'
                                                }`}
                                            />
                                        </CommandItem>
                                    );
                                })}
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>

            {selectedOptions.length > 0 && (
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    className="w-fit"
                    onClick={() => onChange([])}
                >
                    <X className="mr-1 size-4" />
                    Limpiar selección
                </Button>
            )}

            <InputError message={error} />
        </Field>
    );
}
