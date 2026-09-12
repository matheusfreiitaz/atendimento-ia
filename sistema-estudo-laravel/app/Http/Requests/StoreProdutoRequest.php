<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $produtoId = $this->route('produto')?->id;

        return [
            'categoria_id' => 'required|exists:categorias,id',
            'sku' => 'required|string|max:50|unique:produtos,sku,' . $produtoId,
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'ativo' => 'boolean',
        ];
    }
}
