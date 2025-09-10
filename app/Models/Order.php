<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $incrementing = false; // UUID uchun
    protected $keyType   = 'string';

    protected $fillable = ['id', 'user_id', 'comment', 'date'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
