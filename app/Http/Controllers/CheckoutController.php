<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkoutSuccess(Request $request)
    {
        dd($request->all());
    }
}
