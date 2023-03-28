<!-- Single Property Start -->
<div class="col-lg-4 col-md-4 col-sm-6">
    <div class="property-listing property-1">

        <div class="listing-img-wrapper">
            <a href="/hall/{{$hall->id}}">
                <img src="/uploads/{{$hall->onePhoto->photo}}" class="img-fluid mx-auto" alt="" />
            </a>
            <div class="listing-price">

                <h4 class="list-pr">{{$hall->getCurrency->code.' '. $hall->price_per_hour}}</h4>
            </div>
            <div class="listing-like-top">
                <i class="ti-heart"></i> {{$hall->likes_count}}
            </div>

        </div>

        <div class="listing-content">

            <div class="listing-detail-wrapper">
                <div class="listing-short-detail">
                    <h4 class="listing-name mb-5">
                        <a href="">
                            <img src="/site/images/hall.png">
                            {{$hall->title}}</a>
                    </h4>
                    <h5 class="listing-location mb-5"> <img src="/site/images/location.png">
                        {{$hall->address}}</h5>
                    <h5 class="listing-location mb-5"> <img src="/site/images/chair.png"> {{$hall->chairs}}
                        مقعد</h5>

                    <div class="listing-rating">
                        @for($i=1; $i<=(int)$hall->hall_rate; $i++)
                            <i class="ti-star filled"></i>
                        @endfor


                    </div>

                </div>
            </div>

            <div class="listing-footer-wrapper">

                <div class="listing-detail-btn">
                    <a href="/hall/{{$hall->id}}" class="more-btn">تفاصيل أكثر</a>
                </div>
            </div>

        </div>

    </div>
</div>
<!-- Single Property End -->
