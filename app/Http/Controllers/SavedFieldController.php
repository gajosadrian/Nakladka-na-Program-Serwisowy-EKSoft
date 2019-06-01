<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class SavedFieldController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $user->setSaveField($request->name, $request->value);

        return response()->json('success', 200);
    }
}
