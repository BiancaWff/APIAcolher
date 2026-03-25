<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // Útil se for usar para login
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario'; // Define o nome exato da tabela
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nome',
        'cpf_cnpj',
        'tipo_usuario',
        'email',
        'telefone',
        'instagram',
        'facebook',
        'foto_perfil',
        'descricao',
        'endereco',
        'senha',
    ];

    protected $hidden = [
        'senha', // Esconde a senha em retornos de API/JSON
    ];

    protected $casts = [
        'senha' => 'hashed', // Faz o hash automático se estiver no Laravel 10+
    ];
}
