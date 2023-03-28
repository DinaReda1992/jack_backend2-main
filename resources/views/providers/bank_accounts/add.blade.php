@extends('providers.layout')
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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} حساب بنكي</h4>
						</div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li><a href="/provider-panel/bank_accounts">عرض الحسابات البنكية</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} حساب بنكي</li>
						</ul>
						
					</div>
				</div>
				<!-- /page header -->

				<!-- Content area -->
				<div class="content">
				@include('providers.message')
					<!-- Form horizontal -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} حساب بنكي </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/bank_accounts/'.$object->id : '/provider-panel/bank_accounts'  }}" enctype="multipart/form-data">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
								   
									<div class="form-group{{ $errors->has('bank_name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم البنك</label>
										<div class="col-lg-10">
										<input type="text" name="bank_name" value="{{ isset($object) ? $object->bank_name  : old('bank_name')  }}" class="form-control" placeholder="أدخل اسم البنك">
										@if ($errors->has('bank_name'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('bank_name') }}</strong>
		                                    </span>
		                                @endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('bank_name_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم البنك بالانجليزية</label>
										<div class="col-lg-10">
											<input type="text" name="bank_name_en" value="{{ isset($object) ? $object->bank_name_en  : old('bank_name_en')  }}" class="form-control" placeholder="أدخل اسم البنك بالانجليزية">
											@if ($errors->has('bank_name_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('bank_name_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('account_name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم صاحب الحساب</label>
										<div class="col-lg-10">
											<input type="text" name="account_name" value="{{ isset($object) ? $object->account_name  : old('account_name')  }}" class="form-control" placeholder="أدخل اسم صاحب الحساب">
											@if ($errors->has('account_name'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('account_name') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('account_number') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل رقم الحساب</label>
										<div class="col-lg-10">
											<input type="text" name="account_number" value="{{ isset($object) ? $object->account_number  : old('account_number')  }}" class="form-control" placeholder="أدخل رقم الحساب">
											@if ($errors->has('account_number'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('account_number') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

{{--									<div class="form-group{{ $errors->has('account_ipan') ? ' has-error' : '' }}">--}}
{{--										<label class="control-label col-lg-2">أدخل رقم الايبان</label>--}}
{{--										<div class="col-lg-10">--}}
{{--											<input type="text" name="account_ipan" value="{{ isset($object) ? $object->account_ipan  : old('account_ipan')  }}" class="form-control" placeholder="أدخل رقم الايبان">--}}
{{--											@if ($errors->has('account_ipan'))--}}
{{--												<span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('account_ipan') }}</strong>--}}
{{--		                                    </span>--}}
{{--											@endif--}}
{{--										</div>--}}
{{--									</div>--}}

{{--									<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">--}}
{{--									<label class="control-label col-lg-2">أدخل شعار البنك</label>--}}
{{--									<div class="col-lg-10">--}}

{{--									<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">--}}
{{--									@if ($errors->has('photo'))--}}
{{--									<span class="help-block">--}}
{{--									<strong>{{ $errors->first('photo') }}</strong>--}}
{{--									</span>--}}
{{--									@endif--}}



{{--									</div>--}}

{{--									</div>--}}
{{--									@if(isset($object->photo))--}}
{{--									<div class="form-group">--}}
{{--									<label class="control-label col-lg-2">الشعار الحالي</label>--}}
{{--									<div class="col-lg-10">--}}
{{--									<img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">--}}
{{--									</div>--}}

{{--									</div>--}}
{{--									@endif--}}


								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الحساب البنكي <i class="icon-arrow-left13 position-right"></i></button>
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
			</div>
@stop	