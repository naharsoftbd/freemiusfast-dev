import type { PurchaseData, CheckoutSerialized } from '@freemius/sdk';
import { CheckoutProvider } from '@/components/freemius/checkout-provider';
import { Subscribe } from '@/components/freemius/subscribe';

const endpoint = '/api/checkout';
const checkoutData: CheckoutSerialized = {
  options: { 
    product_id: Number(import.meta.env.VITE_FREEMIUS_PRODUCT_ID),
    public_key: import.meta.env.VITE_FREEMIUS_PUBLIC_KEY, // Often required by the SDK
  },
  link: `https://checkout.freemius.com/${import.meta.env.VITE_FREEMIUS_PRODUCT_ID}/`,
  baseUrl: import.meta.env.VITE_FREEMIUS_BASE_URL
};

console.log(checkoutData);

export default function PricingPage() {
  return (
    <CheckoutProvider endpoint={endpoint} checkout={checkoutData}>
      <Subscribe />
    </CheckoutProvider>
  );
}