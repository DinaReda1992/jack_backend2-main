<?php

namespace App\Http\Controllers\Website;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use App\Models\MainCategories;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResources;

class ProductController extends Controller
{
    public function productPage($id = 0)
    {
        $product = Products::getProducts('web')->where('id', $id)->first();
        if (!$product) {
            abort(404);
        }

        $ratings = ProductRating::where('item_id', $product->id)->paginate(20);
        $relatedProducts = ProductResources::collection(Products::getProducts('web')->where('category_id', $product->category_id)
            ->where('id', '<>', $product->id)->paginate(12));
        return view('website.product_page', ['product' => json_encode(ProductResources::make($product)), 'ratings' => $ratings, 'relatedProducts' => json_encode($relatedProducts)]);
    }

    public function get_search(Request $request)
    {
        $products = Products::getProducts('web')->paginate(8);
        $products->{$products} = ProductResources::collection($products);
        if ($request->ajax() && $request->ajax == 1) {
            return response()->json(['status' => 200, 'data' => $products, 'url' => trim($_SERVER['QUERY_STRING'], '&ajax=1!')]);
        }

        $categories = MainCategories::orderBy('sort')->where('stop', 0)->with('subCategories')->withCount('subCategories')->get();
        $categories = json_encode(CategoryResource::collection($categories));
        return view('website.search', compact('categories'));
    }

    public function fetch(Request $request)
    {
        if ($request->get('query')) {
            request()->merge(['keyword' => $request->get('query')]);
            $data = Products::getProducts('web')->get();
            if ($request->mobile != 1) {
                $output = '<ul id="search_box_result" class="dropdown-menu" aria-labelledby="navbarDropdown" style="display:block; position:absolute;width: 268px">';
                foreach ($data as $row) {
                    $title = app()->getLocale() == "ar" ? $row->title : $row->title_en;
                    $output .= '<li class="px-2 py-2"><a class="text-dark" href="/product/' . $row->id . '">
                        <img class="mx-1 rounded-circle" src="/uploads/' . $row->photo . '" width="30" height="30"  />' . $title . '</a></li>';
                }
                if (count($data) == 0) {
                    $output .= '<li class="px-2 py-2" class="alert alert-danger text-center">' . __('dashboard.not_found_product') . '</li>';
                }
                $output .= '</ul>';
                echo $output;
            } else {
                $output = '';
                foreach ($data as $row) {
                    $title = app()->getLocale() == "ar" ? $row->title : $row->title_en;
                    $output .= '<a class="dropdown-item" href="/product/' . $row->id . '">
                        <img class="mx-1 rounded-circle" src="/uploads/' . $row->photo . '" width="30" height="30"  />' . $title . '</a>';
                }
                echo $output;
            }
        }
    }
}
