@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<link rel="stylesheet" href="/assets/css/jquery.fancybox.min.css" type="text/css" media="screen" />
	<script type="text/javascript" src="/assets/js/jquery.fancybox.min.js"></script>


@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">{{__('dashboard.dashboard')}} </span> - {{__('dashboard.warehouse')}}</h4>
						</div>

					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{__('dashboard.home')}}</a></li>
							<li><a href="/admin-panel/warehouse">{{__('dashboard.warehouse')}}</a></li>
							<li class="active">{{__('dashboard.order_id')}} {{$object->id}}  </li>
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
							<h5 class="panel-title">{{__('dashboard.order_id')}} {{$object->id}} </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">
							<div class="w-100"  tabindex="-1" role="dialog"
								 aria-labelledby="myModalLabel">
								<div class="modal-dialog1" role="document">
									<div class="modal-content" >
										<div class="modal-header pb-3" >

											<h4 class="modal-title pull-{{__('dashboard.right')}}" id="myModalLabel">{{__('dashboard.order_details')}}</h4>
											<div class="pull-{{__('dashboard.left')}}">

												@if($object->status!=5)
													{{__('dashboard.order_status')}}:
													<span  class=" ">
																		{{@$object->orderStatus->name}}
																	</span>
												@endif

												{{--@if($object->status!=7)

													<a  href="/admin-panel/warehouse/approve_order/{{$object->id}}">
														<button type="button" name="button" class="btn btn-primary">
															{{@$object->orderStatus->btn_text}}
														</button>
													</a>
												@endif--}}

													<a href="/admin-panel/purchases-orders/{{$object->id}}/edit" target="_blank"
													   class="btn btn-default "><i class="fa fa-print"></i> Print</a>
											</div>
										</div>
										<div class="modal-body">


											<div class="row flex">
												<label class="col-sm-4"><strong>{{__('dashboard.supplier')}}</strong></label>

												<div class="col-sm-12">
													<div class="box-body box-profile">
														<img class="profile-user-img img-responsive img-circle" src="/uploads/{{@$object->provider->photo?:'default-user.png' }}" style="height: 100px;" alt="User profile picture">
														<h3 class="profile-username text-center">{{ @$object->provider->username }}</h3>
														<p class="text-muted text-center">{{@$object->provider->phone}}</p>
														<div class="text-center">
															{{--@if($object->sent_sms==0)
																<p  class="text-center">{{__('dashboard.the_invoice_has_been_sent_to_the_customer')}}</p>
																<a  href="/admin-panel/orders/send-invoice/{{$object->id}}">
																	<button type="button" name="button" class="btn btn-primary">
																		{{__('dashboard.send_again')}}
																	</button>
																</a>
															@else
																<a  href="/admin-panel/orders/send-invoice/{{$object->id}}">
																	<button type="button" name="button" class="btn btn-primary">
																		{{__('dashboard.send_the_invoice_to_the_customer')}}
																	</button>
																</a>
															@endif--}}
														</div>


													</div>
												</div>
											</div>
											<hr>



											<div class="row flex">
												<label class="col-sm-4"><strong> {{__('dashboard.payment_type')}} </strong></label>
												<div class="col-sm-8">
													<p>{{ @$object->paymentMethod->name }}</p>
														@if($object->transfer_photo!='')
															@php
																$allowedMimeTypes = ['image/jpeg','image/gif','image/png','image/bmp','image/svg+xml'];
                                                                    $contentType = @mime_content_type('uploads/'.@$object->transfer_photo);


															@endphp
															@if(in_array($contentType, $allowedMimeTypes) )
																<img
																		data-fancybox
																		data-caption="{{__('dashboard.transfer_image')}}"
																		width="300" src="/uploads/{{@$object->transfer_photo}}" />
															@else
																<a target="_blank" href="/uploads/{{@$object->transfer_photo}}">{{__('dashboard.view_file')}}</a>
															@endif

														@endif

														<form action="/admin-panel/warehouse-purchases/upload-invoice/{{$object->id}}" method="POST" enctype="multipart/form-data">
															@csrf
															<div class="form-group mt-3">
																<label> {{__('dashboard.add_payment_receipt')}}</label>
																<input class="form-control" type="file" name="photo" />
															</div>
															<div class="form-group">
																<button type="submit" class="btn btn-primary">حفظ</button>
															</div>
														</form>
												</div>
											</div>

											<hr>

											<div class="row flex">
												<label class="col-sm-4"><strong> {{__('dashboard.payment_terms')}}</strong></label>
												<div class="col-sm-8">
													{{ @$object->paymentTerm->name }}
												</div>
											</div>
											<div class="row flex">
												<label class="col-sm-4"><strong> {{__('dashboard.order_price')}}</strong></label>
												<div class="col-sm-8">
													{{ @$object->final_price }} {{__('dashboard.sar')}}
												</div>
											</div>

											<hr>
											@if($object->purchase_item->count()>0)
												<div class="row">
													<label class="col-sm-12"><strong> {{__('dashboard.order_details')}}</strong></label>
													<div class="col-sm-12">
														<table class="table table-bordered text-center">
															<tr style="background: #e8e8e8">
																<th>{{__('dashboard.product')}}</th>
																<th>{{__('dashboard.quantity')}}</th>
																<th>{{__('dashboard.unit_price')}}</th>
																<th>{{__('dashboard.total')}}</th>

															</tr>
															@foreach($object->purchase_item as $key2=> $item)
																<tr>
																	<td>{{@$item->product->title }}</td>
																	<td>{{ @$item->quantity }}  </td>
																	<td>{{ @$item->price }} {{__('dashboard.sar')}}</td>
																	<td>{{ @$item->price*$item->quantity }} {{__('dashboard.sar')}}</td>

																</tr>
															@endforeach

															<tr style="background: #daefff">
																<td colspan="3">{{__('dashboard.total')}}</td>
																<td>{{ $object->order_price}} {{__('dashboard.sar')}}</td>
															</tr>
														</table>

													</div>
												</div>
												<div class="row">
													<label class="col-sm-12"><strong> </strong></label>
													<div class="col-sm-12">
														<h3>{{__('dashboard.summary')}}</h3>
														<table class="table table-bordered text-center">
															<tr style="background: #e8e8e8">
																<td colspan="3">{{__('dashboard.products_price')}}</td>
																<td>{{$object->order_price}} {{__('dashboard.sar')}}</td>
															</tr>

															@if($object->taxes >0)

																<tr style="background: #e8e8e8">
																	<td colspan="3">{{__('dashboard.tax')}}</td>
																	<td> {{$object->taxes}} {{__('dashboard.sar')}} </td>
																</tr>
															@endif

															<tr style="background: #daefff">
															<td colspan="3">{{__('dashboard.total')}}</td>
															<td>{{ $object->final_price}} {{__('dashboard.sar')}}</td>
														</tr>
														</table>
													</div>
												</div>
												<hr>
											@endif

										</div>

									</div>
								</div>
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
