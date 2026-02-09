import  api  from '../api'; // Ensure this path points to your axios instance file

export function cancelSubscription(
    subscriptionId: number | string,
    payload: {
        reason?: string;
        reason_ids?: number[];
    }
) {
    return api.delete(
        `/subscriptions/${subscriptionId}/cancel`,
        {
            params: {
                reason: payload.reason,
                reason_id: payload.reason_ids?.[0], // Freemius expects single ID
            },
        }
    );
}
