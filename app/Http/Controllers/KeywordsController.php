<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\KeywordsImport;
use App\Models\Keyword;
use App\Models\RelatedKeyword;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Session;
use Illuminate\Support\Str;
use Redirect;

class KeywordsController extends Controller
{
    public function import(Request $request) 
    {
        $validator = Validator::make(
            [
                'file'      => $request->keywords,
                'extension' => strtolower($request->keywords->getClientOriginalExtension()),
            ],
            [
                'file'      => 'required',
                'extension' => 'required|in:csv,xlsx,xls',
            ]
        );
        if ($validator->passes()) { 
            $name = Str::random(20);
            if ($request->hasFile('keywords')) {
                $file_name = $name.'.'.request('keywords')->getClientOriginalExtension();
                request('keywords')->move(storage_path('app'), $file_name);
                try {
                    Excel::import(new KeywordsImport, $file_name);
                } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                    $failures = $e->failures();
                    dd($failures);
                    foreach ($failures as $failure) {
                        $failure->row(); // row that went wrong
                        $failure->attribute(); // either heading key (if using heading row concern) or column index
                        $failure->errors(); // Actual error messages from Laravel validator
                        $failure->values(); // The values of the row that has failed.
                    }
                }
            }else{
                Session::flash('toaster', array('error', 'Please attached a file.'));
            }
            Session::flash('toaster', array('success', 'The importing of keywords data using .'.request('keywords')->getClientOriginalExtension().' file is successfully saved.'));
            return Redirect::back();
        }else{
            return Redirect::back()->withErrors($validator);
        }
    }

    public function list(Request $request)
    {
        $keywords = Keyword::where('name', 'like', '%' . $request->term . '%')
                            ->select(
                                    'id',
                                    'name'
                                )
                            ->get();
        return response()->json($keywords);
    }

    public function related_keywords($id)
    {

        $related_keywords = RelatedKeyword::where('keyword_id', '=', $id)
                                            ->orWhere('related_keyword_id', '=', $id)
                                            ->with('keyword', 'related_keyword')
                                            ->get();
        $keywords = Keyword::whereNotIn('id', [$id])->limit(100)->get();
        return response()->json(['keywords' => $keywords, 'related_keywords' => $related_keywords]);
        
    }
}
