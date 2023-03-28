@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/ui/moment/moment.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/daterangepicker.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/anytime.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/picker_date.js"></script>

	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<!-- /theme JS files -->
@stop
@section('content')
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} كارت</h4>
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
					<li><a href="/provider-panel/cards_categories">عرض أقسام الكروت</a></li>
					<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} كارت</li>
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
					<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} كارت </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<div class="panel-body">

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="/provider-panel/edit-cards-categories/{{ $object->id }}">
						{!! csrf_field() !!}
						{{--@if(isset($object))--}}
							{{--<input type="hidden" name="_method" value="PATCH" />--}}
						{{--@endif--}}
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->




							<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم الكارت</label>
								<div class="col-lg-10">
									<input readonly type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="اسم الكارت">
									@if ($errors->has('name'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم الكارت بالانجليزية</label>
								<div class="col-lg-10">
									<input readonly type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="اسم الكارت بالانجليزية">
									@if ($errors->has('name_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name_en') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-2">إخفاء الكارت</label>
								<div class="col-lg-10">
										<div class="checkbox">
											<label>
												<input name="hidden"  type="checkbox" value="1" {{ isset($object) && $object->hidden==1  ? 'checked':'' }}>
												إخفاء الكارت
											</label>
										</div>

								</div>
							</div>

							<div class="clearfix"></div>
							<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل صورة الكارت</label>
								<div class="col-lg-10">

									<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('photo'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('photo') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							@if(isset($object->photo) && $object->photo )
								<div class="form-group">
									<label class="control-label col-lg-2">الشعار الحالي</label>
									<div class="col-lg-10">
										<img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">
										<br>
										<a href="/provider-panel/delete-photo-store/{{ $object->id }}">حذف الصورة</a>
									</div>
								</div>
							@endif

						</fieldset>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الكارت <i class="icon-arrow-left13 position-right"></i></button>
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
