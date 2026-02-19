import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { freemius } from '@/lib/freemius';
import { usePage } from '@inertiajs/react';

export default function Dashboard() {
    const { auth } = usePage().props;
    if (auth.api_token) {
        localStorage.setItem('api_token', auth.api_token);
    }
    async function main() {
  const pricing = await freemius.pricing.retrieve();
  const purchasesByEmail = await freemius.purchase.retrievePurchasesByEmail('abusalah01diu@gmail.com');
  console.log('Purchases by Email:', purchasesByEmail);
  //console.log(pricing);
}
main();
console.log("Token in LocalStorage:", localStorage.getItem('api_token'));
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            You're logged in!
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
