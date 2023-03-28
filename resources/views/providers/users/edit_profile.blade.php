@extends('providers.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('.country_id').change(function () {
                var country_id = $(this).val();
                $('.currency').val(country_id);
                $('.phonecode').val(country_id);
            });
            $('.user_type_id').change(function () {
                var change_val = $(this).val();
                if(change_val == 2){
                    $('.previliges').show();
                }else{
                    $('.previliges').hide();
                }

            })
            $('.country_id').change(function () {
                var country_id = $('.country_id').val();
                $.ajax({
                    url: '/provider-panel/getStates/' + country_id,
                    success: function (data) {
                        $('.state_id').html(data);
                    }
                });

            });
        });
    </script>


@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} مستخدم</h4>
                </div>

                <!-- 						<div class="heading-elements"> -->
                <!-- 							<div class="heading-btn-group"> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a> -->
                <!-- 							</div> -->
                <!-- 						</div> -->
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                    <li class="active"> تعديل الحساب </li>
                </ul>



                <!-- 						<ul class="breadcrumb-elements"> -->
                <!-- 							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li> -->
                <!-- 							<li class="dropdown"> -->
                <!-- 								<a href="#" class="dropdown-toggle" data-toggle="dropdown"> -->
                <!-- 									<i class="icon-gear position-left"></i> -->
                <!-- 									Settings -->
                <!-- 									<span class="caret"></span> -->
                <!-- 								</a> -->

                <!-- 								<ul class="dropdown-menu dropdown-menu-right"> -->
                <!-- 									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li> -->
                <!-- 									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li> -->
                <!-- 									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li> -->
                <!-- 									<li class="divider"></li> -->
                <!-- 									<li><a href="#"><i class="icon-gear"></i> All settings</a></li> -->
                <!-- 								</ul> -->
                <!-- 							</li> -->
                <!-- 						</ul> -->
            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

        @include('providers.message')

        <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} مستخدم </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">

                    <form enctype="multipart/form-data" method="post" class="form-horizontal" action="/provider-panel/edit-profile">
                        {!! csrf_field() !!}
                        <fieldset class="content-group">
                            <!-- 									<legend class="text-bold">Basic inputs</legend> -->

                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">اسم المستخدم</label>
                                <div class="col-lg-10">
                                    <input type="text" name="username" value="{{ isset($object) ? $object->username  : old('username')  }}" class="form-control" placeholder="اسم المستخدم">
                                    @if ($errors->has('username'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('username') }}</strong>
		                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">البريد الالكتروني</label>
                                <div class="col-lg-10">
                                    <input type="text" name="email" value="{{ isset($object) ? $object->email  : old('email')  }}" class="form-control" placeholder="البريد الالكتروني">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('email') }}</strong>
		                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }} {{ $errors->has('phonecode') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">رقم الجوال</label>

                                <div class="col-lg-7">
                                    <input type="text" name="phone" value="{{ isset($object) ? $object->phone  : old('phone')  }}" class="form-control" placeholder="رقم الجوال">
                                    @if ($errors->has('phone'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('phone') }}</strong>
		                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-3">
                                    <select disabled name="phonecode" class="form-control">
                                        <option value="966">+966</option>
{{--                                        @foreach(\App\Models\Countries::all() as $code)--}}
{{--                                            <option value="{{ $code->phonecode }}" {{ isset($object) && $object->phonecode==$code->phonecode ? 'selected' : (old('phonecode') == $code->phonecode ? 'selected' : '') }}>+{{ $code->phonecode }}</option>--}}
{{--                                        @endforeach--}}
                                    </select>
                                    @if ($errors->has('phonecode'))
                                        <span class="help-block">
											<strong>{{ $errors->first('phonecode') }}</strong>
										</span>
                                    @endif
                                </div>
                            </div>
                            {{--									<div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">--}}
                            {{--										<label for="inputLogType" class="col-lg-2 control-label">النوع</label>--}}
                            {{--										<div class="col-lg-10">--}}

                            {{--											<div class="radio">--}}
                            {{--												<label>--}}
                            {{--													<input type="radio" name="gender" id="optionsRadios1" value="1" {{ isset($object) && $object->gender == 1 ? 'checked' : ''  }}>--}}
                            {{--													ذكر--}}
                            {{--												</label>--}}
                            {{--											</div>--}}
                            {{--											<div class="radio">--}}
                            {{--												<label>--}}
                            {{--													<input type="radio" name="gender" id="optionsRadios2" value="2" {{ isset($object) && $object->gender == 2 ? 'checked' : ''  }}>--}}
                            {{--													أنثى--}}
                            {{--												</label>--}}
                            {{--											</div>--}}
                            {{--										</div>--}}
                            {{--									</div>--}}


{{--                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">--}}
{{--                                <label class="control-label col-lg-2">إختر الدولة</label>--}}
{{--                                <div class="col-lg-10">--}}
{{--                                    <select name="country_id" class="form-control country_id">--}}
{{--                                        <option value="0">اختر الدولة</option>--}}
{{--                                        @foreach(\App\Models\Countries::all() as $country)--}}
{{--                                            <option value="{{ $country->id }}"  {{ isset($object) && $object->country_id==$country->id ? 'selected' : (old('country_id') == $country->id ? 'selected' : '') }}>{{ $country->name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @if ($errors->has('country_id'))--}}
{{--                                        <span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('country_id') }}</strong>--}}
{{--		                                    </span>--}}
{{--                                    @endif--}}
{{--                                </div>--}}

{{--                            </div>--}}
                            <div class="form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                            <label class="control-label col-lg-2">إختر المدينة</label>
                            <div class="col-lg-10">
                            <select name="state_id" class="form-control state_id">

                            <option value="">اختر المدينة</option>
                            @if(isset($object) && $object->country_id)
                            @foreach (\App\Models\States::where('country_id', 188)->get() as $state)
                            <option value="{{ $state->id }}"  {{ isset($object) && $object->state_id==$state->id ? 'selected' : (old('state_id') == $state->id ? 'selected' : '') }}>  {{$state->name}} </option>

                            @endforeach
                            @endif

                            </select>
                            @if ($errors->has('state_id'))
                            <span class="help-block">
                            <strong>{{ $errors->first('state_id') }}</strong>
                            </span>
                            @endif
                            </div>
                            </div>
{{--                            <div class="form-group{{ $errors->has('currency_id') ? ' has-error' : '' }}">--}}
{{--                                <label class="control-label col-lg-2">إختر العملة</label>--}}
{{--                                <div class="col-lg-10">--}}
{{--                                    <select name="currency_id" class="form-control">--}}
{{--                                        <option value="">اختر العملة</option>--}}
{{--                                        @foreach(\App\Models\Currencies::all() as $currency)--}}
{{--                                            <option value="{{ $currency->id }}"  {{ isset($object) && $object->currency_id==$currency->id ? 'selected' : (old('currency_id') == $currency->id ? 'selected' : '') }}>{{ $currency->name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @if ($errors->has('currency_id'))--}}
{{--                                        <span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('currency_id') }}</strong>--}}
{{--		                                    </span>--}}
{{--                                    @endif--}}
{{--                                </div>--}}

{{--                            </div>--}}


                            @php
                                $long = @$object->longitude?:(old('longitude'));
                                $lat = @$object->latitude?:(old('latitude'));
                            @endphp
                            <div class="form-group{{ $errors->has('longitude') || $errors->has('latitude') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">الموقع على الخريطة</label>
                                <div class="col-lg-10">
                                    <input type="hidden" id='lon' name="longitude"  />
                                    <input type="hidden" id='lat' name="latitude"   />
                                    <input type="hidden" name="country_code" value="">
                                    @include('admin.items.mapPlace')
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">وصف العنوان</label>
                                <div class="col-lg-10">
                                    <input type="text" name="address" value="{{ isset($object) ? $object->address  : old('address')  }}" class="form-control" placeholder="اكتب العنوان بالتفصيل">
                                    @if ($errors->has('address'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('address') }}</strong>
		                                    </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">كلمة المرور</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password" class="form-control" placeholder="كلمة المرور">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('password') }}</strong>
		                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">اعادة كلمة المرور</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="اعادة كلمة المرور">
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
		                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">أدخل صورة البروفايل</label>
                                <div class="col-lg-10">

                                    <input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
                                    @if ($errors->has('photo'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('photo') }}</strong>
		                                    </span>
                                    @endif



                                </div>

                            </div>
                            @if(isset($object->photo))
                                <div class="form-group">
                                    <label class="control-label col-lg-2">الصورة الحالية</label>
                                    <div class="col-lg-10">
                                        <img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">
                                    </div>

                                </div>
                            @endif

                        </fieldset>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} المستخدم  <i class="icon-arrow-left13 position-right"></i></button>
                        </div>

                    </form>
                </div>
            </div>
            <!-- /form horizontal -->


            <!-- Footer -->
        @include('providers.footer')
        <!-- /footer -->

        </div>
        <!-- /content area -->
        <script src="{{ asset('js/mapPlace.js') }}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkQ8_neT4uZpVaXG1SbZNWKH1fnQHnbGk&libraries=places&callback=initMap&language={{ App::getLocale() }}"
                async defer></script>

        <script type="text/javascript">
            // Warning alert
            $( window ).load(function() {
                @if(!isset($object))
                getCurrentLocation();
                    @else
                {
                    @if($object->latitude && $object->longitude)
                    getLocationName({{$object->latitude}},{{$object->longitude}});
                    @else
                    getCurrentLocation();
                    @endif
                }

                @endif
            });
        </script>
    </div>
@stop
