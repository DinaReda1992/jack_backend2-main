@extends('layouts.layout ')

@section('content')

    <!-- ================= register ================= -->

    @if(Session::has('message'))
        <div class="alert alert-info" role="alert" style="text-align: center;">
            {{ Session::get('message') }}
        </div>
    @endif
    <!-- ================= end login ================= -->



@stop