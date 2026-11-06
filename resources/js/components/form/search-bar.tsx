
import { useEffect, useMemo, useRef, useState, type FormEvent } from 'react';
import debounce from 'lodash/debounce';
import { Search, X } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type SearchBarProps = {
    value?: string;
    onSearch: (value: string | undefined) => void;
    placeholder?: string;
    className?: string;
    debounceMs?: number;
};

export function SearchBar({
    value = '',
    onSearch,
    placeholder = 'Buscar...',
    className,
    debounceMs = 400,
}: SearchBarProps) {
    const [search, setSearch] = useState(value);

    const onSearchRef = useRef(onSearch);
    onSearchRef.current = onSearch;

    const debouncedSearch = useMemo(
        () =>
            debounce((value: string) => {
                const normalized = value.trim();

                onSearchRef.current(normalized || undefined);
            }, debounceMs),
        [debounceMs],
    );

    // Cancelar debounce al desmontar
    useEffect(() => {
        return () => debouncedSearch.cancel();
    }, [debouncedSearch]);

    // Sincronizar con filtros externos
    useEffect(() => {
        debouncedSearch.cancel();
        setSearch(value);
    }, [value, debouncedSearch]);

    const handleChange = (value: string) => {
        setSearch(value);
        debouncedSearch(value);
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        debouncedSearch.cancel();

        const normalized = search.trim();

        onSearch(normalized || undefined);
    };

    const handleClear = () => {
        debouncedSearch.cancel();
        setSearch('');
        onSearch(undefined);
    };

    return (
        <form
            onSubmit={handleSubmit}
            className={`flex w-full items-center gap-2 ${className ?? ''}`}
        >
            <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />

                <Input
                    value={search}
                    onChange={(event) => handleChange(event.target.value)}
                    placeholder={placeholder}
                    className="pl-9 pr-9"
                    aria-label={placeholder}
                />

                {search.length > 0 && (
                    <button
                        type="button"
                        onClick={handleClear}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        aria-label="Limpiar texto"
                    >
                        <X className="size-4" />
                    </button>
                )}
            </div>

            <Button type="submit">
                Buscar
            </Button>
        </form>
    );
}
