@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>

    <script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('.add-advantage').click(function () {
			$('.append-at').append('<br><div class="col-lg-2">-</div><div class="col-lg-2"><input type="file" name="photoss[]" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs"></div><div class="col-lg-4"><input required type="text" name="adv[]" value="" class="form-control" placeholder="الميزة باللغة العربية "></div><div class="col-lg-4"><input required style="direction: ltr" type="text" name="adv_en[]" value="" class="form-control" placeholder="الميزة باللغة الانجليزية"></div><div class="clearfix"></div>');
        	$('.action-create').click();
		});
		$(window).load(function () {
            $('.action-create').click();

        })
    });
</script>

	<link href="https://farbelous.github.io/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css" rel="stylesheet">
	<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }}  خدمة </h4>
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
							<li><a href="/provider-panel/services">عرض الخدمات</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }}  خدمة </li>
						</ul>
					</div>
				</div>
				<!-- /page header -->

				<!-- Content area -->
				<div class="content">

@include('admin.message')

					<!-- Form horizontal -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }}  خدمة  </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/services/'.$object->id : '/provider-panel/services'  }}" enctype="multipart/form-data">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">

									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">اسم الخدمة بالعربية </label>
										<div class="col-lg-10">
										<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="اسم الخدمة بالعربية ">
										@if ($errors->has('name'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>
									<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">اسم الخدمة بالانجليزية </label>
										<div class="col-lg-10">
											<input style="direction: ltr" type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="اسم الخدمة بالانجليزية ">
											@if ($errors->has('name_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('name_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('brief') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نبذة الخدمة بالعربية </label>
										<div class="col-lg-10">
											<input type="text" name="brief" value="{{ isset($object) ? $object->brief  : old('brief')  }}" class="form-control" placeholder="نبذة الخدمة بالعربية ">
											@if ($errors->has('brief'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('brief') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('brief_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نبذة الخدمة بالانجليزية </label>
										<div class="col-lg-10">
											<input style="direction: ltr" type="text" name="brief_en" value="{{ isset($object) ? $object->brief_en  : old('brief_en')  }}" class="form-control" placeholder="نبذة الخدمة بالانجليزية ">
											@if ($errors->has('brief_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('brief_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									@if(isset($object) && $object->id !=8 && $object->id !=3 && $object->id !=7 && $object->id !=4 && $object->id !=5  )
									<div class="form-group{{ $errors->has('price') ? ' has-error' : '' }} {{ $errors->has('currency_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">سعر التوصيل لكل كيلو</label>
										<div class="col-lg-7">
											<input type="text" name="price" value="{{ isset($object) ? $object->price  : old('price')  }}" class="form-control" placeholder="سعر التوصيل لكل كيلو">
											@if ($errors->has('price'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('price') }}</strong>
		                                    </span>
											@endif
										</div>
										<div class="col-lg-3">
											<select name="currency_id" class="form-control">
												<option value="">عرض العملات</option>
												@foreach(\App\Models\Currencies::all() as $code)
													<option value="{{ $code->id }}" {{ isset($object) && $object->currency_id==$code->id ? 'selected' : (old('currency_id') == $code->id ? 'selected' : '') }}>{{ $code->name }} ( {{ $code->code }} )</option>
												@endforeach
											</select>
											@if ($errors->has('currency_id'))
												<span class="help-block">
											<strong>{{ $errors->first('phonecode') }}</strong>

											@endif
										</div>
									</div>
									@elseif($object->id ==3 || $object->id ==7 )
										<div class="form-group{{ $errors->has('price') ? ' has-error' : '' }} {{ $errors->has('currency_id') ? ' has-error' : '' }}">
											<label class="control-label col-lg-2">السعر للشحنة الواحدة</label>
											<div class="col-lg-7">
												<input type="text" name="price" value="{{ isset($object) ? $object->price  : old('price')  }}" class="form-control" placeholder="السعر للشحنة الواحدة">
												@if ($errors->has('price'))
													<span class="help-block">
		                                        <strong>{{ $errors->first('price') }}</strong>
		                                    </span>
												@endif
											</div>
											<div class="col-lg-3">
												<select name="currency_id" class="form-control">
													<option value="">عرض العملات</option>
													@foreach(\App\Models\Currencies::all() as $code)
														<option value="{{ $code->id }}" {{ isset($object) && $object->currency_id==$code->id ? 'selected' : (old('currency_id') == $code->id ? 'selected' : '') }}>{{ $code->name }} ( {{ $code->code }} )</option>
													@endforeach
												</select>
												@if ($errors->has('currency_id'))
													<span class="help-block">
											<strong>{{ $errors->first('phonecode') }}</strong>

												@endif
											</div>
										</div>
										<div class="form-group{{ $errors->has('min_shipments') ? ' has-error' : '' }}">
											<label class="control-label col-lg-2">أقل عدد للشحنات </label>
											<div class="col-lg-10">
												<input type="number" name="min_shipments" value="{{ isset($object) ? $object->min_shipments  : old('min_shipments')  }}" class="form-control" placeholder="أقل عدد للشحنات ">
												@if ($errors->has('min_shipments'))
													<span class="help-block">
		                                        <strong>{{ $errors->first('min_shipments') }}</strong>
		                                    </span>
												@endif
											</div>
										</div>
									@elseif($object->id==4 || $object->id==5)
                                        <div class="form-group{{ $errors->has('min_shipments') ? ' has-error' : '' }}">
                                            <label class="control-label col-lg-2">عدد الارساليات المسموح </label>
                                            <div class="col-lg-10">
                                                <input type="number" name="min_shipments" value="{{ isset($object) ? $object->min_shipments  : old('min_shipments')  }}" class="form-control" placeholder="عدد الارساليات المسموح ">
                                                @if ($errors->has('min_shipments'))
                                                    <span class="help-block">
		                                        <strong>{{ $errors->first('min_shipments') }}</strong>
		                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('min_plane_price') ? ' has-error' : '' }} {{ $errors->has('max_plane_price') ? ' has-error' : '' }} {{ $errors->has('currency_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-lg-2">السعر للارسالية</label>
                                            <div class="col-lg-3">
												<label>أقل سعر للارسالية</label>
                                                <input type="text" name="min_plane_price" value="{{ isset($object) ? $object->min_plane_price  : old('min_plane_price')  }}" class="form-control" placeholder="أقل سعر للارسالية">
                                                @if ($errors->has('min_plane_price'))
                                                    <span class="help-block">
		                                        <strong>{{ $errors->first('min_plane_price') }}</strong>
		                                    </span>
                                                @endif
                                            </div>

                                            <div class="col-lg-3">
												<label>أعلى سعر للارسالية</label>

												<input type="text" name="max_plane_price" value="{{ isset($object) ? $object->max_plane_price  : old('max_plane_price')  }}" class="form-control" placeholder="أعلى سعر للارسالية">
                                                @if ($errors->has('max_plane_price'))
                                                    <span class="help-block">
		                                        <strong>{{ $errors->first('max_plane_price') }}</strong>
		                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-lg-3">
												<label>العملة</label>

												<select name="currency_id" class="form-control">
                                                    <option value="">عرض العملات</option>
                                                    @foreach(\App\Models\Currencies::all() as $code)
                                                        <option value="{{ $code->id }}" {{ isset($object) && $object->currency_id==$code->id ? 'selected' : (old('currency_id') == $code->id ? 'selected' : '') }}>{{ $code->name }} ( {{ $code->code }} )</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('currency_id'))
                                                    <span class="help-block">
											<strong>{{ $errors->first('phonecode') }}</strong>

                                                @endif
                                            </div>
                                        </div>
									@endif
                                    <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">أدخل أيقونة الخدمة</label>
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


								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} خدمة  <i class="icon-arrow-left13 position-right"></i></button>
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


			<script src="/assets/js/fontawesome-iconpicker.js"></script>
			<script>
                $(function() {
                    $('.action-destroy').on('click', function() {
                        $.iconpicker.batch('.icp.iconpicker-element', 'destroy');
                    });
                    // Live binding of buttons
                    $(document).on('click', '.action-placement', function(e) {
                        $('.action-placement').removeClass('active');
                        $(this).addClass('active');
                        $('.icp-opts').data('iconpicker').updatePlacement($(this).text());
                        e.preventDefault();
                        return false;
                    });

                    $(document).on('click','.action-create',function () {


                        $('.icp-auto').iconpicker();

                        $('.icp-dd').iconpicker({
                            //title: 'Dropdown with picker',
                            //component:'.btn > i'
                        });

                        $('.icp-glyphs').iconpicker({
                            title: 'Prepending glypghicons',
                            icons: $.merge(['glyphicon-home', 'glyphicon-repeat', 'glyphicon-search',
                                'glyphicon-arrow-left', 'glyphicon-arrow-right', 'glyphicon-star'], $.iconpicker.defaultOptions.icons),
                            fullClassFormatter: function(val){
                                if(val.match(/^fa-/)){
                                    return 'fa '+val;
                                }else{
                                    return 'glyphicon '+val;
                                }
                            }
                        });
                        $('.icp-opts').iconpicker({
                            title: 'With custom options',
                            icons: ['fa-github', 'fa-heart', 'fa-html5', 'fa-css3'],
                            selectedCustomClass: 'label label-success',
                            mustAccept: true,
                            placement: 'bottomRight',
                            showFooter: true,
                            // note that this is ignored cause we have an accept button:
                            hideOnSelect: true,
                            templates: {
                                footer: '<div class="popover-footer">' +
                                '<div style="text-align:left; font-size:12px;">Placements: \n\
                <a href="#" class=" action-placement">inline</a>\n\
                <a href="#" class=" action-placement">topLeftCorner</a>\n\
                <a href="#" class=" action-placement">topLeft</a>\n\
                <a href="#" class=" action-placement">top</a>\n\
                <a href="#" class=" action-placement">topRight</a>\n\
                <a href="#" class=" action-placement">topRightCorner</a>\n\
                <a href="#" class=" action-placement">rightTop</a>\n\
                <a href="#" class=" action-placement">right</a>\n\
                <a href="#" class=" action-placement">rightBottom</a>\n\
                <a href="#" class=" action-placement">bottomRightCorner</a>\n\
                <a href="#" class=" active action-placement">bottomRight</a>\n\
                <a href="#" class=" action-placement">bottom</a>\n\
                <a href="#" class=" action-placement">bottomLeft</a>\n\
                <a href="#" class=" action-placement">bottomLeftCorner</a>\n\
                <a href="#" class=" action-placement">leftBottom</a>\n\
                <a href="#" class=" action-placement">left</a>\n\
                <a href="#" class=" action-placement">leftTop</a>\n\
                </div><hr></div>'}
                        }).data('iconpicker').show();
                    }).trigger('click');


                    // Events sample:
                    // This event is only triggered when the actual input value is changed
                    // by user interaction
                    $('.icp').on('iconpickerSelected', function(e) {
                        $('.lead .picker-target').get(0).className = 'picker-target fa-3x ' +
                            e.iconpickerInstance.options.iconBaseClass + ' ' +
                            e.iconpickerInstance.options.fullClassFormatter(e.iconpickerValue);
                    });
                });
			</script>


@stop
