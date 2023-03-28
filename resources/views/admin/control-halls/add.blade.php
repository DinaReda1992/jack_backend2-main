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
	<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<link rel="stylesheet" href="/js/selectize/css/selectize.css">
	<script src="/js/selectize/js/standalone/selectize.js"></script>
	<script type="text/javascript" src="/assets/js/admin/halls_add.js"></script>


	<script type="text/javascript">
        $(document).ready(function () {
			// $('#categories').selectize({
			// 	maxItems: 30
			// });

            $('.add-advantage').click(function () {
                var type = $(this).attr('type');
                $.get('/admin-panel/extraFeatures',function (data) {
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
			$('#provider_id').change(function () {
				var provider_id= $(this).val();
				$('.state_id').html("<option>جاري تحميل المدن ..</option>");
				$.get('/admin-panel/get-provider-states/'+provider_id,function (data) {
					$('.state_id').html(data);
				})
			});

		});

	</script>

	<link href="/assets/css/fontawesome-iconpicker.css" rel="stylesheet">
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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }}  قاعة </h4>
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
					<li class="active">أضف قاعة</li>
				</ul>
			</div>
		</div>
		<!-- /page header -->

		<!-- Content area -->
		<div class="content">
			<form method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/halls/'.$object->id : '/admin-panel/halls'  }}" enctype="multipart/form-data">
				{!! csrf_field() !!}
				@if(isset($object))
					<input type="hidden" name="_method" value="PATCH" />

				@endif
				@include('admin.message')


				<div class="panel panel-flat">
					<div class="panel-heading">
						<h5 class="panel-title">بيانات القاعة</h5>
						<div class="heading-elements">
							<ul class="icons-list">
								<li><a data-action="collapse"></a></li>
								<li><a data-action="reload"></a></li>
								<li><a data-action="close"></a></li>
							</ul>
						</div>
						<div style=" text-align: center;" >
							<ul class="nav nav-tabs" role="tablist">
								<li  class="active" ><a data-toggle="tab" href="#main_data">البيانات الاساسية</a></li>
								<li  ><a data-toggle="tab" href="#extra_data">الاضافات</a></li>
								<li  ><a data-toggle="tab" href="#roles_data">الشروط والقواعد</a></li>

							</ul>
						</div>

					</div>



					<div class="panel-body">
						<div class="tab-content">
							<div id="main_data" class="tab-pane fade in active ">


						<fieldset class="content-group">

							<div class="form-group{{ $errors->has('provider_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">إختر صاحب القاعة</label>
								<div class="col-lg-10">
									<select name="provider_id" id="provider_id" class= 'form-control js-example-placeholder-single select2 '>
										<option value="">اختر صاحب قاعات</option>
										@foreach($providers as $prov)
											<option value="{{$prov->id}}" {{isset($provider)&&$provider->id==$prov->id?'selected':''}}>{{$prov->username}}</option>
										@endforeach
									</select>
									@if ($errors->has('provider_id'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('provider_id') }}</strong>
		                                    </span>
									@endif
								</div>

							</div>

							<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم القاعة </label>
								<div class="col-md-10">
									<input  placeholder="اسم القاعة"  value="{{ isset($object) ? $object->title  : old('title')  }}" class="form-control" type="text" name="title">
									@if ($errors->has('title'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('title') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('title_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم القاعة بالانجليزى</label>
								<div class="col-md-10">
									<input  placeholder="اسم القاعة بالانجليزى"  value="{{ isset($object) ? $object->title_en  : old('title_en')  }}" class="form-control" type="text" name="title_en">
									@if ($errors->has('title_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('title_en') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>



							<div class="form-group{{ $errors->has('categories') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">إختر الانواع</label>
								<div class="col-lg-10">
									<select class="select-multiple-tokenization" multiple="multiple" name="categories[]" id="categories">
										<?php $currentTypes=isset($hall_types)?$hall_types:(old('categories')?:[]); ?>
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
							<div class="form-group {{$errors->has('currency')?" input_error":""}}">
								<label class="control-label col-sm-2" for="price_per_hour">السعر للساعة:</label>
								<div class="col-sm-6">
									<input type="number" class="form-control" name="price_per_hour" id="price_per_hour" value="{{ isset($object) ? $object->price_per_hour  : old('price_per_hour')  }}"   placeholder="السعر لكل ساعة" />
								</div>
								<div class="col-sm-4">
									<select name="currency" class="form-control"   data-hide-disabled="true" data-live-search="true">
										@foreach(\App\Models\Currencies::all() as $currency)
											<option value="{{$currency->id}}" {{ isset($object) && $object->currency==$currency->id ? 'selected' : (old('currency') == $currency->id ? 'selected' : (isset($provider)?($currency->id==$provider->currency_id?"selected":""):'')) }}>{{$currency->name}}</option>
										@endforeach
									</select>
								</div>
								@if ($errors->has('currency') ||$errors->has('price_per_hour'))
									<span class="help-block">
                                    <strong>{{ $errors->first('currency') }}</strong>
                                </span>
									<span class="help-block">
                                    <strong>{{ $errors->first('price_per_hour') }}</strong>
                                </span>

								@endif
							</div>

							<div class="form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
							<label class="control-label col-lg-2">إختر المدينة</label>
							<div class="col-lg-10">
							<select name="state_id" class="form-control state_id">

							<option value="">اختر المدينة</option>
								@if(isset($provider))
							@foreach (\App\Models\States::where('country_id', $provider->country_id)->get() as $state)
							<option value="{{ $state->id }}"  {{ isset($object) && $object->state_id==$state->id ? 'selected' : (old('state_id') == $state->id ? 'selected' : '') }}>  {{$state->name}} </option>
							@endforeach
									@endif

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

							<div class="form-group{{ $errors->has('chairs') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">عدد الكراسى </label>
								<div class="col-md-10">
									<input  placeholder="عدد الكراسى"  value="{{ isset($object) ? $object->chairs  : old('chairs')  }}" class="form-control" type="number" name="chairs">
									@if ($errors->has('chairs'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('chairs') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('capacity') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">قدرة الاستيعاب </label>
								<div class="col-md-10">
									<input  placeholder="حدد عدد الاشخاص الذى يمكن استيعابه "  value="{{ isset($object) ? $object->capacity  : old('capacity')  }}" class="form-control" type="number" name="capacity">
									@if ($errors->has('capacity'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('capacity') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('photos') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">صور القاعة</label>
								<div class="col-lg-10">
									<input multiple type="file" name="photos[]" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('photos'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('photos') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							@if(isset($object) && \App\Models\HallPhoto::where('hall_id',$object->id)->count() > 0 )

								<div class="form-group">
									<label class="control-label col-lg-2">الصور الحالية</label>

									<div class="col-lg-10">
										@foreach( \App\Models\HallPhoto::where('hall_id',$object->id)->get() as $photo)
											<div align="center" style="height: 75px;width: 100px;float: right;margin-right: 5px">
												<img alt="" width="100" height="75" src="/uploads/{{ $photo  -> photo }}">
												<a href="/admin-panel/delete-photo-hall/{{ $photo->id }}" style="text-align: center">حذف</a>
											</div>
										@endforeach
									</div>

								</div>

							@endif


						</fieldset>
							</div>
							<div id="extra_data" class="tab-pane fade ">


								<fieldset class="content-group">


									<div class="form-group ">
										<div class="append-at1">
										@if(!isset($object) || $object->features->count()==0)
											<div>
												{!! View::make("halls.items.feature") -> with('features', $features) -> render() !!}
											</div>
										@else
											<div>
												@php $i=0 @endphp
												@foreach($object->hallfeatures as $adv)

														{!! View::make("halls.items.feature") -> with('features', $features) ->with('adv',$adv)-> render() !!}

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
							</div>
							<div id="roles_data" class="tab-pane fade">


								<fieldset class="content-group">

									<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">وصف وتفاصيل القاعة </label>
										<div class="col-md-10">
											<textarea  placeholder="وصف القاعه" style="min-height: 114px;"  class="form-control" name="description">{{ isset($object) ? $object->description  : old('description')  }}</textarea>
											@if ($errors->has('description'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">وصف وتفاصيل القاعة بالانجليزى </label>
										<div class="col-md-10">
											<textarea  placeholder="وصف القاعه بالانجليزى" style="min-height: 114px;"  class="form-control" name="description_en">{{ isset($object) ? $object->description_en  : old('description_en')  }}</textarea>
											@if ($errors->has('description_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('terms') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">شروط الحجز </label>
										<div class="col-md-10">
											<textarea  placeholder="شروط الحجز" style="min-height: 114px;"  class="form-control" name="terms">{{ isset($object) ? $object->terms  : old('terms')  }}</textarea>
											@if ($errors->has('terms'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('terms') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('terms_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">شروط الحجز بالانجليزى </label>
										<div class="col-md-10">
											<textarea  placeholder="شروط الحجز بالانجليزى" style="min-height: 114px;"  class="form-control" name="terms_en">{{ isset($object) ? $object->terms_en  : old('terms_en')  }}</textarea>
											@if ($errors->has('terms_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('terms_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>


									<div class="form-group{{ $errors->has('policy') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">سياسة القاعة </label>
										<div class="col-md-10">
											<textarea  placeholder="سياسة القاعة" style="min-height: 114px;"  class="form-control" name="policy">{{ isset($object) ? $object->policy  : old('policy')  }}</textarea>
											@if ($errors->has('policy'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('policy') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('policy_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">سياسة القاعة بالانجليزى </label>
										<div class="col-md-10">
											<textarea  placeholder="سياسة القاعة بالانجليزى" style="min-height: 114px;"  class="form-control" name="policy_en">{{ isset($object) ? $object->policy_en  : old('policy_en')  }}</textarea>
											@if ($errors->has('policy_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('policy_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

								</fieldset>
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


		<script src="/assets/js/fontawesome-iconpicker.js"></script>
		<script src="{{ asset('js/mapPlace.js') }}"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkQ8_neT4uZpVaXG1SbZNWKH1fnQHnbGk&libraries=places&callback=initMap&language={{ App::getLocale() }}"
				async defer></script>


		<script>
            $(function() {
				$(".js-example-placeholder-single").select2({
					placeholder: "اختر صاحب قاعة",
				});

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				});

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
                        hideOnSelect: true,
                        icons: $.merge(['glyphicon glyphicon-home', 'glyphicon glyphicon-repeat', 'glyphicon glyphicon-search',
                            'glyphicon glyphicon-arrow-left', 'glyphicon glyphicon-arrow-right', 'glyphicon glyphicon-star'], $.iconpicker.defaultOptions.icons),
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

		</script>


@stop
