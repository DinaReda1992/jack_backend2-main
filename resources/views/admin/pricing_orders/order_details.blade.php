@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

    <script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
    <!-- /theme JS files -->

    {{--	<link href="/assets/js/plugins/hierarchical-select/hierarchy-select.min.js" rel="stylesheet">--}}

    {{--	<script type="text/javascript" src="/assets/js/plugins/hierarchical-select/hierarchy-select.min.js"></script>--}}
    <style>
        .response-header {
            text-align: center;
            color: #263238;
            font-weight: 700;
        }

        .order-card {
            background: #929292;
            color: #fff;
            padding: 7px;
            margin-right: 5px;
        }

        .info-label {
            background: #ecedef;
        }
        .response-header{
            text-align: center;
            color: #263238;
            font-weight: 700;
        }
        .offer-title{
            text-align: center;
            color: #2d9e08;
            text-decoration: underline;
        }
        .my-offer{
            background: #fff;
            padding: 5px;
            display: grid;
        }
        .user-block{
            text-align: right;
        }

    </style>

@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span>
                        - تفاصيل الطلب</h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/pricing-orders">
                            <button type="button" class="btn btn-success" name="button"> عرض طلبات التسعير</button>
                        </a>
                    </div>
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
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i></a></li>
                    <li><a href="/admin-panel/pricing-orders">عرض طلبات التسعير</a></li>
                    <li class="active"> تفاصيل الطلب</li>
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
                    <h5 class="panel-title">  تفاصيل الطلب ( رقم {{$object->id}}#)</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <section class="content">

                    <div class="row">
                        <div class="col-md-12">

                            <!-- Profile Image -->
                            <div class="">
                                <div class="box-body box-profile">
                                    <img class="profile-user-img img-responsive img-circle"
                                         src="/uploads/{{$object->user->photo?:'default-user.png'}}"
                                         alt="User profile picture">
                                    <h3 class="profile-username text-center">{{$object->user->username}}</h3>

                                </div><!-- /.box-body -->
                            </div><!-- /.box -->

                            <!-- About Me Box -->
                        </div><!-- /.col -->
                        <div class="col-md-12">
                            <div class="nav-tabs-custom">
                                <ul class="timeline timeline-inverse">
                                    <!-- timeline time label -->
                                    <li class="time-label">
                                        @if($object->status==0)
                                            <span class="bg-green">
												(مفتوح)
                        </span>

                                        @elseif($object->status==1)
                                            <span class="bg-red">
												(مغلق)
                        </span>
                                        @elseif($object->status==4)
                                            <span class="bg-gray">
												(منتهى)
                        </span>


                                        @elseif($object->status==-1)
                                            <span class="bg-red">
												(ملغى)
                        </span>

                                        @endif

                                    </li>
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    <li>
                                        <i class="fa fa-envelope bg-blue"></i>
                                        <div class="timeline-item">

                                            <h3 class="timeline-header"></h3>
                                            <div class="timeline-body" style="display: inline-block;width: 100%;">
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-lg-12">تاريخ الطلب</label>
                                                    <div class="col-lg-10">
                                                        <p class="form-control info-label">{{$object->created_at->diffForHumans()}}</p>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-lg-12">وصف الطلب</label>
                                                    <div class="col-lg-10">
                                                        <p class="form-control info-label">{{$object->description}}</p>
                                                    </div>
                                                </div>


                                                @if($object->photo)
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-12">صورة </label>
                                                        <div class="col-lg-10">
                                                            <a href="/uploads/{{$object->photo}}" target="_blank"><img
                                                                        src="/uploads/{{$object->photo}}"></a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div style=" text-align: center;">
                                                <ul class="nav nav-tabs">
                                                    @php
                                                        $tab_count=0;
$pan_count=0;
                                                    @endphp
                                                    @foreach($object->parts as $part_)
                                                        <li class="{{$tab_count==0?'active':''}}"><a
                                                                    style="color:{{$part_->admin_offer->first()?'#4bda03':'#000'}}"
                                                                    href="#part{{$part_->id}}" data-toggle="tab"
                                                                    aria-expanded="true">{{@$part_->category->name.' ( '.@$part_->subcategory->name.' ) '}}</a>
                                                        </li>
                                                        @php $tab_count++; @endphp
                                                    @endforeach
                                                </ul>
                                                <form method="post" class="form-horizontal"
                                                      action="/admin-panel/add_pricing_offers/{{$object->id}}">
                                                    {!! csrf_field() !!}

                                                    <div class="tab-content">
                                                        @foreach($object->parts as $part)
                                                            <div class="tab-pane {{$pan_count==0?'active':''}}"
                                                                 id="part{{$part->id}}">
                                                                <!-- Post -->
                                                                <div class="post">
                                                                    <div class=" col-md-5 order-card">
                                                                        <label class="control-label col-lg-5">حالة
                                                                            الطلب</label>
                                                                        <div class="col-lg-7">
                                                                            <p class=""
                                                                               style="color: #bf9;">{{$part->type==0?'جديدة':'مستعملة'}}</p>
                                                                        </div>
                                                                    </div>

                                                                    <div class=" col-md-5 order-card">
                                                                        <label class="control-label col-lg-5">الكمية
                                                                            المطلوبة</label>
                                                                        <div class="col-lg-7">
                                                                            <p class=""
                                                                               style="color: #bf9;">{{$part->quantity.' ( '.@$part->measurement->name.' )'}} </p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="post">
                                                                        <div class="row margin-bottom">
                                                                            <div class="col-sm-3">
                                                                                @if($part->photo)

                                                                                    <img class="img-responsive myImg"
                                                                                         src="/uploads/{{$part->photo}}"
                                                                                         alt="Photo">
                                                                                @endif

                                                                            </div><!-- /.col -->
                                                                            <div class="col-sm-9">
                                                                                <p style="text-align: right;
    margin: 23px;">
                                                                                    {{$part->part_name}}

                                                                                </p>
                                                                            </div>
                                                                        </div><!-- /.row -->


                                                                    </div>


                                                                </div><!-- /.post -->
                                                                <div class="panel panel-flat">
                                                                    <div class="panel-heading">
                                                                        @if($part->admin_offer->first())
                                                                            <h3 class="offer-title">العرض</h3>
                                                                        @else
                                                                            <h5 class="panel-title">اضف عرضك ({{$part_->category->name.' - '.@$part_->subcategory->name}}) </h5>
                                                                     @endif
                                                                        <div class="heading-elements">

                                                                            <ul class="icons-list">
                                                                                <li><a data-action="collapse"></a></li>
                                                                                <li><a data-action="close"></a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>


                                                                    <div class="panel-body">
@if($part->admin_offer->first())
                                                                            <div class="my-offer">
                                                                                <div class="post">
                                                                                    <div class="user-block">
                                                                                        <img class="img-circle img-bordered-sm" src="/uploads/{{ $adminUser->photo?:'placeholder.png' }}" alt="user image">
                                                                                        <span class="username">
                          <a href="#">{{ $adminUser->username }}</a>

                        </span>
                                                                                        <span class="description">{{@$part->admin_offer->first()->created_at->diffForHumans()}}</span>
                                                                                    </div><!-- /.user-block -->
                                                                                    <div class="form-group col-md-6">
                                                                                        <label class="control-label col-lg-12">الخامة</label>
                                                                                        <div class="col-lg-10">
                                                                                            <p class="form-control" >{{@$part->admin_offer->first()->manufactureType->name}}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group col-md-6">
                                                                                        <label class="control-label col-lg-12">وقت التحضير</label>
                                                                                        <div class="col-lg-10">
                                                                                            <p class="form-control" >{{@$part->admin_offer->first()->prepare_time}}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group col-md-6">
                                                                                        <label class="control-label col-lg-12">القطع المتاحة</label>
                                                                                        <div class="col-lg-10">
                                                                                            <p class="form-control" >{{@$part->admin_offer->first()->available_quantity}}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group col-md-6">

                                                                                        <label class="control-label col-lg-12">سعر الوحدة</label>
                                                                                        <div class="col-lg-10">
                                                                                            <div class="col-md-9">
                                                                                            <p class="form-control" >{{@$part->admin_offer->first()->price}} ريال </p>
                                                                                            </div>
                                                                                            <div class="col-md-3">
                                                                                            لكل {{@$part->measurement->name}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group col-md-6">
                                                                                        <label class="control-label col-lg-12">اجمالى التكلفة</label>
                                                                                        <div class="col-lg-10">
                                                                                            <p class="form-control" >{{$part->admin_offer->first()->price * @$part->admin_offer->first()->available_quantity}}</p>
                                                                                        </div>
                                                                                    </div>

{{--                                                                                    <div class="form-group col-md-6">--}}
{{--                                                                                        <label class="control-label col-lg-12">نوع الطلب </label>--}}
{{--                                                                                        <div class="col-lg-10">--}}
{{--                                                                                            <p class="form-control" >{{@$part->admin_offer->first()->PricingOrderType->name}}</p>--}}
{{--                                                                                        </div>--}}
{{--                                                                                    </div>--}}
                                                                                    <div class="form-group col-md-6">
                                                                                        <label class="control-label col-lg-12">بلد الصنع</label>
                                                                                        <div class="col-lg-10">
                                                                                            <p class="form-control" >{{@$part->admin_offer->first()->manufacture_country}}</p>
                                                                                        </div>
                                                                                    </div>
{{--                                                                                    <div class="form-group col-md-6">--}}
{{--                                                                                        <label class="control-label col-lg-12">الماركة</label>--}}
{{--                                                                                        <div class="col-lg-10">--}}
{{--                                                                                            <p class="form-control" >{{@$part->admin_offer->first()->brand}}</p>--}}
{{--                                                                                        </div>--}}
{{--                                                                                    </div>--}}

                                                                                </div>


                                                                                </div>

                                                                        @elseif(!$part->admin_offer->first() && $object->status==0)
                                                                        <fieldset class="content-group">
                                                                            <input type="hidden" name="part_id[]"
                                                                                   value="{{$part->id}}">
                                                                            <div class=" form-group col-md-6">
                                                                                <label class="control-label col-lg-4">الخامة
                                                                                    *</label>
                                                                                <div class="col-lg-8">
                                                                                    <select name="manufacture_type[]"
                                                                                            class="form-control">

                                                                                        <option value="1">طبيعى</option>
                                                                                        <option value="2">صناعى</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group col-md-6">
                                                                                <label class="control-label col-lg-4">وقت التحضير* </label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="text"
                                                                                           name="prepare_time[]"
                                                                                           class="form-control"
                                                                                           placeholder="حدد وقت التحضير">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group col-md-6">
                                                                                <label class="control-label col-lg-4">القطع المتاحة* </label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="number"
                                                                                           max="{{$part->quantity}}"
                                                                                           name="available_quantity[]"
                                                                                           class="form-control"
                                                                                           placeholder="حدد القطع المتاحة للبيع">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group col-md-6">
                                                                                <label class="control-label col-lg-4">السعر لكل {{@$part->measurement->name}}  * </label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="number"
                                                                                           name="price[]"
                                                                                           class="form-control"
                                                                                           placeholder="حدد سعر القطعه الواحدة">
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group col-md-6">
                                                                                <label class="control-label col-lg-4">بلد الصنع </label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="text"
                                                                                           name="manufacture_country[]"
                                                                                           class="form-control"
                                                                                           placeholder="بلد الصنع">
                                                                                </div>
                                                                            </div>


                                                                        </fieldset>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <!-- Post -->

                                                                <!-- Post -->
                                                            </div><!-- /.tab-pane -->
                                                            @php $pan_count++; @endphp
                                                        @endforeach
                                                    </div>
                                                    @if($object->status==0 )
                                                        <div class="timeline-footer">
                                                            <div class="text-center" style="    margin: 18px;">
                                                                <button type="submit" class="btn btn-primary">اضف العروض  <i class="icon-arrow-left13 position-right"></i></button>
                                                            </div>
                                                        </div>

                                                    @endif

                                                </form>
                                            </div>

                                        </div>
                                    </li>
                                    <!-- END timeline item -->

                                </ul>
                            </div><!-- /.nav-tabs-custom -->
                        </div>
                    </div><!-- /.row -->

                </section>

            </div>
            <!-- /form horizontal -->


            <!-- Footer -->
        @include('admin.footer')
        <!-- /footer -->
        </div>
        <!-- /content area -->
    </div>
    <div id="myModal" class="modal">

        <!-- The Close Button -->
        <span class="close">&times;</span>

        <!-- Modal Content (The Image) -->
        <img class="modal-content" id="img01">

        <!-- Modal Caption (Image Text) -->
        <div id="caption"></div>
    </div>

    <link rel="stylesheet" href="/css/image-style.css">

    <script>
        $(function () {
            var modal = document.getElementById("myModal");

// Get the image and insert it inside the modal - use its "alt" text as a caption
            var img = document.getElementsByClassName("myImg");
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("caption");
            $('.myImg').click(function () {
                    modal.style.display = "block";
                    modalImg.src = this.src;
                    captionText.innerHTML = this.alt;
                }
            );

// Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
            span.onclick = function () {
                modal.style.display = "none";
            }

        })
    </script>

@stop