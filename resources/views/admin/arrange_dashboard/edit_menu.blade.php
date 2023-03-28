@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>

    <link href="/assets/css/menu.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/assets/js/notify.js"></script>

    <!-- /theme JS files -->

@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($privileges)? 'تعديل':'إضافة' }} قائمة</h4>
						</div>
{{--						<div class="heading-elements">--}}
{{--						<div class="heading-btn-group">--}}
{{--							<a href="/provider-panel/menus"><button type="button" class="btn btn-primary" name="button">  عرض القوائم</button></a>--}}
{{--						</div>--}}
{{--					</div>--}}

					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li class="active">تعديل القائمة</li>
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
							<h5 class="panel-title">{{ isset($privileges)? 'تعديل':'إضافة' }} قائمة </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
                    </div>
                        <div>
<div class="col-lg-7" style="background-color: #fff;    margin-right: 13px;">
						<div class="panel-body">
                            <fieldset>
                                    <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">عناصر القائمة </label>
                                        <div class="col-lg-12">
                                            <div class="cf nestable-lists">

                                                <div class="dd" id="nestable3">

                                                        {{displayList($privileges)}}
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </fieldset>

                                <div class="text-right">
                                    <input type="hidden" name="menu_id" value="">
                                    <form id="menu_form">
                                        <button type="submit" class="btn btn-primary">حفظ التغييرات <i class="icon-arrow-left13 position-right"></i></button>

                                    </form>
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
            <script type="text/javascript" src="/assets/js/plugins/menu/jquery.nestable.js"></script>

            <script>

                $(document).ready(function()
                {


                    // activate Nestable for list 1

                   ff= $('#nestable3').nestable({'maxDepth':2});

                });
    </script>
            <script type="text/javascript" src="/assets/js/admin/arrangedashboard.js"></script>

            <?php
    function displayList($list) {
    ?>
            <ol class="dd-list">
                <?php foreach($list as $item): ?>
                    <li class="dd-item dd3-item" data-id="<?php echo $item["id"]; ?>">
                        <div class="dd-handle dd3-handle"></div><div class="dd3-content" style="font-size:{{$item->parent_id==0?'15px':'10px'}}"><?php echo $item["privilge"]; ?>
{{--                            <a onclick="return false;" item_id="{{$item->id}}"  class="delete-item menu-setting" href="#"><i class="icon-trash"></i></a>--}}
                            <!--i class="icon-gear menu-setting"></i-->
                        </div>


                <?php if (count($item->subProgrames)): ?>
                        <?php displayList($item->subProgrames); ?>
                        <?php endif; ?>

                    </li>
                <?php endforeach; ?>
            </ol>
            <?php
            }
            ?>
<script>
    $(document).ready(function(){
    });
</script>
@stop
