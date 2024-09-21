<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{

    public function general_edit() {
        return view('admin.pages.settings.general');
    }

    public function general_update(Request $request) {
        /*return $request->all();*/
        $rules = [
            'admin_email' => 'required|email|max:255',
        ];
        $messages = [
            'admin_email.required' => 'Admin Email is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.settings.general.edit')
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        else {
            foreach($request->all() as $index => $value ) {
                Setting::where('term', $index)->update(['value' => $value]);
            }
        }

        return redirect()->route('admin.settings.general.edit');
    }
}
