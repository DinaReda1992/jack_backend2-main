<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\BankAccounts;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class BankAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {


    }

    public function index()
    {
        $this->check_provider_settings(411);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        return view('providers.bank_accounts.all',['objects'=>BankAccounts::where('user_id',$provider_id)->get()]);
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->check_provider_settings(410);
        return view('providers.bank_accounts.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->check_provider_settings(410);

        $this->validate($request, [
        	'bank_name' => 'required',
        	'bank_name_en' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
//            'account_ipan' => 'required',
        ]);
		 $object = new BankAccounts;
		 $object->bank_name = $request->bank_name;
		 $object->bank_name_en = $request->bank_name_en;
         $object->account_name = $request->account_name;
         $object->account_number = $request->account_number;
         $object->account_ipan = $request->account_ipan?:'';
         $object->user_id = Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

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
        $this->check_provider_settings(412);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        return view('providers.bank_accounts.add',['object'=> BankAccounts::where('user_id',$provider_id)->first()]);
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
        $this->check_provider_settings(412);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $object= BankAccounts::where('user_id',$provider_id)->first();
        $this->validate($request, [
            'bank_name' => 'required',
            'bank_name_en' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
//            'account_ipan' => 'required',
         ]);

        $object->bank_name = $request->bank_name;
        $object->bank_name_en = $request->bank_name_en;
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
        $this->check_provider_settings(413);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $object = BankAccounts::where('user_id',$provider_id)->first();
        $object ->delete();
    }
}
