import { Button } from '@/components/ui/button';
import MainForm from '@/components/Form/MainForm';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { router, useForm } from '@inertiajs/react';
import { useRef, useState, useEffect } from 'react';
import { X } from 'lucide-react';
import { CheckboxBasic } from '@/components/CheckboxBasic';
import {
    Field,
    FieldDescription,
    FieldGroup,
    FieldLabel,
    FieldLegend,
    FieldSeparator,
    FieldSet,
} from "@/components/ui/field"
import { toast } from "sonner";
import echo from '../../../echo';

interface SettingFormData {
    developer_id: number;
    developer_public_key: string;
    developer_secret_key: string;
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

export default function Form({ setting }) {
    const { data, setData, post, processing, errors } = useForm<SettingFormData>({
        developer_id: setting?.developer_id || '',
        developer_public_key: setting?.developer_public_key || '',
        developer_secret_key: setting?.developer_secret_key || '',
        freemius_product_id: setting?.freemius_product_id || '',
        api_token: setting?.api_token || '',
        title: setting?.title || '',
        slug: setting?.slug || '',
        type: setting?.type || 'saas',
        icon: setting?.icon || null,
        money_back_period: setting?.money_back_period || 0,
        refund_policy: setting?.refund_policy || 'strict',
        annual_renewals_discount: setting?.annual_renewals_discount || 0,
        renewals_discount_type: setting?.renewals_discount_type || 'percentage',
        lifetime_license_proration_days: setting?.lifetime_license_proration_days || 30,
        is_pricing_visible: setting?.is_pricing_visible || true,
        accepted_payments: setting?.accepted_payments || 0,
        expose_license_key: setting?.expose_license_key || true,
        enable_after_purchase_email_login_link: setting?.enable_after_purchase_email_login_link || true,
        public_url: setting?.public_url || '',
        base_url: setting?.base_url || '',
        api_base_url: setting?.api_base_url || '',
    });

    const [previewimage, setPreviewimage] = useState(setting?.image_url);
    const fileInputRef = useRef(null);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('admin.freemius.settings.store'), {
            onSuccess: (page) => {
                toast.success(page.props.flash.message.success);
            },
        });
    };

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) setData('icon', file);
    };

    const handleRemoveImage = () => {
        setPreviewimage(null);
        setData('icon', null);
        if (fileInputRef.current) fileInputRef.current.value = null;
    };

    useEffect(() => {
        const channel = echo.private('product-updates')
            .listen('.ProductSynced', (e: any) => { // Try adding the dot here
                router.reload({ 
                only: ['setting'], // Must match your prop key
                onSuccess: (page) => {
                    // Update the form's internal state with the fresh data
                    setData(page.props.setting.data); 
                }
            });
            });

        return () => echo.leave('product-updates');
    }, []);

    return (
        <MainForm handleSubmit={handleSubmit} handleCancel={false} processing={processing} submitTitle={setting ? 'Update' : 'Save'}>

            <FieldGroup className='grid grid-cols-2 gap-4'>
                <h1 className='text-2xl font-bold'>{setting?.title}</h1>
                <p>Status: {setting?.is_synced ? '✅ Synced' : '⏳ Syncing...'}</p>
                <FieldSet className='grid gap-2 col-span-2'>
                    <FieldLegend>Developer setting</FieldLegend>
                    {/*  Developer Section */}
                    <div className="grid gap-2 col-span-2">
                        <Label htmlFor="developer_id">Developer ID:</Label>
                        <Input required id="developer_id" value={data.developer_id} onChange={(e) => setData('developer_id', e.target.value)} placeholder="Enter Developer id" />
                        {errors.developer_id && <InputError className="text-red-700" message={errors.developer_id} />}
                    </div>
                    <div className="grid gap-2 col-span-2">
                        <Label htmlFor="developer_public_key">Developer Public Key:</Label>
                        <Input required id="developer_public_key" value={data.developer_public_key} onChange={(e) => setData('developer_public_key', e.target.value)} placeholder="Enter developer public key" />
                        {errors.developer_public_key && <InputError className="text-red-700" message={errors.developer_public_key} />}
                    </div>
                    <div className="grid gap-2 col-span-2">
                        <Label htmlFor="developer_secret_key">Developer Secrect Key:</Label>
                        <Input required id="developer_secret_key" value={data.developer_secret_key} onChange={(e) => setData('developer_secret_key', e.target.value)} placeholder="Enter developer secret key" />
                        {errors.developer_secret_key && <InputError className="text-red-700" message={errors.developer_secret_key} />}
                    </div>
                    <div className="grid gap-2 col-span-2">
                        <Label htmlFor="public_url">Public URL:</Label>
                        <Input required id="public_url" value={data.public_url} onChange={(e) => setData('public_url', e.target.value)} placeholder="https://www.yoururl.com" />
                        {errors.public_url && <InputError className="text-red-700" message={errors.public_url} />}
                    </div>
                    <div className="grid gap-2 col-span-2">
                        <Label htmlFor="base_url">Marchent URL:</Label>
                        <Input required id="base_url" value={data.base_url} onChange={(e) => setData('base_url', e.target.value)} placeholder="https://checkout.marchentbaseurl.com" />
                        {errors.base_url && <InputError className="text-red-700" message={errors.base_url} />}
                    </div>
                    <div className="grid gap-2 col-span-2">
                        <Label htmlFor="api_base_url">Marchent API URL:</Label>
                        <Input required id="api_base_url" value={data.api_base_url} onChange={(e) => setData('api_base_url', e.target.value)} placeholder="https://api.mearchenturl.com/v1/" />
                        {errors.api_base_url && <InputError className="text-red-700" message={errors.api_base_url} />}
                    </div>
                </FieldSet>
                <FieldSeparator className='grid gap-2 col-span-2' />

                <FieldSet className='grid gap-2 col-span-2'>
                    <FieldLegend>Product setting</FieldLegend>

                    {/*  Product Section */}
                    <div className="grid gap-2 col-span-2">
                        <Label htmlFor="freemius_product_id">Freemius Product ID:</Label>
                        <Input required id="freemius_product_id" value={data.freemius_product_id} onChange={(e) => setData('freemius_product_id', e.target.value)} placeholder="Enter Freemius product id" />
                        {errors.freemius_product_id && <InputError className="text-red-700" message={errors.freemius_product_id} />}
                    </div>
                    <div className="grid gap-2 col-span-2">
                        <Label htmlFor="api_token">API Bearer Authorization Token:</Label>
                        <Input required id="api_token" value={data.api_token} onChange={(e) => setData('api_token', e.target.value)} placeholder="Enter Api Token" />
                        {errors.api_token && <InputError className="text-red-700" message={errors.api_token} />}
                    </div>

                </FieldSet>
                <div className="grid gap-2">
                    <Label htmlFor="title">Title:</Label>
                    <Input id="title" value={data.title} onChange={(e) => setData('title', e.target.value)} placeholder="Enter Title" />
                    {errors.title && <InputError className="text-red-700" message={errors.title} />}
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="slug">Slug:</Label>
                    <Input id="slug" value={data.slug} onChange={(e) => setData('slug', e.target.value)} placeholder="Enter Slug" />
                    {errors.slug && <InputError className="text-red-700" message={errors.slug} />}
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="type">Type:</Label>
                    <Input id="type" value={data.type} onChange={(e) => setData('type', e.target.value)} placeholder="Enter Type" />
                    {errors.type && <InputError className="text-red-700" message={errors.type} />}
                </div>

                {/* Image Upload */}
                <div className="grid gap-2">
                    <Label>Product Logo:</Label>
                    {data.icon ? (
                        <div className="relative flex justify-center overflow-hidden rounded-md border">
                            <img
                                src={typeof data.icon === 'string' ? data.icon : URL.createObjectURL(data.icon)}
                                alt="Preview"
                                className="h-40 mx-w-[100%]"
                            />
                            <Button type="button" size="icon" variant="destructive" className="absolute top-2 right-2" onClick={handleRemoveImage}>
                                <X className="h-4 w-4" />
                            </Button>
                        </div>
                    ) : previewimage ? (
                        <div className="relative overflow-hidden rounded-md border">
                            <img src={previewimage} alt="Preview" className="h-40 w-full object-cover" />
                            <Button type="button" size="icon" variant="destructive" className="absolute top-2 right-2" onClick={handleRemoveImage}>
                                <X className="h-4 w-4" />
                            </Button>
                        </div>
                    ) : (
                        <Input type="file" accept="image/*" ref={fileInputRef} onChange={handleImageChange} />
                    )}
                    {errors.icon && <p className="text-sm text-red-500">{errors.icon}</p>}
                </div>

                {/* Policy */}
                <div className="grid gap-2">
                    <Label htmlFor="money_back_period">Money Back Period (days):</Label>
                    <Input
                        type="number"
                        id='money_back_period'
                        placeholder="Money Back Period (days)"
                        value={data.money_back_period}
                        onChange={e => setData('money_back_period', e.target.value)}
                        className="border p-2"
                    />
                </div>
                <div className="grid gap-2">
                    <Label htmlFor="refund_policy">Refund_policy:</Label>
                    <select
                        id='refund_policy'
                        value={data.refund_policy}
                        onChange={e => setData('refund_policy', e.target.value)}
                        className="border p-2"
                    >
                        <option value="flexible">Flexible</option>
                        <option value="moderate">Moderate</option>
                        <option value="strict">Strict</option>
                    </select>
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="annual_renewals_discount">Annual Renewal Discount:</Label>
                    <Input
                        id='annual_renewals_discount'
                        type="number"
                        placeholder="Annual Renewal Discount"
                        value={data.annual_renewals_discount}
                        onChange={e => setData('annual_renewals_discount', e.target.value)}
                        className="border p-2"
                    />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="renewals_discount_type">Renewals Discount Type:</Label>
                    <select
                        id='renewals_discount_type'
                        value={data.renewals_discount_type}
                        onChange={e => setData('renewals_discount_type', e.target.value)}
                        className="border p-2"
                    >
                        <option value="percentage">Percentage</option>
                        <option value="fixed">Fixed</option>
                    </select>
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="lifetime_license_proration_days">Lifetime License Proration Days:</Label>
                    <Input
                        id='lifetime_license_proration_days'
                        type="number"
                        placeholder="Lifetime License Proration Days"
                        value={data.lifetime_license_proration_days}
                        onChange={e => setData('lifetime_license_proration_days', e.target.value)}
                        className="border p-2"
                    />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="accepted_payments">Accepted Payments:</Label>
                    <Input
                        id='accepted_payments'
                        type="number"
                        placeholder="Accepted Payments"
                        value={data.accepted_payments}
                        onChange={e => setData('accepted_payments', e.target.value)}
                        className="border p-2"
                    />
                </div>

                {/* Boolean Toggles */}
                <div className="grid gap-2">
                    <CheckboxBasic
                        setData={setData}
                        defaultChecked={data.is_pricing_visible}
                        name='is_pricing_visible'
                        label='Pricing Visible'
                    />
                </div>

                <div className="grid gap-2">
                    <CheckboxBasic
                        setData={setData}
                        defaultChecked={data.expose_license_key}
                        name='expose_license_key'
                        label='Expose License Key'
                    />
                </div>

                <div className="grid gap-2">
                    <CheckboxBasic
                        setData={setData}
                        defaultChecked={data.enable_after_purchase_email_login_link}
                        name='enable_after_purchase_email_login_link'
                        label='Enable Email Login Link'
                    />
                </div>
            </FieldGroup>

        </MainForm>
    );
}
