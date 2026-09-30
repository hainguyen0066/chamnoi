<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Validate cập nhật danh mục bài viết.
 */
class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * Slug bỏ trống thì tự sinh từ tên trước khi validate.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: (string) $this->input('name')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'string', 'max:191', Rule::unique('categories', 'slug')->ignore($category)],
            'parent_id' => [
                'nullable', 'integer', 'exists:categories,id',
                // Không cho chọn chính nó làm cha (tạo vòng lặp phân cấp).
                Rule::notIn([$category?->id]),
            ],
            'description' => ['nullable', 'string', 'max:191'],
            'meta_title' => ['nullable', 'string', 'max:191'],
            'meta_description' => ['nullable', 'string', 'max:191'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'show_on_homepage' => ['nullable', 'boolean'],
        ];
    }
}
