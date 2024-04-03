<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class OpportunityRequest extends FormRequest
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
            'title' => 'required',
            'body' => 'required',
            'file' => 'required|file',
            'location' => 'required',
            'job_type' => 'required|in:full-time, part-time, contract, temporary, volunteer',
            'work-place_type' => 'required|in:on-site, hybrid, remote',
            'job_hours' => 'required',
            'qualifications' => 'required',
            'skills_req' => 'required',
            'salary' => 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors, 'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
