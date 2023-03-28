@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
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
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span>
                        - {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.slider') }}
                        ({{ $mainSlider->name }})
                    </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/slider?main_slider={{ $mainSlider->id }}">
                            <button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.view_sliders') }}</button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a>
                    </li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.slider') }}</li>
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
                        {{ __('dashboard.slider') }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>


                <div class="panel-body">

                    <form method="post" enctype="multipart/form-data" class="form-horizontal"
                        action="{{ isset($object) ? '/admin-panel/slider/' . $object->id . '?main_slider=' . $mainSlider->id : '/admin-panel/slider?main_slider=' . $mainSlider->id }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">
                                    {{ __('dashboard.slider_title') }}
                                    <span style="color: #f90000;font-size: 15px;font-weight: bold;">*</span>
                                </label>
                                <div class="col-lg-10">
                                    <input type="text" name="title"
                                        value="{{ isset($object) ? $object->title : old('title') }}" class="form-control"
                                        placeholder="{{ __('dashboard.slider_title') }}">
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('title_en') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">
                                    {{ __('dashboard.slider_title_en') }}
                                    <span style="color: #f90000;font-size: 15px;font-weight: bold;">*</span>
                                </label>
                                <div class="col-lg-10">
                                    <input type="text" name="title_en"
                                        value="{{ isset($object) ? $object->title_en : old('title_en') }}"
                                        class="form-control" placeholder="{{ __('dashboard.slider_title_en') }}">
                                    @if ($errors->has('title_en'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title_en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.slider_description') }}</label>
                                <div class="col-lg-10">
                                    <textarea name="description" class="form-control" placeholder="{{ __('dashboard.slider_description') }}">{{ isset($object) ? $object->description : old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.slider_description_en') }}</label>
                                <div class="col-lg-10">
                                    <textarea name="description_en"class="form-control" placeholder="{{ __('dashboard.slider_description_en') }}">{{ isset($object) ? $object->description_en : old('description_en') }}</textarea>
                                    @if ($errors->has('description_en'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('description_en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div @if ($mainSlider && ($mainSlider->id == 4 || $mainSlider->id == 5)) style="display: none;" @endif
                                class="form-group{{ $errors->has('button_title') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{__('dashboard.button_title')}} </label>
                                <div class="col-lg-10">
                                    <input type="text" name="button_title"
                                        value="{{ isset($object) ? $object->button_title : old('button_title') }}"
                                        class="form-control"
                                        placeholder="{{__('dashboard.button_title')}} ">
                                    @if ($errors->has('button_title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('button_title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div @if ($mainSlider && ($mainSlider->id == 4 || $mainSlider->id == 5)) style="display: none;" @endif
                                class="form-group{{ $errors->has('button_title_en') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{__('dashboard.button_title_en')}} </label>
                                <div class="col-lg-10">
                                    <input type="text" name="button_title_en"
                                        value="{{ isset($object) ? $object->button_title_en : old('button_title_en') }}"
                                        class="form-control"
                                        placeholder="{{__('dashboard.button_title_en')}} ">
                                    @if ($errors->has('button_title_en'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('button_title_en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div @if ($mainSlider && ($mainSlider->id == 8 || $mainSlider->id == 4 || $mainSlider->id == 5)) style="display: none;" @endif
                                class="form-group{{ $errors->has('button_url') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.button_link') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="button_url"
                                        value="{{ isset($object) ? $object->button_url : old('button_url') }}"
                                        class="form-control" placeholder="{{ __('dashboard.button_link') }}">
                                    @if ($errors->has('button_url'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('button_url') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div @if ($mainSlider && $mainSlider->id == 8) style="display: none;" @endif
                                class="form-group {{ $errors->has('main_slider_id') ? ' has-error' : '' }}">
                                <label class="control-label col-md-2"> {{ __('dashboard.choose_type_of_slider') }} <span
                                        style="color: #f90000;font-size: 15px;font-weight: bold;">*</span></label>
                                <div class="col-lg-10">
                                    <select name="main_slider_id"
                                        class="category_id form-control select-multiple-tokenization">
                                        <option value="">{{ __('dashboard.choose_type_of_slider') }}</option>
                                        @foreach ($allMainSliders as $allMainSlider)
                                            <option value="{{ $allMainSlider->id }}"
                                                {{ isset($object) && $object->main_slider_id == $allMainSlider->id ? 'selected' : (old('main_slider_id') == $allMainSlider->id || $mainSlider->id == $allMainSlider->id ? 'selected' : '') }}>
                                                {{ $allMainSlider->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('main_slider_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('main_slider_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label col-lg-2">{{__('dashboard.text_color')}}</label>
                                <div class="{{ 'col-lg-10' }}">
                                    <input type="color" name="text_color"
                                        value="{{ isset($object) ? $object->text_color : old('text_color') }}"
                                        class="form-control option">
                                        @if ($errors->has('text_color'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('text_color') }}</strong>
                                        </span>
                                    @endif
                                </div>
                               
                                {{-- @if ($value1->input_type == 'color')
                                    <div class="col-md-2">
                                        <a onclick="resetColor('.option{{$value1->id}}')"><i style="font-size: 36px;" class="fa fa-refresh"></i></a>
                                    </div>
                                @endif --}}

                            </div>


                            <div class="form-group {{ $errors->has('photo') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">
                                    {{ __('dashboard.slider_image') }}
                                    <span style="color: #f90000;font-size: 15px;font-weight: bold;">*</span>
                                </label>
                                <div class="col-lg-5">
                                    <input type="file" name="photo" value="" class="form-control">

                                    <span class="help-block">
                                        <strong>{{ $errors->has('photo') ? $errors->has('photo') : ' ' }}</strong>
                                    </span>

                                </div>
                                @if (isset($object) && !empty($object->photo))
                                    <div class="col-lg-5">
                                        <img width="150" height="100px" src="/uploads/{{ $object->photo }}"
                                            alt="" />
                                        <span class="help-block">
                                            <strong>{{ __('dashboard.current_image') }}</strong>
                                        </span>

                                    </div>
                                @endif


                            </div>
                            {{-- <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}"> --}}

                            {{-- <label class="control-label col-lg-2">محتوى ال{{ __('dashboard.slider') }} </label> --}}
                            {{-- <div class="col-lg-10"> --}}

                            {{-- <textarea name="description" class="form-control summernote" rows="8" cols="100">{{ isset($object) ? $object->description  : old('description')  }}</textarea> --}}

                            {{-- @if ($errors->has('description')) --}}
                            {{-- <span class="help-block"> --}}
                            {{-- <strong>{{ $errors->first('description') }}</strong> --}}
                            {{-- </span> --}}
                            {{-- @endif --}}
                            {{-- </div> --}}
                            {{-- </div> --}}

                            <div class="form-group">
                                <div class="">
                                    <label for="checkbox">
                                        <input type="radio" id="locale"
                                            {{ isset($object) && $object->locale == 2 ? 'checked' : '' }}
                                            value="{{ \App\Entities\LocaleType::EN }}" name="_locale">
                                        {{ __('dashboard.en_lang') }} ؟
                                    </label>

                                    <label for="checkbox">
                                        <input type="radio" id="locale"
                                            {{ isset($object) && $object->locale == 1 ? 'checked' : '' }}
                                            value="{{ \App\Entities\LocaleType::AR }}" name="_locale">
                                        {{ __('dashboard.ar_lang') }} ؟
                                    </label>

                                    <label for="checkbox">
                                        <input type="radio" id="locale"
                                            {{ (isset($object) && $object->locale == 3) || !isset($object) ? 'checked' : '' }}
                                            value="{{ \App\Entities\LocaleType::BOTH }}"
                                            name="_locale">{{ __('dashboard.both') }}
                                    </label>
                                </div>
                            </div>


                            {{--                            <div class="form-group"> --}}
                            {{--                                <div class=""> --}}
                            {{--                                    <label for="checkbox"> --}}
                            {{--                                        <input type="checkbox" id="has_link" --}}
                            {{--                                            {{ (isset($object) && $object->has_link) || $mainSlider->id == 8 ? 'checked' : '' }} --}}
                            {{--                                            name="has_link"> --}}
                            {{--                                        @if ($mainSlider->id == 8) --}}
                            {{--                                            {{ __('dashboard.choose_type_offer') }} --}}
                            {{--                                        @else --}}
                            {{--                                          {{ __('dashboard.is_it_related_to_an_offer') }}({{ __('dashboard.category') }} - {{ __('dashboard.brand') }} - {{ __('dashboard.product') }}) --}}
                            {{--                                        @endif --}}
                            {{--                                    </label> --}}
                            {{--                                </div> --}}
                            {{--                            </div> --}}

                            {{--                            <fieldset class="content-group" id="item-content" --}}
                            {{--                                @if ((!isset($object) || !$object->has_link) && $mainSlider->id != 8) style="display: none" @endif> --}}
                            {{--                                <div class="form-group {{ $errors->has('item_type') ? ' has-error' : '' }}"> --}}
                            {{--                                    <label class="control-label col-md-2"> {{ __('dashboard.choose_type_element') }}</label> --}}
                            {{--                                    <div class="col-lg-10"> --}}
                            {{--                                        <select name="item_type" --}}
                            {{--                                            class="category_id form-control select-multiple-tokenization"> --}}
                            {{--                                            <option value="">{{ __('dashboard.choose_type_element') }}</option> --}}
                            {{--                                            @foreach ($itemTypes as $itemType) --}}
                            {{--                                                <option value="{{ $itemType->id }}" --}}
                            {{--                                                    {{ isset($object) && $object->item_type == $itemType->id ? 'selected' : (old('item_type') == $itemType->id ? 'selected' : '') }}> --}}
                            {{--                                                    {{ $itemType->name }}</option> --}}
                            {{--                                            @endforeach --}}
                            {{--                                        </select> --}}
                            {{--                                        @if ($errors->has('item_type')) --}}
                            {{--                                            <span class="help-block"> --}}
                            {{--                                                <strong>{{ $errors->first('item_type') }}</strong> --}}
                            {{--                                            </span> --}}
                            {{--                                        @endif --}}
                            {{--                                    </div> --}}
                            {{--                                </div> --}}

                            {{--                                @if ($mainSlider->id != 8) --}}
                            {{--                                    <div class="form-group{{ $errors->has('item_id') ? ' has-error' : '' }}"> --}}
                            {{--                                        <label class="control-label col-lg-2"> --}}
                            {{--                                            {{ __('dashboard.elment_id') }} --}}
                            {{--                                        </label> --}}
                            {{--                                        <div class="col-lg-10"> --}}
                            {{--                                            <input type="number" name="item_id" --}}
                            {{--                                                value="{{ isset($object) ? $object->item_id : old('item_id') }}" --}}
                            {{--                                                class="form-control" placeholder="{{ __('dashboard.elment_id') }}"> --}}
                            {{--                                            @if ($errors->has('item_id')) --}}
                            {{--                                                <span class="help-block"> --}}
                            {{--                                                    <strong>{{ $errors->first('item_id') }}</strong> --}}
                            {{--                                                </span> --}}
                            {{--                                            @endif --}}
                            {{--                                        </div> --}}
                            {{--                                    </div> --}}
                            {{--                                @endif --}}
                            {{--                            </fieldset> --}}

                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.slider') }}
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

        <script>
            $(function() {
                $('#has_link').on('change', function(e) {
                    const has_link = $('#has_link').is(':checked');
                    if (has_link) {
                        $('#item-content').show();
                    } else {
                        $('#item-content').hide();
                    }
                });
            });
        </script>

    </div>
@stop
