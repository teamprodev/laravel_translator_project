<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Session;
use File;
use App\Models\Language;
use App\Models\Translation;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request)
    {
    	$request->session()->put('locale', $request->input("locale"));
        $language = Language::where('code', $request->input("locale"))->first();
    	// flash(translate('Language changed to ').$language->name)->success();
    }

    public function localize($locale) {
        App::setLocale($locale);
        // store the locale in session so that the middleware can register it
        session()->put('locale', $locale);
        return redirect()->back();
    }

    public function index(Request $request)
    {
        $languages = Language::paginate(10);
        return view('languages', compact('languages'));
    }

    public function create(Request $request)
    {
        return view('languages.create');
    }

    public function store(Request $request)
    {
        $language = new Language;
        $language->name = $request->name;
        $language->code = $request->code;
        if($language->save()){

            // flash(translate('Language has been inserted successfully'))->success();
            return redirect()->route('languages.index');
        }
        else{
            // flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function show(Request $request, $id)
    {
        $sort_search = null;
        $language = Language::findOrFail(decrypt($id));
        $lang_keys = Translation::where('lang', env('DEFAULT_LANGUAGE', 'en'));
        if ($request->has('search')){
            $sort_search = $request->search;
            $lang_keys = $lang_keys->where('lang_key', 'like', '%'.$sort_search.'%');
        }
        $lang_keys = $lang_keys->paginate(1000);
        return view('languages.language_view', compact('language','lang_keys','sort_search'));
    }

    public function edit($id)
    {
        $language = Language::findOrFail(decrypt($id));
        return view('languages.edit', compact('language'));
    }

    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);
        $language->name = $request->name;
        $language->code = $request->code;
        if($language->save()){
            // flash(translate('Language has been updated successfully'))->success();
            return redirect()->route('languages.index');
        }
        else{
            // flash(translate('Something went wrong'))->error();
            return back();
        }
    }


    public function update_rtl_status(Request $request)
    {
        $language = Language::findOrFail($request->id);
        $language->rtl = $request->status;
        if($language->save()){
//            flash(translate('RTL status updated successfully'))->success();
            return 1;
        }
        return 0;
    }

    public function destroy($id)
    {
        $language = Language::findOrFail($id);
        if (env('DEFAULT_LANGUAGE') == $language->code) {
            // flash(translate('Default language can not be deleted'))->error();
        }
        else {
            Language::destroy($id);
            // flash(translate('Language has been deleted successfully'))->success();
        }
        return back();
    }
}
