@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" >
        $(document).ready(function(){



            $('.country_id').change(function () {
                var country_id = $('.country_id').val();
                $.ajax({
                    url: '/getStates/' + country_id,
                    success: function (data) {
                        $('.state_id').html(data);
                    }
                });

            });

            $('.category_id').change(function () {
                var category_id = $('.category_id').val();
                $.ajax({
                    url: '/getSubcategories/' + category_id,
                    success: function (data) {
                        $('.sub_category_id').html(data);
                    }
                });

            });


        });
	</script>

@stop
@section('content')
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} مشروع
					@if(isset($object))
						<a  href="/admin-panel/add-product/{{ $object->id }}" class="btn btn-primary pull-right">أضف منتج</a>
						<a  href="/admin-panel/all-products/{{ $object->id }}" class="btn btn-primary pull-right" style="margin-left: 5px">عرض المنتجات</a>
					@endif
					</h4>
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
					<li><a href="/admin-panel/projects">عرض المشاريع</a></li>
					<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} مشروع</li>
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
					<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} مشروع </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<div class="panel-body">

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/projects/'.$object->id : '/admin-panel/projects'  }}">
						{!! csrf_field() !!}
						@if(isset($object))
							<input type="hidden" name="_method" value="PATCH" />
						@endif
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->

							<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل عنوان المشروع</label>
								<div class="col-lg-10">
									<input type="text" name="title" value="{{ isset($object) ? $object->title  : old('title')  }}" class="form-control" placeholder="أدخل عنوان المشروع">
									@if ($errors->has('title'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('title') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							@php $category_id = isset($object) && $object->category_id  ? $object->category_id  : old('category_id')  @endphp
							<div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">إختر القسم</label>
								<div class="col-lg-10">
									<select name="category_id" class="form-control category_id">
										<option value="">اختر القسم</option>
										@foreach(\App\Models\Categories::all() as $category)
											<option value="{{ $category->id }}"  {{ isset($object) && $object->category_id==$category->id ? 'selected' : ($category_id == $category->id ? 'selected' : '') }}>{{ $category->name }}</option>
										@endforeach
									</select>
									@if ($errors->has('category_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('category_id') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('sub_category_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">إختر القسم الفرعي</label>
								<div class="col-lg-10">
									<select name="sub_category_id" class="form-control sub_category_id">
										<option value="">اختر القسم الفرعي</option>
										@foreach(\App\Models\Subcategories::where('category_id',$category_id)->get() as $sub_category)
											<option value="{{ $sub_category->id }}"  {{ isset($object) && $object->sub_category_id==$sub_category->id ? 'selected' : (old('sub_category_id') == $sub_category->id ? 'selected' : '') }}>{{ $sub_category->name }}</option>
										@endforeach
									</select>
									@if ($errors->has('sub_category_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('sub_category_id') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							@php $country_id = isset($object) && $object->country_id  ? $object->country_id  : old('country_id')  @endphp

							<div class="form-group{{ $errors->has('country_id') || $errors->has('state_id') || $errors->has('city_id')  ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">إختر الدولة والمدينة</label>
								<div class="col-lg-5">
									<select name="country_id" class="form-control country_id">
										<option value="">اختر الدولة</option>
										@foreach(\App\Models\Countries::all() as $country)
											<option value="{{ $country->id }}"  {{ $country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
										@endforeach
									</select>
									@if ($errors->has('country_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('country_id') }}</strong>
		                                    </span>
									@endif
								</div>
								@php $state_id = isset($object) && $object->state_id  ? $object->state_id  : old('state_id')  @endphp
								<div class="col-lg-5">
									<select name="state_id" class="form-control state_id">
										<option value="">اختر المنطقة</option>
										@foreach(\App\Models\States::where('country_id',$country_id)->get() as $state)
											<option value="{{ $state->id }}"  {{ $state_id == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
										@endforeach
									</select>
									@if ($errors->has('state_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('state_id') }}</strong>
		                                    </span>
									@endif
								</div>


							</div>


							<div class="form-group{{ $errors->has('project_status') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">حالة المشروع</label>
								<div class="col-lg-10">
									<select name="project_status" class="form-control sub_category_id">
										<option value="0" {{ isset($object) && $object->project_status==0 ? 'selected' : (old('project_status') == 0 ? 'selected' : '') }}>متاح</option>
										<option value="1" {{ isset($object) && $object->project_status==1 ? 'selected' : (old('project_status') == 1 ? 'selected' : '') }}>مغلق</option>
										<option value="2" {{ isset($object) && $object->project_status==2 ? 'selected' : (old('project_status') == 2 ? 'selected' : '') }}>مشغول</option>
									</select>
									@if ($errors->has('project_status'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('project_status') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>




							<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">التفاصيل </label>
								<div class="col-lg-10">
									<textarea placeholder="تفاصيل المشروع" name="description" class="form-control">{{ isset($object) ? $object->description  : old('description')  }}</textarea>
									@if ($errors->has('description'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('photos') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">صور المشروع</label>
								<div class="col-lg-10">
									<input multiple type="file" name="photos[]" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('photos'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('photos') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							@if(isset($object) && \App\Models\ProjectPhotos::where('project_id',$object->id)->count() > 0 )

								<div class="form-group">
									<label class="control-label col-lg-2">الصور الحالية</label>

									<div class="col-lg-10">
										@foreach( \App\Models\ProjectPhotos::where('project_id',$object->id)->get() as $photo)
											<div align="center" style="height: 75px;width: 100px;float: right;margin-right: 5px">
												<img alt="" width="100" height="75" src="/uploads/{{ $photo  -> photo }}">
												<a href="/admin-panel/delete-photo-project/{{ $photo->id }}" style="text-align: center">حذف</a>
											</div>
										@endforeach
									</div>

								</div>

							@endif

						</fieldset>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} المشروع<i class="icon-arrow-left13 position-right"></i></button>
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
