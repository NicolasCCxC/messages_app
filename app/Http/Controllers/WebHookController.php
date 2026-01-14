<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebHookController
{
    public function websiteCLientTransfer(Request $request, string $transactionId)
    {
        Log::info(json_encode($request->all()));
    }
}
