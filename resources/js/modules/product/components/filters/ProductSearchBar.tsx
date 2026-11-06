import { SearchBar } from '@/components/form/search-bar';
import { useFilters } from '@/hooks/use-filters';
import { ProductFilters } from '../../types/Filter';
import { products as productsRoute } from '@/routes';
type ProductsSearchBarProps = {
    filters: ProductFilters;
};
export const ProductsSearchBar = ({ filters }: ProductsSearchBarProps) => {
    const { apply } = useFilters<ProductFilters>(filters, productsRoute().url);
    return (
        <SearchBar
            value={filters.search ?? ''}
            onSearch={(search) => apply({ search })}
            placeholder="Buscar clientes..."
            debounceMs={500}
        />
    );
};
