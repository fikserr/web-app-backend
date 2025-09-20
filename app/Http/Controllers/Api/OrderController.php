<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OneCService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $onec;

    public function __construct(OneCService $onec)
    {
        $this->onec = $onec;
    }

    public function store(Request $request)
    {
        // 1️⃣ Request loglash
        Log::info('Incoming order request', [
            'request' => $request->all()
        ]);

        // 2️⃣ Validation
        $validated = $request->validate([
            'userId' => 'required|string',
            'UUID' => 'required|string',
            'date' => 'required|date',
            'comment' => 'nullable|string',
            'basket' => 'required|array',
            'basket.*.productId' => 'required|string',
            'basket.*.measureId' => 'required|string',
            'basket.*.quantity' => 'required|integer',
            'basket.*.price' => 'required|numeric',
        ]);

        // 3️⃣ Validated ma'lumotni loglash
        Log::info('Validated order data', [
            'validated' => $validated
        ]);

        // 4️⃣ OneC ga yuborish
        $result = $this->onec->createOrder($validated);

        // 5️⃣ OneC javobini loglash
        Log::info('OneC response', [
            'result' => $result
        ]);

        return response()->json($result);
    }
}
