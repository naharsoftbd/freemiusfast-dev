import * as React from 'react';
import { PortalData } from '@freemius/sdk';
import { SectionHeading } from './section-heading';
import { useLocale } from '@/utils/locale';
import { BillingForm } from './billing-form';
import { BillingInfo } from './billing-info';
import { fetchBilling, updateBilling } from '@/services/billingService';
import Spinner from '@/icons/spinner';


export function BillingSection(props: {
    billing: NonNullable<PortalData['billing']>;
    user: NonNullable<PortalData['user']>;
}) {
    const [isUpdating, setIsUpdating] = React.useState<boolean>(false);
    const locale = useLocale();
    const [billing, setBilling] = React.useState<NonNullable<PortalData['billing']>>({
        ...props.billing,
    });
    const [loading, setLoading] = React.useState(true);
    const { user } = props;
    const fs_user_id = user.id;

    React.useEffect(() => {
        async function loadBilling() {
            if (!fs_user_id) return; // Guard clause for missing ID

            try {
                setLoading(true);
                const data = await fetchBilling(fs_user_id);

                // Only set if data exists and isn't empty
                if (data && Object.keys(data).length > 0) {
                    setBilling(data);
                } else {
                    console.warn("No billing data found for this user.");
                }
            } catch (err) {
                console.error("Failed to fetch billing:", err);
            } finally {
                setLoading(false);
            }
        }
        loadBilling();
    }, [fs_user_id]);

    const handleUpdateBilling = async (payload: any) => {
        const updated = await updateBilling(fs_user_id, payload);
        setBilling(updated);
        setIsUpdating(false);
    };

    if (loading) return <Spinner />;
    if (!billing) return <p>No billing info found.</p>;

    return (
        <div className="fs-saas-starter-billing-section">
            <SectionHeading>{locale.portal.billing.title()}</SectionHeading>

            {isUpdating ? (
                <BillingForm billing={billing} setIsUpdating={setIsUpdating} updateBilling={handleUpdateBilling} />
            ) : (
                <BillingInfo billing={billing} user={props.user} setIsUpdating={setIsUpdating} />
            )}
        </div>
    );
}
