<?php

namespace App\Http\Controllers\Panel;


use Carbon\Carbon;
use App\Models\Categories;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use App\Models\Subcategories;
use App\Models\MainCategories;
use App\Models\PageCategoryProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PageCategoryController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        return view('admin.page-categories.all');
    }

    public function getPageCategoriesData(Request $request)
    {
        $pages = PageCategory::orderBy('id', 'DESC');
        return DataTables::of($pages)
            ->addColumn('actions', function ($page) {
                $ul = '<ul class="icons-list">';

                $ul .= '<li class="text-primary-600"><a href="/admin-panel/page-categories/' . $page->id . '/edit"><i class="icon-pencil7"></i></a></li>';
                $ul .= '<li class="text-danger-600"><a onclick="return false;" object_id="' . $page->id
                    . '" delete_url="/admin-panel/page-categories/' . $page->id
                    . '"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>';

                $ul .= '</ul>';
                return $ul;
            })
            ->make(true);
    }

    public function create(Request $request)
    {
        $categories = MainCategories::all();
        return view('admin.page-categories.add', compact('categories'));
    }

    public function store(Request $request)
    {
        $todayDate = Carbon::tomorrow();

        $validator = Validator::make($request->all(), [
            'products' => 'required',
            'name_ar' => 'required',
            'name_en' => 'required',
            'is_offer' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        if (count(json_decode($request->products)) == 0 && !$request->is_offer && !$request->category_id) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'اختر منتج واحد علي الأقل',
                ],
                202
            );
        }

        $page_category = PageCategory::create(['is_offer' => $request->is_offer ? 1 : 0] + $request->all());
        if (!$request->is_offer) {
            foreach (json_decode($request->products) as $product) {
                PageCategoryProduct::create(['product_id' => $product->id, 'page_category_id' => $page_category->id]);
            }
        }
        return \response()->json([
            'status' => 200,
            'url' => url('/admin-panel/page-categories'),
            'message' => 'تم انشاء القسم بنجاح ',
        ]);
    }

    public function edit($id)
    {
        $object = PageCategory::with(['products'=>function($query){
            $query->select('*','products.id as id')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
        }])->find($id);
        $categories = MainCategories::all();
        $sub_categories = Categories::where('parent_id', $object->category_id)->get();
        return view('admin.page-categories.add', compact('object', 'categories', 'sub_categories'));
    }

    public function update($id, Request $request)
    {
        $todayDate = Carbon::tomorrow();

        $validator = Validator::make($request->all(), [
            'products' => 'required_if:is_offer,0',
            'name_ar' => 'required',
            'name_en' => 'required',
            'is_offer' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $page_category = PageCategory::find($id);
        if (count(json_decode($request->products)) == 0 && !$request->is_offer &&  $page_category->products()->count() == 0 && !$request->category_id) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'اختر منتج واحد علي الأقل',
                ],
                202
            );
        }

        $page_category->update(['is_offer' => $request->is_offer ? 1 : 0, 'category_id' => !$request->is_offer ? $request->category_id : 0, 'sub_category_id' => !$request->is_offer ? $request->sub_category_id : 0] + $request->all());
        if (!$request->is_offer) {
            PageCategoryProduct::where('page_category_id', $page_category->id)->delete();
            // dd(json_decode($request->products));
            foreach (json_decode($request->products) as $product) {
                Log::info($product->id);
                PageCategoryProduct::create(['product_id' => $product->id, 'page_category_id' => $page_category->id]);
            }
        }
        return \response()->json([
            'status' => 200,
            'url' => url('/admin-panel/page-categories'),
            'message' => 'تم تعديل القسم بنجاح ',
        ]);
    }

    public function destroy($id)
    {
        $page_category = PageCategory::find($id);
        if (!$page_category) {
            return 0;
        }
        $page_category->delete();
        return 1;
    }

    public function getSubCategoriesData($id)
    {
        $categories = categories::where('parent_id', $id)->get();
        return  $categories->toArray();
    }
}
