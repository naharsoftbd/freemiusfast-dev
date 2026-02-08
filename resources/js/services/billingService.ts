import { BillingUpdatePayload } from '@freemius/sdk';
import  api  from '../api'; // Ensure this path points to your axios instance file

/**
 * Fetch billing details
 */
export const fetchBilling = (fs_user_id: string) => 
    api.get(`/freemius-billings/${fs_user_id}`).then(res => res.data);

/**
 * Update billing details
 */
export async function updateBilling(fs_user_id: string, payload: BillingUpdatePayload) {
    try {
        // We use 'api' instead of 'axios'. 
        // The interceptor automatically attaches the Token from localStorage.
        // We no longer need 'await axios.get("/sanctum/csrf-cookie")' for Token auth.
        
        const { data } = await api.put(
            `/freemius-billings/${fs_user_id}`,
            payload
        );
        
        return data;
    } catch (error: any) {
        // Handle validation errors
        if (error.response?.status === 422) {
            alert("Validation failed: " + JSON.stringify(error.response.data.errors));
        }
        throw error;
    }
}