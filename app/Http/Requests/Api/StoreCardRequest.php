<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('deck'));
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:5'],
            'answer' => ['required', 'string', 'min:1'],
        ];
    }
}