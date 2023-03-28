<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'address'   => 'required',
            'details'   => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
            'region_id' => 'required|integer',
            'state_id'  => 'required|integer',
            'phone1'    => 'nullable|numeric|digits_between:9,10',
            'phone2'    => 'nullable|numeric|digits_between:9,10',
            'email'     => 'nullable|email',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'address.required' => 'يرجى ادخال العنوان',
    //         'details.required' => 'يرجى ادخال التفاصيل',
    //         'latitude.required' => 'يرجى ادخال العرض',
    //         'longitude.required' => 'يرجى ادخال الطول',
    //         'region_id.required' => 'يرجى اختيار المنطقة',
    //         'state_id.required' => 'يرجى اختيار المنطقة',
    //         'phone1.numeric' => 'يرجى ادخال رقم هاتف صحيح',
    //         'phone1.digits_between' => 'يرجى ادخال رقم هاتف صحيح',
    //         'phone2.numeric' => 'يرجى ادخال رقم هاتف صحيح',
    //         'phone2.digits_between' => 'يرجى ادخال رقم هاتف صحيح',
    //         'email.email' => 'يرجى ادخال بريد إلكتروني صالح',
    //     ];
    // }

    public function attributes()
    {
        return [
            'address'   => app()->getLocale() == 'ar' ? 'العنوان' : 'Address',
            'details'   => app()->getLocale() == 'ar' ? 'التفاصيل' : ' Details',
            'latitude'  => app()->getLocale() == 'ar' ? 'العرض' : 'Latitude',
            'longitude' => app()->getLocale() == 'ar' ? 'الطول' : 'Longitude',
            'region_id' => app()->getLocale() == 'ar' ? 'المنطقة' : ' Region',
            'state_id'  => app()->getLocale() == 'ar' ? 'المنطقة' : ' State',
            'phone1'    => app()->getLocale() == 'ar' ? 'رقم الهاتف الاول' : ' Phone 1',
            'phone2'    => app()->getLocale() == 'ar' ? 'رقم الهاتف الثاني' : ' Phone 2',
            'email'     => app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : ' Email',
        ];
    }
}
