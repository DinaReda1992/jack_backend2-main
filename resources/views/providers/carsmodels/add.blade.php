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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} موديل</h4>
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
							<li><a href="/provider-panel/carsmodels">عرض الموديلات</a></li>
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

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/carsmodels/'.$object->id : '/provider-panel/carsmodels'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">




								   <div class="form-group{{ $errors->has('car_category_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">إختر الماركة</label>
										<div class="col-lg-10">
										<select name="car_category_id" class="form-control car_category_id">
										<option value="">اختر الماركة</option>
										@foreach($cars as $car)
										<option value="{{ $car->id }}"  {{ isset($object) && $object->cars_category_id==$car->id ? 'selected' : (old('car_category_id') == $car->id ? 'selected' : '') }}>{{ $car->name }}</option>
										@endforeach
										</select>
										@if($errors->has('car_category_id'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('car_category_id') }}</strong>
		                                    </span>
		                                @endif
										</div>
										
									</div>
								   

										<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
											<label class="control-label col-lg-2">أدخل اسم الموديل بالعربية</label>
											<div class="col-lg-10">
												<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل اسم الموديل بالعربية">
												@if ($errors->has('name'))
													<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
												@endif
											</div>

										</div>

									<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم الموديل بالانجليزية</label>
										<div class="col-lg-10">
											<input type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="أدخل اسم الموديل بالانجليزية">
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
<script type="text/javascript">
	$(document).ready(function(){
		$('.category_id').change(function(){
			var category_id = $(this).val();
			$.get('/getCarsModels/'+category_id , function (data) {
				$('.car_category_id').html(data);
            });
		});
	});
</script>
			</div>
@stop	