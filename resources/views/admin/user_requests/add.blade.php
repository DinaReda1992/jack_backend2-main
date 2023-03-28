@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	{{--	<script type="text/javascript" src="/assets/js/core/app.js"></script>--}}

	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>

	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

	<script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
	<!-- InputMask -->
	<script src="/assets/js/plugins/input-mask/jquery.inputmask.js"></script>
	<script src="/assets/js/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
	<script src="/assets/js/plugins/input-mask/jquery.inputmask.extensions.js"></script>

	<!-- /theme JS files -->
	<!-- /theme JS files -->

	{{--	<script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>--}}

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>

	<link href="/site/js/datepicker/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css">
	<link href="/site/js/datepicker/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css">


	<script src="/site/js/datepicker/js/bootstrap-datepicker.js"></script>

	<!-- /theme JS files -->
	<script type="text/javascript">
		$(document).ready(function () {
			$("[data-mask]").inputmask();
			$('#commercial_end_date').datepicker();

			$('.country_id').change(function () {
				var country_id = $(this).val();
				$('.currency').val(country_id);
				$('.phonecode').val(country_id);
			});

			$('.user_type_id').change(function () {
				var change_val = $(this).val();
				if(change_val == 3){
					$('.profit_rate').show();
				}else{
					$('.profit_rate').hide();
				}

			})
			$('.country_id').change(function () {
				var country_id = $('.country_id').val();
				$.ajax({
					url: '/admin-panel/getRegions/' + country_id,
					success: function (data) {
						$('.region_id').html(data);
					}
				});

			});

			$('.region_id').change(function () {
				var country_id = $('.region_id').val();
				$.ajax({
					url: '/admin-panel/getRegionStates/' + country_id,
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
					<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
					<li><a href="/admin-panel/all-users">عرض المستخدمين</a></li>
					<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} مستخدم</li>
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

		@include('admin.message')

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

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/user-requests/'.$object->id : '/admin-panel/user-requests'  }}">
						{!! csrf_field() !!}
						@if(isset($object))
							<input type="hidden" name="_method" value="PATCH" />
						@endif
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->
							<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم المستخدم *</label>
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
								<label class="control-label col-lg-2">البريد الالكتروني *</label>
								<div class="col-lg-10" style="float: left;">
									<div class="input-group" style="direction: ltr;">
										<div class="input-group-addon">
											<i class="fa fa-envelope-o"></i>
										</div>
										<input type="text" name="email" value="{{ isset($object) ? $object->email  : old('email')  }}" class="form-control" placeholder="البريد الالكتروني">
									</div><!-- /.input group -->

								</div>

							</div>
							<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }} {{ $errors->has('phonecode') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">رقم الجوال *</label>

								<div class="col-lg-10" style="float: left;">
									<div class="input-group" style="direction: ltr;">
										<div class="input-group-addon">
											<i class="fa fa-phone"></i>
										</div>
										<input type="text"name="phone" value="{{ isset($object) ? $object->phone  : old('phone')  }}" placeholder="رقم الجوال" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
									</div><!-- /.input group -->

									@if ($errors->has('phone'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('phone') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div   class=" form-group{{ $errors->has('client_type') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">نوع النشاط</label>
								<div class="col-lg-10">
									<select name="client_type" class="form-control">

										<option value="">اختر نوع النشاط</option>
										@foreach (\App\Models\ClientTypes::all() as $shopType)
											<option value="{{ $shopType->id }}"  {{ isset($object) && $object->client_type==$shopType->id ? 'selected' : (old('client_type') == $shopType->id ? 'selected' : '') }}>  {{$shopType->name}} </option>

										@endforeach

									</select>
									@if ($errors->has('client_type'))
										<span class="help-block">
																		<strong>{{ $errors->first('client_type') }}</strong>
																	</span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">إختر الدولة</label>
								<div class="col-lg-10">
									<select name="country_id" class="form-control country_id">
										<option value="0">اختر الدولة</option>
										@foreach(\App\Models\Countries::all() as $country)
											<option value="{{ $country->id }}"  {{ isset($object) && $object->country_id==$country->id ? 'selected' : (old('country_id') == $country->id ? 'selected' : '') }}>{{ $country->name }}</option>
										@endforeach
									</select>
									@if ($errors->has('country_id'))
										<span class="help-block">
									                                        <strong>{{ $errors->first('country_id') }}</strong>
									                                    </span>
									@endif
								</div>

							</div>
							<div class="form-group{{ $errors->has('region_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2"> إختر المنطقة *</label>
								<div class="col-lg-10">
									<select name="region_id" class="form-control region_id">

										<option value="">اختر المنطقة</option>
										@if(isset($object))
											@foreach (\App\Models\Regions::where('country_id', $object->country_id)->get() as $region)
												<option value="{{ $region->id }}"  {{ isset($object) && $object->region_id==$region->id ? 'selected' : (old('region_id') == $region->id ? 'selected' : '') }}>  {{$region->name}} </option>

											@endforeach
										@endif
									</select>
									@if ($errors->has('region_id'))
										<span class="help-block">
                                    		                                        <strong>{{ $errors->first('region_id') }}</strong>
                                    		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2"> إختر المدينة *</label>
								<div class="col-lg-10">
									<select name="state_id" class="form-control state_id">

										<option value="">اختر المدينة</option>
										@if(isset($object))
											@foreach (\App\Models\States::where('region_id', $object->region_id)->get() as $state)
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
							<div class="form-group{{ $errors->has('commercial_no') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">رقم السجل التجارى</label>
								<div class="col-lg-10" style="float: left;">
									<div class="input-group" style="direction: ltr;">
										<input type="number" name="commercial_no" value="{{ isset($object) ? $object->commercial_no  : old('commercial_no')  }}" class="form-control" placeholder="السجل التجارى">
									</div><!-- /.input group -->
									@if ($errors->has('commercial_no'))
										<span class="help-block">
                                    		                                        <strong>{{ $errors->first('commercial_no') }}</strong>
                                    		                                    </span>
									@endif

								</div>

							</div>
							<div class="form-group{{ $errors->has('tax_number') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">الرقم الضريبي</label>
								<div class="col-lg-10" style="float: left;">
									<div class="input-group" style="direction: ltr;">
										<input type="number" name="tax_number" value="{{ isset($object) ? $object->tax_number  : old('tax_number')  }}" class="form-control" placeholder="الرقم الضريبي">
									</div><!-- /.input group -->
									@if ($errors->has('tax_number'))
										<span class="help-block">
                                    		                                        <strong>{{ $errors->first('tax_number') }}</strong>
                                    		                                    </span>
									@endif

								</div>

							</div>
							<div class="form-group{{ $errors->has('commercial_end_date') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">تاريخ انتهاء السجل</label>
								<div class="col-lg-10" style="float: left;">
									<div class="input-group" style="direction: ltr;">
										<input type="text" id="commercial_end_date" name="commercial_end_date" value="{{ isset($object) ? $object->commercial_end_date  : old('commercial_end_date')  }}" class="form-control" placeholder="تاريخ انتهاء السجل التجارى">
									</div><!-- /.input group -->
									@if ($errors->has('commercial_end_date'))
										<span class="help-block">
                                    		                                        <strong>{{ $errors->first('commercial_end_date') }}</strong>
                                    		                                    </span>
									@endif

								</div>

							</div>
							<div class="form-group{{ $errors->has('commercial_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل صورة السجل التجارى</label>
								<div class="col-lg-10">

									<input type="file" name="commercial_id" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('commercial_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('commercial_id') }}</strong>
		                                    </span>
									@endif



								</div>

							</div>
							@if(isset($object)&&$object->commercial_id)
								<div class="form-group">
									<label class="control-label col-lg-2">الصورة الحالية</label>
									<div class="col-lg-10">
										<img alt="" width="100" height="75" src="/uploads/{{ $object  -> commercial_id }}">
									</div>

								</div>
							@endif


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
									<input type="hidden" name="user_type_id" value="3">

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
								<label class="control-label col-lg-2">كلمة المرور *</label>
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
							@if(isset($object)&&$object->photo)
								<div class="form-group">
									<label class="control-label col-lg-2">الصورة الحالية</label>
									<div class="col-lg-10">
										<img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">
									</div>

								</div>
							@endif

						</fieldset>
						<div class="text-right">
							<button type="submit" name="save_request" value="save_only" class="btn btn-primary" style="    float: left;margin-right: 23px;">حفظ التعديلات  <i class="icon-arrow-left13 position-right"></i></button>
						</div>
						@if($object->activate==0 || $object->activate==2)
						<div class="text-right">
							<button type="submit"  name="save_request" value="save_accept" class="btn btn-success">حفظ وقبول  <i class="icon-arrow-left13 position-right"></i></button>
						</div>
							@endif


					</form>
				</div>
			</div>
			<!-- /form horizontal -->


			<!-- Footer -->
		@include('admin.footer')
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
			// var elem1 = document.querySelector('.js-switch1');
			// var init = new Switchery(elem1);
			// var elem2 = document.querySelector('.js-switch2');
			// var init = new Switchery(elem2);
			// var elem3 = document.querySelector('.js-switch3');
			// var init = new Switchery(elem3);

		</script>
	</div>
@stop
