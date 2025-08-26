<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $deck = \App\Models\Deck::find($this->input('deck_id'));
        return $deck && $this->user()->can('view', $deck);
    }

    public function rules(): array
    {
        return [
            'deck_id' => ['required', 'integer', Rule::exists('decks', 'id')],
        ];
    }
}