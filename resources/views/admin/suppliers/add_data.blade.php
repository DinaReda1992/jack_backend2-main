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
	<link href="/assets/js/plugins/step-progress/step-progress.min.css" rel="stylesheet">
	<link href="/assets/js/plugins/step-progress/style.css" rel="stylesheet">


	{{--	<script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>--}}

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript">
		$(document).ready(function () {
			$("[data-mask]").inputmask();

		});


	</script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/switch.min.js"></script>

	<script type="text/javascript" src="/assets/js/pages/form_checkboxes_radios.js"></script>


@stop
@section('content')
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($supplier_data)? 'تعديل':'إضافة' }}  بيانات منشأة ( {{@$user->username}} )</h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/admin-panel/suppliers"><button type="button" class="btn btn-success" name="button"> عرض الموردين</button></a>
					</div>
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
				<div class="steps">
					<ul class="steps-container">

						<li style="width:50%;" class="activated">
							<a href="{{isset($user)?'/admin-panel/suppliers/'.$user->id."/edit":'#'}}">

							<div class="step">
								<div class="step-image"><span>
{{--                                                                        <i class="fa fa-check"></i>--}}

                                    </span></div>
								<div class="step-current">Step 1</div>
								<div class="step-description">بيانات الحساب</div>
							</div>
							</a>
						</li>
						<li style="width:50%;"class="activated">
								<div class="step">
									<div class="step-image"><span></span></div>
									<div class="step-current">Step 2</div>
									<div class="step-description">بيانات المنشأة</div>
								</div>
						</li>
					</ul>
					<div class="step-bar" style="width: 50%;"></div>
					<div class="step-bar" style="width: 51%;right: 0;left: auto"></div>

				</div>




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
					<h5 class="panel-title">{{ isset($supplier_data)? 'تعديل':'إضافة' }}   بيانات منشأة ( {{@$user->username}} ) </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<div class="panel-body">

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ '/admin-panel/post_supplier_data'}}">
						{!! csrf_field() !!}
						<input type="hidden" name="user_id" value="{{$user->id}}">
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->

							<div class="form-group{{ $errors->has('supplier_name') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم المنشأة بالعربى *</label>
								<div class="col-lg-10">
									<input type="text" name="supplier_name" value="{{ isset($supplier_data) ? $supplier_data->supplier_name  : old('supplier_name')  }}" class="form-control" placeholder="اسم المنشأة باللغة العربية حسب السجل التجارى / الصناعى او معروف">
									@if ($errors->has('supplier_name'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('supplier_name') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('supplier_name_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم المنشأة بالانجليزى *</label>
								<div class="col-lg-10">
									<input type="text" name="supplier_name_en" value="{{ isset($supplier_data) ? $supplier_data->supplier_name_en  : old('supplier_name_en')  }}" class="form-control" placeholder="اسم المنشأة باللغة الانجليزية حسب السجل التجارى / الصناعى او معروف">
									@if ($errors->has('supplier_name_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('supplier_name_en') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('commercial_no') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">رقم السجل التجارى / الصناعى </label>
								<div class="col-lg-10">
									<input type="text" name="commercial_no" value="{{ isset($supplier_data) ? $supplier_data->commercial_no  : old('commercial_no')  }}" class="form-control" placeholder="رقم السجل التجارى / الصناعى (ان وجد)">
									@if ($errors->has('commercial_no'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('commercial_no') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('maroof_no') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">رقم التسجيل بمعروف </label>
								<div class="col-lg-10">
									<input type="text" name="maroof_no" value="{{ isset($supplier_data) ? $supplier_data->maroof_no  : old('maroof_no')  }}" class="form-control" placeholder="رقم التسجيل بمعروف (ان وجد)">
									<span class="help-block">

		                                        <strong>
													يجب ان يتوفر رفم السجل التجارى / الصناعى او رقم التسجيل بمعروف
												</strong>
		                                    </span>

								@if ($errors->has('maroof_no'))
										<span class="help-block">

		                                        <strong>{{ $errors->first('maroof_no') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('tax_no') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">الرقم الضريبى *</label>
								<div class="col-lg-10">
									<input type="text" name="tax_no" value="{{ isset($supplier_data) ? $supplier_data->tax_no  : old('tax_no')  }}" class="form-control" placeholder="الرقم الضريبى">
									@if ($errors->has('tax_no'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('tax_no') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">البريد الالكتروني الرسمى </label>
								<div class="col-lg-10" style="float: left;">
									<div class="input-group" style="direction: ltr;">
										<div class="input-group-addon">
											<i class="fa fa-envelope-o"></i>
										</div>
										<input type="text" name="email" value="{{ isset($supplier_data) ? $supplier_data->email  :( old('email')?:$user->email)  }}" class="form-control" placeholder="البريد الالكتروني">
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
										<input type="text"name="phone" value="{{ isset($supplier_data) ? $supplier_data->phone  : (old('phone')?:$user->phone)  }}" placeholder="رقم الجوال" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
									</div><!-- /.input group -->

									@if ($errors->has('phone'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('phone') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('bio') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">نبذة عن المورد</label>
								<div class="col-lg-10">
									<textarea  name="bio"  class="form-control" placeholder="اكتب نبذة عن المورد">{{ isset($supplier_data) ? $supplier_data->bio  : old('bio')  }}</textarea>
									@if ($errors->has('bio'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('bio') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل صورة المنشأة</label>
								<div class="col-lg-10">

									<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('photo'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('photo') }}</strong>
		                                    </span>
									@endif



								</div>

							</div>
							@if(isset($supplier_data)&&$supplier_data->photo)
								<div class="form-group">
									<label class="control-label col-lg-2">الصورة الحالية</label>
									<div class="col-lg-10">
										<img alt="" width="100" height="75" src="/uploads/{{ $supplier_data  -> photo }}">
									</div>

								</div>
							@endif
							<div class="form-group{{ $errors->has('stop') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">تفعيل المورد </label>
								<div class="col-md-10 checkbox checkbox-switchery switchery-sm switchery-double">

									<p><input type="checkbox" class="switchery sweet_switch" name="stop" {{ isset($supplier_data) ? ($supplier_data->stop==0?'checked':'')  :'checked'  }} />  </p>
								</div>
							</div>

						</fieldset>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($supplier_data)? 'تعديل':'إضافة' }} المستخدم  <i class="icon-arrow-left13 position-right"></i></button>
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

		<script type="text/javascript">

		</script>
	</div>
@stop
