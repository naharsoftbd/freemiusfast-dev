import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { freemius } from '@/lib/freemius';
import { CheckoutProvider } from '@/components/freemius/checkout-provider';
import type { PurchaseData, CheckoutSerialized } from '@freemius/sdk';
import SomeComponent from '@/components/freemius/SomeComponent';

const checkoutEndpoint = import.meta.env.VITE_FREEMIUS_PUBLIC_URL + '/api/checkout';
const checkout: CheckoutSerialized = {
    options: {
        product_id: Number(import.meta.env.VITE_FREEMIUS_PRODUCT_ID),
        public_key: import.meta.env.VITE_FREEMIUS_PUBLIC_KEY, // Often required by the SDK
    },
    link: `${import.meta.env.VITE_FREEMIUS_BASE_URL}/${import.meta.env.VITE_FREEMIUS_PRODUCT_ID}/`,
    baseUrl: import.meta.env.VITE_FREEMIUS_BASE_URL
};

export default function Checkout() {
    async function main() {
        const pricing = await freemius.pricing.retrieve();
        console.log(pricing);
    }
    main()
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Account
                </h2>
            }
        >
            <Head title="Account" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <CheckoutProvider endpoint={checkoutEndpoint} checkout={checkout}>
                                <SomeComponent />
                            </CheckoutProvider>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout >
    );
}
