@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
    <script type="text/javascript" src="/assets/js/admin/cbFamily.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("h3 input:checkbox").cbFamily(function() {
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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.discount_coupon') }}
                    </h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }} </a>
                    </li>
                    <li><a href="/admin-panel/cobons">{{ __('dashboard.view_discount_coupons') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.discount_coupon') }} </li>
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
                        {{ __('dashboard.discount_coupon') }} </h5>
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
                        action="{{ isset($object) ? '/' . app()->getLocale() . '/admin-panel/cobons/' . $object->id : '/' . app()->getLocale() . '/admin-panel/cobons' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.enter_the_coupon_code') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="code"
                                        value="{{ isset($object) ? $object->code : substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10) }}"
                                        class="form-control" placeholder="{{ __('dashboard.enter_the_coupon_code') }}">
                                    <span class="help-block">
                                        <strong>{{ __('dashboard.Note: It is preferable not to modify the discount code, as it is automatically generated') }}</strong>
                                    </span>
                                    @if ($errors->has('code'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('code') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>

                            <div class="form-group{{ $errors->has('percent') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.discount_percentage') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="percent"
                                        value="{{ isset($object) ? $object->percent : old('percent') }}"
                                        class="form-control" placeholder="{{ __('dashboard.discount_percentage') }}">
                                    <span
                                        class="help-block">{{ __('dashboard.Note: Only write a number, as it is a percentage, for example (10)') }}</span>
                                    @if ($errors->has('percent'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('percent') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>

                            <div class="form-group{{ $errors->has('days') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.coupon_validity_period') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="days"
                                        value="{{ isset($object) ? $object->days : old('days') }}" class="form-control"
                                        placeholder="{{ __('dashboard.coupon_validity_period') }}">
                                    <span
                                        class="help-block">{{ __('dashboard.Note: The coupon validity period is written in days, for example (10)') }}</span>
                                    @if ($errors->has('days'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('days') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>

                            <div class="form-group{{ $errors->has('usage_quota') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.coupon_usage_times') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="usage_quota"
                                        value="{{ isset($object) ? $object->usage_quota : old('usage_quota') }}"
                                        class="form-control" placeholder="{{ __('dashboard.coupon_usage_times') }}">
                                    <span
                                        class="help-block">{{ __('dashboard.The number of times the coupon is allowed to be used by users') }}</span>
                                    @if ($errors->has('usage_quota'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('usage_quota') }}</strong>
                                        </span>
                                    @endif
                                </div>


                            </div>
                            <div class="form-group{{ $errors->has('max_money') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.maximum_discount') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="max_money"
                                        value="{{ isset($object) ? $object->max_money : old('max_money') }}"
                                        class="form-control" placeholder="{{ __('dashboard.maximum_discount') }}">
                                    <span
                                        class="help-block">{{ __('dashboard.Determine the maximum amount that the discount can reach (write 0 in case there is no maximum)') }}</span>
                                    @if ($errors->has('max_money'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('max_money') }}</strong>
                                        </span>
                                    @endif
                                </div>


                            </div>

                            <div class="form-group">
                                <div class="col-lg-10">
                                    <label class="checkbox-inline">
                                        <input type="radio" name="link_type" value="category"
                                            {{ isset($object) ? ($object->link_type == 'category' ? 'checked' : '') : (old('link_type') == 'category' ? 'checked' : (!old('link_type') ? 'checked' : '')) }}>
                                        {{ __('dashboard.categories') }}
                                    </label>
                                    {{-- <label class="checkbox-inline">
                                        <input type="radio" name="link_type" value="provider"
                                            {{ isset($object) ? ($object->link_type == 'provider' ? 'checked' : '') : (old('link_type') == 'provider' ? 'checked' : '') }}>
                                        {{ __('dashboard.suppliers') }}
                                    </label>
 --}}
                                </div>
                            </div>


                            <section
                                @if ((isset($object) && $object->link_type == 'category') ||
                                    (!isset($object) && (old('link_type') == 'category' || !old('link_type')))) style="display: block;margin-right: 55px;" @else style="display: none;margin-right: 55px;" @endif
                                id="categories-sec">
                                <h3><label><input type="checkbox" name="categories[]" />
                                        {{ __('dashboard.categories') }}
                                    </label></h3>
                                <div class="children">
                                    @foreach (\App\Models\Categories::where('stop', 0)->where('is_archived', 0)->get() as $category)
                                        <label><input type="checkbox" name="category[]" value="{{ $category->id }}"
                                                {{ isset($prev_categories) && in_array($category->id, $prev_categories) ? 'checked' : '' }} />
                                            {{ $category->name }}
                                        </label>&nbsp; &nbsp;
                                    @endforeach

                                </div>
                            </section>

                            {{-- <section
                                @if ((isset($object) && $object->link_type == 'provider') || (!isset($object) && old('link_type') == 'provider')) style="display: block;margin-right: 55px;" @else style="display: none;margin-right: 55px;" @endif
                                id="providers-sec">
                                <h3><label><input type="checkbox" name="providers[]" />
                                        {{ __('dashboard.suppliers') }}
                                    </label></h3>
                                <div class="children">
                                    @foreach (\App\Models\User::where('user_type_id', 3)->where('is_archived', 0)->get() as $provider)
                                        <label><input type="checkbox" name="provider[]" value="{{ $provider->id }}"
                                                {{ isset($prev_providers) && in_array($provider->id, $prev_providers) ? 'checked' : '' }} />
                                            {{ $provider->username }}
                                        </label>&nbsp; &nbsp;
                                    @endforeach

                                </div>
                            </section> --}}
                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.discount_coupon') }}<i
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
        <script>
            $(function() {
                $('input[type="radio"]').on('change', function(e) {
                    const value = $('input[type="radio"]:checked').val();
                    if (value === 'provider') {
                        $('#categories-sec').hide();
                        $('#providers-sec').show();
                    } else {
                        $('#categories-sec').show();
                        $('#providers-sec').hide();
                    }
                });
            });
        </script>
    </div>
@stop
