<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\Freemius\FreemiusService;

class PortalController extends Controller
{
    protected $freemiusService;

    public function __construct(FreemiusService $freemiusService)
    {
        $this->freemiusService = $freemiusService;
    }

    public function getPortal(Request $request)
    {
        return $this->freemiusService->getPortalData();
    }

    public function downloadInvoice($paymentId)
    {
        $response = $this->freemiusService->downloadInvoice($paymentId);

        if (! $response->successful()) {
            abort(404, 'Invoice not available');
        }

        return response($response->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-'.$paymentId.'.pdf"',
        ]);
    }
}
