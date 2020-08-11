<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'card_holder' => 'required',
            'card_number' => 'required',
            'card_month' => 'required',
            'card_year' => 'required',
            'card_cvv' => 'required',
            'parcelas' => 'required',
            'encryptedCard' => 'required',
        ];
    }
}
