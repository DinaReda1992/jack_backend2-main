@extends('admin.layout')
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
							<li><a href="/provider-panel/all-users">عرض المستخدمين</a></li>
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

							<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/all-users/'.$object->id : '/provider-panel/all-users'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
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
@if(@$object->user_type_id==4)
										<div class="form-group">
											<label class="control-label col-lg-2">الخدمات التي يستطيع تقديمها</label>
											<div class="col-lg-10">
												@foreach(\App\Models\Services::where('id','!=',8)->get() as $service)
												<div class="checkbox">
													<label>
														<input name="service[{{ $service->id }}]"  type="checkbox" value="1" {{ \App\Models\UserServices::where('user_id',@$object->id)->where('service_id',$service->id)->first() ? 'checked':'' }}>
														{{ $service->name }}
													</label>
												</div>
													@endforeach

											</div>
										</div>

									<div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">الاسم الاول</label>
										<div class="col-lg-10">
											<input type="text" name="first_name" value="{{ isset($object) ? $object->first_name  : old('first_name')  }}" class="form-control" placeholder="الاسم الاول">
											@if ($errors->has('first_name'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('first_name') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">الاسم الاخير</label>
										<div class="col-lg-10">
											<input type="text" name="last_name" value="{{ isset($object) ? $object->last_name  : old('last_name')  }}" class="form-control" placeholder="الاسم الاخير">
											@if ($errors->has('last_name'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('last_name') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('brand') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">ماركة السيارة</label>
										<div class="col-lg-10">
											<input type="text" name="brand" value="{{ isset($object) ? $object->brand  : old('brand')  }}" class="form-control" placeholder="ماركة السيارة">
											@if ($errors->has('brand'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('brand') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>


									<div class="form-group{{ $errors->has('model') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">موديل السيارة</label>
										<div class="col-lg-10">
											<input type="text" name="model" value="{{ isset($object) ? $object->model  : old('model')  }}" class="form-control" placeholder="موديل السيارة">
											@if ($errors->has('model'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('model') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
@endif
									<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">البريد الاكتروني</label>
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
													<option value="{{ $code->phonecode }}" {{ isset($object) && $object->phonecode==$code->phonecode ? 'selected' : (old('phonecode') == $code->id ? 'selected' : '') }}>+{{ $code->phonecode }}</option>
												@endforeach
											</select>
											@if ($errors->has('phonecode'))
												<span class="help-block">
											<strong>{{ $errors->first('phonecode') }}</strong>
										</span>
											@endif
										</div>
									</div>
									<div   class=" form-group{{ $errors->has('user_type_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نوع العضوية</label>
										<div class="col-lg-10">
											<select name="user_type_id" class="form-control user_type_id">
												<option value="">اختر نوع العضوية </option>
												<option value="3" {{ old('user_type_id') == 3 || (isset($object) && $object->user_type_id==3) ? 'selected'  : ''  }}>مستخدم عادي </option>
												<option value="4" {{ old('user_type_id') == 4 || (isset($object) && $object->user_type_id==4) ? 'selected'  : ''  }}>مندوب </option>
											</select>
											@if ($errors->has('user_type_id'))
												<span class="help-block">
											<strong>{{ $errors->first('user_type_id') }}</strong>
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

									@if(@$object->user_type_id==4)
									<div class="clearfix"></div>



									<div class="form-group{{ $errors->has('liscense') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">صورة الرخصة</label>
										<div class="col-lg-10">

											<input type="file" name="liscense" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('liscense'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('liscense') }}</strong>
		                                    </span>
											@endif



										</div>

									</div>
									@if(isset($object->liscense))
										<div class="form-group">
											<label class="control-label col-lg-2">الصورة الحالية</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> liscense }}">
											</div>

										</div>
									@endif


									<div class="clearfix"></div>



									<div class="form-group{{ $errors->has('national_photo') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">صورة الهوية</label>
										<div class="col-lg-10">

											<input type="file" name="national_photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('national_photo'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('national_photo') }}</strong>
		                                    </span>
											@endif



										</div>

									</div>
									@if(isset($object->national_photo))
										<div class="form-group">
											<label class="control-label col-lg-2">الصورة الحالية</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> national_photo }}">
											</div>

										</div>
									@endif

									<div class="clearfix"></div>



									<div class="form-group{{ $errors->has('front_car') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">الصورة الامامية للسيارة</label>
										<div class="col-lg-10">

											<input type="file" name="front_car" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('front_car'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('front_car') }}</strong>
		                                    </span>
											@endif



										</div>

									</div>
									@if(isset($object->front_car))
										<div class="form-group">
											<label class="control-label col-lg-2">الصورة الحالية</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> front_car }}">
											</div>

										</div>
									@endif

									<div class="clearfix"></div>



									<div class="form-group{{ $errors->has('back_car') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">الصورة الخلفية للسيارة</label>
										<div class="col-lg-10">

											<input type="file" name="back_car" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('back_car'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('back_car') }}</strong>
		                                    </span>
											@endif



										</div>

									</div>
									@if(isset($object->back_car))
										<div class="form-group">
											<label class="control-label col-lg-2">الصورة الحالية</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> back_car }}">
											</div>

										</div>
									@endif
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
					@include('admin.footer')
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
@stop
