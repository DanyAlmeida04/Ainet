<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes; // Ativa os timestamps automáticos e o soft delete

    public $incrementing = false; // Como diz no enunciado, o ID vem do User
    // A tabela customers não define created_at/updated_at nas migrations
    // portanto desactivar os timestamps automáticos no modelo.
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nif',
        'address',
        'default_payment_type',
        'default_payment_ref',
        'custom',
    ];

    // Relação 1 para 1 com o User
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
