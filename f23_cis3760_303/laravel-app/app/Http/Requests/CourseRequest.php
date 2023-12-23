<?php

namespace App\Http\Requests;

use App\Http\Requests\JsonRequest;

class CourseRequest extends JsonRequest
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
            'CourseCode' => 'required|string|max:64',
            'CourseName' => 'required|string|max:255',
            'CourseOffering' => 'required|string|max:64',
            'CourseWeight' => 'required|numeric',
            'CourseDescription' => 'required|string',
            'CourseFormat' => 'required|string|max:255',
            'Prerequisites' => 'required|string|max:255',
            'PrerequisiteCredits' => 'required|numeric',
            'Corequisites' => 'required|string|max:255',
            'Restrictions' => 'required|string|max:255',
            'Equates' => 'required|string|max:255',
            'Department' => 'required|string',
            'Location' => 'required|string|max:64'
        ];
    }
}
