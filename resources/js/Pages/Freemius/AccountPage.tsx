import { CustomerPortal } from '@/components/freemius/customer-portal';
import { CheckoutProvider } from '@/components/freemius/checkout-provider';
import { type CheckoutOptions } from '@freemius/checkout';
import type { PurchaseData, CheckoutSerialized } from '@freemius/sdk';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

const checkoutEndpoint = import.meta.env.VITE_FREEMIUS_PUBLIC_URL + '/api/checkout';
const checkoutOptions: CheckoutOptions = {
  product_id: import.meta.env.VITE_FREEMIUS_PRODUCT_ID!,
};
const checkout: CheckoutSerialized = {
  options: { product_id: import.meta.env.VITE_FREEMIUS_PRODUCT_ID! },
  link: `${import.meta.env.VITE_FREEMIUS_BASE_URL}/${import.meta.env.VITE_FREEMIUS_PRODUCT_ID}/`,
  baseUrl: `${import.meta.env.VITE_FREEMIUS_BASE_URL}/`

};
const portalEndpoint = import.meta.env.VITE_FREEMIUS_PUBLIC_URL + '/api/portal';

export default function AccountPage() {
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
                <CustomerPortal endpoint={portalEndpoint} />
              </CheckoutProvider>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout >
  );
}