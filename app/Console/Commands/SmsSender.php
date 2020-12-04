<?php

namespace App\Console\Commands;

use App\Models\Zlecenie\Status;
use App\Models\Zlecenie\Zlecenie;
use App\Services\HostedSms;
use App\Sms;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SmsSender extends Command
{
    protected $signature = 'sms:sender';
    protected $description = '';

    public const DISABLED_CUSTOMERS = [
        99, // DAR-GAZ
    ];
    public const REQUIRED_MESSAGES = [
        Status::DZWONIC_PO_ODBIOR_ID,
        Status::DO_ODBIORU_ID,
    ];
    public const MESSAGES = [
        // -------------------------------------------------------------------------------------------------------------+++++
        Status::ZLECENIE_WPISANE_ID => [
            'repeat' => null,
            'gwarancja' => 'Informujemy o otrzymaniu zlecenia na urzadzenie %producent%',
            'ubezpieczenie' => 'Informujemy o otrzymaniu zlecenia nr %nr_obcy%',
            'odplatne' => 'Informujemy o otrzymaniu zlecenia na urzadzenie %nazwa_urzadzenia%',
            'brak_urzadzenia' => 'Informujemy o otrzymaniu zlecenia',
        ],
        Status::GOTOWE_DO_WYJAZDU_ID => [
            'repeat' => null,
            'gwarancja' => 'Zlecenie na urzadzenie %producent% jest w trakcie planowania wizyty technika. Prosimy oczekiwac kontaktu w najblizszych dniach',
            'ubezpieczenie' => 'Zlecenie nr %nr_obcy% jest w trakcie planowania wizyty technika. Prosimy oczekiwac kontaktu w najblizszych dniach',
            'odplatne' => 'Zlecenie na %nazwa_urzadzenia% jest w trakcie planowania wizyty technika. Prosimy oczekiwac kontaktu w najblizszych dniach',
            'brak_urzadzenia' => 'Zlecenie jest w trakcie planowania wizyty technika. Prosimy oczekiwac kontaktu w najblizszych dniach',
        ],
        Status::UMOWIONO_ID => [
            'repeat' => null,
            'gwarancja' => 'Potwierdzamy wizyte technika do urzadzenia %producent% %data%',
            'ubezpieczenie' => 'Potwierdzamy wizyte technika do zlecenia nr %nr_obcy% %data%',
            'odplatne' => 'Potwierdzamy wizyte technika do %nazwa_urzadzenia% %data%',
            'brak_urzadzenia' => 'Potwierdzamy wizyte technika do zlecenia, %data%',
        ],
        Status::NA_WARSZTACIE_ID => [
            'repeat' => null,
            'gwarancja' => 'Urzadzenie %producent% otrzymalo status "na warsztacie". Prosimy oczekiwac dalszych informacji',
            'ubezpieczenie' => 'Zlecenie nr %nr_obcy% otrzymalo status "na warsztacie". Prosimy oczekiwac dalszych informacji',
            'odplatne' => 'Urzadzenie %nazwa_urzadzenia% otrzymalo status "na warsztacie". Prosimy oczekiwac dalszych informacji',
            'brak_urzadzenia' => 'Zlecenie otrzymalo status "na warsztacie". Prosimy oczekiwac dalszych informacji',
        ],
        Status::DO_WYCENY_ID => [
            'repeat' => null,
            'gwarancja' => null,
            'ubezpieczenie' => 'Zlecenie nr %nr_obcy% jest w trakcie wyceny. Prosimy oczekiwac kontaktu',
            'odplatne' => 'Zlecenie na %nazwa_urzadzenia% jest w trakcie wyceny. Prosimy oczekiwac kontaktu',
            'brak_urzadzenia' => 'Zlecenie jest w trakcie wyceny. Prosimy oczekiwac kontaktu',
        ],
        Status::ZAMOWIONO_CZESC_ID => [
            'repeat' => null,
            'gwarancja' => 'Informujemy o zamowieniu czesci do zlecenia urzadzenia %producent%',
            'ubezpieczenie' => 'Informujemy o zamowieniu czesci do zlecenia nr %nr_obcy%',
            'odplatne' => 'Informujemy o zamowieniu czesci do zlecenia na %nazwa_urzadzenia%',
            'brak_urzadzenia' => 'Informujemy o zamowieniu czesci do zlecenia',
        ],
        Status::DZWONIC_PO_ODBIOR_ID => [
            'repeat' => 7,
            'gwarancja' => 'Urzadzenie %producent% oczekuje na odbior',
            'ubezpieczenie' => '%nazwa_urzadzenia% ze zlecenia %nr_obcy% oczekuje na odbior',
            'odplatne' => 'Urzadzenie %nazwa_urzadzenia% oczekuje na odbior',
            'brak_urzadzenia' => null,
        ],
        Status::DO_ODBIORU_ID => [
            'repeat' => 7,
            'gwarancja' => 'Urzadzenie %producent% oczekuje na odbior',
            'ubezpieczenie' => '%nazwa_urzadzenia% ze zlecenia %nr_obcy% oczekuje na odbior',
            'odplatne' => 'Urzadzenie %nazwa_urzadzenia% oczekuje na odbior',
            'brak_urzadzenia' => null,
        ],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(HostedSms $hostedSms)
    {
        while (true) {
            $this->line('Listening to send SMSes... ' . now());

            $zlecenia = Zlecenie::with(['klient', 'urzadzenie', 'terminarz', 'last_sms'])->niezakonczone()->get();

            foreach ($zlecenia as $zlecenie) {
                if (in_array($zlecenie->klient->id, self::DISABLED_CUSTOMERS)) continue;

                $message = self::MESSAGES[$zlecenie->status_id] ?? null;

                if (! $message) continue;
                if ($zlecenie->last_sms and $zlecenie->last_sms->zlecenie_status_id == $zlecenie->status_id and ! $message['repeat']) continue;
                if ($message['repeat'] and $zlecenie->last_sms and $zlecenie->last_sms->created_at->copy()->startOfDay()->diffInDays(now()->startOfDay(), false) < $message['repeat']) continue;

                if (! $zlecenie->is_urzadzenie) {
                    $message = $message['brak_urzadzenia'];
                } elseif ($zlecenie->is_gwarancja) {
                    $message = $message['gwarancja'];
                } elseif ($zlecenie->is_ubezpieczenie) {
                    $message = $message['ubezpieczenie'];
                } else {
                    $message = $message['odplatne'];
                }

                // if (! $message) continue;

                $message = Str::replaceFirst('%producent%', $zlecenie->urzadzenie->producent, $message);
                $message = Str::replaceFirst('%nr_obcy%', $zlecenie->nr_obcy, $message);
                $message = Str::replaceFirst('%nazwa_urzadzenia%', $zlecenie->urzadzenie->nazwa, $message);
                if ($zlecenie->terminarz->is_data_rozpoczecia) {
                    $message = Str::replaceFirst('%data%', $zlecenie->data->format('Y-m-d'), $message);
                } else {
                    $message = Str::replaceFirst('%data%', '', $message);
                }

                if ($komorkowy = $zlecenie->klient->komorkowy_numeric) {
                    $hostedSms->send([$komorkowy], ($message . Sms::FOOTER), [
                        'auto' => true,
                        'zlecenie_id' => $zlecenie->id,
                        'zlecenie_status_id' => $zlecenie->status_id,
                    ]);
                } elseif (@$zlecenie->last_sms->type != 'error' and in_array($zlecenie->status_id, self::REQUIRED_MESSAGES)) {
                    $sms = new Sms;
                    $sms->type = 'error';
                    $sms->sender = 'System';
                    $sms->phones = [];
                    $sms->message = "Nie można wysłać SMS, brak nr komórkowego:\n> „{$message}”";
                    $sms->auto = true;
                    $sms->zlecenie_id = $zlecenie->id;
                    $sms->save();

                    // app('App\Http\Controllers\ZlecenieController')->apiZatwierdzBlad($zlecenie->id);
                }
            }

            sleep(5 * 60);
        }
    }
}