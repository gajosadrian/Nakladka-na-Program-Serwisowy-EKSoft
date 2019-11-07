<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Zlecenie\Zlecenie;
use App\Models\Zlecenie\Zdjecie;

class ZlecenieZdjecieController extends Controller
{
    public function show(int $zlecenie_id)
    {
        $zlecenie = Zlecenie::with('zdjecia')->findOrFail($zlecenie_id);
        return view('zlecenie.zdjecia', compact('zlecenie'));
    }

    public function make(Zdjecie $zdjecie)
    {
        return response()->file(
            Storage::disk('images')->path($zdjecie->path)
        );
    }

    public function store(Request $request)
    {
        if (!$request->zlecenie_id or !$request->image or !$request->type) return redirect()->back();

        Zdjecie::create([
            'zlecenie_id' => $request->zlecenie_id,
            'type' => $request->type,
            'path' => Storage::disk('images')->putFile('', $request->image),
        ]);

        return redirect()->back();
    }
}
