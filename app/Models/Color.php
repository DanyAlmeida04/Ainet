<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'name', 'custom'])]
class Color extends Model
{
    use HasFactory, SoftDeletes;

    // Como a chave primária não se chama 'id' e é uma string (texto)
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    // Relação: Uma cor pode estar presente em muitos itens de encomendas
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'color_code', 'code');
    }
}