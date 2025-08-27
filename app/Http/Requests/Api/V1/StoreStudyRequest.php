<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Deck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $deck = Deck::find($this->input('deck_id'));

        return $deck && $this->user()->can('view', $deck);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'deck_id' => ['required', 'integer', Rule::exists('decks', 'id')],
        ];
    }
}
