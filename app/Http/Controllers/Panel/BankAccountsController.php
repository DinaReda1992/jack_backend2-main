<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\BankAccounts;

use App\Http\Controllers\Controller;


class BankAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(264);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user_id=$request->user_id?:0;
        $objects=BankAccounts::where(function ($query)use($user_id){
            if($user_id){
                $query->where('user_id',$user_id);
            }
        })->get();

        return view('admin.bank_accounts.all',['objects'=>$objects]);
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.bank_accounts.add');
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
        	'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
//            'account_ipan' => 'required',
        ]);
		 $object = new BankAccounts;
		 $object->bank_name = $request->bank_name;
         $object->account_name = $request->account_name;
         $object->account_number = $request->account_number;
         $object->account_ipan = $request->account_ipan?:'';

         $file = $request->file('photo');
		 if ($request->hasFile('photo')) {
		 	$fileName = 'bank-logo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
		 	$destinationPath = 'uploads';
		 	$request->file('photo')->move($destinationPath, $fileName);
	 	    $object->photo=$fileName;
        }

        $object->save();

		 return redirect()->back()->with('success','تم اضافة الحساب البنكي بنجاح');
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
    	return view('admin.bank_accounts.add',['object'=> BankAccounts::find($id)]);
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
        $object= BankAccounts::find($id);
        $this->validate($request, [
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
//            'account_ipan' => 'required',
         ]);

        $object->bank_name = $request->bank_name;
        $object->account_name = $request->account_name;
        $object->account_number = $request->account_number;
        $object->account_ipan = $request->account_ipan?:'';
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->icon;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'bank-logo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

         $object->save();
		 return redirect()->back()->with('success','تم تعديل الحساب البنكي بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = BankAccounts::find($id);
        $object ->delete();
    }
}
