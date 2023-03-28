@extends('admin.layout')
@section('js_files')
	<script type="text/javascript" src="/assets/js/plugins/editors//.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/editor_.js"></script>
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>

	<!-- /theme JS files -->
	<!-- /theme JS files -->

@stop
@section('content')
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} محتوى الموقع</h4>
				</div>

			</div>

			<div class="breadcrumb-line">
				<ul class="breadcrumb">
					<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
					<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} محتوى الموقع</li>
				</ul>


			</div>
		</div>
		<!-- /page header -->

		<!-- Content area -->
		<div class="content">

		@include('admin.message')

		<!-- Form horizontal -->





					<form enctype="multipart/form-data" method="post" class="form-horizontal" action='/admin-panel/updateSiteContent'>
						{!! csrf_field() !!}
						<div class="panel panel-flat">
							<div class="panel-heading">
								<h5 class="panel-title">اعلى الصفحة </h5>
								<div class="heading-elements">
									<ul class="icons-list">
										<li><a data-action="collapse"></a></li>
										<li><a data-action="reload"></a></li>
										<li><a data-action="close"></a></li>
									</ul>
								</div>
							</div>
							<div class="panel-body">
						<fieldset class="content-group">
							<div class="form-group{{ $errors->has('top_photo') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل صورة اعلى الصفحة</label>
								<div class="col-lg-10">

									<input type="file" name="top_photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('top_photo'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('top_photo') }}</strong>
		                                    </span>
									@endif



								</div>

							</div>
							@if(isset($object->top_photo))
								<div class="form-group">
									<label class="control-label col-lg-2">الصورة الحالية</label>
									<div class="col-lg-10">
										<img alt="" width="100" height="75" src="/uploads/{{ $object->top_photo }}">
									</div>

								</div>
							@endif



						</fieldset>
						</div>
						</div>

						<div class="panel panel-flat">
							<div class="panel-heading">
								<h5 class="panel-title"> عن التطبيق </h5>
								<div class="heading-elements">
									<ul class="icons-list">
										<li><a data-action="collapse"></a></li>
										<li><a data-action="reload"></a></li>
										<li><a data-action="close"></a></li>
									</ul>
								</div>
							</div>
							<div class="panel-body">
								<fieldset class="content-group">
									<div class="form-group{{ $errors->has('about_text') ? ' has-error' : '' }}">

										<label class="control-label col-lg-2">عن التطبيق </label>
										<div class="col-lg-10">

											<textarea name="about_text" class="form-control " rows="8" cols="100">{{ isset($object) ? $object->about_text  : old('about_text')  }}</textarea>

											@if ($errors->has('about_text'))
												<span class="help-block">
																					<strong>{{ $errors->first('about_text') }}</strong>
																			</span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('about_photo') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل صورة عن التطبيق</label>
										<div class="col-lg-10">

											<input type="file" name="about_photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('about_photo'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('about_photo') }}</strong>
		                                    </span>
											@endif



										</div>
									</div>
									@if(isset($object->about_photo))
										<div class="form-group">
											<label class="control-label col-lg-2">الصورة الحالية</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> about_photo }}">
											</div>

										</div>
									@endif
								</fieldset>
							</div>
						</div>
						<div class="panel panel-flat">
							<div class="panel-heading">
								<h5 class="panel-title"> صورة المميزات </h5>
								<div class="heading-elements">
									<ul class="icons-list">
										<li><a data-action="collapse"></a></li>
										<li><a data-action="reload"></a></li>
										<li><a data-action="close"></a></li>
									</ul>
								</div>
							</div>
							<div class="panel-body">
								<fieldset class="content-group">
									<div class="form-group{{ $errors->has('features_photo') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل صورة المميزات</label>
										<div class="col-lg-10">

											<input type="file" name="features_photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('features_photo'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('features_photo') }}</strong>
		                                    </span>
											@endif



										</div>
									</div>
									@if(isset($object->features_photo))
										<div class="form-group">
											<label class="control-label col-lg-2">الصورة الحالية</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> features_photo }}">
											</div>

										</div>
									@endif
								</fieldset>
							</div>
						</div>

						<div class="panel panel-flat">
							<div class="panel-heading">
								<h5 class="panel-title">صور التطبيق </h5>
								<div class="heading-elements">
									<ul class="icons-list">
										<li><a data-action="collapse"></a></li>
										<li><a data-action="reload"></a></li>
										<li><a data-action="close"></a></li>
									</ul>
								</div>
							</div>
						<div class="panel-body">

						<fieldset class="content-group">
							<div class="form-group{{ $errors->has('screenshots') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">صور من شاشات التطبيق</label>
								<div class="col-lg-10">
									<input multiple type="file" name="screenshots[]" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('screenshots'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('screenshots') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							@if($screenshots->count()> 0 )

								<div class="form-group">
									<label class="control-label col-lg-2">الصور الحالية</label>

									<div class="col-lg-10">
										@foreach( $screenshots as $photo)
											<div align="center" style="height: 75px;width: 100px;float: right;margin-right: 5px">
												<img alt="" width="100" height="75" src="/uploads/{{ $photo  -> photo }}">
												<a href="/admin-panel/delete-screenshot-photo/{{ $photo->id }}" style="text-align: center">حذف</a>
											</div>
										@endforeach
									</div>

								</div>

							@endif

						</fieldset>
						</div>
						</div>
						<div class="panel panel-flat">
							<div class="panel-heading">
								<h5 class="panel-title"> رابط الفيديو التعريفى </h5>
								<div class="heading-elements">
									<ul class="icons-list">
										<li><a data-action="collapse"></a></li>
										<li><a data-action="reload"></a></li>
										<li><a data-action="close"></a></li>
									</ul>
								</div>
							</div>
							<div class="panel-body">
								<fieldset class="content-group">
									<div class="form-group{{ $errors->has('app_video') ? ' has-error' : '' }}">

										<label class="control-label col-lg-2">رابط الفيديو من اليوتيوب </label>
										<div class="col-lg-10">

											<input name="app_video" class="form-control " value="{{isset($object) ? $object->app_video  : old('app_video') }}">

											@if ($errors->has('app_video'))
												<span class="help-block">
																					<strong>{{ $errors->first('app_video') }}</strong>
																			</span>
											@endif
										</div>
									</div>

								</fieldset>
							</div>
						</div>
{{--						<div class="panel panel-flat">--}}
{{--							<div class="panel-heading">--}}
{{--								<h5 class="panel-title">صور اسفل الصفحة </h5>--}}
{{--								<div class="heading-elements">--}}
{{--									<ul class="icons-list">--}}
{{--										<li><a data-action="collapse"></a></li>--}}
{{--										<li><a data-action="reload"></a></li>--}}
{{--										<li><a data-action="close"></a></li>--}}
{{--									</ul>--}}
{{--								</div>--}}
{{--							</div>--}}
{{--							<div class="panel-body">--}}

{{--								<fieldset class="content-group">--}}
{{--									<div class="form-group{{ $errors->has('footer_photo') ? ' has-error' : '' }}">--}}
{{--										<label class="control-label col-lg-2">أدخل صورة اسفل الصفحة</label>--}}
{{--										<div class="col-lg-10">--}}

{{--											<input type="file" name="footer_photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">--}}
{{--											@if ($errors->has('footer_photo'))--}}
{{--												<span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('footer_photo') }}</strong>--}}
{{--		                                    </span>--}}
{{--											@endif--}}



{{--										</div>--}}

{{--									</div>--}}
{{--									@if(isset($object->footer_photo))--}}
{{--										<div class="form-group">--}}
{{--											<label class="control-label col-lg-2">الصورة الحالية</label>--}}
{{--											<div class="col-lg-10">--}}
{{--												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> footer_photo }}">--}}
{{--											</div>--}}

{{--										</div>--}}
{{--									@endif--}}

{{--								</fieldset>--}}
{{--							</div>--}}
{{--						</div>--}}

						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} محتوى الموقع <i class="icon-arrow-left13 position-right"></i></button>
						</div>
					</form>





			<!-- /form horizontal -->


			<!-- Footer -->
		@include('admin.footer')
		<!-- /footer -->

		</div>
		<!-- /content area -->

	</div>
@stop
