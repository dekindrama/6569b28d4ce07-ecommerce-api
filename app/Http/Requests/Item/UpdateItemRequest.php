<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;

class UpdateItemRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'picture' => ['nullable', 'mimes:png,jpg', 'max:10240'],
            'stock' => ['required', 'integer'],
            'unit' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'integer'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseHelper::generate(
            false,
            $validator->errors()->first(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            null,
            $validator->errors()->toArray(),
        ));
    }
}
