import { SearchBar } from '@/components/form/search-bar';
import { ClientFilters } from '../../types/Filter';
import { useFilters } from '@/hooks/use-filters';
import clients from '@/routes/clients';
type ClientsSearchBarProps = {
    filters: ClientFilters;
};
export const ClientsSearchBar = ({ filters }: ClientsSearchBarProps) => {
    const { apply } = useFilters<ClientFilters>(filters, clients.index().url);
    return (
        <SearchBar
            value={filters.search ?? ''}
            onSearch={(search) => apply({ search })}
            placeholder="Buscar clientes..."
            debounceMs={500}
        />
    );
};
