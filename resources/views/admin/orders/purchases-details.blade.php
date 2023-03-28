<div class="table-responsive">

    <table class="table table-bordered text-center text-center">
        <thead>
            <tr>
                <th>{{__('dashboard.supplier_id')}}</th>
                <th>{{ __('dashboard.supplier_name') }}</th>
                <th style="width: 127px">{{ __('dashboard.order_details') }}</th>
                <th> {{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($objects as $object)
                <tr parent_id="{{ $object->id }}">
                    <td>{{ $object->id }}</td>
                    <td>
                        <a href="{{ '/admin-panel/all-users/' . @$object->id . '/edit' }}">{{ @$object->username }}</a>
                    </td>
                    <td>
                        <a href="{{ url('admin-panel/warehouse-purchases/items/' . $object->id) }}"> {{__('dashboard.details')}} (
                            {{ @count($object->products) }} )
                        </a>
                    </td>
                    <td>

                        <a href="{{ url('admin-panel/warehouse-purchases/items/' . $object->id) }}">
                            <button type="button" name="button" class="btn btn-primary">
                                {{__('dashboard.add_purchase_order')}}
                            </button>
                        </a>
                    </td>
                </tr>
            @endforeach
            @if (count($objects) == 0)
                <tr>
                    <td colspan="11" align="center">لا يوجد نقص بالمخزن بعد</td>
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
