@extends('providers.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>

	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<link rel="stylesheet" href="/assets/js/plugins/daterangepicker/daterangepicker-bs3.css">

	<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<script src="/assets/js/plugins/daterangepicker/moment.js"></script>

	<link rel="stylesheet" href="/assets/js/plugins/timepicker/bootstrap-timepicker.css">
{{--	<script src="/assets/js/plugins/jQuery/jQuery-2.1.4.min.js"></script>--}}
	<script src="/assets/js/plugins/bootstrap/js/bootstrap.min.js"></script>

	<script src="/assets/js/plugins/timepicker/bootstrap-timepicker.js"></script>
	{{--	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">--}}
	{{--	<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>--}}


	<script type="text/javascript">

        $(document).ready(function () {
			// $('#categories').selectize({
			// 	maxItems: 30
			// });
			//Timepicker

		});

	</script>

	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

	<style type="text/css">
		.iconpicker .iconpicker-item{
			float: right;
		}
		.remove-extra{
			width: 23px;
			display: inline-block;
			float: right;
			height: 23px;
			color: #fff;
			background: #dc4747;
			text-align: center;
			border-radius: 50%;
			margin-top: 5px;
			cursor: pointer;
		}
	</style>

@stop
@section('content')

	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }}  مطعم </h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/provider-panel/restaurants"><button type="button" class="btn btn-success" name="button">  عرض المطاعم</button></a>
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
					<li class="active">أضف مطعم</li>
				</ul>
			</div>
		</div>
		<!-- /page header -->

		<!-- Content area -->
		<div class="content">
			<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/restaurants/'.$object->id : '/provider-panel/restaurants'  }}" enctype="multipart/form-data">
				{!! csrf_field() !!}
				@if(isset($object))
					<input type="hidden" name="_method" value="PATCH" />

				@endif
				@include('admin.message')


				<div class="panel panel-flat">
					<div class="panel-heading">
						<h5 class="panel-title">بيانات المطعم</h5>
						<div class="heading-elements">
							<ul class="icons-list">
								<li><a data-action="collapse"></a></li>
								<li><a data-action="reload"></a></li>
								<li><a data-action="close"></a></li>
							</ul>
						</div>
						<div style=" text-align: center;" >
							<ul class="nav nav-tabs" role="tablist">
								<li  class="active" ><a data-toggle="tab" href="#main_data">معلومات المطعم باللغة العربية</a></li>
								<li  ><a data-toggle="tab" href="#english_data">البيانات باللغة الانجليزية</a></li>
								<li  ><a data-toggle="tab" href="#special_offers">مواعيد العمل والمميزات</a></li>

							</ul>
						</div>

					</div>



					<div class="panel-body">
						<div class="tab-content">
							<div id="main_data" class="tab-pane fade in active ">


						<fieldset class="content-group">


							<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم المطعم </label>
								<div class="col-md-10">
									<input  placeholder="اسم المطعم"  value="{{ isset($object) ? $object->title  : old('title')  }}" class="form-control" type="text" name="title">
									@if ($errors->has('title'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('title') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">نبذة عن المطعم </label>
								<div class="col-md-10">
									<textarea  placeholder="وصف المطعم" style="min-height: 114px;"  class="form-control" name="description">{{ isset($object) ? $object->description  : old('description')  }}</textarea>
									@if ($errors->has('description'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>


							<div class="form-group{{ $errors->has('categories') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">انواع المأكولات التى يقدمها المطعم</label>
								<div class="col-lg-10">
									<select class="select-multiple-tokenization" multiple="multiple" name="categories[]" id="categories">

										<?php $currentTypes=isset($restaurant_categories)?$restaurant_categories:(old('categories')?:[]); ?>
										@foreach(\App\Models\Categories::all() as $category)
											<option value="{{ $category->id }}"  <?php if(in_array($category->id, $currentTypes)){ echo "selected"; } ?> >{{ $category->name }}</option>
										@endforeach
									</select>
									@if ($errors->has('categories'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('categories') }}</strong>
		                                    </span>
									@endif
								</div>

							</div>


							<div class="form-group{{ $errors->has('meal_menu') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">قائمة الوجبات</label>
								<div class="col-lg-10">
									<select class="select-multiple-tokenization"  name="meal_menu_id" id="categories">
										@foreach($meal_menus as $meal_menu)
											<option value="{{ $meal_menu->id }}"  {{ isset($object) && $object->meal_menu_id==$meal_menu->id ? 'selected' : (old('meal_menu_id') == $meal_menu->id ? 'selected' : '') }} >{{ $meal_menu->name }}</option>
										@endforeach
									</select>
									@if ($errors->has('meal_menu_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('meal_menu_id') }}</strong>
		                                    </span>
									@endif
								</div>

							</div>

							<div class="form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
							<label class="control-label col-lg-2">إختر المدينة</label>
							<div class="col-lg-10">
							<select name="state_id" class="select-multiple-tokenization form-control state_id">

							<option value="">اختر المدينة</option>
							@foreach ($states as $state)
							<option value="{{ $state->id }}"  {{ isset($object) && $object->state_id==$state->id ? 'selected' : (old('state_id') == $state->id ? 'selected' : '') }}>  {{$state->name}} </option>

							@endforeach

							</select>
							@if ($errors->has('state_id'))
							<span class="help-block">
							<strong>{{ $errors->first('state_id') }}</strong>
							</span>
							@endif
							</div>
							</div>

							@php
								$long = @$object->longitude?:(old('longitude')?: 46.2620208);
                                $lat = @$object->latitude?:(old('latitude')?: 24.7253981);
							@endphp
							<div class="form-group{{ $errors->has('longitude') || $errors->has('latitude') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">الموقع على الخريطة</label>
								<div class="col-lg-10">
									<input type="hidden" id='lon' name="longitude"  />
									<input type="hidden" id='lat' name="latitude"   />
									<input type="hidden" name="country_code" value="">
									@include('admin.items.mapPlace')
								</div>
							</div>

							<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">وصف العنوان</label>
								<div class="col-lg-10">
									<input type="text" name="address" value="{{ isset($object) ? $object->address  : old('address')  }}" class="form-control" placeholder="اكتب العنوان بالتفصيل">
									@if ($errors->has('address'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('address') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('min_order_price') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اقل سعر لاجراء الطلب </label>
								<div class="col-md-10">
									<input  placeholder="الحد الادنى لتكلفة الطلب حتى يستطيع العميل اجراء طلب"  value="{{ isset($object) ? $object->min_order_price  : old('min_order_price')  }}" class="form-control" type="number" name="min_order_price">
									@if ($errors->has('min_order_price'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('min_order_price') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('delivery_price') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">سعر التوصيل </label>
								<div class="col-md-10">
									<input  placeholder="حدد سعر التوصيل "  value="{{ isset($object) ? $object->delivery_price  : old('delivery_price')  }}" class="form-control" type="number" name="delivery_price">
									@if ($errors->has('delivery_price'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('delivery_price') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('delivery_time') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أقصى مدة للتوصيل *</label>
								<div class="col-md-10">
									<input required placeholder="أقصى مدة للتوصيل"  value="{{ isset($object) ? $object->delivery_time  : old('delivery_time')  }}" class="form-control" type="number" name="delivery_time">

									<span class="help-block">
		                                        أقصى مدة للتوصيل بالدقيقة (45)
		                                    </span>
									@if ($errors->has('delivery_time'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('delivery_time') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('cover') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل صورة الخلفية</label>
								<div class="col-lg-10">

									<input type="file" name="cover" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('cover'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('cover') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							@if(isset($object->cover))
								<div class="form-group">
									<label class="control-label col-lg-2">الصورة الحالية</label>
									<div class="col-lg-10">
										<img alt="" width="100" height="75" src="/uploads/{{ $object  -> cover }}">
									</div>

								</div>
							@endif

							<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل صورة المطعم</label>
								<div class="col-lg-10">

									<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('photo'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('photo') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							@if(isset($object->logo))
								<div class="form-group">
									<label class="control-label col-lg-2">الصورة الحالية</label>
									<div class="col-lg-10">
										<img alt="" width="100" height="75" src="/uploads/{{ $object  -> logo }}">
									</div>

								</div>
							@endif
							<div class="form-group{{ $errors->has('publish') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">تفعيل ونشر المطعم </label>
								<div class="col-md-10">
									<p><input type="checkbox" class="js-switch" name="publish" {{ isset($object) ? ($object->stop==0?'checked':'')  :'checked'  }} />  </p>
								</div>
							</div>


						</fieldset>
							</div>
							<div id="english_data" class="tab-pane fade ">


								<fieldset class="content-group">

									<div class="form-group{{ $errors->has('title_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">اسم المطعم بالانجليزى</label>
										<div class="col-md-10">
											<input  placeholder="اسم المطعم بالانجليزى"  value="{{ isset($object) ? $object->title_en  : old('title_en')  }}" class="form-control" type="text" name="title_en">
											@if ($errors->has('title_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('title_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">وصف وتفاصيل المطعم بالانجليزى </label>
										<div class="col-md-10">
											<textarea  placeholder="وصف المطعم بالانجليزى" style="min-height: 114px;"  class="form-control" name="description_en">{{ isset($object) ? $object->description_en  : old('description_en')  }}</textarea>
											@if ($errors->has('description_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('address_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">  وصف العنوان بالانجليزى</label>
										<div class="col-lg-10">
											<input type="text" name="address_en" value="{{ isset($object) ? $object->address_en  : old('address_en')  }}" class="form-control" placeholder=" اكتب العنوان بالتفصيل بالانجليزى">
											@if ($errors->has('address_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('address_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
								</fieldset>
							</div>
							<div id="special_offers" class="tab-pane fade ">
								<div class="panel panel-flat">
									<div class="panel-heading">
										<h5 class="panel-title"> المميزات    </h5>
									</div>
									<div class="panel-body">

										<fieldset class="content-group">

											<div class="form-group{{ $errors->has('delivery_limit') ? ' has-error' : '' }}">
												<label class="control-label col-lg-2"><input type="checkbox" class="js-switch" id="free_delivery" name="free_delivery" {{ isset($object) ? ($object->free_delivery==1?'checked':'')  :''  }} /> توصيل مجانى </label>

												<div class="col-md-10">
													<input  placeholder="اقل مبلغ للاستفادة من التوصيل المجانى"  value="{{ isset($object) ? $object->delivery_limit  : old('delivery_limit')  }}" {{isset($object) ? ($object->free_delivery==1?'':'disabled'):'disabled'}} class="form-control" type="number" name="delivery_limit" id="delivery_limit">
													<span class="help-block">
		                                        <strong>اذا كان التوصيل مجانى لاى طلب فلا تحدد اقل مبلغ للتوصيل المجانى</strong>
		                                    </span>

												</div>
											</div>

										</fieldset>
									</div>
								</div>

								<div class="panel panel-flat">
									<div class="panel-heading">
										<h5 class="panel-title"> مواعيد العمل    </h5>
									</div>
									<div class="panel-body">

								<fieldset class="content-group">
									<?php $counter=0; ?>
@foreach(\App\Models\Days::all() as $day)
	@if(isset($object))
	<?php $this_day=\App\Models\WorkingDays::where('day_id',$day->id)->where('restaurant_id',$object->id)->first() ?>
											@endif
									<div class="form-group{{ $errors->has('work_day') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"><input type="checkbox" class="js-switch"  name="is_worked[{{$day->id}}]" {{in_array($day->id, $work_times)? "checked":'' }}  /> {{$day->name}} </label>
										<input type="hidden" name="day_id[]" value="{{$day->id}}"  />
										<div class="col-md-5">
											<div class="bootstrap-timepicker">

											<div class="input-group" >
												<input type="text" value="{{$object&&$this_day?date('h:i A', strtotime($this_day->time_from)):''}}" name="time_from[]" placeholder="بداية الدوام" class="form-control timepicker">
												<div class="input-group-addon">
													<i class="fa fa-clock-o"></i>
												</div>
											</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="bootstrap-timepicker">

											<div class="input-group">
												<input type="text" value="{{$object&&$this_day?date('h:i A', strtotime($this_day->time_to)):''}}" class="form-control timepicker" name="time_to[]" placeholder="نهاية الدوام">
												<div class="input-group-addon">
													<i class="fa fa-clock-o"></i>
												</div>
											</div>
											</div>
										</div>


									</div>
	<?php $counter++;?>
@endforeach
								</fieldset>
									</div>
								</div>

							</div>

							<div class="text-right">
								<button type="submit" class="btn btn-primary">حفظ <i class="icon-arrow-left13 position-right"></i></button>
							</div>

						</div>

					</div>



				</div>

				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog" role="document">
						{!! csrf_field() !!}

						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
											aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel">رسالة الحذف </h4>
							</div>
							<div class="modal-body">
								<div class="">
									هل أنت متأكد من الحذف ؟
								</div>


							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
								<a class="res" href="#">
									<button type="button" class="btn btn-primary">نعم</button>
								</a>
							</div>
						</div>

					</div>
				</div>


				<!-- Footer -->
			@include('admin.footer')
			<!-- /footer -->
				<!-- /content area -->
			</form>
		</div>


		<script src="{{ asset('js/mapPlace.js') }}"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkQ8_neT4uZpVaXG1SbZNWKH1fnQHnbGk&libraries=places&callback=initMap&language={{ App::getLocale() }}"
				async defer></script>

		<script>
            $(function() {
				$(".timepicker").timepicker({
					showInputs: false,
					use24hours: true,
					format: 'HH:mm'

				});

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				});
			});
			$( window ).load(function() {
				@if(!isset($object))
				getCurrentLocation();
					@else
				{
					@if($object->latitude && $object->longitude)
					getLocationName({{$object->latitude}},{{$object->longitude}});
					@else
					getCurrentLocation();
					@endif
				}

				@endif

			});

			var elem = document.querySelector('.js-switch');
			var init = new Switchery(elem);
			document.getElementById('free_delivery').onchange = function() {
				document.getElementById('delivery_limit').disabled = !this.checked;
			};

		</script>


@stop
