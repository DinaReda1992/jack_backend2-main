@extends('providers.layout')
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

	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<!-- InputMask -->

	{{--	<script type="text/javascript" src="/assets/js/core/app.js"></script>--}}

	{{--	<script type="text/javascript" src="/assets/js/pages/components_modals.js"></script>--}}

	<!-- /theme JS files -->
	<script type="text/javascript">

		$(function () {
				$('.category_id').change(function () {
					$('.subcategory_id').html('<option value="">اختر القسم الفرعى</option>').trigger("change");

					var category_id = $('.category_id').val();
					$.ajax({
						url: '/provider-panel/getSubCategories/' + category_id,
						success: function (data) {
							$('.subcategory_id').html(data);
						}
					});

				});
			$('.subcategory_id').change(function () {
				var subcategory_id = $('.subcategory_id').val();
				if(subcategory_id =='')return false
				$('#measurement_id').empty().trigger("change");


				$.ajax({
					url: '/provider-panel/getCategoryMeasurement/' + subcategory_id,
					success: function (data) {
						$('#measurement_id').html(data);
					}
				});

			});

		});
	</script>
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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }}  منتج  </h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/provider-panel/products"><button type="button" class="btn btn-success" name="button">  عرض المنتجات</button></a>
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
					<li class="active">أضف المنتج </li>
				</ul>
			</div>
			<div style=" text-align: center;" >
				<ul class="nav nav-tabs" role="tablist">
					<li  class="active" ><a data-toggle="tab" href="#main_data">البيانات الاساسية</a></li>
					<li  ><a data-toggle="tab" href="#photos">الصور</a></li>
					{{--					<li  ><a data-toggle="tab" href="#meal_sizes">الاحجام</a></li>--}}

				</ul>
			</div>

		</div>
		<!-- /page header -->

		<!-- Content area -->
		<div class="content">
			<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/products/'.$object->id : '/provider-panel/products'  }}" enctype="multipart/form-data">
				{!! csrf_field() !!}
				@if(isset($object))
					<input type="hidden" name="_method" value="PATCH" />

				@endif
				@include('providers.message')


				<div class="panel panel-flat">
					<div class="panel-heading">
						<h5 class="panel-title">تفاصيل المنتج</h5>
						<div class="heading-elements">
							<ul class="icons-list">
								<li><a data-action="collapse"></a></li>
								<li><a data-action="reload"></a></li>
								<li><a data-action="close"></a></li>
							</ul>
						</div>
					</div>
						<div class="panel-body">
							<div class="tab-content">
								<div id="main_data" class="tab-pane fade in active ">

						<fieldset class="content-group">


							<div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2"> اختر القسم الرئيسى *</label>
								<div class="col-lg-10">
									<select name="category_id" class="category_id form-control select-multiple-tokenization">
										<option value="">اختر النوع</option>
										@foreach(\App\Models\Categories::where('stop',0)->where('is_archived',0)->orderBy('sort','asc')->get() as $category)
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
							<div class="form-group{{ $errors->has('subcategory_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اختر القسم الفرعى *</label>
								<div class="col-lg-10">
									<select name="subcategory_id" class="subcategory_id form-control select-multiple-tokenization">
										<option value="">اختر القسم الفرعى</option>
										@if(isset($my_subcategories))
											@foreach($my_subcategories as $subcategory)
												<option value="{{ $subcategory->id }}"  {{ isset($object) && $object->subcategory_id==$subcategory->id ? 'selected' : (old('subcategory_id') == $subcategory->id ? 'selected' : (isset($my_subcategory)&&$my_subcategory->id == $subcategory->id ? 'selected':'')) }}>{{ $subcategory->name }}</option>
											@endforeach
										@endif
									</select>
									@if ($errors->has('subcategory_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('subcategory_id') }}</strong>
		                                    </span>
									@endif
								</div>

							</div>

							<div class="form-group{{ $errors->has('measurement_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اختر وحدة القياس *</label>
								<div class="col-lg-10">
									<select name="measurement_id" class="measurement_id form-control select-multiple-tokenization" id="measurement_id">
										<option value="">اختر وحدة القياس</option>
										@if(isset($my_measurements))
											@foreach($my_measurements as $measurement)
												<option value="{{ $measurement->id }}"  {{ isset($object) && $object->measurement_id==$measurement->id ? 'selected' : (old('measurement_id') == $measurement->id ? 'selected' : '') }}>{{ $measurement->name }}</option>
											@endforeach
										@endif
									</select>
									@if ($errors->has('measurement_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('measurement_id') }}</strong>
		                                    </span>
									@endif
								</div>

							</div>

							<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم المنتج بالعربية *</label>
								<div class="col-md-10">
									<input required placeholder="اسم المنتج"  value="{{ isset($object) ? $object->title  : old('title')  }}" maxlength="30" class="form-control" type="text" name="title">
									@if ($errors->has('title'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('title') }}</strong>
		                                    </span>
									@endif
									<span class="help-block">
		                                        <strong>حد اقصى 30 حرف</strong>
		                                    </span>

								</div>
							</div>
							<div class="form-group{{ $errors->has('title_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم المنتج بالانجليزية *</label>
								<div class="col-md-10">
									<input required placeholder=" اسم المنتج باللغة الإنجليزية" maxlength="30"  value="{{ isset($object) ? $object->title_en  : old('title_en')  }}" class="form-control" type="text" name="title_en">
									@if ($errors->has('title_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('title_en') }}</strong>
		                                    </span>
									@endif
									<span class="help-block">
		                                        <strong>حد اقصى 30 حرف</strong>
		                                    </span>

								</div>
							</div>


							<div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">سعر المنتج *</label>
								<div class="col-md-10">
									<input required placeholder="سعر المنتج بالريال"  value="{{ isset($object) ? $object->price  : old('price')  }}" class="form-control" type="number" step="0.01" name="price">
									@if ($errors->has('price'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('price') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('price_after_discount') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">السعر بعد الخصم ان وجد </label>
								<div class="col-md-10">
									<input  placeholder="السعر بعد الخصم ان كان يوجد خصم على المنتج "  value="{{ isset($object) ? $object->price_after_discount  : old('price_after_discount')  }}" class="form-control" type="number" step="0.01" name="price_after_discount">

									<span class="help-block">
		                                        هذا السعر اختيارى فى حالة ان المنتج يحتوى عل خصم فيجب تحديد السعر الاول اعلى من السعر بعد الخصم
		                                    </span>
									@if ($errors->has('price_after_discount'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('price_after_discount') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">الكمية فى المخزن *</label>
								<div class="col-md-10">
									<input required placeholder="الكمية "  value="{{ isset($object) ? $object->quantity  : old('quantity')  }}" class="form-control" type="number" name="quantity">
									@if ($errors->has('quantity'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('quantity') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('min_quantity') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اقل كمية للطلب</label>
								<div class="col-md-10">
									<input required placeholder="اقل كمية يمكن طلبها "  value="{{ isset($object) ? $object->min_quantity  : old('min_quantity')  }}" class="form-control" type="number" name="min_quantity">
									@if ($errors->has('min_quantity'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('min_quantity') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">وصف المنتج بالعربى</label>
								<div class="col-md-10">
									<textarea class="form-control" name="description">{{ isset($object) ? $object->description  : old('description')  }}</textarea>
									@if ($errors->has('description'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">وصف المنتج بالانجليزى </label>
								<div class="col-md-10">
									<textarea class="form-control" name="description_en">{{ isset($object) ? $object->description_en  : old('description_en')  }}</textarea>
									@if ($errors->has('description_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('description_en') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('usage_ar') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">الغرض والاستخدامات بالعربى</label>
								<div class="col-md-10">
									<textarea class="form-control" name="usage_ar">{{ isset($object) ? $object->usage_ar  : old('usage_ar')  }}</textarea>
									@if ($errors->has('usage_ar'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('usage_ar') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('usage_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">الغرض والاستخدامات بالانجليزية </label>
								<div class="col-md-10">
									<textarea class="form-control" name="usage_en">{{ isset($object) ? $object->usage_en  : old('usage_en')  }}</textarea>
									@if ($errors->has('usage_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('usage_en') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

						</fieldset>
								</div>


							<div id="photos" class="tab-pane fade ">
								<fieldset class="content-group">

									<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل صورة المنتج *</label>
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
											<label class="control-label col-lg-2">الصورة الحالية</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">
											</div>

										</div>
									@endif

									<div class="form-group{{ $errors->has('photos') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">صور المنتج (حد أقصى 6 صور ) </label>
										<div class="col-lg-10">
											<input multiple type="file" id="product_photos" name="photos[]" value="{{old('photos[]')}}" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('photos'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('photos') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									@if(isset($object) && \App\Models\ProductPhotos::where('product_id',$object->id)->count() > 0 )

										<div class="form-group">
											<label class="control-label col-lg-2">الصور الحالية</label>

											<div class="col-lg-10">
												@foreach( \App\Models\ProductPhotos::where('product_id',$object->id)->get() as $photo)
													<div align="center" style="height: 75px;width: 100px;float: right;margin-right: 5px">
														<img alt="" width="100" height="75" src="/uploads/{{ $photo  -> photo }}">
														<a href="/provider-panel/delete-photo-product/{{ $photo->id }}" style="text-align: center">حذف</a>
													</div>
												@endforeach
											</div>

										</div>

									@endif

								</fieldset>
							</div>
							</div>
									<div class="text-right">
										<button type="submit" class="btn btn-primary">حفظ <i class="icon-arrow-left13 position-right"></i></button>
									</div>

							</div>
				</div>



				<!-- Footer -->
			@include('providers.footer')
			<!-- /footer -->
				<!-- /content area -->
			</form>
		</div>

<script>
	$(function(){
		$("input[type='submit']").click(function(){
			var $fileUpload = $("#product_photos");
			if (parseInt($fileUpload.get(0).files.length)>2){
				alert("You can only upload a maximum of 2 files");
			}
		});
	});

</script>

@stop
