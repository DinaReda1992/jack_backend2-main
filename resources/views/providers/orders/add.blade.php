@extends('providers.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script src="{{ mix('js/app.js') }}"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script>
		$('#search_box').on('keyup',function(){
			if($(this).val().length < 4 ){
				$('#user_id').val('');
				$('#searchList').fadeOut();
				return;
			}
			var query = $(this).val();
			if(query != '')
			{
				var _token = $('meta[name="csrf-token"]').attr('content');
				$.ajax({
					url:"/provider-panel/orders/search-users",
					method:"GET",
					data:{query:query, _token:_token},
					success:function(data){
						$('#searchList').fadeIn();
						$('#searchList').html(data);
					}
				});
			}
		});

		   $(document).on('click', 'li a.get-user', function(e){
			   e.preventDefault()
               $('#search_box').val($(this).data('name'));
               $('#user_id').val($(this).data('id'));
               $('#searchList').fadeOut();
           });
		   $(document).on('click', '.add-user', function(e){
			   e.preventDefault()
			   $('.add-user-con,.add-order-con').toggle()
               $('#user_id').val('');
               $('#searchList').fadeOut();
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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} عضو</h4>
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
							<li><a href="/provider-panel/orders">عرض كل الطلبات</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }}  طلب لعميل</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} طلب لعميل </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">

							<form method="post" class="form-horizontal add-order-con" action="{{ isset($object)? '/provider-panel/orders/'.$object->id : '/provider-panel/orders'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->

									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">ابحث بأسم او جوال العميل</label>
										<div class="col-lg-10">
										<input type="hidden" id="user_id" name="user_id" value="" >
										<input autocomplete="off" type="text" id="search_box" name="username"  class="form-control" placeholder="بحث">
										<div id="searchList">
										</div>
										@if ($errors->has('user_id'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('user_id') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>
								</fieldset>
								<div class="text-right">
									<a href="#"  class="btn btn-info add-user" style="float: right">اضافة عميل جديد </a>
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} طلب <i class="icon-arrow-left13 position-right"></i></button>
								</div>
							</form>

							<div class="add-user-con" style="display:none;">
								<create_user />
							</div>
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
