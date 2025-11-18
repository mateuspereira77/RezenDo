<?php

namespace App\Http\Requests;

use App\Rules\NoProfanity;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
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
            'text' => ['sometimes', 'required', 'string', 'max:200', new NoProfanity],
            'description' => ['nullable', 'string', 'max:500', new NoProfanity],
            'completed' => ['sometimes', 'boolean'],
            'priority' => ['sometimes', 'in:simple,medium,urgent'],
            'day' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'assigned_to' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
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

        // Se date vier como string vazia, definir como null explicitamente
        if ($this->has('date') && $this->input('date') === '') {
            $this->merge(['date' => null]);
        }

        // Se assigned_to vier como string vazia ou '0', definir como null
        if ($this->has('assigned_to')) {
            $assignedTo = $this->input('assigned_to');
            \Log::info('UpdateTodoRequest - prepareForValidation - assigned_to raw:', ['value' => $assignedTo, 'type' => gettype($assignedTo)]);

            if ($assignedTo === '' || $assignedTo === '0' || $assignedTo === null || $assignedTo === 0) {
                $this->merge(['assigned_to' => null]);
                \Log::info('UpdateTodoRequest - assigned_to convertido para null');
            } else {
                $intValue = (int) $assignedTo;
                $this->merge(['assigned_to' => $intValue]);
                \Log::info('UpdateTodoRequest - assigned_to convertido para int:', ['value' => $intValue]);
            }
        } else {
            \Log::info('UpdateTodoRequest - assigned_to não presente no request');
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
            'priority.in' => 'A prioridade deve ser: Simples, Média ou Urgente.',
        ];
    }
}
