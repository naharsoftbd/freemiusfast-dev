<?php

namespace App\Http\Requests\Freemius;

use Illuminate\Foundation\Http\FormRequest;

class FreemiusPaymentSuccessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'action' => 'required|string',
            'amount' => 'required|numeric',
            'billing_cycle' => 'required|integer',
            'currency' => 'required|string|max:3',
            'email' => 'required|email',
            'expiration' => 'nullable|date',
            'license_id' => 'required|string',
            'plan_id' => 'required|integer',
            'pricing_id' => 'required|integer',
            'quota' => 'required|integer',
            'subscription_id' => 'nullable|integer',
            'payment_id' => 'nullable|integer',
            'signature' => 'required|string',
            'tax' => 'required|integer',
        ];
    }
}
