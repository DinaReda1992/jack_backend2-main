@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>

	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<link rel="stylesheet" href="/js/selectize/css/selectize.css">
	<script src="/js/selectize/js/standalone/selectize.js"></script>

	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<!-- /theme JS files -->
@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} قسم فرعي</h4>
						</div>
						<div class="heading-elements">
							<div class="heading-btn-group">
								<a href="/admin-panel/subcategories"><button type="button" class="btn btn-success" name="button">  عرض الاقسام الفرعية</button></a>
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
							<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li><a href="/admin-panel/subcategories">عرض الأقسام الفرعية</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} قسم فرعي</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} قسم فرعي </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/subcategories/'.$object->id : '/admin-panel/subcategories'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
								   <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">إختر القسم الرئيسي</label>
										<div class="col-lg-10">
										<select name="category_id" class="form-control">
										<option value="">اختر القسم الرئيسي</option>
										@foreach($categories as $category)
										<option value="{{ $category->id }}"  {{ isset($object) && $object->category_id==$category->id ? 'selected' : (old('category_id') == $category->id ? 'selected' : '') }}>{{ $category->name }}</option>
										@endforeach
										</select>
										@if ($errors->has('category_id'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('category_id') }}</strong>
		                                    </span>
		                                @endif
										</div>
										
									</div>
								   
								   
									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم القسم فرعي</label>
										<div class="col-lg-10">
										<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل اسم القسم فرعي">
										@if ($errors->has('name'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
		                                @endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم القسم الفرعي بالانجليزية</label>
										<div class="col-lg-10">
											<input style="direction: ltr" type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="أدخل اسم القسم الفرعي بالانجليزية">
											@if ($errors->has('name_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('name_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('measurementUnits') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">إختر وحدات القياس</label>
										<div class="col-lg-10">
											<select class="select-multiple-tokenization" multiple="multiple" name="measurementUnits[]" id="categories">

												<?php $currentTypes=isset($sub_measurementUnits)?$sub_measurementUnits:(old('measurementUnits')?:[]); ?>
												@foreach($measurementUnits as $rest)
													<option value="{{ $rest->id }}"  {{(in_array($rest->id, $currentTypes)) ? "selected":'' }} >{{ $rest->name }}</option>
												@endforeach
											</select>
											@if ($errors->has('measurementUnits'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('measurementUnits') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									{{--<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">--}}
										{{--<label class="control-label col-lg-2">أدخل صورة القسم</label>--}}
										{{--<div class="col-lg-10">--}}

											{{--<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">--}}
											{{--@if ($errors->has('photo'))--}}
												{{--<span class="help-block">--}}
		                                        {{--<strong>{{ $errors->first('photo') }}</strong>--}}
		                                    {{--</span>--}}
											{{--@endif--}}



										{{--</div>--}}

									{{--</div>--}}
									{{--@if(isset($object->photo))--}}
										{{--<div class="form-group">--}}
											{{--<label class="control-label col-lg-2">الصورة الحالية</label>--}}
											{{--<div class="col-lg-10">--}}
												{{--<img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">--}}
											{{--</div>--}}

										{{--</div>--}}
									{{--@endif--}}
									{{----}}
									
								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} القسم فرعي <i class="icon-arrow-left13 position-right"></i></button>
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