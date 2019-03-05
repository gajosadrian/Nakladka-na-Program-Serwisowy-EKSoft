<?php

namespace App\Http\Controllers\Admin;

use App\Models\Zlecenie;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RozliczeniaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $zlecenieNierozliczone = Zlecenie\Zlecenie::where('id_zlecenia', '>', 45000)->withRelations()->oldest('DataPrzyjecia')->get();

        return view('rozliczenie.index', compact(
            'zlecenieNierozliczone'
        ));
    }
}
