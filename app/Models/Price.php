<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'unit_price_catalog', 'unit_price_own', 
    'unit_price_catalog_discount', 'unit_price_own_discount', 
    'qty_discount', 'custom'
])]
class Price extends Model
{
    use HasFactory;

    // Desativa os timestamps automáticos se esta tabela de configuração não os incluir
    public $timestamps = false; 

    /**
     * Atalho estático profissional: permite obter os preços atuais em qualquer 
     * parte do projeto fazendo apenas: Price::current()
     */
    public static function current()
    {
        return self::find(1);
    }
}