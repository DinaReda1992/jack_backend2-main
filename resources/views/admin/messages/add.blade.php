@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
    <script type="text/javascript" src="/assets/js/pages/gallery.js"></script>
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
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} رد </h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li><a href="/admin-panel/messages">عرض جميع الرسائل</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} رد </li>
                </ul>
                <ul class="breadcrumb-elements">
                    @if ($ticket->closed == 0)
                        <li><a class="btn btn-danger" style="color: #fff;"
                                href="/admin-panel/close-ticket/{{ $ticket->id }}"><i
                                    class="icon-switch position-left"></i> {{ __('dashboard.close_tickets') }}</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">
            @include('admin.message')

            @if ($ticket->closed == 1)
                <div>

                    <div class="alert alert-danger alert-styled-left">
                        <button type="button" class="close" data-dismiss="alert"><span>×</span><span
                                class="sr-only">Close</span></button>
                        التذكرة مغلقه , قم بإرسال رسالة لفتح التذكرة .
                    </div>
                </div>
            @endif

            <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.replay') }} </h5>
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
                        action="/{{ app()->getLocale() }}/admin-panel/messages">
                        {!! csrf_field() !!}
                        <fieldset class="content-group">

                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.replay') }}</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" name="message">{{ old('message') }}</textarea>
                                    <input type="hidden" name="reciever_id" value="{{ @$other_user->id }}">
                                    <input type="hidden" name="ticket_id" value="{{ $ticket_id }}">
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                رد
                                <i class="icon-arrow-left13 position-right"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /form horizontal -->

            <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_messages') }} {{ @$other_user->username }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div id="chat" class="panel-body">

                    @php
                        $last_id = 0;
                        $i = 1;
                    @endphp
                    @foreach ($objects as $object)
                        @php
                            if ($i == 1) {
                                $last_id = $object->id;
                            }
                            
                        @endphp
                        @if ($object->sender_id == 1)
                            <div style="background-color: #cccccc52;border: grey 1px solid;border-radius: 10px"
                                class="col-xs-9 " message_id="{{ $object->id }}">
                                <p>{{ $object->message }}
                                    @if ($object->image)
                                        <br>
                                        <div class="thumbnail">
                                            <div class="thumb">
                                                <img style="width: 150px;height: 150px;"
                                                    src="/uploads/{{ $object->image }}" alt="">
                                                <div class="caption-overflow">
                                                    <span>
                                                        <a href="/uploads/{{ $object->image }}" data-popup="lightbox"
                                                            rel="gallery"
                                                            class="btn border-white text-white btn-flat btn-icon btn-rounded"><i
                                                                class="icon-plus3"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </p>
                                <br>
                                <span>Admin</span>
                                <br>
                                <span>{{ $object->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                        @else
                            <div style="background-color: #cccccc52;border: grey 1px solid;border-radius: 10px"
                                class="col-xs-9 col-xs-offset-3 pull-left" message_id="{{ $object->id }}">
                                <p>{{ $object->message }}
                                    @if ($object->image)
                                        <br>
                                        <div class="thumbnail">
                                            <div class="thumb">
                                                <img style="width: 150px;height: 150px;"
                                                    src="/uploads/{{ $object->image }}" alt="">
                                                <div class="caption-overflow">
                                                    <span>
                                                        <a href="/uploads/{{ $object->image }}" data-popup="lightbox"
                                                            rel="gallery"
                                                            class="btn border-white text-white btn-flat btn-icon btn-rounded"><i
                                                                class="icon-plus3"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </p>
                                <br>
                                <span>{{ @$object->getSenderUser->first_name . ' ' . @$object->getSenderUser->last_name }}</span>
                                <br>
                                <span>{{ $object->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                            @php
                                $object->status = 1;
                                $object->save();
                            @endphp
                        @endif
                        @php
                            $i++;
                        @endphp
                    @endforeach

                </div>
            </div>
            <script type="text/javascript">
                $(document).ready(function() {

                    var last1 = "{{ $last_id }}";

                    // setInterval(ajaxCall, 2000); //300000 MS == 5 minutes
                    //
                    // // function ajaxCall() {
                    // //     $.get('/get-last-message-with-user/'+last1,function (data) {
                    // // 	$('#chat').html(data);
                    // //     });
                    // // }
                });
            </script>
            <!-- /form horizontal -->

            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
@stop
