@extends('layouts.layout ')
@section('title')
<title>  الرسائل {{$this_hall?'-'.$this_hall->title:''}} </title>
<meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
<meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop
@section('content')

    <!-- ============================ Our Story Start ================================== -->
    <section>

        <div class="container">

            <div class='head-page'>
                <h4> الرسائل</h4>
            </div>

            <!-- row Start -->
            <div class="row align-items-center">

                <div class=" col-md-12">
                    <div id="frame">
                        <div id="sidepanel">
                            <div id="profile">
                                <p>الرسائل </p>
                            </div>
                            <div id="search">
                                <input type="text" placeholder="بحث" />
                                <label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
                            </div>
                            <div id="contacts">
                                <ul>
                                    @if($all_messages->count())
                                    @foreach($all_messages as $msg)
                                  <a href="/messages?hall={{$msg->hall->id}}">  <li class="contact {{$msg->hall->id==$hall_id?'active':''}}">
                                        <div class="wrap">
                                            <img src="/uploads/{{$msg->hall->onePhoto->photo}}" alt="" />
                                            <div class="meta">
                                                <p class="name">{{$msg->hall->title}} </p>
                                                <span class="time">{{$msg->created_at -> diffForHumans()}}</span>
                                                <p class="preview">{{$msg->message}} </p>
                                            </div>
                                        </div>
                                    </li></a>
                                        @endforeach
                                        @else
                                            <span>لا توجد رسائل</span>
@endif

                                </ul>
                            </div>

                        </div>
                        <div class="content">
                            <div class="contact-profile">
                                <img src="/uploads/{{$this_hall?$this_hall->onePhoto->photo:''}}" alt="" />
                                <p style="margin-top: 15px;"> {{$this_hall?$this_hall->title:''}} </p>
                            </div>
                            <div class="messages" id="messages">
                                <ul>
                                    @foreach($messages as $message)
                                    <li class="{{$message->sender_id== Auth::Id()?'sent':'replies'}}">
                                        <p>
                                            {{$message->message}}
                                        </p>
                                    </li>
                                        @endforeach
                                </ul>
                            </div>
                            <div class="message-input">
                                <div class="wrap">
                                    <form method="post" action="/add-message">
                                        {!! csrf_field() !!}
                                    <input type="text" name="message" placeholder="اكتب رسالتك " />
{{--                                    <i class="fa fa-paperclip attachment" aria-hidden="true"></i>--}}
                                        <input type="hidden" name="to" value="{{$this_hall->user_id!=Auth::id()?$this_hall->user_id:($messages->count()?($messages->count() && $messages->first()->sender_id==Auth::Id()?$messages->first()->reciever_id:$messages->first()->sender_id):'')}}" />
                                        <input type="hidden" name="hall_id" value="{{$this_hall->id}}" />

                                        <button type="submit" class="submit"><i class="fa fa-paper-plane"
                                                              aria-hidden="true"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /row -->

        </div>

    </section>
    <!-- ============================ Our Story End ================================== -->



@stop