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
		    var type = $(this).attr('type');
			$('.append-at'+type).append('<br><div class="col-lg-2">-</div><div class="col-lg-4"><input type="text" required name="property['+type+'][]" value="" class="form-control" placeholder="الخاصية باللغة العربية "></div><div class="col-lg-4"><input required style="direction: ltr" type="text" name="property_en['+type+'][]" value="" class="form-control" placeholder="الخاصية باللغة الانجليزية"></div><div class="col-lg-2"><select required name="icon_class['+type+'][]" class="form-control icon_class"><option value="">اختر حالة الخاصية</option><option value="fa-check"><i class="fa fa-bus"></i>جيد </option><option value="fa-warning"><i class="fa fa-bus"></i>لديه مشاكل </option></select></div><div class="clearfix"></div><br><div class="desc" style="display: none"><div class="col-lg-2"> -</div><div class="col-lg-4"><textarea placeholder="وصف الخطأ بالعربية" class="form-control" name="description['+type+'][]"></textarea></div><div class="col-lg-4"><textarea style="direction: ltr" placeholder="وصف الخطأ بالانجليزية" class="form-control" name="description_en['+type+'][]"></textarea></div><div class="col-lg-2"></div></div><div class="clearfix"></div>');
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

        $('.brand_id').change(function () {
            var brand_id= $(this).val();
            $('.model_id').html("<option>Loading ...</option>");
            $.get('{{ url('/') }}/ar/get-models/'+brand_id,function (data) {
                $('.model_id').html(data);
            })
        });

    });
</script>

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
							<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li class="active">تعديل نموذج خدمة {{ $service ->name  }}</li>
						</ul>
					</div>
				</div>
				<!-- /page header -->

				<!-- Content area -->
				<div class="content">
					<form method="post" class="form-horizontal" action="/admin-panel/add-report/{{ $order }}" enctype="multipart/form-data">
						{!! csrf_field() !!}

				@include('admin.message')


						<div class="panel panel-flat">
							<div class="panel-heading">
								<h5 class="panel-title">محتوى التقرير</h5>
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

									<div class="clearfix"></div>
									<div class="form-group{{ $errors->has('photoss') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">صور السيارة</label>
										<div class="col-lg-10">
											<input multiple type="file" name="photoss[]" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('photoss'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('photoss') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									@if(isset($object) && \App\Models\ReportPhotos::where('report_id',$object->id)->where('type',1)->count() > 0 )
										<div class="form-group">
											<label class="control-label col-lg-2">الصور الحالية</label>
											<div class="col-lg-10">
												@foreach( \App\Models\ReportPhotos::where('report_id',$object->id)->where('type',1)->get() as $photo)
													<div align="center" style="height: 75px;width: 100px;float: right;margin-right: 5px">
														<img alt="" width="100" height="75" src="/uploads/{{ $photo  -> photo }}">
														<a href="/admin-panel/delete-photo-report/{{ $photo->id }}" style="text-align: center">حذف</a>
													</div>
												@endforeach
											</div>
										</div>
									@endif
									<div class="form-group{{ $errors->has('date_of_report') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">تاريخ انشاء التقرير </label>
										<div class="col-md-10">
											<input required  value="{{ isset($object) ? $object->date_of_report  : old('date_of_report')  }}" class="form-control" type="date" name="date_of_report">
											@if ($errors->has('date_of_report'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('date_of_report') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('description_') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2"> محتوى التقرير </label>
										<div class="col-lg-5">
											<textarea required rows="10" class="form-control" placeholder="محتوى التقرير بالعربية" name="description_" >{{ isset($object) ? $object->description  : old('description_')  }}</textarea>
											@if ($errors->has('description_'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
											@endif
										</div>
										<div class="col-lg-5">
											<textarea required style="direction: ltr"  rows="10" class="form-control" placeholder="محتوى التقرير باالنجليزية" name="description_en_" >{{ isset($object) ? $object->description_en  : old('description_en_')  }}</textarea>
											@if ($errors->has('description_en_'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description_en_') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									@php
										$car_id = $object->brand_id ? $object->brand_id : old('brand_id');
									@endphp
                                        <div class="form-group{{ $errors->has('brand_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-lg-2">ماركة السياراة </label>
                                            <div class="col-md-10">
                                                <select required name="brand_id" class="form-control brand_id">
                                                        <option value="">اختر الماركة</option>
                                                    @foreach(\App\Models\Cars::all() as $brand)
                                                    <option value="{{ $brand->id }}" {{ $object->brand_id == $brand->id || $car_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('brand_id'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('brand_id') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>

                                        <div class="form-group{{ $errors->has('model_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-lg-2">موديل  السياراة </label>
                                            <div class="col-md-10">
                                                <select required name="model_id" class="form-control model_id">
                                                    <option value="">اختر الموديل</option>
                                                    @foreach(\App\Models\CarsModels::where('cars_category_id',$car_id)->get() as $model)
                                                        <option value="{{ $model->id }}" {{ (isset($object->model_id) && $object->model_id== $model->id ) || (old('model_id') && old('model_id')== $model->id ) ? 'selected' : '' }}>{{ $model->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('model_id'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('model_id') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>

									<div class="form-group{{ $errors->has('year_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">سنة الصنع </label>
										<div class="col-md-10">
											<select required name="year_id" class="form-control year_id">
												<option value="">اختر سنة الصنع</option>
												@foreach(\App\Models\Years::orderBy('name','DESC')->get() as $year)
													<option value="{{ $year->id }}" {{ $object->year_id == $year->id || old('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
												@endforeach
											</select>
											@if ($errors->has('year_id'))
												<span class="help-block">
                                                    <strong>{{ $errors->first('year_id') }}</strong>
                                                </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('color_car') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">لون السيارة </label>
										<div class="col-md-10">
											<input required placeholder="لون السيارة"  value="{{ isset($object) ? $object->color_car  : old('color_car')  }}" class="form-control" type="text" name="color_car">
											@if ($errors->has('color_car'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('color_car') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('kilometer') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">عدد الكيلومترات </label>
										<div class="col-md-10">
											<input required value="{{ isset($object) ? $object->kilometer  : old('kilometer')  }}" placeholder="عدد الكيلومترات" class="form-control" type="text" name="kilometer">
											@if ($errors->has('kilometer'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('kilometer') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('car_plate') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">رقم اللوحة </label>
										<div class="col-md-10">
											<input required value="{{ isset($object) ? $object->car_plate  : old('car_plate')  }}" placeholder="رقم اللوحة" class="form-control" type="text" name="car_plate">
											@if ($errors->has('car_plate'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('car_plate') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>


									<div class="form-group{{ $errors->has('vin') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">رقم VIN </label>
										<div class="col-md-10">
											<input  value="{{ isset($object) ? $object->vin  : old('vin')  }}" placeholder="رقم VIN" class="form-control" type="text" name="vin">
											@if ($errors->has('vin'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('vin') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>


									<div class="form-group{{ $errors->has('gas_type') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نوع الوقود </label>
										<div class="col-md-10">
											<input required  value="{{ isset($object) ? $object->gas_type  : old('gas_type')  }}" placeholder="نوع الوقود" class="form-control" type="text" name="gas_type">
											@if ($errors->has('gas_type'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('gas_type') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>


									<div class="form-group{{ $errors->has('engine_no') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">رقم المحرك </label>
										<div class="col-md-10">
											<input  value="{{ isset($object) ? $object->engine_no  : old('engine_no')  }}" placeholder="رقم المحرك" class="form-control" type="text" name="engine_no">
											@if ($errors->has('engine_no'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('engine_no') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-2">السيارة من معرض</label>
										<div class="col-md-10">
										<div class="custom-control custom-radio">
											<input type="radio" id="customRadio1" value="1" name="auto_show"
												   class="custom-control-input" {{ (isset($object)  && $object->auto_show == 1) || old('auto_show')==1 ? 'checked' : '' }}>
											<label class="custom-control-label" for="customRadio1">نعم </label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" id="customRadio2" value="0" name="auto_show"
												   class="custom-control-input" {{ isset($object)  && $object->auto_show == 0 ? 'checked' : '' }}>
											<label class="custom-control-label" for="customRadio2">لا</label>
										</div>
										</div>
									</div>


									<div class="form-group{{ $errors->has('video_url') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">رابط فيديو ( ان وجد ) </label>
										<div class="col-md-10">
											<input  value="{{ isset($object) ? $object->video_url  : old('video_url')  }}" placeholder="رابط فيديو" class="form-control" type="text" name="video_url">
											@if ($errors->has('video_url'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('video_url') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>



								</fieldset>


                                </div>



                            </div>



                        @foreach(\App\Models\ReportTypes::all() as $type)
                        <!-- Form horizontal -->
                        <div class="panel panel-flat">
                            <div class="panel-heading">
                                <h5 class="panel-title">{{ $type->name }}</h5>
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



                                        <div class="form-group">
                                            <label class="control-label col-lg-2">الخاصية </label>
                                            @if(!isset($object) || \App\Models\ReportPoints::where('report_id',$object->id)->where('type',$type->id)->count()==0)
                                            <div class="append-at{{ $type->id }}">
                                            <div class="col-lg-4">
                                                <input required type="text" name="property[{{ $type->id }}][]" value="" class="form-control" placeholder="الخاصية باللغة العربية ">

                                            </div>

                                            <div class="col-lg-4">
                                                <input required style="direction: ltr" type="text" name="property_en[{{ $type->id }}][]" value="" class="form-control" placeholder="الخاصية باللغة الانجليزية">
                                            </div>

												<div class="col-lg-2">
													<select required name="icon_class[{{ $type->id }}][]" class="form-control icon_class">
														<option value="">اختر حالة الخاصية</option>
														<option value="fa-check"><i class="fa fa-bus"></i>جيد </option>
														<option value="fa-warning"><i class="fa fa-bus"></i>لديه مشاكل </option>
													</select>
												</div>

												<div class="clearfix"></div>
												<br>
												<div class="desc" style="display: none">
													<div class="col-lg-2"> -
													</div>
													<div class="col-lg-4">
														<textarea placeholder="وصف الخطأ بالعربية" class="form-control" name="description[{{ $type->id }}][]"></textarea>
													</div>
													<div class="col-lg-4">
														<textarea style="direction: ltr" placeholder="وصف الخطأ بالانجليزية" class="form-control" name="description_en[{{ $type->id }}][]"></textarea>
													</div>

													<div class="col-lg-2">
													</div>
												</div>
                                                <div class="clearfix"></div>
                                            </div>

                                        @else
                                                <div class="append-at{{ $type->id }}">
                                                @php $i=0 @endphp
													@foreach(\App\Models\ReportPoints::where('report_id',$object->id)->where('type',$type->id)->get() as $adv)
									@if($i!=0)	<br> <div class="col-lg-2">-</div> @endif

												<div class="col-lg-4">
													<input required type="text" name="property[{{ $type->id }}][]" value="{{ $adv->name }}" class="form-control" placeholder="الخاصية باللغة العربية ">
												</div>

												<div class="col-lg-4">
													<input required style="direction: ltr" type="text" name="property_en[{{ $type->id }}][]" value="{{ $adv->name_en }}" class="form-control" placeholder="الخاصية باللغة الانجليزية">


												</div>
										<div class="col-lg-2">
											<select required name="icon_class[{{ $type->id }}][]" class="form-control icon_class">
												<option value="">اختر حالة الخاصية</option>
												<option value="fa-check"  {{ $adv->icon_class == "fa-check" ? "selected" : "" }}><i class="fa fa-check"></i>جيد </option>
												<option value="fa-warning" {{ $adv->icon_class == "fa-warning" ? "selected" : "" }}><i class="fa fa-warning"></i>لديه مشاكل </option>
											</select>
										</div>
												<div class="clearfix"></div>
									<br>
									<div class="desc" style="@if($adv->icon_class=="fa-warning") display: block @else display: none @endif">
										<div class="col-lg-2"> -
										</div>
										<div class="col-lg-4">
											<textarea placeholder="وصف الخطأ بالعربية" class="form-control" name="description[{{ $type->id }}][]">{{ $adv->description }}</textarea>
										</div>

										<div class="col-lg-4">
											<textarea style="direction: ltr" placeholder="وصف الخطأ بالانجليزية" class="form-control" name="description_en[{{ $type->id }}][]">{{ $adv->description_en }}</textarea>
										</div>

										<div class="col-lg-2">
										</div>
									</div>
										<div class="clearfix"></div>
													@php $i++ @endphp
														@endforeach
											</div>
										@endif
										<div class="clearfix"></div>
										<br>
										<a onclick="return false" type="{{ $type->id }}" class="btn btn-primary add-advantage pull-right">أضف خاصية</a>

									</div>

								</fieldset>


						</div>



					</div>
					<!-- /form horizontal -->
				@endforeach


						<div class="panel panel-flat">
							<div class="panel-heading">
								<h5 class="panel-title">الصور التوضيحية</h5>
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
									<div class="clearfix"></div>
									<div class="form-group{{ $errors->has('photos') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">الصور التوضيحية</label>
										<div class="col-lg-10">
											<input multiple type="file" name="photos[]" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('photos'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('photos') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									@if(isset($object) && \App\Models\ReportPhotos::where('report_id',$object->id)->where('type',0)->count() > 0 )
										<div class="form-group">
											<label class="control-label col-lg-2">الصور الحالية</label>
											<div class="col-lg-10">
												@foreach( \App\Models\ReportPhotos::where('report_id',$object->id)->where('type',0)->get() as $photo)
													<div align="center" style="height: 75px;width: 100px;float: right;margin-right: 5px">
														<img alt="" width="100" height="75" src="/uploads/{{ $photo  -> photo }}">
														<a href="/admin-panel/delete-photo-report/{{ $photo->id }}" style="text-align: center">حذف</a>
													</div>
												@endforeach
											</div>
										</div>
									@endif

								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">حفظ التقرير<i class="icon-arrow-left13 position-right"></i></button>
								</div>


							</div>



						</div>


					<!-- Footer -->
					@include('admin.footer')
					<!-- /footer -->
				<!-- /content area -->
				</form>
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
