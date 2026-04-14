<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTravelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'              => ['sometimes', 'string'],
            'destination'         => ['sometimes', 'string'],

            'departure_date_from' => ['sometimes', 'date'],
            'departure_date_to'   => ['sometimes', 'date', 'after_or_equal:departure_date_from'],
            'return_date_from'    => ['sometimes', 'date'],
            'return_date_to'      => ['sometimes', 'date', 'after_or_equal:return_date_from'],
        ];
    }

    public function messages()
    {
        return [
            'departure_date_from.date'         => 'Filtro de Data Inicial da Viagem deve ser uma Data Válida',
            'departure_date_to.date'           => 'Filtro de Data Final da Viagem deve ser uma Data Válida',
            'departure_date_to.after_or_equal' => 'Data Final da Viagem não pode ser inferior a Data Inicial da Viagem',

            'return_date_from.date'            => 'Filtro de Data Inicial de Retorno da Viagem deve ser uma Data Válida',
            'return_date_to.date'              => 'Filtro de Data Final de Retorno Viagem deve ser uma Data Válida',
            'return_date_to.after_or_equal'    => 'Data Final de Retorno da Viagem não pode ser inferior a Data Inicial de Retorno da Viagem',
        ];
    }
}
