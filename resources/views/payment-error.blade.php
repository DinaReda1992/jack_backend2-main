@extends('layouts.layout-app')

@section('content')

    <!-- ============================ Our Story Start ================================== -->
    <section class="about-page about">

        <div class="container">

            <!-- row Start -->
<p>{{isset($pay_error)?$pay_error:'خطأ فى عملية الدفع'}}</p>
            <!-- /row -->

        </div>

    </section>
    <!-- ============================ Our Story End ================================== -->
@stop