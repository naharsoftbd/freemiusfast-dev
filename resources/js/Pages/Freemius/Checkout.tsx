import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { freemius } from '@/lib/freemius';
import { CustomerPortal } from '@/components/customer-portal';
import { CheckoutProvider } from '@/components/checkout-provider';
import type { PurchaseData, CheckoutSerialized } from '@freemius/sdk';
import  SomeComponent  from '@/components/SomeComponent';

const checkoutEndpoint = import.meta.env.VITE_FREEMIUS_PUBLIC_URL + '/api/checkout';
const checkout: CheckoutSerialized = {
  options: { product_id: import.meta.env.VITE_FREEMIUS_PRODUCT_ID! },
  link: `https://checkout.freemius.com/${import.meta.env.VITE_FREEMIUS_PRODUCT_ID}/`,
  baseUrl: 'https://checkout.freemius.com'

};
const portalEndpoint = import.meta.env.VITE_FREEMIUS_PUBLIC_URL + '/api/portal';

export default function Dashboard() {
    async function main() {
        const pricing = await freemius.pricing.retrieve();
        console.log(pricing);
    }
    main()
    console.log(portalEndpoint);
    return (
        <CheckoutProvider endpoint={checkoutEndpoint} checkout={checkout}>
            <SomeComponent />
        </CheckoutProvider>
    );
}
