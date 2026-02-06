import { useCheckout } from '@/hooks/checkout';
import { Button } from '@/components/ui/button';

export default function SomeComponent() {
  const checkout = useCheckout();

  return <Button onClick={() => checkout.open()}>Open Checkout</Button>;
}