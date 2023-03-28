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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }}سياسة إلغاء</h4>
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
							<li><a href="/admin-panel/cancellation_types">عرض سياسات الإلغاء</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }}سياسة إلغاء</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }}سياسة إلغاء </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/cancellation_types/'.$object->id : '/admin-panel/cancellation_types'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->

									<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل وصف السياسة</label>
										<div class="col-lg-10">
										<input type="text" name="description" value="{{ isset($object) ? $object->description  : old('description')  }}" class="form-control" placeholder="أدخل وصف السياسة">
										@if ($errors->has('description'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل وصف السياسة بالانجليزية</label>
										<div class="col-lg-10">
											<input type="text" name="description_en" value="{{ isset($object) ? $object->description_en  : old('description_en')  }}" class="form-control" placeholder="أدخل  وصف السياسة بالانجليزية">
											@if ($errors->has('description_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description_en') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('days') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">عدد الايام قبل الالغاء</label>
										<div class="col-lg-10">
											<input type="number" name="days" value="{{ isset($object) ? $object->days  : old('days')  }}" class="form-control" placeholder="عدد الايام قبل الالغاء">
											@if ($errors->has('days'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('days') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>
									
									<div class="form-group{{ $errors->has('percent') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نسبة الخصم لهذه السياسة</label>
										<div class="col-lg-10">
											<input type="number" name="percent" value="{{ isset($object) ? $object->percent  : old('percent')  }}" class="form-control" placeholder="نسبة الخصم لهذه السياسة">
											@if ($errors->has('percent'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('percent') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									
								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? ' تعديل ':' إضافة ' }}البنك <i class="icon-arrow-left13 position-right"></i></button>
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
