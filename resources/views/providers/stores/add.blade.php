@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/ui/moment/moment.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/daterangepicker.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/anytime.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/picker_date.js"></script>

	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript">
		$(document).ready(function () {
			$(document).on('click','.add-advantage',function () {
				$('.append-at').append('<br>' +
						'<div class="col-lg-2">-</div>' +
						'<div class="col-lg-3"><input type="text" name="day_of_plan[]" value="" class="form-control datepicker" placeholder="يوم النقطة"></div>' +
						'<div class="col-lg-3"><input type="text" name="time[]" value="" class="form-control timepicker" placeholder="وقت النقطة"></div>' +
						'<div class="col-lg-4"><input type="text" name="plans[]" value="" class="form-control" placeholder="تفاصيل النقطة"></div>' +
						'<div class="clearfix"></div>');
				$('.action-create').click();
					$( ".datepicker" ).datepicker({
						dateFormat:'yy-mm-dd',
						changeMonth: true,
						changeYear: true
					});
					$('.timepicker').pickatime();
			});
			$(window).load(function () {
				$('.action-create').click();
			})
		});
	</script>
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
                    url: '/provider-panel/get-sub-categories/' + category_id,
                    success: function (data) {
                        $('.sub_category_id').html(data);
                    }
                });

            });


        });
	</script>
	<script type="text/javascript">
		var map, infoWindow,marker;
		function initialize() {

			map = new google.maps.Map(document.getElementById('gmap'), {
				center: {lat: {{ @$object->latitude ? $object->latitude : 24.7253981  }}, lng: {{ @$object->longitude ? $object->longitude : 46.2620208  }}},
				zoom: 10
			});

			@if(@$object->latitude && $object->longitude  )
			var pos = {
						lat: {{ $object->latitude }},
						lng: {{ $object->longitude }}
					};
			document.getElementById('lon').value = pos.lng;
			document.getElementById('lat').value = pos.lat;
			map.setCenter(pos);
			marker = new google.maps.Marker({
				position: pos,
				map: map,
				title: 'موقعك الجغرافي'
			});
			@endif


					infoWindow = new google.maps.InfoWindow;
			// Try HTML5 geolocation.
			@if(!isset($object) || !$object->longitude)
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					var pos = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					document.getElementById('lon').value = pos.lng;
					document.getElementById('lat').value = pos.lat;
					map.setCenter(pos);
					marker = new google.maps.Marker({
						position: pos,
						map: map,
						title: 'موقعك الجغرافي'
					});
				}, function() {
					handleLocationError(true, infoWindow, map.getCenter());
				});
			} else {
				// Browser doesn't support Geolocation
				handleLocationError(false, infoWindow, map.getCenter());
			}
			@endif


			google.maps.event.addListener(map, "click", function(event) {
				// clearOverlays();
				// get lat/lon of click
				var clickLat = event.latLng.lat();
				var clickLon = event.latLng.lng();

				// show in input box
				document.getElementById("lat").value = clickLat.toFixed(5);
				document.getElementById("lon").value = clickLon.toFixed(5);


				marker.setPosition(new google.maps.LatLng(clickLat,clickLon));
				markersArray.push(marker);
			});


		}

		var markersArray = [];
		function clearOverlays() {
			for (var i = 0; i < markersArray.length; i++ ) {
				markersArray[i].setMap(null);
			}
			markersArray.length = 0;
		}

		function handleLocationError(browserHasGeolocation, infoWindow, pos) {
			infoWindow.setPosition(pos);
			infoWindow.setContent(browserHasGeolocation ?
					'Error: The Geolocation service failed.' :
					'Error: Your browser doesn\'t support geolocation.');
			infoWindow.open(map);
		}



		window.onload = function () { initialize() };
	</script>
	<script async defer
			src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWEcSMyJieMBR1LXOA_fx0YgQfPF8LlSY"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$( function() {
			$( ".datepicker" ).datepicker({
				dateFormat:'yy-mm-dd',
				changeMonth: true,
				changeYear: true
			});
			$('.timepicker').pickatime();
		} );
	</script>
@stop
@section('content')
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} متجر</h4>
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
					<li><a href="/provider-panel/stores">عرض المتاجر</a></li>
					<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} متجر</li>
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
					<h5 class="panel-title">اضافة متاجر بملف إكسل</h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<div class="panel-body">

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="/provider-panel/import-excel">
						{!! csrf_field() !!}
						@if(isset($object))
							<input type="hidden" name="_method" value="PATCH" />
						@endif
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->


							<div class="form-group{{ $errors->has('excel_sheet') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أرفق ملف الاكسل</label>
								<div class="col-lg-10">

									<input type="file" name="excel_sheet" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('excel_sheet'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('excel_sheet') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>


						</fieldset>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} رفع الملف <i class="icon-arrow-left13 position-right"></i></button>
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

		<!-- Content area -->
		<div class="content">

		@include('admin.message')

		<!-- Form horizontal -->
			<div class="panel panel-flat">
				<div class="panel-heading">
					<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} متجر </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<div class="panel-body">

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/stores/'.$object->id : '/provider-panel/stores'  }}">
						{!! csrf_field() !!}
						@if(isset($object))
							<input type="hidden" name="_method" value="PATCH" />
						@endif
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->

							<div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">إختر القسم </label>
								<div class="col-lg-10">
									<select name="category_id" class="form-control">
										<option value="">اختر القسم </option>
										@foreach(\App\Models\Categories::all() as $category)
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


							<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم المتجر</label>
								<div class="col-lg-10">
									<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="اسم المتجر">
									@if ($errors->has('name'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">اسم المتجر بالانجليزية</label>
								<div class="col-lg-10">
									<input type="text" name="name_en" value="{{ isset($object) ? $object->name_en  : old('name_en')  }}" class="form-control" placeholder="اسم المتجر بالانجليزية">
									@if ($errors->has('name_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name_en') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">عنوان المتجر</label>
								<div class="col-lg-10">
									<input type="text" name="address" value="{{ isset($object) ? $object->address  : old('address')  }}" class="form-control" placeholder="عنوان المتجر">
									@if ($errors->has('address'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('address') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('address_en') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">عنوان المتجر بالانجليزية</label>
								<div class="col-lg-10">
									<input type="text" name="address_en" value="{{ isset($object) ? $object->address  : old('address_en')  }}" class="form-control" placeholder="عنوان المتجر بالانجليزية">
									@if ($errors->has('address_en'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('address_en') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>






							<div class="form-group{{ $errors->has('longitude') || $errors->has('latitude') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">الموقع على الخريطة</label>
								<div class="col-lg-10">
									<input type="hidden" id='lon' name="longitude"  value="{{ isset($object) ? $object->longitude  : old('longitude')  }}" />
									<input type="hidden" id='lat' name="latitude"  value="{{ isset($object) ? $object->latitude  : old('latitude')  }}" />
									<div class="element" id="gmap" style="height:250px;width:100%;">
									</div>
								</div>
							</div>


							<div class="clearfix"></div>
							<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل شعار المتجر</label>
								<div class="col-lg-10">

									<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
									@if ($errors->has('photo'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('photo') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>
							@if(isset($object->photo) && $object->photo )
								<div class="form-group">
									<label class="control-label col-lg-2">الشعار الحالي</label>
									<div class="col-lg-10">
										<img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">
										<br>

										<a href="/provider-panel/delete-photo-store/{{ $object->id }}">حذف الصورة</a>
									</div>
								</div>
							@endif

						</fieldset>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} المتجر <i class="icon-arrow-left13 position-right"></i></button>
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
