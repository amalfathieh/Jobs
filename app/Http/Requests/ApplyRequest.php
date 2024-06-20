<?php

namespace App\Http\Requests;

use App\Traits\responseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class ApplyRequest extends FormRequest
{
    use responseTrait;
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
            'cv' => 'required_without:full_name,birth_day,location,about,skills,certificates,languages,projects,experiences,contacts',

            'full_name' => 'required_without:cv',
            'birth_day' => 'required_without:cv',
            'location' => 'required_without:cv',
            'about' => 'required_without:cv',
            'skills' => 'required_without:cv',
            'certificates' => 'required_without:cv',
            'languages' => 'required_without:cv',
            'projects' => 'required_without:cv',
            'experiences' => 'required_without:cv',
            'contacts' => 'required_without:cv',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            $this->apiResponse(null, $errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
