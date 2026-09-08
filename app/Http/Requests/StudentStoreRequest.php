<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'matricule' => [
                'required', 'string', 'max:50',
                Rule::unique('students', 'matricule')->where('etablissement_id', Student::etablissementActifId()),
            ],
            'nom' => ['required', 'string', 'max:100'],
            'prenoms' => ['required', 'string', 'max:150'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'lieu_naissance' => ['required', 'string', 'max:150'],
            'sexe' => ['required', 'in:M,F'],
            'classe' => ['required', 'string', Rule::in(config('ecole.classes'))],
            'telephone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'annee_scolaire' => ['required', 'string', 'max:20'],
            'statut' => ['nullable', 'in:actif,inactif'],
        ];
    }
}
