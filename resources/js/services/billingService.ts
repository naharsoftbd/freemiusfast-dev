import axios from 'axios';
import { BillingUpdatePayload } from '@freemius/sdk';

export async function fetchBilling(fs_user_id: string) {
    const { data } = await axios.get(`/api/freemius-billings/${fs_user_id}`);
    return data;
}

export async function updateBilling(fs_user_id: string, payload: BillingUpdatePayload) {
    const { data } = await axios.patch(`/api/freemius-billings/${fs_user_id}`, payload);
    return data;
}
