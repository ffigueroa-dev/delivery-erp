import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { products as productsRoute } from '@/routes';
import { create as createProduct } from '@/routes/products';
import { ProductCard } from '../components/ProductCard';
import type { Product } from '../types/Product';
import { PaginatedResponse } from '@/types';
import { ProductFilters } from '../types/Filter';
import { ProductsSearchBar } from '../components/filters/ProductSearchBar';
import { Pagination } from '@/components/common/pagination';

interface IndexProps {
    products: PaginatedResponse<Product>;
    filters: ProductFilters;
}

export default function Index({ products, filters }: IndexProps) {
    return (
        <>
            <Head title="Productos" />

            <div className="p-6">
                <PageHeader
                    title="Productos"
                    description="Gestiona tus productos"
                >
                    <ProductsSearchBar filters={filters} />
                    <Button asChild size="icon">
                        <Link href={createProduct()} prefetch>
                            <Plus />
                            <span className="sr-only">Create Product</span>
                        </Link>
                    </Button>
                </PageHeader>

                <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {products.data.map((product) => (
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>
                <div className="mt-4">
                    <Pagination links={products.meta.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = {
    breadcrumbs: [
        {
            title: 'Productos',
            href: productsRoute(),
        },
    ],
};
