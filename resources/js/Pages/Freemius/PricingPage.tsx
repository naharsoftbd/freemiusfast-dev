import type { PurchaseData, CheckoutSerialized } from '@freemius/sdk';
import { CheckoutProvider } from '@/components/freemius/checkout-provider';
import { Subscribe } from '@/components/freemius/subscribe';
import { Topup } from '@/components/freemius/topup';
import { usePage } from '@inertiajs/react';

const endpoint = '/api/checkout';

export default function PricingPage() {
  const { freemius } = usePage().props;

  const checkoutData: CheckoutSerialized = {
    options: {
      product_id: Number(freemius.product_id),
      public_key: freemius.public_key, // Often required by the SDK
    },
    link: `${freemius.base_url}/${freemius.product_id}//`,
    baseUrl: freemius.base_url
  };
  return (
    <CheckoutProvider endpoint={endpoint} checkout={checkoutData}>
      <Subscribe />
    </CheckoutProvider>
  );
}