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
                    url: '/getStates/' + country_id,
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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} مشرف</h4>
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
							<li><a href="/provider-panel/users">عرض المشرفين</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} مشرف</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} مشرف </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">

							<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/users/'.$object->id : '/provider-panel/users'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->

									<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">اسم المشرف</label>
										<div class="col-lg-10">
										<input type="text" name="username" value="{{ isset($object) ? $object->username  : old('username')  }}" class="form-control" placeholder="اسم المشرف">
										<input type="hidden" name="user_type_id" value="2">
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
											<select name="phonecode" class="form-control">
												<option value="">كود الدولة</option>
												@foreach(\App\Models\Countries::all() as $code)
													<option value="{{ $code->phonecode }}" {{ isset($object) && $object->phonecode==$code->phonecode ? 'selected' : ($code->phonecode == 966 ? 'selected' : '') }}>+{{ $code->phonecode }}</option>
												@endforeach
											</select>
											@if ($errors->has('phonecode'))
												<span class="help-block">
											<strong>{{ $errors->first('phonecode') }}</strong>
										</span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('privilege_id') ? ' has-error' : '' }} {{ $errors->has('privilege_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">رتبة المشرف</label>
										<div class="col-lg-10">
											<select name="privilege_id" class="form-control">
												<option value="">اختر رتبة المشرف</option>
												@foreach($groups as $group)
													<option value="{{ $group->id }}" {{ isset($object) && $object->privilege_id==$group->id ? 'selected' : (old('privilege_id') == $group->id ? 'selected' : '') }}>{{ $group->name }}</option>
												@endforeach
											</select>
											@if ($errors->has('privilege_id'))
												<span class="help-block">
											<strong>{{ $errors->first('privilege_id') }}</strong>
										</span>
											@endif
										</div>
									</div>

									{{--<div class="form-group {{ $errors->has('country_id') ? ' has-error' : '' }}">--}}
										{{--<label class="control-label col-lg-2">الدولة : </label>--}}
										{{--<div class="col-lg-10">--}}
											{{--<select name="country_id" class="country_id form-control">--}}
												{{--<option value="">اختر الدولة</option>--}}
												{{--@foreach (\App\Models\Countries::all() as $country)--}}
													{{--<option value="{{ $country->id }}"--}}
															{{--{{ @$object->country_id == $country->id ? ' selected' : '' }} >{{ $country->name }}</option>--}}
												{{--@endforeach--}}
											{{--</select>--}}
											{{--@if ($errors->has('country_id'))--}}
												{{--<span class="help-block">--}}
												{{--{{ $errors->first('country_id') }}--}}
											{{--</span>--}}
											{{--@endif--}}
										{{--</div>--}}
									{{--</div>--}}

									{{--<div class="form-group {{ $errors->has('state_id') ? ' has-error' : '' }}">--}}
										{{--<label class="control-label col-lg-2">المنطقة : </label>--}}
										{{--<div class="col-lg-10">--}}
											{{--<select name="state_id" class="state_id form-control">--}}
												{{--<option value="">اختر المنطقة</option>--}}
												{{--@if(!empty($object ->country_id))--}}
													{{--@foreach(\App\Models\States::where('country_id',@$object->country_id)->get() as $state)--}}
														{{--<option value="{{ $state->id }}" {{ $state->id == @$object->state_id ? ' selected' : '' }}>{{ $state -> name }}</option>--}}
													{{--@endforeach--}}
												{{--@endif--}}
											{{--</select>--}}
											{{--@if ($errors->has('state_id'))--}}
												{{--<span class="help-block">--}}
                                    {{--{{ $errors->first('state_id') }}--}}
                                {{--</span>--}}

											{{--@endif--}}
										{{--</div>--}}
									{{--</div>--}}

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
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} المشرف  <i class="icon-arrow-left13 position-right"></i></button>
								</div>
							</form>
						</div>
					</div>
					<!-- /form horizontal -->


					<!-- Footer -->
					@include('admin.footer')
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
@stop
