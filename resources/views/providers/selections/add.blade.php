@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript">
        $(document).ready(function () {
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
        $(document).ready(function () {
            $('.add-advantage').click(function () {
                var type = $(this).attr('type');
                @if(@$object->parent)

                     $('.append-at' + type).append('<div class="col-lg-2">-</div><div class="col-lg-4">\n' +
                    '                                                        <select  class="form-control"  name="option_parent_id[]" >\n' +
                    '                                                            <option value=""> إختر الأوبشن الرئيسي</option>\n' +
                    '                                                            @foreach($object->parent->options as $option)\n' +
                    '                                                            <option value="{{ $option->id }}"> {{ $option->name }}</option>\n' +
                    '                                                            @endforeach    \n' +
                    '                                                        </select>\n' +
                    '                                                    </div><div class="col-lg-6"><input type="text"  name="option_name[]" value="" class="form-control" placeholder="الأوبشن بالعربية "></div>' +
                    '' +
                    '<div class="clearfix"></div><br><div class="desc" style="display: none"></div><div class="clearfix"></div>');
                @else
                $('.append-at' + type).append('<div class="col-lg-2">-</div><div class="col-lg-10"><input type="text"  name="option_name[]" value="" class="form-control" placeholder="الأوبشن بالعربية "></div><div class="clearfix"></div><br><div class="desc" style="display: none"></div><div class="clearfix"></div>');

                @endif

                $('.action-create').click();
            });
            $(window).load(function () {
                $('.action-create').click();
            });
            $(document).on('change', '.icon_class', function () {
                var icon = $(this).val();
                if (icon == "fa-warning") {
                    $(this).parent('div').next().next().next('.desc').show();
                } else {
                    $(this).parent('div').next().next().next('.desc').hide();
                }
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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span>
                        - {{ isset($object)? 'تعديل':'إضافة' }} خاصية</h4>
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
                    <li><a href="/provider-panel/selections">عرض الخصائص</a></li>
                    @if(@$object)
                    <li><a href="/provider-panel/selections/{{ $object->id }}">التحكم في الاوبشنز</a></li>
                    @endif
                    <li class="active">{{ isset($object)? 'تعديل':'إضافة' }} خاصية</li>
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
                    <h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} خاصية </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>


                <div class="panel-body">

                    <form enctype="multipart/form-data" method="post" class="form-horizontal"
                          action="{{ isset($object)? '/provider-panel/selections/'.$object->id : '/provider-panel/selections'  }}">
                        {!! csrf_field() !!}
                        @if(isset($object))
                            <input type="hidden" name="_method" value="PATCH"/>
                        @endif
                        <fieldset class="content-group">


                            <div class="form-group{{ $errors->has('name') || $errors->has('name_en')  ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">أدخل عنوان الخاصية </label>
                                <div class="col-lg-10">
                                    <input type="text" name="name"
                                           value="{{ isset($object) ? $object->name  : old('name')  }}"
                                           class="form-control" placeholder="أدخل الخاصية بالعربية ">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
                                    @endif
                                </div>

                            </div>

                            @php $parent_id = isset($object) && $object->parent_id  ? $object->parent_id  : old('parent_id')  @endphp
                            <div class="form-group{{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">تابع لخاصية أخرى ؟ </label>
                                <div class="col-lg-10">
                                    <select name="parent_id" class="form-control category_id">
                                        <option value="">إختر الخاصية التابع لها</option>
                                        @foreach(\App\Models\Selections::where('parent_id',0)->orderBy('sort','asc')->get() as $category)
                                            <option value="{{ $category->id }}" {{ $parent_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('parent_id'))
                                        <span class="help-block">
                            				<strong>{{ $errors->first('parent_id') }}</strong>
                            			</span>
                                    @endif
                                </div>
                            </div>


                            {{--                                <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">--}}
                            {{--                                    <label class="control-label col-lg-2">أدخل صورة القسم</label>--}}
                            {{--                                    <div class="col-lg-10">--}}

                            {{--                                        <input type="file" name="photo" class="file-input" data-show-caption="false"--}}
                            {{--                                               data-show-upload="false" data-browse-class="btn btn-primary btn-xs"--}}
                            {{--                                               data-remove-class="btn btn-default btn-xs">--}}
                            {{--                                        @if ($errors->has('photo'))--}}
                            {{--                                            <span class="help-block">--}}
                            {{--									                                        <strong>{{ $errors->first('photo') }}</strong>--}}
                            {{--									                                    </span>--}}
                            {{--                                        @endif--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                                @if(isset($object->photo))--}}
                            {{--                                    <div class="form-group">--}}
                            {{--                                        <label class="control-label col-lg-2">الصورة الحالية</label>--}}
                            {{--                                        <div class="col-lg-10">--}}
                            {{--                                            <img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">--}}
                            {{--                                        </div>--}}

                            {{--                                    </div>--}}
                            {{--                            @endif--}}
                            @if(@$object)
                            <div class="form-group">
                                <label class="control-label col-lg-2">الأوبشنز </label>
                                @if(!isset($object) || $object->options->count()==0)
                                    <div class="append-at1">
                                        @if($object->parent)
                                            <div class="col-lg-4">
                                                <select class="form-control"  name="option_parent_id[]" >
                                                    <option value=""> إختر الأوبشن الرئيسي</option>
                                                    @foreach($object->parent->options as $option)
                                                        <option value="{{ $option->id }}" > {{ $option->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" name="option_name[]"
                                                       class="form-control" placeholder="الأوبشن بالعربية ">
                                            </div>

                                        @else
                                        <div class="col-lg-10">
                                            <input type="text" name="option_name[]" value="" class="form-control"
                                                   placeholder="الأوبشن بالعربية ">
                                        </div>

                                        @endif
                                        <div class="clearfix"></div>
                                        <br>
                                    </div>
                                @else
                                    <div class="append-at1">
                                        @php $i=0 @endphp
                                        @foreach($object->options as $adv)
                                            @if($i!=0)    <br>
                                            <div class="col-lg-2">-</div> @endif
                                            @if($object->parent)
                                                    <div class="col-lg-4">
                                                        <select class="form-control" name="" >
                                                            <option value=""> إختر الأوبشن الرئيسي</option>
                                                            @foreach($object->parent->options as $option)
                                                            <option value="{{ $option->id }}" {{ $option->id == $adv->parent_id ? 'selected':'' }}> {{ $option->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                            <div class="col-lg-6">
                                                <input type="text" name="" value="{{ $adv->name }}"
                                                       class="form-control" placeholder="الأوبشن بالعربية ">
                                            </div>

                                                @else
                                                    <div class="col-lg-10">
                                                        <input type="text" name="" value="{{ $adv->name }}"
                                                               class="form-control" placeholder="الأوبشن بالعربية ">
                                                    </div>

                                                @endif
                                            <div class="clearfix"></div>
                                            @php $i++ @endphp
                                        @endforeach
                                        <br>

                                    </div>
                                @endif
                                <div class="clearfix"></div>
                                <br>
                                <a onclick="return false" type="1" class="btn btn-primary add-advantage pull-right">أضف
                                    تفصيل آخر</a>

                            </div>
                            <div class="clearfix"></div>
                            <br>
                            @endif

                        </fieldset>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الخاصية
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