@extends('layouts.layout-app')

@section('content')

    <!-- ============================ Our Story Start ================================== -->
    <section class="about-page about">

        <div class="container">

            <!-- row Start -->
            <div class="row align-items-center">

                <div class="{{$object->photo || $object->photo2?'col-lg-7 col-md-6':'col-lg-12 col-md-12'}}">
                    <div class="story-wrap explore-content">
                       {!! $object->content !!}
                    </div>
                </div>
@if($object->photo || $object->photo2)
                <div class="col-lg-5 col-md-6">
                    @if($object->photo )
                    <img src="/uploads/{{$object->photo}}" class="img-fluid about-lg" alt="">
                    @endif
                        @if($object->photo2)
                            <img src="/uploads/{{$object->photo2}}" class="img-fluid about-sm" alt="">

                        @endif
                </div>
                @endif

                <!--<div class="col-lg-6 col-md-6">-->
                <!--	<div class="img-about">-->
                <!--		<img src="/site/images/about2.jpg" class="img-fluid" alt="" />-->
                <!--	</div>-->
                <!--</div>-->

                <!--<div class="col-lg-6 col-md-6">-->
                <!--	<div class="img-about">-->
                <!--		<img src="/site/images/about2.jpg" class="about2 img-fluid" alt="" />-->
                <!--	</div>-->
                <!--</div>-->

                <!--<div class="col-lg-6 col-md-6">-->
                <!--	<div class="story-wrap explore-content">-->
                <!--		<h2>زفاف احلامك <span class="main-color">يبدا</span><br>-->
                <!--			مع قاعة حفل الزفاف</h2>-->
                <!--		<p>هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي-->
                <!--			للنص أو شكل توضع الفقرات في الصفحة التي يقرأها. ولذلك يتم استخدام طريقة لوريم إيبسوم لأنها تعطي توزيعاَ-->
                <!--			طبيعياَ -إلى حد ما- للأحرف عوضاً عن استخدام "هنا يوجد محتوى نصي، هنا يوجد محتوى نصي" فتجعلها تبدو (أي-->
                <!--			الأحرف) وكأنها نص مقروء. العديد من برامح النشر المكتبي</p>-->
                <!--	</div>-->
                <!--</div>-->

            </div>
            <!-- /row -->

        </div>

    </section>
    <!-- ============================ Our Story End ================================== -->
@stop