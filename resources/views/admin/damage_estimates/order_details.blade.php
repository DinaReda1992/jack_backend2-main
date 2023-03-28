@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

	<script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<!-- /theme JS files -->

	{{--	<link href="/assets/js/plugins/hierarchical-select/hierarchy-select.min.js" rel="stylesheet">--}}

	{{--	<script type="text/javascript" src="/assets/js/plugins/hierarchical-select/hierarchy-select.min.js"></script>--}}
<style>
.response-header{
	text-align: center;
	color: #263238;
	font-weight: 700;
}
.offer-title{
	text-align: center;
	color: #2d9e08;
	text-decoration: underline;
}
	.my-offer{
		background: #fff;
		padding: 5px;
		display: grid;
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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - تفاصيل الطلب</h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/admin-panel/damage-estimates"><button type="button" class="btn btn-success" name="button">  عرض طلبات الخدمات</button></a>
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
					<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i></a></li>
					<li><a href="/admin-panel/damage-estimates">عرض طلبات الخدمات</a></li>
					<li class="active"> تفاصيل الطلب </li>
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
					<h5 class="panel-title">  تفاصيل الطلب ( رقم {{$object->id}}#)</h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>

				<section class="content">

					<div class="row">
						<div class="col-md-12">

							<!-- Profile Image -->
							<div class="">
								<div class="box-body box-profile">
									<img class="profile-user-img img-responsive img-circle" src="/uploads/{{@$object->user->photo?:'default-user.png'}}" alt="User profile picture">
									<h3 class="profile-username text-center">{{@$object->user->username}}</h3>
									@if($object->shop_id==235)

									<p class="text-muted text-center">رقم الجوال : {{@$object->user->phone}}</p>
									<p class="text-muted text-center">البريد الالكترونى : {{@$object->user->email}}</p>
									@endif
								</div><!-- /.box-body -->
							</div><!-- /.box -->

							<!-- About Me Box -->
						</div><!-- /.col -->
						<div class="col-md-12">
							<div class="nav-tabs-custom">
								<ul class="timeline timeline-inverse">
									<!-- timeline time label -->
									<li class="time-label">
										@if($object->status==0)
											<span class="bg-green">
												(مفتوح)
                        </span>

										@elseif($object->status==1)
											@if($object->shop_id==235)
												<span style="color:green">(تم قبول عرضك)</span>

											@else
												<span class="bg-gray">
												(منتهى)
                        </span>

											@endif

										@elseif($object->status==3)
											<span class="bg-red">
												(ملغى)
                        </span>

										@endif

									</li>
									<!-- /.timeline-label -->
									<!-- timeline item -->
									<li>
										<i class="fa fa-envelope bg-blue"></i>
										<div class="timeline-item">
												@if($object->status==1)
													@if($object->shop_id==235)
													<span style="color:green">(تم قبول عرضك)</span>

												@else
													<span style="color:red">(منتهى)</span>
												@endif
											@elseif($object->status==3)
												<span style="color:grey">(ملغى)</span>

											@endif

												<h3 class="timeline-header"></h3>
													<div class="timeline-body" style="display: inline-block;width: 100%;">
														<div class="form-group col-md-6">
															<label class="control-label col-lg-12">نوع الخدمة </label>
															<div class="col-lg-10">
																<p class="form-control" >{{@$object->service->name}}</p>
															</div>
														</div>

														<div class="form-group col-md-6">
															<label class="control-label col-lg-12">تاريخ  الطلب</label>
															<div class="col-lg-10">
																<p class="form-control" >{{$object->created_at->diffForHumans()}}</p>
															</div>
														</div>

														<div class="form-group">
															<label class="control-label col-lg-12">وصف الطلب</label>
															<div class="col-lg-10">
																<p class="form-control" >{{$object->description}}</p>
															</div>
														</div>
														@if($object->photos->count())

														<div class="post">
															<div class="row margin-bottom">
																@foreach($object->photos as $photo)
																		<div class="col-sm-3">
																			<img class="img-responsive myImg" src="/uploads/{{$photo->photo}}" alt="Photo">
																		</div><!-- /.col -->
																@endforeach
															</div><!-- /.row -->


														</div>
														@endif
														@if($my_reply)
															<div class="my-offer">
														<h3 class="offer-title">العرض</h3>
															<div class="post">
																<div class="user-block">
																	<img class="img-circle img-bordered-sm" src="/uploads/{{ $adminUser->photo?:'placeholder.png' }}" alt="user image">
																	<span class="username">
                          <a href="#">{{ $adminUser->username }}</a>

                        </span>
																	<span class="description">{{$my_reply->created_at->diffForHumans()}}</span>
																</div><!-- /.user-block -->
																<div class="form-group col-md-6">
																	<label class="control-label col-lg-12">السعر من</label>
																	<div class="col-lg-10">
																		<p class="form-control" >{{$my_reply->cost_from}}</p>
																	</div>
																</div>
																<div class="form-group col-md-6">
																	<label class="control-label col-lg-12">السعر الى </label>
																	<div class="col-lg-10">
																		<p class="form-control" >{{$my_reply->cost_to}}</p>
																	</div>
																</div>
																<div class="form-group col-md-6">
																	<label class="control-label col-lg-12">وقت المعاينة </label>
																	<div class="col-lg-10">
																		<p class="form-control" >{{$my_reply->time}}</p>
																	</div>
																</div>
																<div class="form-group col-md-6">
																	<label class="control-label col-lg-12">وصف العرض </label>
																	<div class="col-lg-10">
																		<p class="form-control" >{{$my_reply->description}}</p>
																	</div>
																</div>

															</div>
															</div>
															@endif
													</div>
											@if($object->status==0 && $object->is_replied==0)
											<div class="timeline-footer">
												<a data-toggle="modal" data-target="#acceptModal" onclick="return false;" href="#" class="btn btn-success btn-xs">أضف العرض</a>
											</div>
														<div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																	<div class="modal-header">
																		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																		<h4 class="modal-title" id="myModalLabel">أضف عرضك</h4>
																	</div>
																	<form method="post" action="/admin-panel/add_damage_offer/{{$object->id}}">
																		{!! csrf_field() !!}
																		<div class="modal-body">
																			<div class="row">
																				<h5>حدود التكلفة</h5>
																				<div class="form-group col-md-6">
																					<div class="col-lg-12">
																						<input type="number" name="cost_from" class="form-control" value="{{old('cost_from')}}" placeholder="تبدأ من  ."/>
																					</div>
																				</div>
																				<div class="form-group col-md-6">
																					<div class="col-lg-12">
																						<input type="number" name="cost_to" class="form-control" value="{{old('cost_to')}}" placeholder="الى  ."/>
																					</div>
																				</div>
																				<div class="clearfix"></div>
																				<h5>موعد وتفاصيل العرض</h5>

																				<div class="form-group col-md-12">
																					<div class="col-lg-12">
																						<input type="text" name="time" class="form-control" value="{{old('time')}}" placeholder="موعد المعاينة  ."/>
																					</div>
																				</div>

																				<div class="form-group">
																					<div class="col-lg-12">
																						<textarea name="description" class="form-control" placeholder="اكتب تفاصيل العرض  .">{{old('description')}}</textarea>
																					</div>
																				</div>

																			</div>
																		</div>
																		<div class="modal-footer">
																			<input type="submit" class="btn btn-success" value="الرد" />

																		</div>
																	</form>
																</div>
															</div>
														</div>

													@endif
										</div>
									</li>
									<!-- END timeline item -->

								</ul>
							</div><!-- /.nav-tabs-custom -->
						</div>
					</div><!-- /.row -->

				</section>

			</div>
			<!-- /form horizontal -->


			<!-- Footer -->
		@include('admin.footer')
		<!-- /footer -->
		</div>
		<!-- /content area -->
	</div>
	<div id="myModal" class="modal">

		<!-- The Close Button -->
		<span class="close">&times;</span>

		<!-- Modal Content (The Image) -->
		<img class="modal-content" id="img01">

		<!-- Modal Caption (Image Text) -->
		<div id="caption"></div>
	</div>
	<link rel="stylesheet" href="/css/image-style.css">

	<script>
		$(function () {
			@if(isset($errors) && count($errors) > 0)
			$('#acceptModal').modal('show');
			@endif
			var modal = document.getElementById("myModal");

// Get the image and insert it inside the modal - use its "alt" text as a caption
			var img = document.getElementsByClassName("myImg");
			var modalImg = document.getElementById("img01");
			var captionText = document.getElementById("caption");
			$('.myImg').click(function(){
						modal.style.display = "block";
						modalImg.src = this.src;
						captionText.innerHTML = this.alt;
					}
			);

// Get the <span> element that closes the modal
			var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
			span.onclick = function() {
				modal.style.display = "none";
			}
			$('.close').click(function(){$("#myModal").hide()})
		})
	</script>

@stop