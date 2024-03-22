<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
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
<<<<<<< HEAD
            'username' => ['required', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required'],
=======
            'username'=>'required|unique:users,username',
            'email'=>['required','email','unique:users,email'],
            'password'=>['required','string','min:6'],
            'role'=>'required|in:company,job_seeker',
>>>>>>> bd87855d075935ca1bbc25d794b2acf764982de7
        ];
    }
}
