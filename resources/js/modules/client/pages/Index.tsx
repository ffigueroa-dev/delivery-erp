import { PageHeader } from '@/components/page-header';
import {
    index as clientsRoute,
    create as createDeliveryRoute,
} from '@/routes/clients';
import { Head, Link } from '@inertiajs/react';
import { Client } from '../types/Client';
import { ClientCard } from '../components/ClientCard';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { ClientFilter } from '../components/filters/ClientFilter';
import { PaginatedResponse } from '@/types';
import { ClientFilters } from '../types/Filter';
import { useState } from 'react';
import { Pagination } from '@/components/common/pagination';
import { ClientsSearchBar } from '../components/filters/ClientsSearchBar';

interface IndexPageProps {
    clients: PaginatedResponse<Client>;
    filters: ClientFilters;
}

const Index = ({ clients, filters }: IndexPageProps) => {
    const [isFilterOpen, setIsFilterOpen] = useState<boolean>(false);
    return (
        <>
            <Head title="Clients" />
            <div className="p-6">
                <PageHeader title="Clients" description="Manage your clients">
                    <ClientsSearchBar filters={filters} />

                    <Button onClick={() => setIsFilterOpen(true)}>
                        Filters
                    </Button>
                    <Button asChild size={'icon'} className="shrink-0">
                        <Link href={createDeliveryRoute()}>
                            <Plus />
                            <span className="sr-only">Create Client</span>
                        </Link>
                    </Button>
                </PageHeader>

                <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {clients.data.map((c) => (
                        <ClientCard client={c} key={c.id} />
                    ))}
                </div>
                <div className="mt-4">
                    <Pagination links={clients.meta.links} />
                </div>
            </div>
            <ClientFilter
                filters={filters}
                isOpen={isFilterOpen}
                setIsOpen={setIsFilterOpen}
            />
        </>
    );
};

Index.layout = {
    breadcrumbs: [
        {
            title: 'Clients',
            href: clientsRoute(),
        },
    ],
};

export default Index;
