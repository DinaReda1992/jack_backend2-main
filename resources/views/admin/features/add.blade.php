@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->

@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} خاصية</h4>
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
							<li><a href="/admin-panel/features">عرض الخصائص</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} خاصية</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} خاصية </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post" enctype="multipart/form-data" class="form-horizontal" action="{{ isset($object)? '/admin-panel/features/'.$object->id : '/admin-panel/features'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
										<input type="hidden" name="_method" value="PATCH" />
									@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> اسم الخاصية بالعربية </label>
										<div class="col-lg-10">
										<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="اسم الخاصية بالعربية">
										@if ($errors->has('name'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
		                                @endif
										</div>
										
									</div>
									<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> اسم الخاصية بالانجليزية </label>
										<div class="col-lg-10">
											<input style="direction: ltr" type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="اسم الخاصية بالانجليزية">
											@if ($errors->has('name_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('name_en') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>
									<div class="form-group{{ $errors->has('min_price') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> اقل سعر </label>
										<div class="col-lg-10">
											<input type="text" name="min_price" value="{{ isset($object) ? $object->min_price  : old('min_price')  }}" class="form-control" placeholder="حدد اقل سعر للخاصية يمكن تحديده للخاصية">
											@if ($errors->has('min_price'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('min_price') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('max_price') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> اعلى سعر </label>
										<div class="col-lg-10">
											<input type="text" name="max_price" value="{{ isset($object) ? $object->max_price  : old('max_price')  }}" class="form-control" placeholder="حدد اعلى سعر للخاصية يمكن تحديده للخاصية">
											@if ($errors->has('max_price'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('max_price') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('is_one') ? ' has-error' : '' }}">
										<div class="col-lg-10">
											<label class="checkbox-inline">
												<input type="checkbox" name="is_one" {{ isset($object) ? $object->is_one?'checked':''  : old('is_one')?'checked':''  }}>
												تحجز مره واحدة
											</label>
											@if ($errors->has('is_one'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('is_one') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>


									{{--									<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">--}}
{{--										<label class="control-label col-lg-2">أدخل علم الخاصية</label>--}}
{{--										<div class="col-lg-10">--}}

{{--											<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">--}}
{{--											@if ($errors->has('photo'))--}}
{{--												<span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('photo') }}</strong>--}}
{{--		                                    </span>--}}
{{--											@endif--}}



{{--										</div>--}}

{{--									</div>--}}



{{--									@if(isset($object->photo))--}}
{{--										<div class="form-group">--}}
{{--											<label class="control-label col-lg-2">العلم الحالي</label>--}}
{{--											<div class="col-lg-10">--}}
{{--												<img alt="" width="100" height="75" src="/flags/{{ $object  -> photo }}">--}}
{{--											</div>--}}

{{--										</div>--}}
{{--									@endif--}}

								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الخاصية <i class="icon-arrow-left13 position-right"></i></button>
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