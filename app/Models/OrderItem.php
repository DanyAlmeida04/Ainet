<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['order_id', 'tshirt_image_id', 'color_code', 'size', 'qty', 'unit_price', 'sub_total', 'custom'])]
class OrderItem extends Model
{
    use HasFactory;

    // Desativa os timestamps criados_at/updated_at se a tabela order_items não os tiver
    public $timestamps = false; 

    // Relação: O item pertence a uma Encomenda
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    // Relação: O item usa um Design/Imagem específico
    public function tshirtImage()
    {
        return $this->belongsTo(TshirtImage::class, 'tshirt_image_id', 'id');
    }

    // Relação: O item tem uma Cor base
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_code', 'code');
    }
}