@extends('layouts.layout')
@section('title')
<title>{{ \App\Models\Settings::find(5)->value }} - تفعيل رقم الجوال</title>
{{--<meta name="description" content="{{ \App\Models\Settings::find(6)->value }}">--}}
{{--<meta name="keywords" content="{{ \App\Models\Settings::find(7)->value }}">--}}
@endsection
@section('content')
    <!-- start verify section -->
    <section class="page-head">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head-con">
                        <ul>
                            <li>
                                <a href="/">الرئيسية</a>
                            </li>
                            <li>/</li>
                            <li>تفعيل رقم الهاتف</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if($sms_active==1)
    <label>{{$activation_code}}</label>
    @endif
    <section class="verify-section">
        <div id="wrapper">
            <div id="dialog">
                <h3>من فضلك ادخل الارقام المرسلة لكـ في رسالة نصية</h3>
                <span>(نأسف لذلك لكن يجب التأكد من صحة رقم الهاتف الذي ادخلة)</span>
                <br>
                <form id="form" method="post" action="/activate_phone_number" class="login-form activated">
                    {{ csrf_field() }}
                    <input name="phone" type="hidden" value="{{$phone}}">
                    <input type="text" name="activation_code[]" class="verify-input" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                    <input type="text" name="activation_code[]" class="verify-input" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                    <input type="text" name="activation_code[]" class="verify-input" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                    <input type="text" name="activation_code[]" class="verify-input" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                    @if ($errors->has('activation_code'))
                        <span class="help-block">
                                    <strong>{{ $errors->first('activation_code') }}</strong>
                                </span>
                    @endif

                    <button type="submit"  class="btn btn-primary btn-embossed" value="تأكيد">تأكيد</button>
                </form>

                <div>
                    لم تستقبل رقم التأكيد؟<br />
{{--                    <a href="/resend-phone-code/{{$phone}}">ارسال رقم التأكيد مرة اخرى</a><br />--}}
                    <br>
                    <form method="post" method="login">
                        {{ csrf_field() }}

                        <input type="hidden" value="{{$phone}}">
                        <button class="btn btn-app" style="color: #d18332" type="submit">ارسال رقم التأكيد مرة اخرى</button>
                    </form>
                    <a href="/login">تغيير رقم الهاتف الذي تم ادخالة مسبقا</a>
                </div>
            </div>
        </div>
    </section>
    <!-- end virfy section -->

    <!--start product -->
    <div class="login-register">
        <div class="container">

            <form method="post" action="/activate_phone_number/" class="login-form activated">
                {{ csrf_field() }}
                <div class="clearfix"></div>
                <div class="form-group {{ $errors->has('activation_code') ? ' has-error' : '' }}">
                    <label for=""><i class="fa fa-user"></i>  كود التفعيل </label>
                    <input   value="{{ old('activation_code') }}" name="activation_code" type="text" class="form-control"  placeholder="">
                    @if ($errors->has('activation_code'))
                        <span class="help-block">
                                    <strong>{{ $errors->first('activation_code') }}</strong>
                                </span>
                    @endif
                </div>

                <button class="btn btn-default reg-btn">تفعيل</button>
            </form>
        </div>
    </div>
    <!--end product-->
@endsection