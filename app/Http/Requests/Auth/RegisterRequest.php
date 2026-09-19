<?php

namespace App\Http\Requests\Auth;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roles = [Role::Client->value, Role::Pharmacie->value, Role::Livreur->value];

        return [
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'telephone' => ['required', 'string', 'max:30', 'regex:/^[+0-9 ().-]{8,}$/'],
            'sexe' => ['required', 'in:M,F'],
            'date_naissance' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'role' => ['required', 'in:'.implode(',', $roles)],
            'password' => ['required', 'confirmed', Password::defaults()],

            // Profil pharmacie
            'nom_commercial' => ['required_if:role,'.Role::Pharmacie->value, 'string', 'max:255'],
            'adresse' => ['required_if:role,'.Role::Pharmacie->value.','.(string) Role::Client->value, 'nullable', 'string', 'max:500'],
            'heure_ouverture' => ['nullable', 'date_format:H:i'],
            'heure_fermeture' => ['nullable', 'date_format:H:i', 'after:heure_ouverture'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_commercial.required_if' => 'Le nom commercial de la pharmacie est obligatoire.',
            'adresse.required_if' => 'L\'adresse est obligatoire.',
            'heure_fermeture.after' => 'L\'heure de fermeture doit être postérieure à l\'heure d\'ouverture.',
        ];
    }
}
