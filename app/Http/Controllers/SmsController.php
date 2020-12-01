<?php

namespace App\Http\Controllers;

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

    public function store(Request $request, HostedSms $hostedSms)
    {
        $hostedSms->send($request->phones, $request->message, [
            'auto' => $request->auto ?? false,
            'zlecenie_id' => $request->zlecenie_id,
            'zlecenie_status_id' => $request->zlecenie_status_id,
        ]);

        return response()->json('success');
    }
}
