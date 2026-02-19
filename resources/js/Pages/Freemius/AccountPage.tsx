import { CustomerPortal } from '@/components/freemius/customer-portal';
import { CheckoutProvider } from '@/components/freemius/checkout-provider';
import type { PurchaseData, CheckoutSerialized } from '@freemius/sdk';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import echo from '../../echo';



export default function AccountPage() {
  const { auth, freemius } = usePage().props;
  const [reloadKey, setReloadKey] = useState(0);

  const checkoutEndpoint = freemius.public_url + '/api/checkout';
  const checkoutData: CheckoutSerialized = {
    options: {
      product_id: Number(freemius.product_id),
      public_key: freemius.public_key, // Often required by the SDK
    },
    link: `${freemius.base_url}/${freemius.product_id}//`,
    baseUrl: freemius.base_url
  };

  const portalEndpoint = freemius.public_url + '/api/portal';

  if (auth.api_token) {
    localStorage.setItem('api_token', auth.api_token);
  }


  useEffect(() => {
    const channel = echo.private('customerdata-update')
      .listen('.CustomerDataSynced', (e: any) => { // Try adding the dot here
        setReloadKey(prev => prev + 1);
      });


    return () => echo.leave('customerdata-update');
  }, []);


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
              <CheckoutProvider endpoint={checkoutEndpoint} checkout={checkoutData}>
                <CustomerPortal key={reloadKey} endpoint={portalEndpoint} />
              </CheckoutProvider>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout >
  );
}