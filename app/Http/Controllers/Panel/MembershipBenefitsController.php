<?php

namespace App\Http\Controllers\Panel;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\MembershipBenefits;
class MembershipBenefitsController extends Controller
{

    public function index()
    {
        return view('admin.membership_benefits.all',['objects'=>MembershipBenefits::all()]);
    }


    public function create()
    {
        return view('admin.membership_benefits.add');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
        	'name' => 'required',
            'name_en' => 'required',
        ]);
		 $object = new MembershipBenefits;
		 $object->name = $request->name;
         $object->name_en = $request->name_en ;

         $object->save();
		 return redirect()->back()->with('success','تم اضافة ميزة العضوية بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	return view('admin.membership_benefits.add',['object'=> MembershipBenefits::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object= MembershipBenefits::find($id);
        $this->validate($request, [
            'name' => 'required',
            'name_en' => 'required',
         ]);

        $object->name = $request->name;
        $object->name_en = $request->name_en ;

         $object->save();
		 return redirect()->back()->with('success','تم تعديل ميزة العضوية بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = MembershipBenefits::find($id);
        $object ->delete();
    }
}
