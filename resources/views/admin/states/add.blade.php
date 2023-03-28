@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script>
        $(function() {
            $('.country_id').change(function() {
                var country_id = $('.country_id').val();
                $.ajax({
                    url: '/admin-panel/getRegions/' + country_id,
                    success: function(data) {
                        $('.region_id').html(data);
                    }
                });

            });

        })
    </script>
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
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.city')}}</h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/states"><button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.view_cities') }}</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li><a href="/admin-panel/states">{{ __('dashboard.view_cities') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.city')}}</li>
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
                    <h5 class="panel-title">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.city')}} </h5>
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
                        action="{{ isset($object) ? '/' . app()->getLocale() .'/admin-panel/states/' . $object->id : '/' . app()->getLocale() .'/admin-panel/states' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.select_country') }}</label>
                                <div class="col-lg-10">
                                    <select name="country_id" class="form-control country_id">
                                        <option value="">{{ __('dashboard.select_country') }}</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ isset($object) && $object->country_id == $country->id ? 'selected' : (old('country_id') == $country->id ? 'selected' : '') }}>
                                                {{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('country_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('region_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2"> {{ __('dashboard.select_region') }} *</label>
                                <div class="col-lg-10">
                                    <select name="region_id" class="form-control region_id">

                                        <option value="">{{ __('dashboard.select_region') }}</option>
                                        @if (isset($object))
                                            @foreach (\App\Models\Regions::where('country_id', $object->country_id)->get() as $region)
                                                <option value="{{ $region->id }}"
                                                    {{ isset($object) && $object->region_id == $region->id ? 'selected' : (old('region_id') == $region->id ? 'selected' : '') }}>
                                                    {{ $region->name }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('region_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('region_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2"> {{ __('dashboard.city_name_ar') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name"
                                        value="{{ isset($object) ? $object->name : old('name') }}" class="form-control"
                                        placeholder=" {{ __('dashboard.city_name_ar') }}">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2"> {{ __('dashboard.city_name_en') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name_en"
                                        value="{{ isset($object) ? $object->name_en : old('name_en') }}"
                                        class="form-control" placeholder=" {{ __('dashboard.city_name_en') }}">
                                    @if ($errors->has('name_en'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name_en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('routeCode') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.route_code') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="routeCode"
                                        value="{{ isset($object) ? $object->routeCode : old('routeCode') }}"
                                        class="form-control" placeholder=" {{ __('dashboard.route_code') }}">
                                    @if ($errors->has('routeCode'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('routeCode') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('photo') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.city_image') }} </label>
                                <div class="col-lg-5">
                                    <input type="file" name="photo" value="" class="form-control">

                                    <span class="help-block">
                                        <strong>{{ $errors->has('photo') ? $errors->has('photo') :''}}</strong>
                                    </span>

                                </div>
                                @if (isset($object) && $object->photo)
                                    <div class="col-lg-5">
                                        <img width="150" height="100px" src="/uploads/{{ $object->photo }}"
                                            alt="" />
                                        <span class="help-block">
                                            <strong>{{ __('dashboard.current_image') }}</strong>
                                        </span>

                                    </div>
                                @endif
                            </div>


                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{__('dashboard.city')}} <i class="icon-arrow-left13 position-right"></i></button>
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
