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

	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>

	{{--	<script src="/assets/js/plugins/jQuery/jQuery-2.1.4.min.js"></script>--}}
	<script src="/assets/js/plugins/bootstrap/js/bootstrap.min.js"></script>


	<!-- /theme JS files -->
	<script>
		$(function () {
			$('.make_id').change(function () {
				var make_id = $('.make_id').val();
				$('.make_id').val('');
				$.ajax({
					url: '/admin-panel/getMakeYears/' + make_id,
					success: function (data) {
						$('.year_id').html(data);
					}
				});

			});

		})
	</script>

@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} موديل {{isset($my_year)?'ل'.$my_make->name.'-'.$my_year->year:''}}  </h4>
						</div>
						<div class="heading-elements">
							<div class="heading-btn-group">
								<a href="/admin-panel/models{{isset($my_year)?'?year='.$my_year->id:''}}"><button type="button" class="btn btn-success" name="button">   عرض موديلات السيارات {{isset($my_year)?'ل'.$my_make->name.'-'.$my_year->year:''}}</button></a>
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
							<li><a href="/admin-panel/models">عرض الموديلات</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} موديل</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} موديل </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">

							<form method="post" enctype="multipart/form-data" class="form-horizontal" action="{{ isset($object)? '/admin-panel/models/'.$object->id : '/admin-panel/models'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
										<input type="hidden" name="_method" value="PATCH" />
									@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
									<div class="form-group{{ $errors->has('make_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">اختر نوع السيارة</label>
										<div class="col-lg-10">
											<select name="make_id" class="make_id form-control select-multiple-tokenization">
												<option value="">اختر النوع</option>
												@foreach(\App\Models\Make::where('stop',0)->where('is_archived',0)->get() as $make)
													<option value="{{ $make->id }}"  {{ isset($object) && $object->year->make->id==$make->id ? 'selected' : (old('make_id') == $make->id ? 'selected' : (isset($my_make)&&$my_make->id == $make->id ? 'selected':'')) }}>{{ $make->name }}</option>
												@endforeach
											</select>
											@if ($errors->has('make_id'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('make_id') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>
									<div class="form-group{{ $errors->has('year_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">اختر سنة الصنع</label>
										<div class="col-lg-10">
											<select name="year_id" class="year_id form-control select-multiple-tokenization">
												<option value="">اختر السنة</option>
												@if($my_make)
												@foreach(\App\Models\MakeYear::where('make_id',$my_make->id)->where('is_archived',0)->get() as $year)
													<option value="{{ $year->id }}"  {{ isset($object) && $object->year_id==$year->id ? 'selected' : (old('year_id') == $year->id ? 'selected' : (isset($my_year)&&$my_year->id == $year->id ? 'selected':'')) }}>{{ $year->year }}</option>
												@endforeach
													@endif
											</select>
											@if ($errors->has('year_id'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('year_id') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> الاسم باللغة العربية </label>
										<div class="col-lg-10">
											<input  type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="ادخل اسم الموديل بالعربية">
											@if ($errors->has('name'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>
									<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> الاسم باللغة الإنجليزية </label>
										<div class="col-lg-10">
											<input  type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="ادخل اسم الموديل باللغة الإنجليزية">
											@if ($errors->has('name_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('name_en') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>


								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الموديل <i class="icon-arrow-left13 position-right"></i></button>
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