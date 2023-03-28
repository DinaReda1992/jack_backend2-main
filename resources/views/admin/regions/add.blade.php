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
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.region') }} </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/regions"><button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.view_regions') }}</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li><a href="/admin-panel/regions">{{ __('dashboard.view_regions') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.region') }} </li>
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
                    <h5 class="panel-title">
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}{{ __('dashboard.region') }} </h5>
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
                        action="{{ isset($object) ?  '/' . app()->getLocale() .'/admin-panel/regions/' . $object->id :  '/' . app()->getLocale() .'/admin-panel/regions' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">


                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.select_country') }}</label>
                                <div class="col-lg-10">
                                    <select name="country_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_country') }}</option>
                                        @foreach (\App\Models\Countries::all() as $country)
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


                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2"> {{ __('dashboard.region_name_ar') }}
                                </label>
                                <div class="col-lg-10">
                                    <input type="text" name="name"
                                        value="{{ isset($object) ? $object->name : old('name') }}" class="form-control"
                                        placeholder="{{ __('dashboard.region_name_ar') }}">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2"> {{ __('dashboard.region_name_en') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name_en"
                                        value="{{ isset($object) ? $object->name_en : old('name_en') }}"
                                        class="form-control" placeholder=" {{ __('dashboard.region_name_en') }}">
                                    @if ($errors->has('name_en'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name_en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="form-group{{ $errors->has('longitude') || $errors->has('latitude') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.location_on_map') }}</label>
                                <div class="col-lg-10">
                                    <input type="hidden" id='lon' name="longitude" />
                                    <input type="hidden" id='lat' name="latitude" />
                                    <input type="hidden" name="country_code" value="">
                                    <input type="hidden" name="user_type_id" value="3">

                                    @include('admin.items.mapPlace')
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('photo') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.region_image') }} </label>
                                <div class="col-lg-5">
                                    <input type="file" name="photo" value="" class="form-control">

                                    <span class="help-block">
                                        <strong>{{ $errors->has('photo') ? $errors->has('photo') : '' }}</strong>
                                    </span>

                                </div>
                                @if (isset($object))
                                    @if (!empty($object->photo))
                                        <div class="col-lg-5">
                                            <img width="150" height="100px" src="/uploads/{{ $object->photo }}"
                                                alt="" />
                                            <span class="help-block">
                                                <strong>{{ __('dashboard.current_image') }}</strong>
                                            </span>

                                        </div>
                                    @endif
                                @endif
                            </div>


                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.region') }} <i class="icon-arrow-left13 position-right"></i></button>
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
    <script src="{{ asset('js/mapPlace.js') }}"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkQ8_neT4uZpVaXG1SbZNWKH1fnQHnbGk&libraries=places&callback=initMap&language={{ App::getLocale() }}"
        async defer></script>

    <script type="text/javascript">
        // Warning alert
        $(window).load(function() {
            @if (!isset($object))
                getCurrentLocation();
            @else
                {
                    @if ($object->latitude && $object->longitude)
                        getLocationName({{ $object->latitude }}, {{ $object->longitude }});
                    @else
                        getCurrentLocation();
                    @endif
                }
            @endif
        });
    </script>
@stop
