<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class OneCService
{
    protected $baseUrl  = 'https://ssglink.uz/UNIVERSAL_ORIGINAL/hs/web.app/';
    protected $username = 'Direktor';
    protected $password = '1122';
    protected $userId   = 1147407714;

    public function getCategories($page = 1, $pageSize = 10)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->get($this->baseUrl . 'category', [
                'page'     => $page,
                'pageSize' => $pageSize,
                'userId'   => $this->userId,
            ]);

        return $response->json();
    }

    public function getProducts($categoryId, $page, $pageSize)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->get($this->baseUrl . 'product', [
                'page'       => $page,
                'pageSize'   => $pageSize,
                'userId'     => $this->userId,
                'categoryId' => $categoryId,
            ]);

        return $response->json();
    }

    public function createOrder(array $orderData)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl . 'order', $orderData);

        return $response->json();
    }
}
