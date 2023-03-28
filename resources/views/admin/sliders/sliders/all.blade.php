@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/bootbox.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    @if (app()->getLocale() == 'ar')
        <script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis.js"></script>
    @else
        <script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis_en.js"></script>
    @endif
    <script type="text/javascript" src="/assets/js/pages/components_modals.js"></script>

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
                            class="text-semibold">{{ __('dashboard.dashboard') }}</span> -
                        {{ __('dashboard.view_sliders') }} ({{ $mainSlider->name }})</h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/slider/create?main_slider={{ $mainSlider->id }}">
                            <button type="button" class="btn btn-success" name="button"> {{ __('dashboard.add') }}
                                {{ __('dashboard.slider') }} +</button>
                        </a>
                    </div>
                </div>

            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
                    <li class="active"><a href="">{{ __('dashboard.view_sliders') }}</a></li>
                </ul>

            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_sliders') }}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>


                <table class="table datatable-colvis-basic text-center">
                    <thead>
                        <tr>

                            <th id="sort_id">{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.slider_title') }}</th>
                            <th>{{ __('dashboard.language') }}</th>
                            <th>{{ __('dashboard.slider_image') }}</th>
                            <th class="text-center">{{ __('dashboard.action_taken') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>

                        @foreach ($objects as $object)
                            <tr parent_id="{{ $object->id }}">

                                <td> <?php print $i++; ?></td>
                                <td>{{ app()->getLocale() == 'ar' ? $object->title : $object->title_en }}</td>

                                <td>
                                    @if ($object->locale === \App\Entities\LocaleType::EN)
                                        English
                                    @elseif ($object->locale === \App\Entities\LocaleType::AR)
                                        عربى
                                    @elseif ($object->locale === \App\Entities\LocaleType::BOTH)
                                        كلاهما
                                    @endif
                                </td>
                                <td>
                                    @if ($object->photo)
                                        <img width="50" height="50" src="/uploads/{{ $object->photo }}">
                                    @endif
                                </td>
                                <td align="center" class="center">
                                    <ul class="icons-list">
                                        <li class="text-primary-600"><a
                                                href="/admin-panel/slider/{{ $object->id }}/edit"><i
                                                    class="icon-pencil7"></i></a></li>
                                        <li class="text-danger-600"><a onclick="return false;"
                                                object_id="{{ $object->id }}"
                                                delete_url="/admin-panel/slider/{{ $object->id }}" class="sweet_warning"
                                                href="#"><i class="icon-trash"></i></a></li>
                                        <!-- 												<li class="text-teal-600"><a href="#"><i class="icon-cog7"></i></a></li> -->
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /basic example -->


            <!-- Restore column visibility -->


            <!-- State saving -->

            <!-- Column groups -->


            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->
    <script type="text/javascript">
        // Warning alert
    </script>

@stop
