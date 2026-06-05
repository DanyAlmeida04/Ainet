<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['customer_id', 'category_id', 'name', 'description', 'image_url', 'custom'])]
class TshirtImage extends Model
{
    use HasFactory, SoftDeletes;

    // Helper útil para saberes se a imagem é privada ou de catálogo no teu código
    public function isPrivate(): bool
    {
        return $this->customer_id !== null;
    }

    // Relação: O design pertence a um Cliente (pode ser nulo)
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    // Relação: O design pertence a uma Categoria (pode ser nulo)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}