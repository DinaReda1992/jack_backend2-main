@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>

    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
    <link rel="stylesheet" href="/js/selectize/css/selectize.css">
    <script src="/js/selectize/js/standalone/selectize.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.country_id').change(function() {
                var country_id = $(this).val();
                $('.currency').val(country_id);
                $('.phonecode').val(country_id);
            });
            $('.user_type_id').change(function() {
                var change_val = $(this).val();
                if (change_val == 2) {
                    $('.previliges').show();
                } else {
                    $('.previliges').hide();
                }

            })
            $('.country_id').change(function() {
                var country_id = $('.country_id').val();
                $.ajax({
                    url: '/getStates/' + country_id,
                    success: function(data) {
                        $('.state_id').html(data);
                    }
                });

            });
            $('#has_regions').change(function() {
                if ($(this).is(":checked")) {
                    $('.choose-regions').show();
                } else {
                    $('.choose-regions').hide();

                }
            })

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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.supervisor') }}
                    </h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li><a href="/admin-panel/users">{{ __('dashboard.view_supervisors') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.supervisor') }}</li>
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
                        {{ __('dashboard.supervisor') }} </h5>
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
                        action="{{ isset($object) ? '/' . app()->getLocale() . '/admin-panel/users/' . $object->id : '/' . app()->getLocale() . '/admin-panel/users' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.supervisor_name') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="username" readonly onfocus="if (this.hasAttribute('readonly')) { this.removeAttribute('readonly');this.blur();this.focus();}"
                                        value="{{ isset($object) ? $object->username : old('username') }}"
                                        class="form-control" placeholder="{{ __('dashboard.supervisor_name') }}">
                                    <input type="hidden" name="user_type_id" value="2">
                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.email') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="email"
                                        value="{{ isset($object) ? $object->email : old('email') }}" class="form-control"
                                        placeholder="{{ __('dashboard.email') }}">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="form-group{{ $errors->has('phone') ? ' has-error' : '' }} {{ $errors->has('phonecode') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.phone') }}</label>
                                <div class="col-lg-7">
                                    <input type="text" name="phone"
                                        value="{{ isset($object) ? $object->phone : old('phone') }}" class="form-control"
                                        placeholder="{{ __('dashboard.phone') }}">
                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-lg-3">
                                    <select name="phonecode" class="form-control">
                                        <option value="">{{ __('dashboard.phonecode') }}</option>
                                        @foreach (\App\Models\Countries::all() as $code)
                                            <option value="{{ $code->phonecode }}"
                                                {{ isset($object) && $object->phonecode == $code->phonecode ? 'selected' : ($code->phonecode == 966 ? 'selected' : '') }}>
                                                +{{ $code->phonecode }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('phonecode'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phonecode') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="form-group{{ $errors->has('privilege_id') ? ' has-error' : '' }} {{ $errors->has('privilege_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.supervisor_type') }}</label>
                                <div class="col-lg-10">
                                    <select name="privilege_id" class="form-control">
                                        <option value="">{{ __('dashboard.supervisor_type') }}</option>
                                        @foreach (\App\Models\Groups::all() as $group)
                                            <option value="{{ $group->id }}"
                                                {{ isset($object) && $object->privilege_id == $group->id ? 'selected' : (old('privilege_id') == $group->id ? 'selected' : '') }}>
                                                {{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('privilege_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('privilege_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="">
                                    <label for="has_regions">
                                        <input type="checkbox" id="has_regions"
                                            {{ (isset($object) && $object->has_regions) || old('has_regions') ? 'checked' : '' }}
                                            name="has_regions"> {{ __('dashboard.has_regions') }}
                                    </label>

                                </div>
                            </div>

                            <div class="form-group choose-regions {{ $errors->has('regions') ? ' has-error' : '' }}"
                                style="display: {{ (isset($object) && $object->has_regions) || old('has_regions') ? 'block' : 'none' }};">
                                <label class="control-label col-lg-2">{{ __('dashboard.select_regions') }}</label>
                                <div class="col-lg-10">
                                    <select class="select-multiple-tokenization" multiple="multiple" name="regions[]">

                                        <?php $currentTypes = isset($current_regions) ? $current_regions : (old('regions') ?: []); ?>
                                        @foreach (\App\Models\Regions::where('is_archived', 0)->get() as $rest)
                                            <option value="{{ $rest->id }}" id="regions"
                                                {{ in_array($rest->id, $currentTypes) ? 'selected' : '' }}>
                                                {{ $rest->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('regions'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('regions') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.password') }}</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="{{ __('dashboard.password') }}">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.password_confirmation') }}</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="{{ __('dashboard.password_confirmation') }}">
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.profile_photo') }}</label>
                                <div class="col-lg-10">

                                    <input type="file" name="photo" class="file-input" data-show-caption="false"
                                        data-show-upload="false" data-browse-class="btn btn-primary btn-xs"
                                        data-remove-class="btn btn-default btn-xs">
                                    @if ($errors->has('photo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('photo') }}</strong>
                                        </span>
                                    @endif



                                </div>

                            </div>
                            @if (isset($object) && $object->photo)
                                <div class="form-group">
                                    <label class="control-label col-lg-2">{{ __('dashboard.current_image') }}</label>
                                    <div class="col-lg-10">
                                        <img alt="" width="100" height="75"
                                            src="/uploads/{{ $object->photo }}">
                                    </div>

                                </div>
                            @endif

                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.supervisor') }} <i class="icon-arrow-left13 position-right"></i></button>
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
