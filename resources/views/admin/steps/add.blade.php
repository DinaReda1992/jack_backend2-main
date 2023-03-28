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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} خطوة</h4>
						</div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li><a href="/admin-panel/steps">عرض الخطوات</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} خطوة</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} خطوة </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/steps/'.$object->id : '/admin-panel/steps'  }}" enctype="multipart/form-data">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
								   
									<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل عنوان الخطوة</label>
										<div class="col-lg-10">
										<input type="text" name="title" value="{{ isset($object) ? $object->title  : old('title')  }}" class="form-control" placeholder="أدخل عنوان الخطوة">
										@if ($errors->has('title'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('title') }}</strong>
		                                    </span>
		                                @endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل وصف الخطوة</label>
										<div class="col-lg-10">
											<input type="text" name="description" value="{{ isset($object) ? $object->description  : old('description')  }}" class="form-control" placeholder="أدخل وصف الخطوة">
											@if ($errors->has('description'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>


									<div class="form-group{{ $errors->has('title_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل عنوان الخطوة بالانجليزية</label>
										<div class="col-lg-10">
											<input type="text" style="direction: ltr" name="title_en" value="{{ isset($object) ? $object->title_en  : old('title_en')  }}" class="form-control" placeholder="أدخل عنوان الخطوة بالانجليزية">
											@if ($errors->has('title_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('title_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل وصف الخطوة بالانجليزية</label>
										<div class="col-lg-10">
											<input type="text" style="direction: ltr" name="description_en" value="{{ isset($object) ? $object->description_en  : old('description_en')  }}" class="form-control" placeholder="أدخل وصف الخطوة بالانجليزية">
											@if ($errors->has('description_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>




								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الخطوة <i class="icon-arrow-left13 position-right"></i></button>
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