@extends('admin.layout')
@section('js_files')
    {{--    <script type="text/javascript" src="/assets/js/plugins/editors//.min.js"></script>--}}
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    {{--    <script type="text/javascript" src="/assets/js/pages/editor_.js"></script>--}}
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>

    <link href="/assets/css/fontawesome-iconpicker.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <style type="text/css">
        .iconpicker .iconpicker-item{
            float: right;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
            $(window).load(function () {
                $('.action-create').click();
            });
            $(document).on('change','.icon_class',function () {
                var icon = $(this).val();
                if(icon=="fa-warning"){
                    $(this).parent('div').next().next().next('.desc').show();
                }else{
                    $(this).parent('div').next().next().next('.desc').hide();
                }
            });


        });
    </script>

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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} محتوى الموقع</h4>
                </div>

            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                    <li class="active">{{ isset($object)? 'تعديل':'إضافة' }} محتوى الموقع</li>
                </ul>


            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

        @include('admin.message')

        <!-- Form horizontal -->





            <form enctype="multipart/form-data" method="post" class="form-horizontal" action='/admin-panel/updateSiteFeatures'>
                {!! csrf_field() !!}
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title"> مميزات التطبيق </h5>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                                <li><a data-action="reload"></a></li>
                                <li><a data-action="close"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <fieldset class="content-group">
                            {!! View::make("admin.site_manager.feature_items") -> with('features', $features) -> render() !!}

                        </fieldset>
                    </div>
                </div>


                <div class="text-right">
                    <button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} محتوى الموقع <i class="icon-arrow-left13 position-right"></i></button>
                </div>
            </form>





            <!-- /form horizontal -->


            <!-- Footer -->
        @include('admin.footer')
        <!-- /footer -->

        </div>
        <!-- /content area -->
        <script src="/assets/js/fontawesome-iconpicker.js"></script>
        <script>
            $(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $('.action-destroy').on('click', function() {
                    $.iconpicker.batch('.icp.iconpicker-element', 'destroy');
                });
                // Live binding of buttons
                $(document).on('click', '.action-placement', function(e) {
                    $('.action-placement').removeClass('active');
                    $(this).addClass('active');
                    $('.icp-opts').data('iconpicker').updatePlacement($(this).text());
                    e.preventDefault();
                    return false;
                });

                $(document).on('click','.action-create',function () {


                    $('.icp-auto').iconpicker();

                    $('.icp-dd').iconpicker({
                        //title: 'Dropdown with picker',
                        //component:'.btn > i'
                    });

                    $('.icp-glyphs').iconpicker({
                        title: 'Prepending glypghicons',
                        hideOnSelect: true,
                        icons: $.merge(['glyphicon glyphicon-home', 'glyphicon glyphicon-repeat', 'glyphicon glyphicon-search',
                            'glyphicon glyphicon-arrow-left', 'glyphicon glyphicon-arrow-right', 'glyphicon glyphicon-star'], $.iconpicker.defaultOptions.icons),
                        fullClassFormatter: function(val){
                            if(val.match(/^fa-/)){
                                return 'fa '+val;
                            }else{
                                return 'glyphicon '+val;
                            }
                        }
                    });

                    $('.icp-opts').iconpicker({
                        title: 'With custom options',
                        icons: ['fa-github', 'fa-heart', 'fa-html5', 'fa-css3'],
                        selectedCustomClass: 'label label-success',
                        mustAccept: true,
                        placement: 'bottomRight',
                        showFooter: true,
                        // note that this is ignored cause we have an accept button:
                        hideOnSelect: true,
                        templates: {
                            footer: '<div class="popover-footer">' +
                                '<div style="text-align:left; font-size:12px;">Placements: \n\
                <a href="#" class=" action-placement">inline</a>\n\
                <a href="#" class=" action-placement">topLeftCorner</a>\n\
                <a href="#" class=" action-placement">topLeft</a>\n\
                <a href="#" class=" action-placement">top</a>\n\
                <a href="#" class=" action-placement">topRight</a>\n\
                <a href="#" class=" action-placement">topRightCorner</a>\n\
                <a href="#" class=" action-placement">rightTop</a>\n\
                <a href="#" class=" action-placement">right</a>\n\
                <a href="#" class=" action-placement">rightBottom</a>\n\
                <a href="#" class=" action-placement">bottomRightCorner</a>\n\
                <a href="#" class=" active action-placement">bottomRight</a>\n\
                <a href="#" class=" action-placement">bottom</a>\n\
                <a href="#" class=" action-placement">bottomLeft</a>\n\
                <a href="#" class=" action-placement">bottomLeftCorner</a>\n\
                <a href="#" class=" action-placement">leftBottom</a>\n\
                <a href="#" class=" action-placement">left</a>\n\
                <a href="#" class=" action-placement">leftTop</a>\n\
                </div><hr></div>'}
                    }).data('iconpicker').show();
                }).trigger('click');


                // Events sample:
                // This event is only triggered when the actual input value is changed
                // by user interaction
                $('.icp').on('iconpickerSelected', function(e) {
                    $('.lead .picker-target').get(0).className = 'picker-target fa-3x ' +
                        e.iconpickerInstance.options.iconBaseClass + ' ' +
                        e.iconpickerInstance.options.fullClassFormatter(e.iconpickerValue);
                });
            });
        </script>

    </div>
@stop
