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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} دولة</h4>
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
							<li><a href="/provider-panel/countries">عرض الدول</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} دولة</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} دولة </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post" enctype="multipart/form-data" class="form-horizontal" action="{{ isset($object)? '/provider-panel/countries/'.$object->id : '/provider-panel/countries'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
										<input type="hidden" name="_method" value="PATCH" />
									@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
									<div class="form-group{{ $errors->has('name_ar') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> اسم الدولة بالعربية </label>
										<div class="col-lg-10">
										<input type="text" name="name_ar" value="{{ isset($object) ? $object->name_ar  : old('name_ar')  }}" class="form-control" placeholder="اسم الدولة بالعربية">
										@if ($errors->has('name_ar'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('name_ar') }}</strong>
		                                    </span>
		                                @endif
										</div>
										
									</div>
									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> اسم الدولة بالانجليزية </label>
										<div class="col-lg-10">
											<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="اسم الدولة بالانجليزية">
											@if ($errors->has('name'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>
									<div class="form-group{{ $errors->has('phonecode') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> الرمز الافتتاحي لأرقام الهواتف </label>
										<div class="col-lg-10">
											<input type="text" name="phonecode" value="{{ isset($object) ? $object->phonecode  : old('phonecode')  }}" class="form-control" placeholder="الرمز الافتتاحي لأرقام الهواتف">
											@if ($errors->has('phonecode'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('phonecode') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
										@foreach(\App\Models\Services::all() as $service)
										<div class="form-group">
											<label class="control-label col-lg-2"> أسعار خدمة {{ $service->name }}  </label>
											<div class="col-lg-5">
												<input type="text" name="price[{{ $service->id }}]" value="{{ isset($object) ? @\App\Models\Prices::whereIn('state_id', function ($query) use ($object) { $query->select('id')->from(with(new \App\Models\States())->getTable())->where('country_id', $object->id); })->where('service_id',$service->id)->first()->price :'' }}"  class="form-control" placeholder=" السعر ">
											</div>
											<div class="col-lg-5">
												<select name="currency_id[{{ $service->id }}]" class="form-control">
													<option value="">اختر العملة</option>
													@foreach(\App\Models\Currencies::all() as $currency)
													<option value="{{ $currency->id }}" {{ isset($object) && @\App\Models\Prices::whereIn('state_id', function ($query) use ($object) { $query->select('id')->from(with(new \App\Models\States())->getTable())->where('country_id', $object->id); })->where('service_id',$service->id)->first()->currency_id == $currency->id ? ' selected' : ''  }} >{{ $currency->name }} ( {{ $currency->code }} )</option>
													@endforeach
												</select>
											</div>
										</div>
									@endforeach

									<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل علم الدولة</label>
										<div class="col-lg-10">

											<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('photo'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('photo') }}</strong>
		                                    </span>
											@endif



										</div>

									</div>



									@if(isset($object->photo))
										<div class="form-group">
											<label class="control-label col-lg-2">العلم الحالي</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/flags/{{ $object  -> photo }}">
											</div>

										</div>
									@endif

								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الدولة <i class="icon-arrow-left13 position-right"></i></button>
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