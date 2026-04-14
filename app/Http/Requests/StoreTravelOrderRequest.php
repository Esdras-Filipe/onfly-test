<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTravelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination'    => ['required', 'string'],
            'departure_date' => ['required', 'date'],
            'return_date'    => ['required', 'date', 'after:departure_date'],
        ];
    }

    public function messages()
    {
        return [
            'destination.required'    => 'Obrigatório enviar o destino da viagem',
            'departure_date.required' => 'Obrigatório enviar a data da viagem',
            'departure_date.date'     => 'Campo de data da viagem deve ser uma data válida',
            'return_date.required'    => 'Obrigatório enviar a data de retorno da viagem',
            'return_date.date'        => 'Campo de data de retorna da viagem deve ser uma data válida',
            'return_date.after'       => 'Data de retorno da viagem não pode ser inferior a data de partida da viagem',
        ];
    }
}
