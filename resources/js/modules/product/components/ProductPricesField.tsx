
import InputError from "@/components/input-error";
import { Field, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";

import type { ProductPriceForm } from "../types/ProductForm";
import type { PriceTypeOption } from "../types/ProductPrice";

type ProductPricesFieldProps = {
    options: PriceTypeOption[];
    prices: ProductPriceForm[];
    errors: Record<string, string>;
    onChange: (prices: ProductPriceForm[]) => void;
};

export const ProductPricesField = ({
    options,
    prices,
    errors,
    onChange,
}: ProductPricesFieldProps) => {
    const setPriceAmount = (type: ProductPriceForm["type"], amount: string) => {
        onChange(
            prices.map((price) =>
                price.type === type
                    ? { ...price, amount }
                    : price,
            ),
        );
    };

    return (
        <div className="space-y-4">
            <div>
                <h3 className="font-medium">Prices</h3>
                <p className="text-sm text-muted-foreground">
                    Both commercial and retail prices are required.
                </p>
            </div>

            {options.map((priceType) => {
                const priceIndex = prices.findIndex(
                    (price) => price.type === priceType.value,
                );

                const amountError =
                    priceIndex >= 0
                        ? errors[`prices.${priceIndex}.amount`]
                        : undefined;

                return (
                    <Field key={priceType.value}>
                        <FieldLabel htmlFor={`price-${priceType.value}`}>
                            {priceType.label} Price
                        </FieldLabel>

                        <Input
                            id={`price-${priceType.value}`}
                            type="number"
                            min="0"
                            value={prices[priceIndex]?.amount ?? ""}
                            onChange={(e) =>
                                setPriceAmount(
                                    priceType.value,
                                    e.target.value,
                                )
                            }
                            placeholder={`Enter ${priceType.label.toLowerCase()} price`}
                            aria-invalid={!!amountError}
                        />

                        <InputError message={amountError} />
                    </Field>
                );
            })}

            <InputError message={errors.prices} />
        </div>
    );
};
