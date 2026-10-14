import { Head, Link } from '@inertiajs/react';
import { Order } from '../types/Order';
import * as ordersRoute from '@/routes/orders';
import { PageHeader } from '@/components/page-header';
import { Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { OrderCard } from '../components/OrderCard';
import { PaginatedResponse } from '@/types';
import { Pagination } from '@/components/common/pagination';
import { OrderFilters } from '../types/Filter';
import { useFilters } from '@/hooks/use-filters';
import { SelectOption, UUID } from '@/types/common';
import { OrderFilter } from '../components/filters/OrderFilter';
import { useState } from 'react';

type IndexPageProps = {
    orders: PaginatedResponse<Order>;
    filters: OrderFilters;
    dropdowns: {
        clients: SelectOption<UUID>[];
        deliveries: SelectOption<UUID>[];
        products: SelectOption<UUID>[];
    };
};

const Index = ({ orders, filters, dropdowns }: IndexPageProps) => {
    const [openOrderFilterDialog, setOpenOrderFilterDialog] =
        useState<boolean>(false);

    return (
        <>
            <Head title="Orders" />
            <div className="p-6">
                <PageHeader title="Orders" description="Manage your Orders">
                    <Button onClick={() => setOpenOrderFilterDialog(true)}>
                        Filters
                    </Button>
                    <Button size={'icon'}>
                        <Link href={ordersRoute.create()}>
                            <Plus />
                            <span className="sr-only">Create Order</span>
                        </Link>
                    </Button>
                </PageHeader>

                <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {orders.data.map((o) => (
                        <OrderCard order={o} key={o.id} />
                    ))}
                </div>
                <div className="mt-4">
                    <Pagination links={orders.meta.links} />
                </div>
            </div>
            <OrderFilter
                filters={filters}
                dropdowns={dropdowns}
                isOpen={openOrderFilterDialog}
                setIsOpen={setOpenOrderFilterDialog}
            />
        </>
    );
};

Index.layout = {
    breadcrumbs: [
        {
            title: 'Orders',
            href: ordersRoute.index(),
        },
    ],
};
export default Index;
