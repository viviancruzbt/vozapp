<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'googleid' => ['nullable', 'string', 'max:255'], // Validação do googleid
            'custom_event' => ['nullable', 'string', 'max:255'], // Evento principal
            'evento_2' => ['nullable', 'string', 'max:255'], // Evento principal
            'evento_3' => ['nullable', 'string', 'max:255'], // Evento principal
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }
}
