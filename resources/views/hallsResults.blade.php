@extends('layout ')
@section('title')
    <title>  نتائج البحث </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop


@section('content')


    <!-- search -->
    <section class="search-page" style="padding: 138px 0 0px;">
        <div class="container">
            <div class="hero-search-wrap full-width">
                <div class="hero-search-content">

                    <div class="row">

                        <div class="col-lg-2 col-md-2 d-none d-md-block">
                            <div class="logo">
                                <img src="/site/images/logo.png">
                            </div>
                        </div>
                        <div class="col-lg-10 col-md-10">
                            <form method="get" action="/search-halls">
                                <div class="container">

                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 pl-0">
                                            <div class="form-group">
                                                <label>اسم الفندق</label>
                                                <input type="text" name="title" class="form-control" placeholder="اسم الفندق ">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 pl-0">
                                            <div class="form-group">
                                                <label>العنوان</label>
                                                <input type="text" name="address" class="form-control" placeholder="عنوان الفندق " id="search_location" autocomplete="off">
                                                <input name="search_lat" type="hidden">
                                                <input name="search_lng" type="hidden">

                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 pl-0">
                                            <div class="form-group">
                                                <label>نوع الفندق </label>
                                                <select class="form-control select-search" name="hall_type" id="">
                                                    <option></option>
                                                    @foreach(\App\Models\Categories::all() as $category)
                                                        <option value="{{$category->id}}"> {{$category->name}}</option>

                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-lg-4 col-md-4 col-sm-6 pl-0">
                                            <div class="form-group">
                                                <label>عدد المقاعد</label>
                                                <input type="text" name="chairs" class="form-control" placeholder="عدد المقاعد ">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-12 pl-0">
                                            <div class="form-group">
                                                <input type="submit" value="ابحث الان"  class="btn search-btn"/>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </form>


                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>


    <!-- ============================ Nears Halls Start ================================== -->
    <section>
        <div class="container">

            <div class='head-page'>
                <h4> نتائج البحث</h4>
            </div>

            <!-- row Start -->
            <div class="halls">
                <div class="row">
@if($halls->count())
                        @foreach($halls as $hall)
                            {!! View::make("items.hall") -> with('hall',$hall) -> render() !!}

                        @endforeach

    @else
    <span>لا توجد نتائج !</span>
    @endif

                </div>
                <!-- /row -->
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! $halls->render() !!}
                </div>
            </div>

        </div>
    </section>
    <!-- ============================ Nears Halls End ================================== -->


@section('js')
    <script>
        $(window).load(function(){
            activityMap.initMap(24.7135517, 46.6752957);

        })
    </script>

@endsection
@stop