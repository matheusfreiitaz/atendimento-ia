<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos');
            $table->string('cliente_nome');
            $table->unsignedInteger('quantidade');
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('total', 10, 2)->nullable();
            $table->string('status')->default('pendente'); // pendente | pago | cancelado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
