<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Freemius\FreemiusProductService;
use App\Enums\RefundPolicy;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\Setting;

class FreemiusProductController extends Controller
{
    protected $productService;

    public function __construct(FreemiusProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'per_page']);
        $recordsPerPage = Setting::getValue('records_per_page', 10);
        $perPage = $request->input('per_page', $recordsPerPage); // default 10
        $search = null;

        if ($request->filled('search')) {
            $search = $request->search;
        }

        $products = $this->productService->getProducts($perPage, $search);
        
        return Inertia::render('Freemius/Products/Index', [
                'products' => $products,
                'filters' => $filters,
            ]);

    }

    public function create()
    {
        return Inertia::render('Freemius/Products/Create');
    }


    public function store(Request $request) 
    {
        $validated = $request->validate([
            'freemius_product_id' => ['required', 'integer', 'min:0', 'unique:freemius_products,freemius_product_id'],
            'api_token' => ['required', 'string', 'min:0', 'unique:freemius_products,api_token'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:freemius_products,slug'],
            'type' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string'],
            'money_back_period' => ['required', 'integer', 'min:0'],
            'refund_policy' => ['required', Rule::in(RefundPolicy::values())],
            'annual_renewals_discount' => ['required', 'integer', 'min:0'],
            'renewals_discount_type' => ['required', 'in:percentage,fixed'],
            'lifetime_license_proration_days' => ['required', 'integer', 'min:0'],
            'is_pricing_visible' => ['boolean'],
            'accepted_payments' => ['required', 'integer', 'min:0'],
            'expose_license_key' => ['boolean'],
            'enable_after_purchase_email_login_link' => ['boolean'],
    ]);

        $product = $this->productService->create([
            ...$validated,
            'user_id' => auth()->id(),
    ]);

        return redirect(route('admin.freemius-products.index'))->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = $this->productService->getProduct($id);

        return Inertia::render('Freemius/Products/Edit',[
            'product' => $product
        ]);
    }

    public function update(Request $request, $id) 
    {
        $validated = $request->validate([
            'freemius_product_id' => [
                'required',
                'integer',
                'min:0',
                Rule::unique('freemius_products', 'freemius_product_id')->ignore($id),
            ],
            'api_token' => [
                'required',
                'string',
                Rule::unique('freemius_products', 'api_token')->ignore($id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('freemius_products', 'slug')->ignore($id),
            ],
            'type' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string'],
            'money_back_period' => ['required', 'integer', 'min:0'],
            'refund_policy' => ['required', Rule::in(RefundPolicy::values())],
            'annual_renewals_discount' => ['required', 'integer', 'min:0'],
            'renewals_discount_type' => ['required', 'in:percentage,fixed'],
            'lifetime_license_proration_days' => ['required', 'integer', 'min:0'],
            'is_pricing_visible' => ['boolean'],
            'accepted_payments' => ['required', 'integer', 'min:0'],
            'expose_license_key' => ['boolean'],
            'enable_after_purchase_email_login_link' => ['boolean'],
        ]);

        $product = $this->productService->updateProduct($id, $validated);

        return redirect(route('admin.freemius-products.index'))->with('success', 'Product updated successfully.');
    }
}
