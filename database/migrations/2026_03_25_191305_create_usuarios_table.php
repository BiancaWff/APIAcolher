<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('usuario', function (Blueprint $table) {
        $table->id('id_usuario'); // Chave primária conforme a imagem
        $table->string('nome', 100);
        $table->string('cpf_cnpj', 100)->unique();
        $table->enum('tipo_usuario', ['comum', 'admin']); // Ajuste os valores conforme sua regra
        $table->string('email', 100)->unique();
        $table->string('telefone', 11)->nullable();
        $table->string('instagram', 100)->nullable();
        $table->string('facebook', 100)->nullable();
        $table->string('foto_perfil', 100)->nullable();
        $table->string('descricao', 500)->nullable();
        $table->string('endereco', 100)->nullable();
        $table->string('senha', 500);
        $table->timestamps(); // Recomendado pelo Laravel (created_at e updated_at)
    });
}
};
