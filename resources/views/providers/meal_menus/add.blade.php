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
	<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<link rel="stylesheet" href="/js/selectize/css/selectize.css">
	<script src="/js/selectize/js/standalone/selectize.js"></script>

	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

	<style type="text/css">
		.iconpicker .iconpicker-item{
			float: right;
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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }}  قائمة  </h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/provider-panel/meal-menus"><button type="button" class="btn btn-success" name="button">  عرض قوائم الوجبات</button></a>
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
					<li class="active">أضف قائمة وجبات </li>
				</ul>
			</div>

		</div>
		<!-- /page header -->

		<!-- Content area -->
		<div class="content">
			<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/meal-menus/'.$object->id : '/provider-panel/meal-menus'  }}" enctype="multipart/form-data">
				{!! csrf_field() !!}
				@if(isset($object))
					<input type="hidden" name="_method" value="PATCH" />

				@endif
				@include('providers.message')


				<div class="panel panel-flat">
					<div class="panel-heading">
						<h5 class="panel-title">محتوى القائمة</h5>
						<div class="heading-elements">
							<ul class="icons-list">
								<li><a data-action="collapse"></a></li>
								<li><a data-action="reload"></a></li>
								<li><a data-action="close"></a></li>
							</ul>
						</div>
					</div>
						<div class="panel-body">

						<fieldset class="content-group">






							<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم القائمة *</label>
								<div class="col-md-10">
									<input required placeholder="اسم قائمة الوجبات"  value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" type="text" name="name">
									@if ($errors->has('name'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>




							<div class="form-group{{ $errors->has('products') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">إختر الوجبات</label>
								<div class="col-lg-10">
									<select class="select-multiple-tokenization" multiple="multiple" name="products[]" id="categories">

									<?php $currentTypes=isset($meal_products)?$meal_products:(old('products')?:[]); ?>
										@foreach($products as $product)
											<option value="{{ $product->id }}"  <?php if(in_array($product->id, $currentTypes)){ echo "selected"; } ?> >{{ $product->title }}</option>
										@endforeach
									</select>
									@if ($errors->has('products'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('products') }}</strong>
		                                    </span>
									@endif
								</div>

							</div>

						</fieldset>
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


@stop
