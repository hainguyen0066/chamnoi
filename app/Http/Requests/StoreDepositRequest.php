<?php

namespace App\Http\Requests;

use App\Models\Deposit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate form "Nạp tiền cho user" (nhập tay bởi admin).
 *
 * LƯU Ý BẢO MẬT: form có ô "Số xu nhận được" nhưng chỉ để xem trước —
 * giá trị đó KHÔNG được validate/nhận ở đây; controller luôn tính lại
 * bằng Deposit::calculateAmountReceived() từ amount + promotion_percent.
 */
class StoreDepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(array_keys(Deposit::TYPES))],
            // Form nhập tay chỉ cho 3 phương thức thủ công; vnpay/vnptpay
            // chỉ được tạo qua luồng gateway riêng.
            'method' => ['required', Rule::in(['momo', 'bank_transfer', 'ctv'])],
            'amount' => ['required', 'integer', 'min:1000', 'max:1000000000'],
            'promotion_percent' => ['required', 'integer', Rule::in(Deposit::PROMOTION_PERCENTS)],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account.required' => 'Vui lòng nhập tài khoản cần nạp.',
            'amount.min' => 'Số tiền tối thiểu 1.000 VND.',
            'promotion_percent.in' => 'Tỉ lệ khuyến mãi chỉ nhận các mức 0/5/10/20%.',
        ];
    }
}
