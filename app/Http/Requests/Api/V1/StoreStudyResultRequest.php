<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Study;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudyResultRequest extends FormRequest
{

    public function authorize(): bool
    {
        // Check if user is authenticated first
        if (!$this->user()) {
            return false;
        }
        
        $study = Study::find($this->input('study_id'));
        
        return $study && $study->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        // If user is not authenticated, return basic rules (validation will still fail due to authorization)
        if (!$this->user()) {
            return [
                'study_id' => ['required', 'integer'],
                'card_id' => ['required', 'integer'], 
                'is_correct' => ['required', 'boolean'],
            ];
        }
        
        return [
            'study_id' => [
                'required',
                'integer',
                Rule::exists('studies', 'id')->where('user_id', $this->user()->id)
            ],
            'card_id' => [
                'required',
                'integer',
                Rule::exists('cards', 'id')->where('user_id', $this->user()->id)
            ],
            'is_correct' => ['required', 'boolean'],
        ];
    }
}