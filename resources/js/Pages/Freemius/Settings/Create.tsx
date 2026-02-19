import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import Form from './Form';


export default function Create() {
    return (
           <AuthenticatedLayout
              header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                  Freemius Settings
                </h2>
              }
            >
            <Head title="Freemius Settings" />
            <div className="flex h-full flex-1 flex-col items-center gap-4 overflow-x-auto rounded-xl p-4">
                <Form settings={null} />
            </div>
        </AuthenticatedLayout>
    );
}
