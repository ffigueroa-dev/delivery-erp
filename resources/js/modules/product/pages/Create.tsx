import { Head, useForm } from '@inertiajs/react';
import type { SubmitEvent } from 'react';
import { TextField } from '@/components/form/text-field';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { products } from '@/routes';
import { create as createProductRoute } from '@/routes/products';

import { ProductPricesField } from '../components/ProductPricesField';
import type { ProductForm } from '../types/ProductForm';
import type { PriceTypeOption } from '../types/ProductPrice';

type CreatePageProps = {
    priceTypes: PriceTypeOption[];
};

const Create = ({ priceTypes }: CreatePageProps) => {
    const form = useForm<ProductForm>({
        name: '',
        description: '',
        prices: priceTypes.map((priceType) => ({
            type: priceType.value,
            amount: '',
        })),
    });

    const handleSubmit = (e: SubmitEvent<HTMLFormElement>) => {
        e.preventDefault();

        form.post(products().url, {
            onError: (errors) => {
                console.log('ON ERROR:', errors);
            },
        });
    };

    return (
        <>
            <Head title="Create Product" />

            <div className="p-6">
                <PageHeader
                    title="Create Product"
                    description="Create a new product"
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <TextField
                        id="product-name"
                        label="Product Name"
                        value={form.data.name}
                        error={form.errors.name}
                        placeholder="Please enter product name"
                        onChange={(value) => form.setData('name', value)}
                    />

                    <TextField
                        id="product-description"
                        label="Description"
                        value={form.data.description}
                        error={form.errors.description}
                        placeholder="Please enter product description"
                        onChange={(value) => form.setData('description', value)}
                    />

                    <ProductPricesField
                        options={priceTypes}
                        prices={form.data.prices}
                        errors={form.errors}
                        onChange={(prices) => form.setData('prices', prices)}
                    />

                    <Button type="submit" disabled={form.processing}>
                        Create Product
                    </Button>
                </form>
            </div>
        </>
    );
};

Create.layout = {
    breadcrumbs: [
        {
            title: 'Create Product',
            href: createProductRoute(),
        },
    ],
};

export default Create;
