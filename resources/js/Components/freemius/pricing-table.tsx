'use client';

import * as React from 'react';
import { CURRENCY, parseCurrency, parseNumber, PortalPlans } from '@freemius/sdk';
import { formatCurrency } from '@/utils/formatter';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { useCheckout } from '@/hooks/checkout';
import { useLocale } from '@/utils/locale';
import CheckIcon from '@/icons/check';
import { fetchUser } from '@/services/userService';

export type PricingTableData = {
    id: string;
    pricing_id: string;
    title?: string | null;
    description?: string | null;
    price: string;
    features: {
        title: string;
        value?: string;
    }[];
    featured: boolean;
    monthly_price: number | null;
    annual_price: number | null;
    sandboxParam: [];
};

export function PricingTableItem(props: {
    plan: PricingTableData;
    trial: 'free' | 'paid' | false;
    onCheckout?: () => void;
}) {
    const { plan, trial = false, onCheckout } = props;
    const checkout = useCheckout();
    const locale = useLocale();
    const [userInfo, setUserInof] = React.useState([]);

    React.useEffect(() => {
        let isMounted = true;

        const loadUser = async () => {
            try {
                const userInof = await fetchUser();

                if (isMounted && userInof && Object.keys(userInof).length > 0) {
                    setUserInof(userInof.data);
                } else if (isMounted) {
                    console.warn("No billing data found for this user.");
                }
            } catch (err) {
                if (isMounted) {
                    console.error("Failed to fetch billing:", err);
                }
            }
        };

        loadUser();

        return () => {
            isMounted = false;
        };
    }, [fetchUser]);
    console.log('userInfo', Object.keys(userInfo).length);
    return (
        <Card className={`relative flex flex-col h-full gap-2 ${plan.featured
            ? 'bg-blue-50 border-2 border-blue-600 shadow-xl scale-105'
            : 'bg-white border shadow-sm'
            }`}>
            <CardHeader className="text-center">
                <div className="flex items-center justify-center gap-2">
                    <CardTitle className="text-xl">{plan.title}</CardTitle>
                    {trial && (
                        <Badge variant="secondary" className="text-xs">
                            {trial === 'free' ? locale.pricing.freeTrial() : locale.pricing.paidTrial()}
                        </Badge>
                    )}
                </div>

                {plan.description ? <CardDescription className="text-sm">{plan.description}</CardDescription> : null}
            </CardHeader>
            <CardContent className="flex-1">
                <div className="flex flex-col items-center">
                    <div className="flex items-baseline gap-2 mb-2 justify-center">
                        <div className="text-3xl font-bold">{plan.price}</div>
                        <div className="text-sm text-muted-foreground">{locale.pricing.billingSeparator()}</div>
                        <div className="text-sm text-muted-foreground">{plan.monthly_price ? locale.pricing.monthly() : locale.pricing.annual()}</div>
                    </div>
                </div>

                {plan.features.length ? (
                    <>
                        <Separator className="my-4" />

                        <ul className="space-y-2 px-2">
                            {plan.features.map((feature, index) => (
                                <li key={index} className="flex items-start gap-2 text-sm">
                                    <span>
                                        <CheckIcon className="h-4 w-4 mt-0.5 text-primary" />
                                    </span>
                                    <span className="flex flex-col flex-nowrap">
                                        <span className="font-semibold">{feature.title}</span>
                                        {feature.value ? (
                                            <span className="text-muted-foreground">{feature.value}</span>
                                        ) : null}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </>
                ) : null}
            </CardContent>

            <CardFooter className="mt-6">
                {Object.keys(userInfo).length > 0 ? (
                    <Button
                        className="w-full"
                        variant={plan.featured ? 'default' : 'outline'}
                        size="lg"
                        onClick={() => {
                            onCheckout?.();
                            checkout.open({
                                plan_id: plan.id,
                                pricing_id: plan.pricing_id,
                                trial: trial,
                                sandbox: plan.sandboxParam,
                                user_email: userInfo?.email,
                                user_firstname: userInfo?.first_name,
                                user_lastname: userInfo?.last_name,
                                purchaseCompleted: (response) => {
                                    // The logic here will be executed immediately after the purchase confirmation
                                    console.log('Purchase completed:', response);
                                    console.log('User email:', response.user.email);
                                    console.log('License key:', response.license.key);
                                },
                                success: (response) => {
                                    // The logic here will be executed after the customer closes the checkout, 
                                    // after a successful purchase
                                    console.log('Checkout closed after successful purchase:', response);
                                    console.log('User email:', response.user.email);
                                    console.log('License key:', response.license.key);
                                }
                            });
                        }}
                    >
                        {trial ? locale.pricing.action.trial() : locale.pricing.action.buynow()}
                    </Button>
                ) : (
                    <Button
                        className="w-full"
                        variant={plan.featured ? 'default' : 'outline'}
                        size="lg"
                        disabled
                    >
                        Login required
                    </Button>
                )}
            </CardFooter>
        </Card>
    );
}

export function PricingTable(props: {
    plans: PortalPlans;
    trial?: 'free' | 'paid' | false;
    currency?: CURRENCY;
    onCheckout?: () => void;
}) {
    const { plans, trial = false, currency = CURRENCY.USD, onCheckout } = props;

    const tableData: PricingTableData[] = React.useMemo(() => {
        const data: PricingTableData[] = [];

        plans.forEach((plan) => {
            const pricing = plan.pricing?.find(
                (p) =>
                    parseCurrency(p.currency) === currency &&
                    // Must not be hidden
                    p.is_hidden !== true &&
                    // Must have subscription pricing
                    (p.monthly_price || p.annual_price)
            );

            if (!pricing) {
                return;
            }

            const { annual_price, monthly_price } = pricing;

            let minPrice;

            if (monthly_price) {
                minPrice = Math.min(
                    (parseNumber(annual_price) ?? Infinity) / 12,
                    parseNumber(monthly_price) ?? Infinity
                );
            } else {
                minPrice = parseNumber(annual_price) ?? Infinity;
            }


            const features: PricingTableData['features'] = [];

            plan.features?.forEach((feature) => {
                if (feature.title) {
                    features.push({ title: feature.title, value: feature.value });
                }
            });

            data.push({
                id: plan.id!,
                pricing_id: pricing.id!,
                title: plan.title,
                description: plan.description,
                featured: plan.is_featured ?? false,
                price: formatCurrency(minPrice, currency),
                features: features,
                monthly_price: parseNumber(monthly_price),
                annual_price: parseNumber(annual_price),
                sandboxParam: plan.sandboxParam
            });
        });

        return data;
    }, [plans, currency]);

    if (tableData.length === 0) {
        return <p>No plans found that supports subscription</p>;
    }

    const gridColsMap = {
        1: 'md:grid-cols-1',
        2: 'md:grid-cols-2',
        3: 'md:grid-cols-3',
        4: 'md:grid-cols-4',
        5: 'md:grid-cols-5',
        6: 'md:grid-cols-6',
        // add more as needed
    };

    return (
        <div className={`grid grid-cols-1 gap-6 ${gridColsMap[tableData.length] || 'md:grid-cols-3'}`}>
            {tableData.map((plan) => (
                <PricingTableItem key={plan.id} plan={plan} trial={trial} onCheckout={onCheckout} />
            ))}
        </div>
    );
}
