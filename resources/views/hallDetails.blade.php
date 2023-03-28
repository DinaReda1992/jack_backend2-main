@extends('layout ')
@section('title')
    <title>  {{$hall->title}} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop

@section('content')




    <!-- ============================ Property Detail Start ================================== -->
    <section class="">
        <div class="container">
            <div class="row">

                <!-- property main detail -->
                <div class="col-lg-8 col-md-12 col-sm-12">



                    <div class="property3-slide single-advance-property mb-4">

                        <div class="slider-for">
                            @foreach($hall->photos as $photo)
                            <a href="/uploads/{{$photo->photo}}" class="item-slick"><img src="/uploads/{{$photo->photo}}"
                                                                                    alt="Alt"></a>
                                @endforeach
                        </div>
                        <div class="slider-nav">
                            @foreach($hall->photos as $thum)

                            <div class="item-slick"><img src="/uploads/{{$thum->photo}}" alt="Alt"></div>
@endforeach
                        </div>

                    </div>




                    <!-- Single Block Wrap -->
                    <div class="block-wrap">

                        <div class="listing-detail-wrapper">
                            <div class="listing-short-detail">
                                <h4 class="listing-name mb-5">
                                    <a href="">
                                        <img src="/site/images/hall.png">
                                        {{$hall->title}}</a>
                                </h4>
                                <h5 class="listing-location mb-5"> <img src="/site/images/location.png">
                                    {{$hall->address}}
                                </h5>
                                <h5 class="listing-location mb-5"> <img src="/site/images/chair.png"> {{$hall->chairs}} مقعد</h5>

                                <div class="listing-rating">
                                    {!! View::make("items.hallStars") -> with('hall',$hall) -> render() !!}

                                </div>

                            </div>
                        </div>

                        <div class="block-header">
                            <h4 class="block-title">تفاصيل القاعة</h4>
                        </div>

                        <div class="block-body" style="white-space: pre-wrap;">
                            <p>{{$hall->description}}</p>
                        </div>

                        <div class="block-header">
                            <h4 class="block-title">شروط الحجز</h4>
                        </div>

                        <div class="block-body" style="white-space: pre-wrap;">
<p>
    {{$hall->terms}}
</p>
                        </div>

                    </div>


                </div>

                <!-- property Sidebar -->
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="page-sidebar">

                        <!-- Find New Property -->
                        <div class="sidebar-widgets">

                            <div class="card">
                                <p>سعر الحجز
                                    <span>{{ $hall->getCurrency->code.' '. $hall->price_per_hour}} </span>
                                </p>
                            </div>
                            <button class="btn btn-theme full-width"> احجز الان</button>

                            <!-- slide-property-sec -->
                            <div class="slide-property-sec mb-4">
                                <div class="pr-all-info">

                                    <a href="/messages?hall={{$hall->id}}" class="btn btn-theme btn-chat"> مراسلة </a>

                                    <div class="pr-single-info">
                                        <a hall_id="{{ $hall->id }}" onclick="return false"  class="like-bitt add-to-favorite"><i
                                                    class="lni-heart{{$is_like?'-filled':''}}"></i></a>
                                    </div>

                                    <div class="pr-single-info">
                                        <div class="share-opt-wrap">
                                            <button type="button" class="btn-share" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"
                                                    data-original-title="Share this">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                            <div class="dropdown-menu animated flipInX">
                                                <a href="#" class="cl-facebook"><i class="lni-facebook"></i></a>
                                                <a href="#" class="cl-twitter"><i class="lni-twitter"></i></a>
                                                <a href="#" class="cl-gplus"><i class="lni-google-plus"></i></a>
                                                <a href="#" class="cl-instagram"><i class="lni-instagram"></i></a>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <!-- Single Block Wrap -->
                            <div class="block-wrap">

                                <div class="block-header">
                                    <h4 class="block-title">المكان على الخريطة</h4>
                                </div>

                                <div class="block-body">
                                    <div class="map-container">
                                        <div id="mapLocation" style="height:450px; "></div>
{{--                                        <iframe--}}
{{--                                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13671.230262094645!2d31.388466549999997!3d31.059456249999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m3!3e2!4m0!4m0!5e0!3m2!1sen!2seg!4v1582145583180!5m2!1sen!2seg"--}}
{{--                                                width="600" height="450" frameborder="0" style="border:0;"--}}
{{--                                                allowfullscreen=""></iframe>--}}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- ============================ Property Detail End ================================== -->

@section('js')
    <script>
        function initMap(lat , lng) {
            markers = [];
            map = new google.maps.Map(document.getElementById('mapLocation'), {
                center: {lat:lat?lat:38.898648, lng:lng?lng:77.037692},
                zoom: 12
            });



            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });
            addMarker(lat,lng);
            // marker.setPosition(place.geometry.location);
            function addMarker(lat,lng)
            {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat,lng),
                    map: map,
                });
            }

        }

        $(window).load(function(){
initMap({{$hall->latitude}},{{$hall->longitude}});
        })
        $(function () {
            $('.add-to-favorite').click(function () {
                var tag = $(this);
                var hall_id = $(this).attr('hall_id');
                $.get('/add-to-favorite/'+hall_id,function (data) {
                    tag.html(data)
                })
            });

        })
    </script>
    @endsection

@stop