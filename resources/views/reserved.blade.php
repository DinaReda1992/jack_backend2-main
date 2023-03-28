@extends('layout ')
@section('title')
    <title>   الرئيسية - المحجوزة </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop

@section('content')


    <!-- ================= mu Hall ================= -->

    <section class="favorite-page">
        <div class="container">
            <div class='head-page'>
                <h4> قاعاتى</h4>
            </div>

            <div class="row">
                <div class="custom-tab style-1">

                    <!-- tabs -->

                    <ul class="nav nav-tabs  b-0" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link " id="fav-tab"  href="#favorite" role="tab"
                               aria-controls="favorite" aria-selected="false">المفضلة</a>
                        </li>
                        <li class="nav-item">
                            <a href="/halls/reserved" class="nav-link active" id="reseved-tab" data-toggle="tab" role="tab"
                               aria-controls="reseved" aria-selected="true">المحجوزة</a>
                        </li>
                        <li class="nav-item">
                            <a href="/halls/finished" class="nav-link" id="finish-tab"  aria-selected="false">المنتهية</a>
                        </li>
                    </ul>

                    <!-- tabs content -->
                    <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="reseved" role="tabpanel" aria-labelledby="reseved-tab">

                            <div class='list-layout'>

                                @if($halls->count())
                                    @foreach($halls as $hall)
                                        {!! View::make("items.myHall") -> with('hall',$hall)->with('type','reserved') -> render() !!}

                                    @endforeach

                                @else
                                    <span>لا توجد قاعات محجوزة !</span>
                                @endif

                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    {!! $halls->render() !!}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- =================  mu Hall ================= -->

@stop