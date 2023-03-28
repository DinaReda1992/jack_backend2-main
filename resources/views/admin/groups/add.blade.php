@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<script type="text/javascript" src="/assets/js/admin/cbFamily.js"></script>

	<!-- /theme JS files -->
	<script type="text/javascript">
		$(document).ready(function () {
			$("h3 input:checkbox").cbFamily(function () {
				return $(this).parents("h3").next().find("input:checkbox");
			});

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
					<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">{{__('dashboard.dashboard')}} </span>
						- {{ isset($object)? __('dashboard.edit'):__('dashboard.add') }} {{__('dashboard.group')}}</h4>
				</div>
				<div class="heading-elements">
					<div class="heading-btn-group">
						<a href="/admin-panel/groups">
							<button type="button" class="btn btn-primary" name="button"> {{__('dashboard.view_group_permissions')}}</button>
						</a>
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
					<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{__('dashboard.home')}} </a></li>
					<li><a href="/admin-panel/groups">{{__('dashboard.view_group_permissions')}}</a></li>
					<li class="active">{{ isset($object)? __('dashboard.edit'):__('dashboard.add') }} {{__('dashboard.group')}}</li>
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
					<h5 class="panel-title">{{ isset($object)? __('dashboard.edit'):__('dashboard.add') }} {{__('dashboard.group')}} </h5>
					<div class="heading-elements">
						<ul class="icons-list">
							<li><a data-action="collapse"></a></li>
							<li><a data-action="reload"></a></li>
							<li><a data-action="close"></a></li>
						</ul>
					</div>
				</div>


				<div class="panel-body">

					<form method="post" class="form-horizontal"
						  action="{{ isset($object)?  '/' . app()->getLocale() .'/admin-panel/groups/'.$object->id :  '/' . app()->getLocale() .'/admin-panel/groups'  }}">
						{!! csrf_field() !!}
						@if(isset($object))
							<input type="hidden" name="_method" value="PATCH"/>
						@endif
						<fieldset class="content-group">
							<!-- 									<legend class="text-bold">Basic inputs</legend> -->


							<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="control-label col-lg-2">{{__('dashboard.enter_name_group')}}</label>
								<div class="col-lg-10">
									<input type="text" name="name"
										   value="{{ isset($object) ? $object->name  : old('name')  }}"
										   class="form-control" placeholder="{{__('dashboard.enter_name_group')}}">
									@if ($errors->has('name'))
										<span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
									@endif
								</div>

							</div>


							<div class="form-group prevs">
								<label class="control-label col-lg-2">{{__('dashboard.permissions')}}</label>

								<div class="col-lg-10">
									<?php $prevs = [];$i = 1;  ?>
									@foreach($privileges as $privilege)
										<?php    if (isset($object->id) && !empty($object->privileges)) {
											$prevs = isset($prArray)?$prArray:[];
										} ?>

										<section>
											<h3><label><input type="checkbox" name="privileges[]"
															  value="{{ $privilege->id }}"
															  {{in_array($privilege->id, $prevs)? "checked":''}}
													 />
													{{ __($privilege->privilge) }}
												</label></h3>
											@if(count($privilege->subProgrames->where('hidden',0)))
												<div class="children">
													@foreach($privilege->subProgrames->where('hidden',0) as $subProgram)

														<label><input type="checkbox" name="privileges[]"
																	  value="{{ $subProgram->id }}" <?php if (in_array($subProgram->id, $prevs)) {
																echo "checked";
															} ?> />
															{{__($subProgram->privilge)}}
														</label>&nbsp; &nbsp;
													@endforeach

												</div>
											@endif
										</section>
									@endforeach


								</div>
							</div>

						</fieldset>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">{{ isset($object)? __('dashboard.edit'):__('dashboard.add') }} {{__('dashboard.group')}}
								<i class="icon-arrow-left13 position-right"></i></button>
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
