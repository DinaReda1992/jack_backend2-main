<?php

namespace App\Http\Controllers\Providers;

use App\Exports\CardsExport;
use App\Models\Categories;
use App\Models\MrmandoobCards;
use App\Models\MrmandoobCardsDetails;
use App\Models\Settings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Cobons;
use App\Models\Subcategories;
class MrmandoobCardsController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings(230);
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
        return view('providers.mrmandoob-cards.all', ['objects' => MrmandoobCards::orderBy('id', 'DESC')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.mrmandoob-cards.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:mrmandoob_cards,name',
            'quantity' => 'required|numeric',
        ]);
        $object = new MrmandoobCards();
        $object->name = $request->name;
        $object->save();

        $cards_type = [25, 50, 75, 100, 200];
        foreach ($cards_type as $type) {
            for ($i = 0; $i < $request->quantity; $i++) {
                $check = 0;
                while ($check == 0) {
                    $rand = mt_rand('1000000', '9999999') . mt_rand('1000000', '9999999');
                    if (!@MrmandoobCardsDetails::where('code', $rand)->first()) {
                        $mrmandoob_details = new MrmandoobCardsDetails();
                        $mrmandoob_details->mrmandoob_card_id = $object->id;
                        $mrmandoob_details->type = $type;
                        $mrmandoob_details->code = $rand;
                        $mrmandoob_details->save();
                        $check = 1;
                    }
                }
            }
        }


        return redirect()->back()->with('success', 'تم اضافة الكروت بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $card_name = MrmandoobCards::find($id);
        $cards = MrmandoobCardsDetails::where('mrmandoob_card_id', $id)->get();
        return view('providers.mrmandoob-cards.details', ['objects' => $cards, 'card_name' => $card_name->name ,'card'=>$card_name]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('providers.mrmandoob-cards.add', ['object' => MrmandoobCards::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object = MrmandoobCards::find($id);
        $this->validate($request, [
            'name' => 'required|unique:mrmandoob_cards,name,' . $object->id . ',id',
        ]);
        $object->name = $request->name;

        $object->save();
        return redirect()->back()->with('success', 'تم تعديل عنوان المجموعة بنجاح');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        $object = Cobons::find($id);
//        $old_file = 'uploads/'.$object->photo;
//        if(is_file($old_file))	unlink($old_file);
//        $object->delete();
//    }
    public function download($mrmandoob_card_id=0)
    {
        return (new CardsExport($mrmandoob_card_id))->download('Mr-mandoob-cards.xlsx');
    }

}