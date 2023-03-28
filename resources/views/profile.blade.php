@extends('layout')
@section('title')
    <title>   الرئيسية - {{Auth::User()->username}} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@endsection

@section('content')

    <!-- ================= register ================= -->

    <section class="profile">
        <div class="container">

            <div class='head-page'>
                <h4> الحساب الشخصى</h4>
            </div>
            <div class="login-form">
                <form method="post" action="/update-profile">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
                                <label>الاسم بالكامل</label>
                                <div class="input-with-icon">
                                    <input type="text" name="username" value="{{old('username')?:Auth::User()->username}}" class="form-control" placeholder="الاسم بالكامل">
                                    @if ($errors->has('username'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('username') }}</strong>
		                                    </span>
                                    @endif

                                </div>

                            </div>

                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label>البريد الالكترونى</label>
                                <div class="input-with-icon">
                                    <input type="email" value="{{old('email')?:Auth::User()->email}}" name="email" class="form-control" placeholder="البريد الالكترونى">
                                </div>
                                @if ($errors->has('email'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('email') }}</strong>
		                                    </span>
                                @endif

                            </div>

                            <div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
                                <label>العنوان</label>
                                <div class="input-with-icon">
                                    <input type="text" name="address" value="{{old('address')?:Auth::User()->address}}" class="form-control" placeholder="العنوان">
                                </div>
                                @if ($errors->has('address'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('address') }}</strong>
		                                    </span>
                                @endif

                            </div>

                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label>رقم الهاتف</label>
                                <div class="input-with-icon">
                                    <input type="tel" id="phone" name="phone" value="{{old('phone')?:Auth::User()->phone}}" class="form-control" placeholder="ادخل رقم الهاتف">
                                </div>
                                @if ($errors->has('phone'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('phone') }}</strong>
		                                    </span>
                                @endif
                                <input type="hidden" name="country_code" id="country_code">

                            </div>

                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label>كلمة المرور</label>
                                <div class="input-with-icon">
                                    <input type="password" name="password"  class="form-control" placeholder="*******">
                                </div>
                                @if ($errors->has('password'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('password') }}</strong>
		                                    </span>
                                @endif

                            </div>
                            <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label>تأكيد كلمة المرور</label>
                                <div class="input-with-icon">
                                    <input type="password" value="{{old('password_confirmation')}}" name="password_confirmation" class="form-control" placeholder="*******">
                                </div>
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
		                                    </span>
                                @endif

                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="avatar">
                                {{--<div class="tump user-header-photo">--}}
                                <img src="/uploads/{{Auth::User()->photo?:'user-default.png'}}" class="profile-image" alt="">
                                {{--</div>--}}

                                <a href="#uploader" class="" data-toggle="modal">
                                    <div class="dz-default dz-message">
                                        <i class="ti-gallery"></i>
                                        <span>ارفاق صورة شخصية</span>
                                    </div>
                                        </a>
                            </div>
                        </div>


                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-theme"> تعديل</button>
                    </div>

                </form>
            </div>

        </div>
    </section>

    <!-- ================= end login ================= -->


    <div class="modal fade" id="uploader" tabindex="-1" role="dialog" aria-labelledby="updater"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">✕</button>
                    <br>
                    <i class="icon-credit-card icon-7x"></i>
                    <p class="no-margin">{{trans('local.choose_picture')}}</p>
                </div>
                <div class="modal-body">
                    <form action="" class="uploadform dropzone no-margin dz-clickable">
                        <div class="dz-default dz-message">
                            <span> {{trans('local.pull_picture')}} </span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default attachtopost"
                            data-dismiss="modal">{{trans('local.cancel')}}
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('js')

    <link rel="stylesheet" href="/site/js/phonecodes/css/intlTelInput.css" />


    <script src="/site/js/phonecodes/js/intlTelInput.js"></script>

    <script type="text/javascript" src="/site/js/dropzone.js"></script>
    <script type="text/javascript" src="/site/js/profile.js"></script>
        <script>
            $("#phone").intlTelInput({
                // allowDropdown: false,
                // autoHideDialCode: false,
                // autoPlaceholder: "off",
                // dropdownContainer: "body",
                // excludeCountries: ["us"],
                // formatOnDisplay: false,
                // geoIpLookup: function(callback) {
                //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                //     var countryCode = (resp && resp.country) ? resp.country : "";
                //     callback(countryCode);
                //   });
                // },
                // hiddenInput: "full_number",
                initialCountry: "sa",
                // nationalMode: false,
                // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                // placeholderNumberType: "MOBILE",
                preferredCountries: ['sa','eg', 'su'],
                // separateDialCode: true,
                // localizedCountries:{ 'de': 'Deutschland' },
                utilsScript: "/site/js/phonecodes/js/utils.js"
            });
            $(document).ready(function () {
                profile.initUserZropZone(1, '.uploadform');
                $("#country_code").val($(this).intlTelInput("getSelectedCountryData").dialCode);
                $("#phone").on("countrychange", function(e, countryData) {
                    $("#country_code").val($(this).intlTelInput("getSelectedCountryData").dialCode);
                })

            })

        </script>

@endsection
