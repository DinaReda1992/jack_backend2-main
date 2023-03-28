<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits_between:9,10',
            'subject' => 'required',
            'message' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يرجى ادخال اسمك',
            'email.required' => 'يرجى ادخال بريدك الإلكتروني',
            'email.email' => 'يرجى ادخال بريد إلكتروني صالح',
            'phone.required' => 'يرجى ادخال رقم الهاتف',
            'subject.required' => 'يرجى ادخال الموضوع',
            'message.required' => 'يرجى ادخال رسالتك',
        ];
    }
}
