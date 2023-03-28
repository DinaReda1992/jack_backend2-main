@extends('admin.layout')
@section('js_files')
	<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} محتوى الصفحة</h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/admin-panel/content"><button type="button" class="btn btn-success" name="button">  عرض الصفحات</button></a>
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
				<ul class="breadcrumb">
					<li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
					<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} محتوى الصفحة</li>
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
					<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} محتوى الصفحة </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<div class="panel-body">

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/content/'.$object->id : '/provider-panel/content'  }}">
						{!! csrf_field() !!}
						@if(isset($object))
							<input type="hidden" name="_method" value="PATCH" />
						@endif
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->
								<div class="form-group{{ $errors->has('page_name') ? ' has-error' : '' }}">
									<label class="control-label col-lg-2">اسم الصفحة </label>
									<div class="col-lg-10">
										<input type="text" name="page_name" value="{{ isset($object) ? $object->page_name  : old('page_name')  }}" class="form-control" placeholder="اسم الصفحة">
										@if ($errors->has('page_name'))
											<span class="help-block">
													<strong>{{ $errors->first('page_name') }}</strong>
											</span>
										@endif
									</div>

								</div>

							<div class="form-group{{ $errors->has('page_name_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم الصفحة بالانجليزية </label>
								<div class="col-lg-10">
									<input type="text" name="page_name_en" value="{{ isset($object) ? $object->page_name_en  : old('page_name_en')  }}" class="form-control" placeholder="اسم الصفحة بالانجليزية">
									@if ($errors->has('page_name_en'))
										<span class="help-block">
													<strong>{{ $errors->first('page_name_en') }}</strong>
											</span>
									@endif
								</div>

							</div>

								<div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">

									<label class="control-label col-lg-2">محتوى الصفحة </label>
									<div class="col-lg-10">

													<textarea name="content" class="form-control" rows="8" cols="40">{{ isset($object) ? $object->content  : old('content')  }}</textarea>

										@if ($errors->has('content'))
											<span class="help-block">
																					<strong>{{ $errors->first('content') }}</strong>
																			</span>
										@endif
									</div>
								</div>


							<div class="form-group{{ $errors->has('content_en') ? ' has-error' : '' }}">

								<label class="control-label col-lg-2">محتوى الصفحة </label>
								<div class="col-lg-10" style="direction: ltr">

													<textarea name="content_en" class="form-control" rows="8" cols="40">{{ isset($object) ? $object->content_en  : old('content_en')  }}</textarea>

									@if ($errors->has('content_en'))
										<span class="help-block">
																					<strong>{{ $errors->first('content_en') }}</strong>
																			</span>
									@endif
								</div>
							</div>
						</fieldset>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} محتوى الصفحة <i class="icon-arrow-left13 position-right"></i></button>
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
