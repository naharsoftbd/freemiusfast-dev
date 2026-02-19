import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import Form from './Form';

interface Setting {
    freemius_product_id: number;
    title: string;
    slug: string;
    type: string;
    api_token: string;
    icon: string;
    money_back_period: number;
    refund_policy: string;
    annual_renewals_discount: number;
    renewals_discount_type: string;
    lifetime_license_proration_days: number;
    is_pricing_visible: boolean;
    accepted_payments: number;
    expose_license_key: boolean;
    enable_after_purchase_email_login_link: boolean;
    public_url: string;
    base_url: string;
    api_base_url: string;
}
interface Props {
    setting: Setting[];
}

export default function Edit({ setting }: Props) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Freemius Settings
                </h2>
            }
        >
            <Head title={`Freemius Settings`} />
            <div className="flex h-full flex-1 flex-col items-center gap-4 overflow-x-auto rounded-xl p-4">
                <Form setting={setting.data} />
            </div>
        </AuthenticatedLayout>
    );
}
