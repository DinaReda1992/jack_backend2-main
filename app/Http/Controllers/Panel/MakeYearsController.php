<?php

namespace App\Http\Controllers\Panel;

use App\Models\CategoriesSelections;
use App\Models\Make;
use App\Models\MakeYear;
use App\Models\Selections;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Categories;
class MakeYearsController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(464);
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $make_id=$request->make;
        $type=$request->type;
        $make=Make::where('id',$make_id)->first();
        $years=MakeYear::select('make_years.*')->where(function ($query) use($make_id,$type){
            if($make_id){
                $query->where('make_years.make_id',$make_id);
            }
            if($type=='deleted'){
                $query->where('make_years.is_archived',1);
            }
            else{
                $query ->where('make_years.is_archived',0);
            }
        })->join('makes','makes.id','make_years.make_id')
            ->where('makes.is_archived',0)
            ->get();
        return view('admin.car_years.all',['objects'=>$years,'make'=>$make]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $make=Make::where('id',$request->make)->first();
        return view('admin.car_years.add',['my_make'=>$make]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
        	'make_id' => 'required',
            'year' => 'required',
        ]);
		$year=MakeYear::where('make_id',$request->make_id)->where('year',$request->year)->first();
		if($year){
		    return redirect()->back()->with('error','هذه السنه موجوده بالفعل مع هذا النوع ');
        }
		 $object = new MakeYear();
		 $object->year = $request->year;
         $object->make_id = $request->make_id;

         $object->save();
		 return redirect()->back()->with('success','تم اضافة السنة  بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	return view('admin.car_years.add',['object'=> MakeYear::find($id)]);
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
        $object= MakeYear::find($id);
        $this->validate($request, [
        'make_id' => 'required',
            'year' => 'required',
         ]);
		
		$object->year = $request->year;
        $object->make_id = $request->make_id;
        $year=MakeYear::where('make_id',$request->make_id)->where('year',$request->year)->where('id','!=',$object->id)->first();
        if($year){
            return redirect()->back()->with('error','هذه السنه موجوده بالفعل مع هذا النوع ');
        }

         $object->save();


		 return redirect()->back()->with('success','تم تعديل السنة  بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = MakeYear::find($id);
$object->is_archived=1;
$object->save();
//        $object ->delete();
    }
    public function make_year_archived_restore($id)
    {
        $object = MakeYear::find($id);
        $object->is_archived=0;
        $object->save();
//        $object ->delete();
    }

}
