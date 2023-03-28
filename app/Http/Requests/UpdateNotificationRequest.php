<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
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
            'notification' => 'required|in:1,0',
        ];
    }

    public function messages()
    {
        return [
            'notification.required' => 'يرجى اختيار حالة الاشعار',
            'notification.in' => 'يرجى اختيار حالة الاشعار من بين 0 او 1',
        ];
    }
}
