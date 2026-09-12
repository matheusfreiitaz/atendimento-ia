<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2);
            $table->unsignedInteger('estoque')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('nome');
            $table->index('preco');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
