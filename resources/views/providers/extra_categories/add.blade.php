@extends('providers.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<!-- /theme JS files -->

	<script type="text/javascript">
		$(document).on("click",".cancel-add",function(e){
			$(this).parent().remove();
			var attr=$(this).attr("itemid");
			// alert(attr);

		});

		$(document).ready(function () {
			// $('#categories').selectize({
			// 	maxItems: 30
			// });

			$('.add-advantage').click(function () {
				var type = $(this).attr('type');
				$.get('/provider-panel/extraItems',function (data) {
					$('.append-at'+type).append(data);
				})
				$('.action-create').click();
			});
			$(window).load(function () {
				$('.action-create').click();
			});
			$(document).on('change','.icon_class',function () {
				var icon = $(this).val();
				if(icon=="fa-warning"){
					$(this).parent('div').next().next().next('.desc').show();
				}else{
					$(this).parent('div').next().next().next('.desc').hide();
				}
			});

			$('.category_id').change(function () {
				var category_id= $(this).val();
				$('.sub_category_id').html("<option>جاري تحميل الاقسام الفرعية ..</option>");
				$.get('/admin-panel/get-sub-categories/'+category_id,function (data) {
					$('.sub_category_id').html(data);
				})
			});
		});

	</script>
<style>
	.remove-extra {
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
	.extra-title{
		text-align: center;
		/*text-decoration: underline;*/
		color: #156107;
		margin-bottom: 36px;
		font-size: 24px;
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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} اضافة</h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/provider-panel/extra-categories"><button type="button" class="btn btn-success" name="button">  عرض الاضافات</button></a>
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
					<li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i></a></li>
					<li><a href="/provider-panel/categories">عرض الاضافات</a></li>
					<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} اضافة</li>
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
					<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} اضافة </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<div class="panel-body">

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/extra-categories/'.$object->id : '/provider-panel/extra-categories'  }}">
						{!! csrf_field() !!}
						@if(isset($object))
							<input type="hidden" name="_method" value="PATCH" />
						@endif
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->

{{--							<div class="form-group{{ $errors->has('parent_id') ? ' has-error' : '' }}">--}}
{{--								<label class="control-label col-lg-2">إختر القسم الرئيسي</label>--}}
{{--								<div class="col-lg-10">--}}
{{--									<select name="parent_id" class="form-control">--}}
{{--										<option value="">اختر القسم الرئيسي</option>--}}
{{--										@foreach(\App\Models\Categories::where('parent_id',0)->get() as $category)--}}
{{--											<option value="{{ $category->id }}"  {{ isset($object) && $object->parent_id==$category->id ? 'selected' : (old('parent_id') == $category->id ? 'selected' : '') }}>{{ $category->name }}</option>--}}
{{--										@endforeach--}}
{{--									</select>--}}
{{--									<span class="help-block">--}}
{{--		                                        إذا كنت تريد انشاء قسم فرعي فاختر القسم الرئيسي من هذه القائمة واذا كنت تريد انشاء قسم رئيسي فلا تختر من القائمة--}}
{{--		                                    </span>--}}
{{--									@if ($errors->has('parent_id'))--}}
{{--										<span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('parent_id') }}</strong>--}}
{{--		                                    </span>--}}
{{--									@endif--}}
{{--								</div>--}}

{{--							</div>--}}
							<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل اسم القسم </label>
								<div class="col-lg-10">
									<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل اسم القسم ">
									@if ($errors->has('name'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل اسم القسم بالانجليزية</label>
								<div class="col-lg-10">
									<input  type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="أدخل اسم القسم بالانجليزية">
									@if ($errors->has('name_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name_en') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
{{--							@if(@!$object->parent)--}}
{{--														<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">--}}
{{--															<label class="control-label col-lg-2">أدخل صورة القسم</label>--}}
{{--															<div class="col-lg-10">--}}

{{--																<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">--}}
{{--																@if ($errors->has('photo'))--}}
{{--																	<span class="help-block">--}}
{{--									                                        <strong>{{ $errors->first('photo') }}</strong>--}}
{{--									                                    </span>--}}
{{--																@endif--}}
{{--															</div>--}}
{{--														</div>--}}
{{--														@if(isset($object->photo))--}}
{{--															<div class="form-group">--}}
{{--																<label class="control-label col-lg-2">الصورة الحالية</label>--}}
{{--																<div class="col-lg-10">--}}
{{--																	<img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">--}}
{{--																</div>--}}

{{--															</div>--}}
{{--														@endif--}}
{{--						<div class="clearfix"></div>--}}
{{--							@endif--}}
<div>
	<h1 class="extra-title">الاضافات</h1>
</div>
							<div class="form-group ">
								<div class="append-at1">
									@if(!isset($object) || $object->extra_items->count()==0)
										<div>
											{!! View::make("providers.items.extra") -> render() !!}
										</div>
									@else
										<div>
											@php $i=0 @endphp
											@foreach($object->extra_items as $adv)

												{!! View::make("providers.items.extra") ->with('adv',$adv)-> render() !!}

												<div class="clearfix"></div>
												<br>
												@php $i++ @endphp
											@endforeach
										</div>
									@endif
								</div>
								<div class="clearfix"></div>
								<br>
								<a onclick="return false" type="1" class="btn btn-primary add-advantage pull-right">اضف اضافة جديدة +</a>

							</div>
						</fieldset>



						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} القسم  <i class="icon-arrow-left13 position-right"></i></button>
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