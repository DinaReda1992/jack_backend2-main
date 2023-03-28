<?php

namespace App\Http\Controllers\Panel;

use App\Imports\AutopartImport;
use App\Models\AutoPart;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Articles;
use App\Models\AdsOrders;
use App\Models\ArticlePhotos;
use Maatwebsite\Excel\Facades\Excel;

class AutopartsController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings(482);
            return $next($request);
        });

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.autoparts.all', ['objects' => AutoPart::all()]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.autoparts.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->partFile) {
            $this->validate($request, [
                'partFile' => 'required|mimes:xls,xlsx,csv',

            ]);
            $path = $request->file('partFile')->getRealPath();
          $data=  Excel::import(new AutopartImport, request()->file('partFile'));
            return redirect()->back()->with('success', 'تم اضافة القطعة بنجاح .');

        } else {
            $this->validate($request, [
                'name' => 'required|unique:autoparts|max:100|min:3',
                'name_en' => 'required|unique:autoparts|max:100|min:3',
            ]);
            $object = new AutoPart();
            $object->name = $request->name;
            $object->name_en = $request->name_en;

            $object->save();
            return redirect()->back()->with('success', 'تم اضافة القطعة بنجاح .');

        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.autoparts.add', ['object' => AutoPart::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object = AutoPart::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:3|unique:autoparts,name,' . $object->id . ',id',
            'name_en' => 'required|max:100|min:3|unique:autoparts,name_en,' . $object->id . ',id',

        ]);

        $object->name = $request->name;
        $object->name_en = $request->name_en;

        $object->save();
        return redirect()->back()->with('success', 'تم تعديل قطعة الغيار بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ads = AutoPart::find($id);
        if ($ads) {
            $ads->delete();
        }
    }

//    public function delete_order($id = 0)
//    {
//        $ads = AdsOrders::find($id);
//        if ($ads != false) {
//            $ads = AdsOrders::find($ads->id);
//            $ads->delete();
//        }
//        return redirect()->back('success', 'تم حذف الطلب بنجاح');
//    }
}
