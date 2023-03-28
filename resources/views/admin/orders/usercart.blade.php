<section class="invoice m-0">
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-12 invoice-col">
            <address>
                <strong> {{ __('dashboard.name') }}:{{ @$object->user->username }}</strong><br>
                {{ __('dashboard.phone') }}: {{ '0' . @$object->user->phone }}<br>
                @if (@$object->user->email)
                    {{ __('dashboard.email') }}: {{ $object->user->email }}<br>
                @endif
            </address>
        </div><!-- /.col -->



    </div><!-- /.row -->

    @if ($object)
        <div class="row">
            <label class="col-sm-12"><strong> {{ __('dashboard.order_details') }}</strong></label>
            <div class="col-sm-12">
                <table class="table table-bordered text-center">
                    <tr style="background: #e8e8e8">
                        <th>{{ __('dashboard.product') }}</th>
                        <th>{{ __('dashboard.quantity') }}</th>
                        <th>{{ __('dashboard.unit_price') }}</th>
                        <th>{{ __('dashboard.total') }}</th>

                    </tr>
                    @foreach ($object->cart as $key => $item)
                        <tr>
                            <td>
                                <img src="{{ @$item->product->photo }}" width="30" height="30"
                                    style="border-radius: 50%" />
                                {{ @$item->product->title }}
                            </td>
                            <td>{{ @$item->quantity }} </td>
                            <td>{{ @$item->price }} {{ __('dashboard.sar') }}</td>
                            <td>{{ @$item->price * @$item->quantity }} {{ __('dashboard.sar') }}</td>

                        </tr>
                    @endforeach

                    <tr style="background: #daefff">
                        <td colspan="3">{{ __('dashboard.products_price') }}</td>
                        <td>{{ @$object->total }} {{ __('dashboard.sar') }}</td>
                    </tr>
                </table>

            </div>

        </div>
    @endif
    <!-- this row will not appear when printing -->
</section><!-- /.content -->
