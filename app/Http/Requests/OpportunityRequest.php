<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
