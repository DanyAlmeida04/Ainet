<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; // IMPORTANTE: Adicionar para o Soft Delete
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Atualizado com os campos reais da tabela 'users' do teu diagrama
#[Fillable(['name', 'email', 'password', 'user_type', 'gender', 'blocked', 'photo_url', 'custom'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    // Adicionada a trait SoftDeletes para que o administrador possa apagar contas sem destruir o histórico
    use HasFactory, Notifiable, SoftDeletes; 

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked' => 'boolean', // Facilita trabalhar com 0 e 1 no código como true/false
        ];
    }

    /**
     * Mutator to ensure 'E' (Employee) is mapped to 'F' (Funcionário) before saving to the database
     * to satisfy SQLite ENUM/CHECK constraints.
     */
    public function setUserTypeAttribute($value)
    {
        $this->attributes['user_type'] = ($value === 'E') ? 'F' : $value;
    }

    /**
     * Relação 1 para 1: Um User pode ter um perfil de Customer.
     * O 'id' em ambas as tabelas faz a ligação.
     */
    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'id');
    }
}