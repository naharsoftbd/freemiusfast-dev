'use client';

import { useCheckout } from '@/hooks/checkout';
import { Button } from '@/components/ui/button';

export function CheckoutPricingTable() {
  const checkout = useCheckout();

  return <Button onClick={() => checkout.open()}>Subscribe Now</Button>;
}