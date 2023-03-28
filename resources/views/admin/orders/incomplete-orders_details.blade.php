<div class="table-responsive text-center text-center">

    <table class="table table-bordered text-center text-center">
        <thead>
            <tr>
                <th>{{ __('dashboard.client_id') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_date_creation') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_time_creation') }}</th>
                <th>{{ __('dashboard.client_name') }}</th>
                @if (\Request::segment(2) != 'incomplete-orders-items')
                    <th style="width: 127px">{{ __('dashboard.product_details') }}</th>
                @else
                    <th style="width: 127px">{{ __('dashboard.order_details') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>

            @foreach ($objects as $object)
                <tr parent_id="{{ $object->id }}">

                    <td>{{ $object->user->id }}</td>
                    <td>{{ $object->created_at->format('Y-m-d') }}</td>
                    <td>{{ $object->created_at->diffForHumans() }}</td>

                    <td>
                        <a
                            href="{{ '/admin-panel/all-users/' . @$object->user->id . '/edit' }}">{{ @$object->user->username }}</a>
                    </td>
                    @if (\Request::segment(2) != 'incomplete-orders-items')
                        <td>
                            <a onclick="return false;" data-toggle="modal"
                                data-target="#cancelModal{{ $object->user_id }}555" href="#">
                                {{ __('dashboard.details') }} ( {{ @$object->cart->count() }} )
                            </a>

                            <div class="modal fade" id="cancelModal{{ $object->user_id }}555" tabindex="-1"
                                role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header pb-3">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">
                                                {{ __('dashboard.client_cart') }}
                                                {{ $object->user->username }}</h4>
                                        </div>
                                        <div class="modal-body">

                                            @include('admin.orders.usercart', ['object' => @$object])


                                        </div>


                                        <div style="display:flex;justify-content: space-between" class="p-4">
                                            <a href="/admin-panel/orders/create?user_id={{ $object->user->id }}"
                                                type="submit"
                                                class="btn btn-primary">{{ __('dashboard.prepare_cart_to_client') }}</a>
                                            <a type="button" class="btn btn-default"
                                                data-dismiss="modal">{{ __('dashboard.close') }}</a>

                                        </div>
                                    </div>
                                </div>
                            </div>


                        </td>
                    @else
                        <td>
                            <a href="{{ url('admin-panel/products/' . $object->itemProduct->id . '/edit') }}"> (
                                {{ @$object->itemProduct->title }} )
                            </a>

                        </td>
                    @endif

                </tr>
            @endforeach
            @if (count($objects) == 0)
                <tr>
                    <td colspan="11" align="center">{{ __('dashboard.not_found_orders') }}</td>
                </tr>
            @endif

        </tbody>
    </table>

    <div class="clearfix"></div>
    <br>
    <hr>
    <div align="center">
        {!! $objects->render() !!}
    </div>
</div>
