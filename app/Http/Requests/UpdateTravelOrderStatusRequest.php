<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'     => ['required', 'int'],
            'status' => ['required', 'string', 'in:approved,cancelled'],
        ];
    }

    public function messages()
    {
        return [
            'id.required'     => 'Obrigatório enviar o ID da Viagem',
            'status.required' => 'Obrigatório enviar o Status da Viagem',
            'status.in'       => 'Alteração do Status da Ordem de Viagem deve ser somente Aprovada ou Cancelada'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
