<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;

class StoreOrderRequest extends FormRequest
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
            'total_all_price' => ['required', 'integer'],

            'item.id' => ['required', 'string'],
            'item.name' => ['required', 'string', 'max:255'],
            'item.unit' => ['required', 'string', 'max:255'],
            'item.unit_price' => ['required', 'integer'],
            'item.qty' => ['required', 'integer'],
            'item.subtotal_price' => ['required', 'integer'],

            'payment.payer_name' => ['required', 'string', 'max:255'],
            'payment.paid_amount' => ['required', 'integer'],
            'payment.change_amount' => ['required', 'integer'],
            'payment.payment_type' => ['required', 'string', 'max:255', Rule::in(OrderEnum::PAYMENT_TYPES)],
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
