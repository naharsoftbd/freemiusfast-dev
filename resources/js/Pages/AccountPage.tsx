import { CustomerPortal } from '@/components/customer-portal';
import { CheckoutProvider } from '@/components/checkout-provider';
import { type CheckoutOptions } from '@freemius/checkout';
import type { PurchaseData, CheckoutSerialized } from '@freemius/sdk';

const checkoutEndpoint = import.meta.env.VITE_FREEMIUS_PUBLIC_URL + '/api/checkout';
const checkoutOptions: CheckoutOptions = {
  product_id: import.meta.env.VITE_FREEMIUS_PRODUCT_ID!,
};
const checkout: CheckoutSerialized = {
  options: { product_id: import.meta.env.VITE_FREEMIUS_PRODUCT_ID! },
  link: `https://checkout.freemius.com/${import.meta.env.VITE_FREEMIUS_PRODUCT_ID}/`,
  baseUrl: import.meta.env.VITE_FREEMIUS_PUBLIC_URL

};
const portalEndpoint = import.meta.env.VITE_FREEMIUS_PUBLIC_URL + '/api/portal';

export default function AccountPage() {
  return (
    <CheckoutProvider endpoint={checkoutEndpoint} checkout={checkout}>
      <CustomerPortal endpoint={portalEndpoint} />
    </CheckoutProvider>
  );
}