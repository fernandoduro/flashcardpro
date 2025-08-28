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
        if (!$this->user()) {
            return false;
        }

        $deck = Deck::find($this->input('deck_id'));

        return $deck && $this->user()->can('view', $deck);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // If user is not authenticated, return basic rules (validation will still fail due to authorization)
        if (! $this->user()) {
            return [
                'deck_id' => ['required', 'integer'],
            ];
        }

        return [
            'deck_id' => [
                'required',
                'integer',
                Rule::exists('decks', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
