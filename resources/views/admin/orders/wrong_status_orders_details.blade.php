<div class="table-responsive">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th style="width: 200px">تاريخ انشاء الطلب</th>
                <th style="width: 200px">وقت انشاء الطلب</th>
                <th style="width: 200px">طلب من</th>
                <th>اسم العميل</th>
                @if(\Request::segment(3)=='suppliers-orders')

                <th> المورد</th>
                @endif
                @if(auth()->user()->user_type_id==1 && \Request::segment(2)!='orders' )
                <th>
                    اسم الموظف
                </th>
                <th>
                    اسم المسوق
                </th>

                @endif
                <th>
                    المنطقة
                </th>
                @if(\Request::segment(2)=='orders')
                <th>
                    مرفق إيصال
                </th>
                @endif
                <th style="width: 127px">تفاصيل الطلب</th>

                <th> الاجراء المتخذ</th>
            </tr>
        </thead>
        <tbody>

            @foreach($objects as $object)
            <tr parent_id="{{ $object->id }}">

                <td>{{ $object->id }}</td>
                @if($object->status==0)
                <td>{{ $object->created_at->format('Y-m-d') }}</td>
                <td>{{ $object->created_at->diffForHumans() }}</td>

                @elseif($object->status==1)
                {{-- <td>{{\Carbon\Carbon::parse( $object->marketed_date)->format('Y-m-d h:i a')}}</td>--}}
                {{-- <td>{{\Carbon\Carbon::parse( $object->marketed_date)->diffForHumans()}}</td>--}}
                <td>{{ $object->created_at->format('Y-m-d') }}</td>
                <td>{{ $object->created_at->diffForHumans() }}</td>

                @elseif($object->status>=2)
                <td>{{ $object->created_at->format('Y-m-d') }}</td>
                <td>{{ $object->created_at->diffForHumans() }}</td>

                {{-- <td>{{\Carbon\Carbon::parse( $object->financial_date)->format('Y-m-d')}}</td>--}}
                {{-- <td>{{\Carbon\Carbon::parse( $object->financial_date)->diffForHumans()}}</td>--}}
                @endif
                <td>
                    @if(!$object->platform)
                    التطبيق
                    @else
                    {{@$object->platform}}
                    @endif
                </td>
                <td>
                    <a href="{{'/admin-panel/all-users/'.@$object->user->id.'/edit'}}">{{ @$object->user->username }}</a>
                </td>
                @if(\Request::segment(3)=='suppliers-orders')
                <td>
                    <a href="{{'/admin-panel/suppliers/'.@$object->provider->id.'/edit'}}">{{ @$object->provider->username }}</a>
                </td>
                @endif

                @if(auth()->user()->user_type_id==1 && \Request::segment(2)!='orders')
                <td>
                    @if($object->status==0)
                    <a href="{{'/admin-panel/all-users/'.@$object->added->id.'/edit'}}">{{ @$object->added->username }}</a>
                </td>
                @elseif($object->status==1)
                <a href="{{'/admin-panel/all-users/'.@$object->accepted->id.'/edit'}}">{{ @$object->accepted->username }}</a></td>
                @elseif($object->status>=2)
                <a href="{{'/admin-panel/all-users/'.@$object->reviewd->id.'/edit'}}">{{ @$object->reviewd->username }}</a></td>
                @endif
                </td>
                @endif
                @if(auth()->user()->user_type_id==1 && \Request::segment(2)!='orders')
                <td>
                    <a href="{{'/admin-panel/all-users/'.@$object->added->id.'/edit'}}">{{ @$object->added->username }}</a>
                </td>

                </td>
                @endif
                <td>
                    {{@$object->region->name}}
                </td>
                @if(\Request::segment(2)=='orders')
                <td>
                    @if(@$object->payment_method==3)
                    <span class="badge badge-success">دفع بالرصيد</span>
                    @elseif($object->payment_method==2)
                    <span class="badge badge-success">دفع الكتروني</span>

                    @else
                    @if(@$object->transfer_photo->photo!="")
                    <span class="badge badge-success">مرفق ايصال</span>
                    @else
                    <span class="badge badge-danger">غير مدفوع</span>

                    @endif
                    @endif
                </td>
                @endif
                <td>
                    @if($object->status==0)
                    <a href="{{url('admin-panel/orders/'.$object->id)}}"> التفاصيل ( {{ @$object->cart_items->count() }} )
                    </a>
                    @elseif($object->status==1)
                    <a href="{{url('admin-panel/new-orders/'.$object->id)}}"> التفاصيل ( {{ @$object->cart_items->count() }} )
                    </a>
                    @elseif($object->status>=2)
                    <a href="{{url('admin-panel/warehouse/'.$object->id)}}"> التفاصيل ( {{ @$object->cart_items->count() }} )
                    </a>
                    @endif
                    {{-- <a href="{{url('admin-panel/orders/'.$object->id)}}"
                    > التفاصيل ( {{ @$object->cart_items->count() }} )
                    </a>--}}

                </td>

                <td>
                    <form style="display: inline-block" action="/admin-panel/warehouse/complete-order/{{$object->id}}" method="GET">
                        @csrf
                        <button type="submit" class="btn btn-success submit_form_btn">مكتمل</button>
                    </form>
                </td>


            </tr>

            @endforeach
            @if(count($objects)==0)
            <tr>
                <td colspan="11" align="center">لا يوجد طلبات بعد</td>
            </tr>
            @endif

        </tbody>
    </table>

    <div class="clearfix"></div>
    <br>
    <hr>
    <div align="center">
        {{ $objects->appends(Request::except('page'))->links() }}

        {{-- {!! $objects->render() !!}--}}
    </div>
</div>