<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class UpdatePasswordController extends Controller
{

    public function __construct() {

        $this->middleware('auth');

    }
    public function edit() {
        return view('admin.pages.settings.password');
    }

    public function update(Request $request)
    {
        $current_password = Auth::User()->password;
        /*if (Auth::Check()) {*/
            $rules = [
                'current_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ];
            $messages = [
                'current_password.required' => 'Enter current password.',
                'password.required' => 'Enter new Password.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if (!Hash::check($request->current_password, $current_password)) {
                $validator->getMessageBag()->add('current_password', 'Incorrect Password Entered..');
                if ($request->ajax()) {
                    return response()->json($validator->errors(), 422);
                } else {
                    return redirect()->route('admin.settings.password.edit')
                        ->withErrors($validator->errors());
                }
            }

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json($validator->errors(), 422);
                } else {
                    return redirect()->route('admin.settings.password.edit')
                        ->withErrors($validator)
                        ->withInput($request->all());
                }
            } else {

                    $user_id = Auth::id();
                    $obj_user = User::find($user_id);
                    $obj_user->password = Hash::make($request->password);
                    $obj_user->save();
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => "Password changed successfully",
                        ]);
                    }
                    else {
                        return redirect()->route('admin.index')->with('success', 'Password changed successfully');
                    }
                /*}*/
                /*else {
                    $errors = array('current_password' => 'Enter correct current password');
                    if ($request->ajax()) {
                        //return response()->json(array('errors' => $errors), 422);
                        //return response()->json($validator->errors(), 422);
                        return response()->json(['errors' => $errors], 422);
                    }
                    else {
                        return redirect()->route('admin.settings.password')
                            ->withErrors($errors)
                            ->withInput();
                    }
                }*/

            }

/*        }
        else
        {
            return redirect()->to('/');
        }*/
    }

}
