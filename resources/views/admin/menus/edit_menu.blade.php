@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>

    <link href="/assets/css/menu.css" rel="stylesheet" type="text/css">

    <!-- /theme JS files -->

@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} قائمة</h4>
						</div>
						<div class="heading-elements">
						<div class="heading-btn-group">
							<a href="/admin-panel/menus"><button type="button" class="btn btn-primary" name="button">  عرض القوائم</button></a>
						</div>
					</div>

					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} قائمة </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
                        </div
                        <div>
<div class="col-lg-4" style="background-color: #fff;">
    <form method="post" class="form-horizontal" action="/admin-panel/menuItems">
        {!! csrf_field() !!}
        <h3>اضف عنصر جديد</h3>
    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        <label class="control-label col-lg-12">اسم العنصر</label>
        <div class="col-lg-12">
            <input type="text" name="name"  class="form-control" placeholder="ادخل اسم العنصر">

            @if($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
										 </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('parent_id') ? ' has-error' : '' }}">
        <label class="control-label col-lg-12">إختر القائمة التابعة</label>
        <div class="col-lg-12">
            <select name="parent_id" id="parent_item" class="form-control">
                <option value="">اختر القائمة الاب</option>
                @foreach(\App\Models\Menus::where("menu_id",$object->id)->get() as $menu)
                    <option value="{{ $menu->id }}" >{{ $menu->name }}</option>
                @endforeach
            </select>
            @if($errors->has('parent_id'))
                <span class="help-block">
												 <strong>{{ $errors->first('parent_id') }}</strong>
										 </span>
            @endif
        </div>
    </div>
    <hr>


    <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
        <label class="control-label col-lg-12">أدخل رابط الصفحة </label>
        <div class="col-lg-12">
            <input type="text" name="link" value="{{ isset($object) ? $object->link  : old('link')  }}" class="form-control" placeholder="أدخل رابط الصفحة">
	<span class="help-block">
		ادخل رابط الصفحة ( ان وجد )
	</span>
            @if ($errors->has('link'))
                <span class="help-block">
													<strong>{{ $errors->first('link') }}</strong>
											</span>
            @endif
        </div>
        <label class="control-label col-lg-12">او</label>
        <div class="col-lg-12">
            <select name="page_id" class="form-control">
                <option value="">اختر صفحة</option>
                @foreach(\App\Models\Content::all() as $content)
                    <option value="{{ $content->id }}"  {{ isset($object) && $object->page_id==$content->id ? 'selected' : (old('page_id') == $content->id ? 'selected' : '') }}>{{ $content->page_name }}</option>
                @endforeach
            </select>
            @if ($errors->has('page_id'))
                <span class="help-block">
														<strong>{{ $errors->first('page_id') }}</strong>
												</span>
            @endif
        </div>
    </div>


    <div class="text-right">
        <input type="hidden" name="menu_id" value="{{$object->id}}">
        <button type="submit" class="btn btn-primary">اضف الى القائمة<i class="icon-arrow-left13 position-right"></i></button>
    </div>
</form>
</div>
<div class="col-lg-7" style="background-color: #fff;    margin-right: 13px;width: 65.333333%">
						<div class="panel-body">

							<form method="post" id="menu_form" class="form-horizontal" action="" >
								{!! csrf_field() !!}
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->

<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
	<label class="control-label col-lg-2">اسم القائمة</label>
	<div class="col-lg-10">
	<input type="text" name="menu_name" value="{{ isset($object) ? $object->menu_name  : old('menu_name')  }}" class="form-control" placeholder="أدخل اسم القائمة">
									@if ($errors->has('menu_name'))
											<span class="help-block">
													<strong>{{ $errors->first('menu_name') }}</strong>
											</span>
									@endif
	</div>
</div>
								</fieldset>
                                <fieldset class="content-group">
                                    <!-- 									<legend class="text-bold">Basic inputs</legend> -->

<hr>
                                    <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">عناصر القائمة </label>
                                        <div class="col-lg-12">
                                            <div class="cf nestable-lists">

                                                <div class="dd" id="nestable3">

                                                        {{displayList($object->getItems->where('parent_id',0))}}
                                                </div>

                                            </div>

                                        </div>
                                    </div>



                                </fieldset>

                                <div class="text-right">
                                    <input type="hidden" name="menu_id" value="{{$object->id}}">
									<button type="submit" class="btn btn-primary">{{ count($object->getItems)? 'تعديل':'إضافة' }} قائمة <i class="icon-arrow-left13 position-right"></i></button>
								</div>
							</form>
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

                   ff= $('#nestable3').nestable();

                });
    </script>
            <script type="text/javascript" src="/assets/js/admin/menu.js"></script>

            <?php
    function displayList($list) {
    ?>
            <ol class="dd-list">
                <?php foreach($list as $item): ?>
                    <li class="dd-item dd3-item" data-id="<?php echo $item["id"]; ?>">
                        <div class="dd-handle dd3-handle">Drag</div><div class="dd3-content"><?php echo $item["name"]; ?>
                            <a onclick="return false;" item_id="{{$item->id}}"  class="delete-item menu-setting" href="#"><i class="icon-trash"></i></a>
                            <!--i class="icon-gear menu-setting"></i-->
                        </div>


                <?php if (count($item->get_sub_menus)): ?>
                        <?php displayList($item->get_sub_menus); ?>
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
