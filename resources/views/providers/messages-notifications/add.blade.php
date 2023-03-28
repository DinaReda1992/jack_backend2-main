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
                $('.append-at'+type).append('<div class="col-lg-2">-</div><div class="col-lg-2"><select name="user_type_id['+type+'][]" class="form-control"><option value="">اختر نوع مستخدم</option><option value="3">عميل</option><option value="4">مندوب</option></select></div><div class="col-lg-4"><input type="text"  name="message['+type+'][]" value="" class="form-control" placeholder="الرسالة بالعربية "></div><div class="col-lg-4"><input  style="direction: ltr" type="text" name="message_en['+type+'][]" value="" class="form-control" placeholder="الرسالة بالانجليزية"></div><div class="clearfix"></div><br><div class="desc" style="display: none"></div><div class="clearfix"></div>');
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
                $.get('/provider-panel/get-sub-categories/'+category_id,function (data) {
                    $('.sub_category_id').html(data);
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
	</style>

@stop
@section('content')
	<!-- Main content -->
	<div class="content-wrapper">
		<!-- Page header -->
		<div class="page-header">
			<div class="page-header-content">
				<div class="page-title">
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }}  قائمة </h4>
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
					<li class="active">تنبيهات قبل الشات</li>
				</ul>
			</div>
		</div>
		<!-- /page header -->

		<!-- Content area -->
		<div class="content">
			<form method="post" class="form-horizontal" action="/provider-panel/save-messages-notifications" enctype="multipart/form-data">
				{!! csrf_field() !!}
				@if(isset($object))
					<input type="hidden" name="_method" value="PATCH" />

				@endif
				@include('admin.message')

				@foreach($objects as $service)

				<div class="panel panel-flat">
					<div class="panel-heading">
						<h5 class="panel-title">رسائل خدمة  {{ $service ->name }}</h5>
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
								<label class="control-label col-lg-2">الرسالة </label>
								@if( @$service->getMessages->count() ==0)
									<div class="append-at{{ $service->id }}">
										<div class="col-lg-2">
											<select name="user_type_id[{{ $service->id }}][]" class="form-control">
												<option value="">اختر نوع مستخدم</option>
												<option value="3">عميل</option>
												<option value="4">مندوب</option>
											</select>
										</div>
										<div class="col-lg-4">
											<input  type="text" name="message[{{ $service->id }}][]" value="" class="form-control" placeholder="الرسالة بالعربية ">
										</div>
										<div class="col-lg-4">
											<input  style="direction: ltr" type="text" name="message_en[{{ $service->id }}][]" value="" class="form-control" placeholder="الرسالة بالانجليزية">
										</div>
										<div class="clearfix"></div>
										<br>
									</div>
								@else
									<div class="append-at{{ $service->id }}">
										@php $i=0 @endphp
										@foreach(@$service->getMessages as $msg)
											@if($i!=0)	<br> <div class="col-lg-2">-</div> @endif
												<div class="col-lg-2">
													<select name="user_type_id[{{ $service->id }}][]" class="form-control">
														<option value="">اختر نوع مستخدم</option>
														<option value="3" {{ $msg->user_type_id==3 ? 'selected': '' }}>عميل</option>
														<option value="4" {{ $msg->user_type_id==4 ? 'selected': '' }}>مندوب</option>
													</select>
												</div>
											<div class="col-lg-4">
												<input  type="text" name="message[{{ $service->id }}][]" value="{{ $msg->message }}" class="form-control" placeholder="الرسالة بالعربية ">
											</div>

											<div class="col-lg-4">
												<input  style="direction: ltr" type="text" name="message_en[{{ $service->id }}][]" value="{{ $msg->message_en }}" class="form-control" placeholder="الرسالة بالانجليزية">
											</div>
											<div class="clearfix"></div>
											<br>
											@php $i++ @endphp
										@endforeach
									</div>
								@endif
								<div class="clearfix"></div>
								<br>
								<a onclick="return false" type="{{ $service->id }}" class="btn btn-primary add-advantage pull-right">أضف رسالة أخرى</a>

							</div>

							<div class="text-right">
								<button type="submit" class="btn btn-primary">حفظ <i class="icon-arrow-left13 position-right"></i></button>
							</div>


						</fieldset>


					</div>



				</div>

				@endforeach

				<!-- Footer -->
			@include('admin.footer')
			<!-- /footer -->
				<!-- /content area -->
			</form>
		</div>


		<script src="/assets/js/fontawesome-iconpicker.js"></script>
		<script>
            $(function() {

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
		</script>


@stop
