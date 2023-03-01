<?php

namespace App\Http\Controllers;

/*Requests */
use Illuminate\Http\Request;
use App\Http\Requests\UpdatePasswordRequest;
/*Models */
use App\Models\User;
/*Helpers */
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



class UserController extends Controller
{
    public function updatePassword(UpdatePasswordRequest $request) {
        $this->authorize('settings.manage');

        $user = User::find(auth()->user()->id);
        if(Hash::check($request->currentPassword, auth()->user()->password)) {

        $user->update([
            'password' => Hash::make($request->newPassword),
            
        ]);
        return response()->json(200);

    }else {
        return response()->json(304);
    }

    }
    public function updateRecPassword(UpdatePasswordRequest $request) {
        $this->authorize('settings.manage');
        $user = User::find(2);
        if(Hash::check($request->currentPassword, $user->password)) {

        $user->update([
            'password' => Hash::make($request->newPassword),
            'remember_token' => Str::random(60)

            
        ]);
        return response()->json(200);

    }else {
        return response()->json(304);
    }

    }
}
