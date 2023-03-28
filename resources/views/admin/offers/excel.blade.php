<table>
    <thead>
    <tr>
        <th>رقم العرض</th>
        <th>إسم المنتج عربى</th>
        <th>إسم المنتج إنجليزى</th>
        <th>رقم المنتج</th>
        <th>باركود المنتج</th>
        <th>سعر المنتج شامل الضريبة</th>
        <th>سعر المنتج بعد الخصم شامل الضريبة</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            @php
                $client_price = $product->client_price;
       if($product->has_tax==1){
           $client_price=$client_price+($client_price*$tax/100);
       }
       $offer_price = 0;
       $message = '';
       $offer=null;
       if(@$product->product_offer->offer){
           $offer = $product->product_offer;

       }
       $offer_type_id=0;
       $offer_price=null;

       if($offer ){
           if(@$offer->type_id == 1){
               $offer_price=floatval($product->client_price) - floatval($offer->price_discount);
           }elseif (@$offer->type_id == 2){
               $offer_price=$product->client_price-( floatval($product->client_price) * floatval($offer->percentage) / 100);
           }
           $offer_type_id=$offer->type_id;

       }
       if($offer_price && $product->has_tax==1){
           $offer_price=$offer_price+($offer_price*$tax/100);
       }
            @endphp
            <td>
                {{@$offer->offer_id}}
            </td>
            <td>
                {{$product->title}}
            </td>
            <td>
                {{@$product->title_en}}
            </td>
            <td>
                {{$product->id}}
            </td>
            <td>
                @foreach($product->product->productBarcodes as $barcode)
                    {{$barcode->barcode}}
                @endforeach
            </td>
            <td>
                {{round((float)$client_price,2)}}
            </td>
            <td>
                {{round((float) $offer_price,2)}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
