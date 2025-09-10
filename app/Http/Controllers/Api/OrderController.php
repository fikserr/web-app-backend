<?php

namespace App\Http\Controllers;

use App\Services\OneCService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $onec;

    public function __construct(OneCService $onec)
    {
        $this->onec = $onec;
    }

    public function store(Request $request)
    {
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

        $result = $this->onec->createOrder($validated);

        return response()->json($result);
    }
}
