<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OneCService;
use Illuminate\Http\Request;

class OneCController extends Controller
{
    protected $oneC;

    public function __construct(OneCService $oneC)
    {
        $this->oneC = $oneC;
    }

    public function categories(Request $request)
    {
        $page     = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 10);

        $data = $this->oneC->getCategories($page, $pageSize);

        return response()->json($data); // test uchun dd emas
    }

    public function products(Request $request)
    {
        $categoryId = $request->get('categoryId');
        $page       = $request->get('page', 1);
        $pageSize   = $request->get('pageSize', 10);

        return response()->json($this->oneC->getProducts($categoryId, $page, $pageSize));
    }
}