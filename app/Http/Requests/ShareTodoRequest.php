<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShareTodoRequest extends FormRequest
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
            'user_id' => ['required_without:email', 'integer', 'exists:users,id', 'different:'.auth()->id()],
            'email' => ['required_without:user_id', 'email', 'exists:users,email'],
            'permission' => ['required', Rule::in(['read', 'write'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required_without' => 'É necessário informar o ID do usuário ou o email.',
            'user_id.exists' => 'O usuário informado não existe.',
            'user_id.different' => 'Você não pode compartilhar uma tarefa consigo mesmo.',
            'email.required_without' => 'É necessário informar o email ou o ID do usuário.',
            'email.exists' => 'O email informado não está cadastrado.',
            'permission.required' => 'A permissão é obrigatória.',
            'permission.in' => 'A permissão deve ser "read" ou "write".',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Se foi enviado email, buscar o user_id correspondente
        if ($this->has('email') && ! $this->has('user_id')) {
            $user = User::where('email', $this->email)->first();
            if ($user) {
                $this->merge(['user_id' => $user->id]);
            }
        }
    }
}
