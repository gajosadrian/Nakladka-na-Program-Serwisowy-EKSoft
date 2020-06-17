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
        foreach ($request->phones as $phone) {
            $hostedSms->send($phone, $request->message);
        }

        return response()->json('success');
    }
}
