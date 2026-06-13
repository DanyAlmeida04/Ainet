<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'image_url', 'custom'])]
class Category extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    // Relação: Uma categoria tem muitas imagens de t-shirts no catálogo
    public function tshirtImages()
    {
        return $this->hasMany(TshirtImage::class, 'category_id', 'id');
    }
}