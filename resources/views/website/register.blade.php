@extends('website.layout')
@section('title')
    <title> انشاء حساب </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">

    <link href="/site/js/datepicker/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css">
    <link href="/site/js/datepicker/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css">

    <script src="/site/js/jquerynew.js"></script>

    <script src="/site/js/datepicker/js/bootstrap-datepicker.js"></script>

    <script type="text/javascript">
        jQuery(function() {

            $('#commercial_end_date').datepicker();

            $('#country_id').change(function() {
                var country_id = $('#country_id').val();
                $.ajax({
                    url: '/getRegions/' + country_id,
                    success: function(data) {
                        $('#region_id').html(data);
                    }
                });

            });

            $('#region_id').change(function() {
                var country_id = $('#region_id').val();
                $.ajax({
                    url: '/getRegionStates/' + country_id,
                    success: function(data) {
                        $('#state_id').html(data);
                    }
                });

            });
        });
    </script>
@endsection
@section('content')
    <main id="content">
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body" href="/">{{__('dashboard.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">تسجيل جديد
                        </li>
                    </ol>
                </nav>
            </div>
        </section>
        <section class="py-2 bg-gray-2">
            <div class="card">
                <form>
                    <h2 class="title">تسجيل جديد</h2>
                    <p class="subtitle"> تملك عضوية ؟ <a href="#"> تسجيل دخول</a></p>
                    <form method="post" action="/register">
                        {!! csrf_field() !!}

                        <div class="email-login">
                            <div class="row">
                                <div class="col-12">
                                    <label for=""> <b>رقم الجوال</b></label>
                                    <input type="text" disabled value="{{ isset($phone) ? $phone : '0594455454' }}"
                                        class="form-control">
                                    <input type="hidden" name="phone" value="{{ isset($phone) ? $phone : '' }}">
                                </div>

                                <div class="col-12 {{ $errors->has('username') ? ' has-error' : '' }}">
                                    <label for="username"> <b>الاسم</b></label>
                                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                                        required class="form-control" placeholder="الاسم بالكامل">

                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif

                                </div>
                                <div class="col-12 col-md-6 {{ $errors->has('country_id') ? ' has-error' : '' }}">
                                    <label for="country_id"> <b>الدولة</b></label>
                                    <select name="country_id" id="country_id" required>
                                        <option value="" disabled="">اختر الدولة</option>
                                        @foreach (\App\Models\Countries::all() as $country)
                                            <option
                                                {{ old('country_id') && old('country_id') == $country_id ? 'selected' : ($country->id == 188 ? 'selected' : '') }}
                                                value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('country_id') }}</strong>
                                        </span>
                                    @endif

                                </div>
                                <div class="col-12 col-md-6 {{ $errors->has('region_id') ? ' has-error' : '' }}">
                                    <label for="region_id"> <b>المنطقة</b></label>
                                    <select name="region_id" id="region_id" required>
                                        <option value="">اختر المنطقة</option>
                                        @php
                                            $region_country = old('country_id') ?: 188;
                                        @endphp
                                        @foreach (\App\Models\Regions::where('country_id', $region_country)->where('is_archived', 0)->get() as $region)
                                            <option value="{{ $region->id }}"
                                                {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('region_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('region_id') }}</strong>
                                        </span>
                                    @endif

                                </div>

                                <div class="col-12 col-md-6 {{ $errors->has('state_id') ? ' has-error' : '' }}">
                                    <label for="state_id"> <b>المدينة</b></label>
                                    <select class="js-example-basic-single" name="state_id" id="state_id" required>
                                        <option value="">اختر المدينة</option>
                                        @if (old('region_id'))
                                            @foreach (\App\Models\States::where('region_id', old('region_id'))->where('is_archived', 0)->get() as $state)
                                                <option value="{{ $state->id }}"
                                                    {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('state_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('state_id') }}</strong>
                                        </span>
                                    @endif

                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="client_type"> <b>نوع النشاط</b></label>
                                    <select id="client_type" name="client_type" required>
                                        <option value="" disabled="" selected>اختر نوع النشاط</option>
                                        @foreach (\App\Models\ClientTypes::all() as $shopType)
                                            <option value="{{ old('client_type') }}"
                                                {{ old('client_type') == $shopType->id ? 'selected' : '' }}>
                                                {{ $shopType->name }} </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('client_type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('client_type') }}</strong>
                                        </span>
                                    @endif

                                </div>

                                <div class="col-12 col-md-6 {{ $errors->has('state_id') ? ' has-error' : '' }}">
                                    <label for="email"> <b>البريد الالكترونى</b></label>
                                    <input type="email" id="email" value="{{ old('email') }}"
                                        placeholder="البريد الالكترونى" name="email">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif

                                </div>

                                <div class="col-12 col-md-6 {{ $errors->has('commercial_no') ? ' has-error' : '' }}">
                                    <label for="commercial_no"> <b>رقم السجل التجارى</b></label>
                                    <input type="number" id="commercial_no" value="{{ old('commercial_no') }}"
                                        placeholder="رقم السجل التجارى" name="commercial_no">
                                    @if ($errors->has('commercial_no'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('commercial_no') }}</strong>
                                        </span>
                                    @endif

                                </div>

                                <div class="col-12 col-md-6 {{ $errors->has('commercial_end_date') ? ' has-error' : '' }}">
                                    <label for="commercial_end_date"> <b>تاريخ انتهاء السجل</b></label>
                                    <input type="text" id="commercial_end_date" value="{{ old('commercial_end_date') }}"
                                        placeholder="تاريخ انتهاء السجل التجارى" name="commercial_end_date">
                                    @if ($errors->has('commercial_end_date'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('commercial_end_date') }}</strong>
                                        </span>
                                    @endif

                                </div>

                                <div class="col-md-6 col-12">
                                    <label for="password"><b>الرقم السري</b></label>
                                    <input type="password" id="password" placeholder="الرقم السري" name="psw"
                                        required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="confirm-pass"><b>اعادة الرقم السري</b></label>
                                    <input type="password" id="confirm-pass" placeholder="الرقم السري"
                                        name="confirm-pass" required>
                                </div>
                                <div class="col-12">
                                    <button class="cta-btn">تسجيل</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </form>
            </div>
        </section>
    </main>
@endsection
@section('js')
@endsection
