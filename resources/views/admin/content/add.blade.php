@extends('admin.layout')
@section('js_files')
    <script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>

    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
    <!-- /theme JS files -->
    <!-- /theme JS files -->

@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.page_content') }}
                    </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/content"><button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.view_pages') }}</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.page_content') }}</li>
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
                    <h5 class="panel-title">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.page_content') }}
                    </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                    <div style=" text-align: center;">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="active"><a data-toggle="tab" href="#arabic">{{ __('dashboard.ar_lang') }}</a></li>
                            <li><a data-toggle="tab" href="#english">{{ __('dashboard.en_lang') }}</a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">
                    <form enctype="multipart/form-data" method="post" class="form-horizontal"
                        action="{{ isset($object) ?  '/' . app()->getLocale() .'/admin-panel/content/' . $object->id :  '/' . app()->getLocale() .'/admin-panel/content' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif

                        <div class="tab-content">
                            <div id="arabic" class="tab-pane fade in active ">

                                <fieldset class="content-group">
                                    <!-- 									<legend class="text-bold">Basic inputs</legend> -->
                                    <div class="form-group{{ $errors->has('page_name') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.page_name') }} </label>
                                        <div class="col-lg-10">
                                            <input type="text" name="page_name"
                                                value="{{ isset($object) ? $object->page_name : old('page_name') }}"
                                                class="form-control" placeholder="{{ __('dashboard.page_name') }}">
                                            @if ($errors->has('page_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('page_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">

                                        <label class="control-label col-lg-2">{{ __('dashboard.page_content') }} </label>
                                        <div class="col-lg-10">

                                            <textarea name="content" class="form-control summernote" rows="8" cols="100">{{ isset($object) ? $object->content : old('content') }}</textarea>

                                            @if ($errors->has('content'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('content') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>


                                </fieldset>


                            </div>
                            <div id="english" class="tab-pane fade ">

                                <fieldset class="content-group">

                                    <div class="form-group{{ $errors->has('page_name_en') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.page_name_en') }} </label>
                                        <div class="col-lg-10">
                                            <input type="text" name="page_name_en"
                                                value="{{ isset($object) ? $object->page_name_en : old('page_name_en') }}"
                                                class="form-control" placeholder="{{ __('dashboard.page_name_en') }}">
                                            @if ($errors->has('page_name_en'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('page_name_en') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>




                                    <div class="form-group{{ $errors->has('content_en') ? ' has-error' : '' }}">

                                        <label class="control-label col-lg-2">{{ __('dashboard.page_content_en') }}
                                        </label>
                                        <div class="col-lg-10" style="direction: ltr">

                                            <textarea name="content_en" class="form-control summernote" rows="8" cols="100">{{ isset($object) ? $object->content_en : old('content_en') }}</textarea>

                                            @if ($errors->has('content_en'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('content_en') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>


                                </fieldset>


                            </div>
                            <div id="extra" class="tab-pane fade ">

                                <fieldset class="content-group">
                                    <!-- 									<legend class="text-bold">Basic inputs</legend> -->

                                    <div class="form-group {{ $errors->has('cover') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">صورة الخلفية </label>
                                        <div class="col-lg-5">
                                            <input type="file" name="cover" value="" class="form-control">

                                            <span class="help-block">
                                                <strong>{{ $errors->has('cover') ? $errors->has('cover') : 'قم برفع صورة تظهر اعل الصفحة' }}</strong>
                                            </span>

                                        </div>
                                        @if (isset($object))
                                            @if (!empty($object->cover))
                                                <div class="col-lg-5">
                                                    <img width="150" height="100px" src="/uploads/{{ $object->cover }}"
                                                        alt="" />
                                                    <span class="help-block">
                                                        <strong>{{ __('dashboard.current_image') }}</strong>
                                                    </span>

                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <div class="form-group {{ $errors->has('photo') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">صورة الصفحة </label>
                                        <div class="col-lg-5">
                                            <input type="file" name="photo" value="" class="form-control">

                                            <span class="help-block">
                                                <strong>{{ $errors->has('photo') ? $errors->has('photo') : 'قم برفع صورة صفحة جانبية' }}</strong>
                                            </span>

                                        </div>
                                        @if (isset($object))
                                            @if (!empty($object->photo))
                                                <div class="col-lg-5">
                                                    <img width="150" height="100px"
                                                        src="/uploads/{{ $object->photo }}" alt="" />
                                                    <span class="help-block">
                                                        <strong>{{ __('dashboard.current_image') }}</strong>
                                                    </span>

                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="form-group {{ $errors->has('photo2') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">صورة الصفحة اضافية </label>
                                        <div class="col-lg-5">
                                            <input type="file" name="photo2" value="" class="form-control">

                                            <span class="help-block">
                                                <strong>{{ $errors->has('photo2') ? $errors->has('photo2') : 'قم برفع صورة الصفحة الثانية' }}</strong>
                                            </span>

                                        </div>
                                        @if (isset($object))
                                            @if (!empty($object->photo2))
                                                <div class="col-lg-5">
                                                    <img width="150" height="100px"
                                                        src="/uploads/{{ $object->photo2 }}" alt="" />
                                                    <span class="help-block">
                                                        <strong>{{ __('dashboard.current_image') }}</strong>
                                                    </span>

                                                </div>
                                            @endif
                                        @endif
                                    </div>



                                </fieldset>


                            </div>

                        </div>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.page_content') }} <i
                                    class="icon-arrow-left13 position-right"></i></button>
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
