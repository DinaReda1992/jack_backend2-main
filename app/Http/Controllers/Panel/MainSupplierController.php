<?php

namespace App\Http\Controllers\Panel;

use App\Models\User;
use App\Models\MainSupplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainSupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }

    public function index()
    {
        $objects = MainSupplier::withCount('suppliers')->paginate();
        return view('admin.main_supplier.all', compact('objects'));
    }

    public function create()
    {
        return view('admin.main_supplier.add');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:main_suppliers,name',
        ]);
        MainSupplier::create($request->only('name'));
        return redirect()->back()->with('success', 'تم  الاضافة بنجاح');
    }

    public function edit($id)
    {
        $object = MainSupplier::where('id', $id)->first();

        return view('admin.main_supplier.add', compact('object'));
    }

    public function update(Request $request, $id)
    {
        $object = MainSupplier::where('id', $id)->first();
        $this->validate($request, [
            'name' => 'required|unique:main_suppliers,name,' . $object->id . ',id',
        ]);
        $object->update($request->only('name'));
        return redirect()->back()->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $object = MainSupplier::where('id', $id)->first();


        if (!$object) {
            return response()->json('لا يوجد مورد رئيسي');
        }

        User::where('main_supplier_id', $id)->update(['main_supplier_id' => NULL]);

        $object->delete();
    }
}
