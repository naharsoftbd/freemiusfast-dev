import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from "@inertiajs/react";
import { ConfirmDialog } from '@/components/ConfirmDialog';
import PageHeader from '@/components/PageHeader';
import { Pagination } from '@/components/pagination';
import Table from '@/components/Table';
import React, { FormEventHandler, useEffect, useState } from 'react';
import { useCan } from '@/lib/can';

interface Product {
    id: number;
    title: string;
    slug: string;
    type: string;
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
}

interface Props {
    products: Product[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/admin/freemius-products',
    },
];

export default function Index({ products }: Props) {
    const { flash, filters } = usePage().props;
    const [showConfirm, setShowConfirm] = useState(false);
    const [product, setProduct] = useState(false);
    const canCreate = useCan('Create');
    const canEdit = useCan('Edit');
    const canDelete = useCan('Delete');

    const { data, setData } = useForm({
        search: filters?.search || '',
    });

    const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setData('search', value);
        const queryString = value ? { search: value } : {};
        router.get(route('admin.freemius-products.index'), queryString, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        setData('search', '');
        router.get(
            route('admin.freemius-products.index'),
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    const handlePerPageChange = (e) => {
        router.get(route('admin.freemius-products.index'), { ...filters, per_page: e.target.value }, { preserveState: true });
    };

    const handleCreate = () => {
        router.visit(route('admin.freemius-products.create'));
    };

    const handleConfirm = () => {
        router.delete(route('admin.freemius-products.destroy', product.id), {
            preserveScroll: true,
            onSuccess: () => {
                setShowConfirm(false);
            },
        });
    };

    const handleCancel = () => {
        setShowConfirm(false);
    };

    useEffect(() => {
        if (flash.message.success) {
            toast.success(flash.message.success);
        }
        if (flash.message.error) {
            toast.error(flash.message.error);
        }
    }, [flash]);

    const handleEdit: FormEventHandler = (item) => {
        router.get(route('admin.freemius-products.edit', item.id));
    };

    const handleDelete: FormEventHandler = (item) => {
        setProduct(item);
        setShowConfirm(true);
    };

    const columns = [
        {
            key: 'title',
            label: 'Product',
            render: (item: Product) => (
                <div className="flex items-center gap-3">
                    {item.icon && (
                        <img
                            src={item.icon}
                            alt={item.title}
                            className="w-8 h-8 rounded"
                        />
                    )}
                    <span className="font-medium">{item.title}</span>
                </div>
            ),
        },
        { key: 'slug', label: 'Slug' },

        {
            key: 'type',
            label: 'Type',
            render: (item: Product) => (
                <span className="capitalize">{item.type}</span>
            ),
        },

        {
            key: 'money_back_period',
            label: 'Refund Policy',
            render: (item: Product) => (
                <span>
                    {item.money_back_period} days ({item.refund_policy})
                </span>
            ),
        },

        {
            key: 'annual_renewals_discount',
            label: 'Renewal Discount',
            render: (item: Product) => (
                <span>
                    {item.annual_renewals_discount}
                    {item.renewals_discount_type === 'percentage'
                        ? '%'
                        : ' Fixed'}
                </span>
            ),
        },

        {
            key: 'lifetime_license_proration_days',
            label: 'Lifetime Proration',
            render: (item: Product) => (
                <span>{item.lifetime_license_proration_days} days</span>
            ),
        },

        {
            key: 'is_pricing_visible',
            label: 'Pricing',
            render: (item: Product) => (
                <span
                    className={`px-2 py-1 rounded text-xs font-semibold ${item.is_pricing_visible
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                        }`}
                >
                    {item.is_pricing_visible ? 'Visible' : 'Hidden'}
                </span>
            ),
        },

        {
            key: 'accepted_payments',
            label: 'Accepted Payments',
        },

        {
            key: 'expose_license_key',
            label: 'License Key',
            render: (item: Product) => (
                <span
                    className={`px-2 py-1 rounded text-xs font-semibold ${item.expose_license_key
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                        }`}
                >
                    {item.expose_license_key ? 'Exposed' : 'Hidden'}
                </span>
            ),
        },

        {
            key: 'enable_after_purchase_email_login_link',
            label: 'Email Login',
            render: (item: Product) => (
                <span
                    className={`px-2 py-1 rounded text-xs font-semibold ${item.enable_after_purchase_email_login_link
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                        }`}
                >
                    {item.enable_after_purchase_email_login_link
                        ? 'Enabled'
                        : 'Disabled'}
                </span>
            ),
        },
    ];

    return (
        <AppLayout
            breadcrumbs={breadcrumbs}
        >
            <Head title="Products" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl px-4">
                <PageHeader
                    data={data}
                    perPageItem={filters.per_page}
                    handlePerPageChange={handlePerPageChange}
                    handleSearch={handleSearch}
                    placeholder="Search Products ..."
                    handleReset={handleReset}
                    handleCreate={handleCreate}
                    canCreate={canCreate}
                />
                {/* Table */}
                <div className="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <div className="inline-block min-w-full">
                        <Table
                            items={products.data}
                            columns={columns}
                            canEdit={canEdit}
                            canDelete={canDelete}
                            onEdit={handleEdit}
                            onDelete={handleDelete}
                            actionHeadClass="border p-4 w-24 text-center"
                            emptyMessage="No Blogs found."
                        />
                        {/* Pagination */}
                        <div className="mt-4">
                            <Pagination items={products} />
                        </div>
                    </div>
                </div>
            </div>
            <ConfirmDialog
                title={`Delete Product${product ? `: ${product.title}` : ''}`}
                message={
                    product
                        ? `Are you sure you want to delete "${product.title}"? This action cannot be undone.`
                        : 'Are you sure you want to delete this product?'
                }
                onConfirm={handleConfirm}
                onCancel={handleCancel}
                isOpen={showConfirm}
            />

        </AppLayout >
    );
}
