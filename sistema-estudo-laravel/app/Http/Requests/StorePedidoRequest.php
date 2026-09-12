<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // DESAFIO 2: existe uma validação boa demais pra ser verdade aqui.
    // Teste enviar quantidade = 0 e quantidade = -5 pelo Postman
    // e veja o que acontece.
    public function rules(): array
    {
        return [
            'produto_id' => 'required|exists:produtos,id',
            'cliente_nome' => 'required|string|max:255',
            'quantidade' => 'nullable|integer',
            'status' => 'nullable|in:pendente,pago,cancelado',
        ];
    }
}
