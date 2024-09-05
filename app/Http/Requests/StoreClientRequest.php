<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autoriser uniquement les utilisateurs authentifiés à faire cette demande
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'surname' => ['required', 'string', 'max:255', 'unique:clients'],
    'adresse' => ['required', 'string', 'max:255'],
    'telephone' => ['required', 'string', 'unique:clients', 'max:15'],
    'user.login' => ['sometimes', 'required', 'string', 'unique:users,login'],
    'user.password' => ['sometimes', 'required', 'string', 'min:5', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ];
    }
}
