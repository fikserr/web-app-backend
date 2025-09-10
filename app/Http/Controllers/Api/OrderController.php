<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'userId'             => 'required|string',
            'UUID'               => 'nullable|string',
            'date'               => 'required|date',
            'comment'            => 'nullable|string',
            'basket'             => 'required|array|min:1',
            'basket.*.productId' => 'required|string',
            'basket.*.measureId' => 'required|string',
            'basket.*.quantity'  => 'required|integer|min:1',
            'basket.*.price'     => 'required|numeric|min:0',
        ]);

        $orderId = $data['UUID'] ?? Str::uuid()->toString();

        // Order yaratish
        $order = Order::create([
            'id'      => $orderId,
            'user_id' => $data['userId'],
            'comment' => $data['comment'] ?? null,
            'date'    => $data['date'],
        ]);

        // Basketni yozish
        foreach ($data['basket'] as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['productId'],
                'measure_id' => $item['measureId'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        return response()->json([
            'ok'       => true,
            'message'  => 'Buyurtma DBga saqlandi',
            'order_id' => $order->id,
        ]);
    }
}
