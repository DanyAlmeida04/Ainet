<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'status', 'customer_id', 'date', 'total_price', 'notes', 
    'reason_for_cancellation', 'nif', 'address', 
    'payment_type', 'payment_ref', 'receipt_url', 'custom'
])]
class Order extends Model
{
    use HasFactory;

    // Garante que a data é tratada como um objeto Carbon/Datetime pelo PHP
    protected $casts = [
        'date' => 'date',
        'total_price' => 'decimal:2',
    ];

    // Relação: A encomenda pertence a um cliente
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    // Relação: Uma encomenda tem vários itens (produtos) dentro dela
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    // Relação: Uma encomenda pode ter reportes de recibo
    public function receiptReports()
    {
        return $this->hasMany(ReceiptReport::class, 'order_id', 'id');
    }
}