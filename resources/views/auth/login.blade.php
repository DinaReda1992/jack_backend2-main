@extends('layouts.layout')
@section('title')
<title>{{ \App\Models\Settings::find(2)->value }} - تسجيل الدخول</title>
<meta name="description" content="{{ \App\Models\Settings::find(3)->value }}">
<meta name="keywords" content="{{ \App\Models\Settings::find(4)->value }}">
@endsection
@section('content')

    <!-- ================= login================= -->

    <section class="page-head">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head-con">
                        <ul class="d-inline-block">
                            <li class="d-inline-block">
                                <a href="/">الرئيسية</a>
                            </li>
                            <li class="d-inline-block">/</li>
                            <li class="d-inline-block">تسجيل الدخول</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="login-section">
        <div class="">
            <div >
                <login :countries="{{$countries}}" :clienttypes="{{$clientTypes}}" />
            </div>
<!--                <form method="post" action="/login">
                    <div class="email-login">
                    <label for="email"> <b>ادخل رقم الجوال</b></label>
                    <input type="number" id="phone"     oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                           placeholder="05......." name="phone" required maxlength="10" >
                </div>
                <input class="cta-btn" value="دخول" type="submit">
                </form>-->
        </div>
    </section>
    <!-- start footer section -->

    <!-- ================= end login ================= -->


@endsection
{{--@section('mix')
    <script src="{{ mix('js/app.js') }}"></script>
@endsection--}}
