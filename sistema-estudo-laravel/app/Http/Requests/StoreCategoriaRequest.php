<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sem autenticação neste projeto de estudo
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categoria')?->id;

        return [
            'nome' => 'required|string|max:255|unique:categorias,nome,' . $categoriaId,
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ];
    }
}
