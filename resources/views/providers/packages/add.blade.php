@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>

@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">
				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} باقة</h4>
						</div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li><a href="/provider-panel/packages">عرض الباقات</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} باقة</li>
						</ul>
						
					</div>
				</div>
				<!-- /page header -->

				<!-- Content area -->
				<div class="content">
				@include('admin.message')
					<!-- Form horizontal -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} باقة </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/packages/'.$object->id : '/provider-panel/packages'  }}" enctype="multipart/form-data">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
								   
									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم الباقة بالعربية</label>
										<div class="col-lg-10">
										<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل اسم الباقة بالعربية">
										@if ($errors->has('name'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
		                                @endif
										</div>
									</div>

{{--									<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">--}}
{{--										<label class="control-label col-lg-2">أدخل اسم الباقة بالانجليزية</label>--}}
{{--										<div class="col-lg-10">--}}
{{--											<input type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="أدخل اسم الباقة بالانجليزية">--}}
{{--											@if ($errors->has('name_en'))--}}
{{--												<span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('name_en') }}</strong>--}}
{{--		                                    </span>--}}
{{--											@endif--}}
{{--										</div>--}}
{{--									</div>--}}

									<div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل سعر الباقة</label>
										<div class="col-lg-5">
											<input type="text" name="price" value="{{ isset($object) ? $object->price  : old('price')  }}" class="form-control" placeholder="أدخل سعر الباقة">
											@if ($errors->has('price'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('price') }}</strong>
		                                    </span>
											@endif
										</div>
										<div class="col-lg-5">
											<select class="form-control" name="currency_id">
												<option value="">اختر العملة</option>

												@foreach(\App\Models\Currencies::all() as $currency)
													<option value="{{ $currency->id }}" {{ isset($object) && $object->currency_id == $currency->id ? 'selected':'' }}>{{ $currency->name }}</option>
												@endforeach
											</select>
											@if ($errors->has('currecny_id'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('currecny_id') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('days') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">عدد الايام للباقة</label>
										<div class="col-lg-10">
											<input type="text" name="days" value="{{ isset($object) ? $object->days  : old('days')  }}" class="form-control" placeholder="عدد الايام للباقة">
											@if ($errors->has('days'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('days') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('allowed_ads') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">عدد الاعلانات المسموح بها</label>
										<div class="col-lg-10">
											<input type="text" name="allowed_ads" value="{{ isset($object) ? $object->allowed_ads  : old('allowed_ads')  }}" class="form-control" placeholder="عدد الاعلانات المسموح بها يوميا">
											@if ($errors->has('allowed_ads'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('allowed_ads') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الباقة <i class="icon-arrow-left13 position-right"></i></button>
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