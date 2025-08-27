<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Study;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudyResultRequest extends FormRequest
{

    public function authorize(): bool
    {
        
        $study = Study::find($this->input('study_id'));

        return $study && $study->user_id === $this->user()->id;
    }

    public function rules(): array
    {
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