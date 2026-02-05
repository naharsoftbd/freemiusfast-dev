import { Freemius } from '@freemius/sdk';

export const freemius = new Freemius({
  productId: import.meta.env.VITE_FREEMIUS_PRODUCT_ID!,
  apiKey: import.meta.env.VITE_FREEMIUS_API_KEY!,
  secretKey: import.meta.env.VITE_FREEMIUS_SECRET_KEY!,
  publicKey: import.meta.env.VITE_FREEMIUS_PUBLIC_KEY!,
});