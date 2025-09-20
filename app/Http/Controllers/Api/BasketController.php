<?php
namespace App\Http\Controllers\Api;

use App\Events\BasketUpdated;
use App\Http\Controllers\Controller;
use App\Models\Basket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BasketController extends Controller
{
    /**
     * Plus/minus yoki quantity bilan yangilash.
     * Body:
     * - userId (string) required
     * - productId (string) required
     * - measureId (string) required
     * - action (plus|minus) optional (agar quantity berilmagan bo‘lsa)
     * - quantity (integer) optional (exact set)
     * - price (numeric) required
     * - productName (string) required
     * - productImage (string) required
     */
    public function updateQuantity(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'required|string',
            'product_id' => 'required|string',
            'measure_id' => 'required|string',
            'action'     => 'nullable|in:plus,minus',
            'quantity'   => 'nullable|integer|min:0',
            'price'      => 'required|numeric|min:0',
            'name'       => 'required|string',
            'image'      => 'nullable|string',
        ]);

        $item = Basket::where('user_id', $data['user_id'])
            ->where('product_id', $data['product_id'])
            ->where('measure_id', $data['measure_id'])
            ->first();

        // agar quantity berilgan bo‘lsa
        if (array_key_exists('quantity', $data) && $data['quantity'] !== null) {
            $qty = intval($data['quantity']);

            if (! $item && $qty > 0) {
                $item = Basket::create([
                    'user_id'    => $data['user_id'],
                    'product_id' => $data['product_id'],
                    'measure_id' => $data['measure_id'],
                    'quantity'   => $qty,
                    'price'      => $data['price'],
                    'name'       => 'required|string',
                    'image'      => 'nullable|string',
                ]);
            } elseif ($item) {
                if ($qty <= 0) {
                    $item->delete();
                    return response()->json(['ok' => true, 'message' => 'Tovar basketdan o‘chirildi', 'removed' => true]);
                }
                $item->quantity = $qty;
                $item->price    = $data['price'];
                $item->name     = $data['name'];
                $item->image    = $data['image'];
                $item->save();
            }

            Log::debug('Basket set quantity', $data);

            return response()->json(['ok' => true, 'item' => $item]);
        }

        // aksi holda action (plus/minus)
        if (! $item) {
            if (($data['action'] ?? null) === 'plus') {
                $item = Basket::create([
                    'user_id'    => $data['user_id'],
                    'product_id' => $data['product_id'],
                    'measure_id' => $data['measure_id'],
                    'quantity'   => 1,
                    'price'      => $data['price'],
                    'name'       => $data['name'],
                    'image'      => $data['image'] ?? null,
                ]);

                Log::debug('Basket created (plus)', $data);

                return response()->json(['ok' => true, 'item' => $item]);
            }

            return response()->json(['ok' => false, 'message' => 'Tovar topilmadi'], 404);
        }

        if (($data['action'] ?? null) === 'plus') {
            $item->quantity += 1;
            $item->price = $data['price'];
            $item->name  = $data['name'];
            $item->image = $data['image'];
            $item->save();

            // ✅ plus bo‘lganda
            broadcast(new BasketUpdated($item, $data['user_id']))->toOthers();

            return response()->json(['ok' => true, 'item' => $item]);
        }

        if (($data['action'] ?? null) === 'minus') {
            $item->quantity -= 1;
            if ($item->quantity <= 0) {
                $item->delete();

                // ✅ minusda quantity 0 bo‘lsa
                broadcast(new BasketUpdated(null, $data['user_id']))->toOthers();
                return response()->json(['ok' => true, 'message' => 'Tovar basketdan o‘chirildi', 'removed' => true]);
            }

            $item->price = $data['price'];
            $item->name  = $data['name'];
            $item->image = $data['image'];
            $item->save();

            // ✅ minus bo‘lganda (lekin hali mavjud bo‘lsa)
            broadcast(new BasketUpdated($item, $data['user_id']))->toOthers();

            return response()->json(['ok' => true, 'item' => $item]);
        }

        return response()->json(['ok' => false, 'message' => 'Invalid request, provide action or quantity'], 422);
    }

    // userning basketini olish
    public function list($userId)
    {
        $basket = Basket::where('user_id', $userId)->get();

        $products = $basket->map(function ($item) {
            return [
                'basket_id'  => $item->id,
                'measure_id' => $item->measure_id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->price,
                'name'       => $item->name,
                'image'      => $item->image,
            ];
        });

        return response()->json([
            'ok'     => true,
            'basket' => $products,
        ]);
    }

    public function remove($id)
    {
        $deleted = Basket::where('id', $id)->delete();
        return response()->json(['ok' => true, 'removed' => (bool) $deleted]);
    }
}
