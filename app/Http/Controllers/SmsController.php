<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HostedSms;

class SmsController extends Controller
{
    public function create()
    {
        return view('sms.create');
    }

    public function store(Request $request, HostedSms $hostedSms)
    {
        $hostedSms->send($request->phones, $request->message);

        return response()->json('success');
    }
}
