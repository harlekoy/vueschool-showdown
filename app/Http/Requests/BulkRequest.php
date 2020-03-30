<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkRequest extends FormRequest
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
            'batches' => ['required', 'array'],
            'batches.*.subscribers' => ['required', 'array', 'max:1000'],
            'batches.*.subscribers.*.email' => ['required', 'email'],
        ];
    }
}
