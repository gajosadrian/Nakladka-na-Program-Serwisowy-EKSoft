<?php

namespace App\Http\Controllers;

use App\Models\Zlecenie\Zlecenie;
use Illuminate\Http\Request;
use App\Services\HostedSms;
use App\Sms;

class SmsController extends Controller
{
    public function create()
    {
        $smses = Sms::with(['user', 'zlecenie'])->where('auto', 0)->latest()->limit(100)->get();

        return view('sms.create', compact('smses'));
    }

    public function resolve(Request $request, Sms $sms)
    {
        if ($sms->type == 'error') {
            $sms->type .= '_resolved';
        } else {
            abort(406);
        }

        $sms->save();

        return response()->json('success');
    }

    public function store(Request $request, HostedSms $hostedSms)
    {
        $hostedSms->send($request->phones, $request->message, [
            'auto' => $request->auto ?? false,
            'zlecenie_id' => $request->zlecenie_id,
            'zlecenie_status_id' => $request->zlecenie_status_id,
        ]);

        $user = @auth()->user();
        $opis = null;
        if ($user and $request->zlecenie_id and $zlecenie = Zlecenie::find($request->zlecenie_id)) {
            $zlecenie->appendOpis('SMS: ' . $request->message_form, $user->short_name, ($user->technik_id == 0));
            $zlecenie->save();
            $opis = $zlecenie->opis;
        }

        return response()->json([
            'opis' => $opis,
        ]);
    }
}
