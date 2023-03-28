<?php

namespace App\Http\Controllers\Website;

use App\Models\Content;
use App\Models\Contacts;
use App\Models\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUsRequest;

class GlobalDataController extends Controller
{
    public function page($slug)
    {
        $page = Content::where('slug', $slug)->first();

        if (!$page) {
            return abort(404);
        }
        return view('website.page', ['object' => $page]);
    }

    public function appPage(Request $request, $slug)
    {
        $select_name = $request->lang == "en" ? 'page_name_en as page_name' : 'page_name';
        $select_content = $request->lang == "en" ? 'content_en as content' : 'content';

        $page = Content::select('id', $select_name, $select_content, 'meta_title', 'meta_keywords')->where('slug', $slug)->first();

        if (!$page) {
            return abort(404);
        }
        return view('app-page', ['object' => $page]);
    }

    public function contact()
    {
        $contacts = Settings::select('option_name', 'name', 'value')->where('input_type', 'contact_options')->get();
        $contact_whatsapp = '';
        $contact_email = '';
        $contact_address = '';
        foreach ($contacts as $contact) {
            $contact->option_name == 'whatsapp' ? $contact_whatsapp = $contact->value : '';
            $contact->option_name == 'email' ? $contact_email = $contact->value : '';
            $contact->option_name == 'address' ? $contact_address = $contact->value : '';
        }

        return view('website.contact', ['email' => $contact_email, 'whatsapp' => $contact_whatsapp, 'address' => $contact_address]);
    }

    public function sendMessageEmail(ContactUsRequest $request)
    {
        Contacts::create($request->validated());
        return redirect()->back()->with('success', __('messages.Message was sent successfully'));
    }

    public function getApp(Request $request)
    {
        // android store
        if (preg_match('#android#i', $_SERVER['HTTP_USER_AGENT'])) {
            header('Location:' . Settings::find(27)->value);
            exit;
        }

        // ios
        if (preg_match('#(iPad|iPhone|iPod)#i', $_SERVER['HTTP_USER_AGENT'])) {
            header('Location:' . Settings::find(28)->value);
            exit;
        }
        header('Location:' . Settings::find(27)->value);
    }
}
