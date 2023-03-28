<!-- Single Property Start -->
<div class="col-lg-12 col-md-12">
    <div class="property-listing property-1">

        <div class="listing-img-wrapper">
            <a href="/hall/{{$hall->id}}">
                <img src="/uploads/{{$hall->onePhoto?$hall->onePhoto->photo:''}}" class="img-fluid mx-auto" alt="" />
            </a>

        </div>

        <div class="listing-content">

            <div class="listing-detail-wrapper">
                <div class="listing-short-detail">
                    <h4 class="listing-name mb-5">
                        <a href="">
                            <img src="/site/images/hall.png">
                            {{$hall->title}}</a>
                    </h4>
                    <h5 class="listing-location mb-5"> <img
                                src="/site/images/location.png">  {{$hall->address}}</h5>
                    <h5 class="listing-location mb-5"> <img
                                src="/site/images/chair.png"> {{$hall->chairs}}
                        مقعد</h5>

                    <div class="listing-rating">
                        {!! View::make("items.hallStars") -> with('hall',$hall) -> render() !!}
                    </div>

                </div>
            </div>

            <!-- close -->
            @if($type=='favorites')
            <a href="/hall/remove-favorite/{{$hall->id}}">
                <img src="/site/images/close.png" class="close">
            </a>
            @elseif($type=='reserved')
                <a href="/{{$hall->id}}">
                    <img src="/site/images/close.png" class="close">
                </a>

                @endif

            <!-- btn-footer -->
            <div class="listing-footer-wrapper">
                <div class='price'>
                    <span class="">{{$hall->price_per_hour.' '. $hall->getCurrency->code}}</span>
                </div>
                <div class='favorite'>
                    <i class="ti-heart"></i> {{$hall->likes_count}}
                </div>
                <div class='detail'>
                    <a href="/hall/{{$hall->id}}" class="more-btn">تفاصيل أكتر</a>
                </div>

            </div>

        </div>

    </div>
</div>
<!-- Single Property End -->
