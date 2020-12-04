<?php

namespace App\Http\Controllers;

use App\Models\SMS\Technik;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    public function show(User $user = null)
    {
        $me = auth()->user();
        $user = $user ?? $me;
        if ($me->id != $user->id and ! $me->hasAnyRole('super-admin')) abort(401);

        $technicy = Technik::orderBy('Imie')->orderBy('Nazwisko')->get();

        return view('auth.profile', compact('user', 'technicy'));
    }

    public function update(Request $request, User $user)
    {
        $user->email = $request->login;
        if ($password = $request->new_password) {
            $user->password = Hash::make($password);
        }
        $user->technik_id = $request->technik_id;
        $user->save();

        Session::flash('message', 'Zapisano!');
        return redirect()->back();
    }
}
