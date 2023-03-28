@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<style type="text/css">
		#map{ width:100%; height: 350px; }
	</style>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVyTOqvO4sqpUZ9Wsv4l8i47zQWolK_Tg"></script>

	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<!-- /theme JS files -->
<script>
    //map.js

    //Set up some of our variables.
    var map; //Will contain map object.
    var marker = false; ////Has the user plotted their location marker?

    //Function called to initialize / create the map.
    //This is called when the page has loaded.
    function initMap() {

        //The center location of our map.
        var centerOfMap = new google.maps.LatLng({{ isset($object) && $object->latitude ? $object->latitude : 23.8859 }}, {{ isset($object) && $object->longitude ? $object->longitude : 45.0792 }} );

        //Map options.
        var options = {
            center: centerOfMap, //Set center.
            zoom: 7 //The zoom value.
        };

        //Create the map object.
        map = new google.maps.Map(document.getElementById('map'), options);

        //Listen for any clicks on the map.
        google.maps.event.addListener(map, 'click', function(event) {
            //Get the location that the user clicked.
            var clickedLocation = event.latLng;
            //If the marker hasn't been added.

            if(marker === false){
                //Create the marker.
                marker = new google.maps.Marker({
                    position: clickedLocation,
                    map: map,
                    draggable: true //make it draggable
                });
                //Listen for drag events!
                google.maps.event.addListener(marker, 'dragend', function(event){
                    markerLocation();
                });
            } else{
                //Marker has already been added, so just change its location.
                marker.setPosition(clickedLocation);
            }
            //Get the marker's location.
            markerLocation();
        });
    }

    //This function will get the marker's current location and then add the lat/long
    //values to our textfields so that we can save the location.
    function markerLocation(){
        //Get location.
        var currentLocation = marker.getPosition();
        //Add lat and lng values to a field that we can save.
        document.getElementById('lat').value = currentLocation.lat(); //latitude
        document.getElementById('lng').value = currentLocation.lng(); //longitude
    }


    //Load the map when the page has finished loading.
    google.maps.event.addDomListener(window, 'load', initMap);
</script>
@stop
@section('content')
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} فرع</h4>
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
					<li><a href="/admin-panel/branches">عرض الفروع</a></li>
					<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} فرع</li>
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
					<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} فرع </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>



				<div class="panel-body">

					<form enctype="multipart/form-data" method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/branches/'.$object->id : '/admin-panel/branches'  }}">
						{!! csrf_field() !!}
						@if(isset($object))
							<input type="hidden" name="_method" value="PATCH" />
						@endif
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->

							<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل اسم الفرع</label>
								<div class="col-lg-10">
									<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل اسم الفرع">
									@if ($errors->has('name'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">أدخل رقم الجوال</label>
								<div class="col-lg-10">
									<input type="text" name="phone" value="{{ isset($object) ? $object->phone  : old('phone')  }}" class="form-control" placeholder="أدخل رقم الجوال">
									@if ($errors->has('phone'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('phone') }}</strong>
		                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">عنوان الفرع</label>
								<div class="col-lg-10">
									<input type="text" name="address" value="{{ isset($object) ? $object->address  : old('address')  }}" class="form-control" placeholder="عنوان الفرع">
									@if ($errors->has('address'))
										<span class="help-block">
		                                     <strong>{{ $errors->first('address') }}</strong>
		                                </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">ملاحظات على الفرع</label>
								<div class="col-lg-10">
									<input type="text" name="notes" value="{{ isset($object) ? $object->notes  : old('notes')  }}" class="form-control" placeholder="ملاحظات على الفرع">
									@if ($errors->has('notes'))
										<span class="help-block">
		                                     <strong>{{ $errors->first('notes') }}</strong>
		                                </span>
									@endif
								</div>
							</div>
							<div class="form-group{{ $errors->has('longitude') || $errors->has('latitude') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">الموقع على الخريطة</label>
								<div class="col-lg-10">

									<div id="map"></div>
									<br>
									<input type="text" value="{{ isset($object) ? $object->longitude  : old('longitude')  }}"  name="longitude" class="form-control" id="lng" readonly><br>
									<input type="text" value="{{ isset($object) ? $object->latitude  : old('latitude')  }}"  name="latitude" class="form-control" id="lat" readonly>

								@if ($errors->has('longitude'))
										<span class="help-block">
		                                     <strong>{{ $errors->first('longitude') }}</strong>
		                                </span>
									@endif
									@if ($errors->has('latitude'))
										<span class="help-block">
		                                     <strong>{{ $errors->first('latitude') }}</strong>
		                                </span>
									@endif
								</div>
							</div>
						</fieldset>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الفرع <i class="icon-arrow-left13 position-right"></i></button>
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