@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/notifications/bootbox.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/media/fancybox.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis.js"></script>
	<script type="text/javascript" src="/assets/js/pages/components_modals.js"></script>


	<script type="text/javascript" src="/assets/js/pages/gallery.js"></script>

	<!-- /theme JS files -->

@stop
@section('content')


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> - عرض الشاشات الافتتاحية</h4>
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
							<li class="active"><a href="">عرض الشاشات الافتتاحية</a></li>
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

					<!-- Basic example -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">عرض الشاشات الافتتاحية</h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>

						

						<table class="table table-bordered">
							<thead>
								<tr>
									
									<th>الرقم التعريفي</th>
									<th>العنوان</th>
									<th>الشاشة</th>
									{{--<th>تاريخ اضافة الماركة</th>--}}
									<th>الاجراء المتخذ</th>
								</tr>
							</thead>
							<tbody class="row_position">
							@foreach($objects as $object)
								<tr parent_id="{{ $object->id }}">
									
									<td>{{ $object->id }}</td>
									<td>{{ $object->title }}</td>
									<td>
										@if($object->photo)

											<div class="thumbnail">
												<div class="thumb">
													<img style="width: 50px" src="/uploads/{{$object -> photo}}" alt="">
													<div class="caption-overflow">
														<span>
															<a href="/uploads/{{$object -> photo}}" data-popup="lightbox" rel="gallery" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-plus3"></i></a>
														</span>
													</div>
												</div>
											</div>

										@else
											___________
										@endif
									</td>
{{--
<td>{{ $object->created_at->format("jS \of F, Y g:i:s a") }}</td>--}}
									<td align="center" class="center"> <ul class="icons-list">
												<li class="text-primary-600"><a href="/admin-panel/illustrations/{{ $object->id }}/edit"><i class="icon-pencil7"></i></a></li>
												<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/admin-panel/illustrations/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>
<!-- 												<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
											</ul>  
									</td>
								</tr> 
							@endforeach
								
							</tbody>
						</table>
					</div>
					<!-- /basic example -->


					<!-- Restore column visibility -->
				

					<!-- State saving -->
				
					<!-- Column groups -->
				


					<!-- Footer -->
					@include('admin.footer')
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->
			<script type="text/javascript">
                // Warning alert
                $(document).ready(function () {
                    $(document).on('change','.illustrations_order',function () {
                        var illustrations_id = $(this).attr('illustrations_id');
                        var order = $(this).val();
                        $.get('/admin-panel/save_order_illustrations/'+ illustrations_id + "/" +order,function (data) {

                        })
                    })
                })
			</script>
			<!-- /main content -->
			<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
			{{--	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>--}}

			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


			<script type="text/javascript">
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				});
				$( ".row_position" ).sortable({
					delay: 150,
					stop: function() {
						var selectedData = new Array();
						$('.row_position>tr').each(function() {
							selectedData.push($(this).attr("parent_id"));
						});
						updateOrder(selectedData);
					}
				});


				function updateOrder(data) {
					console.log(data);
					$.ajax({
						url:"/admin-panel/change-sort-illustrations",
						type:'post',
						data:{position:data},
						success:function(){
						}
					})
				}
			</script>

			</body>
			</html>
@stop