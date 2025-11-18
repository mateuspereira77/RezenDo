<?php

namespace App\Http\Requests;

use App\Rules\NoProfanity;
use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
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
            'text' => ['required', 'string', 'max:200', new NoProfanity],
            'description' => ['nullable', 'string', 'max:500', new NoProfanity],
            'priority' => ['required', 'in:simple,medium,urgent'],
            'day' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Garantir que a descrição seja trimada antes da validação
        if ($this->has('description') && is_string($this->description)) {
            $this->merge([
                'description' => trim($this->description) ?: null,
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'text.required' => 'O título da tarefa é obrigatório.',
            'text.max' => 'O título não pode ter mais de 200 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 500 caracteres.',
            'priority.required' => 'A prioridade é obrigatória.',
            'priority.in' => 'A prioridade deve ser: Simples, Média ou Urgente.',
        ];
    }
}
