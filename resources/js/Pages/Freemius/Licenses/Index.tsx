import React from "react";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

interface License {
    id: number | string;
    plan: string;
    issued_at: string;
    key: string;
    user: string;
    subscription: string;
    quota: number;
    expiration: string | null;
}

interface Props {
    licenses: License[];
}

const Index: React.FC<Props> = ({ licenses }) => {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Account
                </h2>
            }
        >
            <Head title="Account" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="p-6">
                                <h1 className="text-2xl font-semibold mb-6">Licenses</h1>

                                <div className="overflow-x-auto bg-white rounded-xl shadow">
                                    <table className="min-w-full text-sm text-left">
                                        <thead className="bg-gray-100 text-gray-600 uppercase text-xs">
                                            <tr>
                                                <th className="px-6 py-4">ID</th>
                                                <th className="px-6 py-4">Plan</th>
                                                <th className="px-6 py-4">Issued At</th>
                                                <th className="px-6 py-4">Key</th>
                                                <th className="px-6 py-4">User</th>
                                                <th className="px-6 py-4">Subscription</th>
                                                <th className="px-6 py-4">Quota</th>
                                                <th className="px-6 py-4">Expiration</th>
                                            </tr>
                                        </thead>

                                        <tbody className="divide-y divide-gray-200">
                                            {licenses.length === 0 && (
                                                <tr>
                                                    <td
                                                        colSpan={8}
                                                        className="px-6 py-6 text-center text-gray-500"
                                                    >
                                                        No licenses found.
                                                    </td>
                                                </tr>
                                            )}

                                            {licenses.map((license) => (
                                                <tr key={license.id} className="hover:bg-gray-50">
                                                    <td className="px-6 py-4 font-medium">
                                                        {license.id}
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        {license.plan}
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        {new Date(
                                                            license.issued_at
                                                        ).toLocaleDateString()}
                                                    </td>

                                                    <td className="px-6 py-4 font-mono text-xs break-all">
                                                        {license.key}
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        {license.user}
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        {license.subscription}
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        {license.quota}
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        {license.expiration
                                                            ? new Date(
                                                                license.expiration
                                                            ).toLocaleDateString()
                                                            : "—"}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout >
    );
};

export default Index;
