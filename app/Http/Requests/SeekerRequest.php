<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeekerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name'=>'required|string',
            'last_name'=>'required|string',
            'birth_day'=>'required',
            'location'=>'string|required',
            'image'=>'image|mimes:jpeg,png,bmp,jpg,gif,sav',
            'skills'=>'required',
            'certificates'=>'required',
            'about'=>'required'
        ];
    }
}
