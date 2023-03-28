<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'phone' => 'required|unique:users,phone|digits_between:9,10',
            'email' => 'nullable|unique:users,email',
            'region_id' => 'required',
            'state_id' => 'required',
            'client_type' => 'required',
            'commercial_no' => 'nullable',
            'commercial_end_date' => 'nullable',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'password' => 'required|min:6',
            'phonecode' => 'nullable',
            'user_type_id' => 'nullable',
            'country_id' => 'nullable',
            'activate' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'يرجى ادخال اسم المستخدم',
            'phone.required' => 'يرجى ادخال رقم الهاتف',
            'phone.unique' => 'يرجى ادخال رقم الهاتف صحيح',
            'email.required' => 'يرجى ادخال بريد الكتروني',
            'email.unique' => 'يرجى ادخال بريد الكتروني صحيح',
            'region_id.required' => 'يرجى اختيار المنطقة',
            'state_id.required' => 'يرجى اختيار المنطقة',
            'client_type.required' => 'يرجى اختيار نوع العميل',
            'commercial_no.required' => 'يرجى ادخال رقم السجل التجاري',
            'commercial_end_date.required' => 'يرجى ادخال تاريخ انتهاء السجل التجاري',
        ];
    }

    public function prepareForValidation()
    {
        $phone = ltrim(request()->phone, '0');
        $phone1 = $this->convertNum(ltrim($phone, '0'));

        $this->merge([
            'password' => bcrypt($this->password),
            'phonecode' => 966,
            'phone' => $phone1,
            'user_type_id' => 5,
            'country_id' => 188,
            'activate' => 1,
        ]);
    }

    public function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        return str_replace($arabic, $english, $number);
    }
}
