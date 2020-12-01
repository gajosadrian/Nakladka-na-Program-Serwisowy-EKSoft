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

    public const MESSAGES = [
        // -------------------------------------------------------------------------------------------------------------+++++
        Status::ZLECENIE_WPISANE_ID => [
            'repeat' => null,
            'gwarancja' => 'Informujemy o otrzymaniu zlecenia naprawy urzadzenia %producent%',
            'ubezpieczenie' => 'Informujemy o otrzymaniu zlecenia naprawy nr %nr_obcy%',
            'odplatne' => 'Informujemy o otrzymaniu zlecenia naprawy %nazwa_urzadzenia%',
            'brak_urzadzenia' => 'Informujemy o otrzymaniu zlecenia',
        ],
        Status::GOTOWE_DO_WYJAZDU_ID => [
            'repeat' => null,
            'gwarancja' => 'Zlecenie naprawy urzadzenia %producent% jest w trakcie planowania wizyty technika. Prosimy oczekiwac kontaktu w najblizszych dniach',
            'ubezpieczenie' => 'Zlecenie naprawy urzadzenia %nr_obcy% jest w trakcie planowania wizyty technika. Prosimy oczekiwac kontaktu w najblizszych dniach',
            'odplatne' => 'Zlecenie naprawy %nazwa_urzadzenia% jest w trakcie planowania wizyty technika. Prosimy oczekiwac kontaktu w najblizszych dniach',
            'brak_urzadzenia' => 'Zlecenie jest w trakcie planowania wizyty technika. Prosimy oczekiwac kontaktu w najblizszych dniach',
        ],
        Status::UMOWIONO_ID => [
            'repeat' => null,
            'gwarancja' => 'Potwierdzamy wizyte technika do naprawy urzadzenia %producent% %data%',
            'ubezpieczenie' => 'Potwierdzamy wizyte technika do naprawy urzadzenia %nr_obcy% %data%',
            'odplatne' => 'Potwierdzamy wizyte technika do naprawy %nazwa_urzadzenia% %data%',
            'brak_urzadzenia' => 'Potwierdzamy wizyte technika %data%',
        ],
        Status::NA_WARSZTACIE_ID => [
            'repeat' => null,
            'gwarancja' => 'Zlecenie naprawy urzadzenia %producent% otrzymalo status "na warsztacie". Prosimy oczekiwac dalszych informacji',
            'ubezpieczenie' => 'Zlecenie naprawy urzadzenia %nr_obcy% otrzymalo status "na warsztacie". Prosimy oczekiwac dalszych informacji',
            'odplatne' => 'Zlecenie naprawy %nazwa_urzadzenia% otrzymalo status "na warsztacie". Prosimy oczekiwac dalszych informacji',
            'brak_urzadzenia' => 'Zlecenie otrzymalo status "na warsztacie". Prosimy oczekiwac dalszych informacji',
        ],
        Status::DO_WYCENY_ID => [
            'repeat' => null,
            'gwarancja' => null,
            'ubezpieczenie' => 'Zlecenie naprawy urzadzenia %nr_obcy% jest w trakcie wyceny. Prosimy oczekiwac kontaktu',
            'odplatne' => 'Zlecenie naprawy %nazwa_urzadzenia% jest w trakcie wyceny. Prosimy oczekiwac kontaktu',
            'brak_urzadzenia' => 'Zlecenie jest w trakcie wyceny. Prosimy oczekiwac kontaktu',
        ],
        Status::ZAMOWIONO_CZESC_ID => [
            'repeat' => null,
            'gwarancja' => 'Informujemy o zamowieniu czesci do zlecenia naprawy urzadzenia %producent%',
            'ubezpieczenie' => 'Informujemy o zamowieniu czesci do zlecenia naprawy urzadzenia %nr_obcy%',
            'odplatne' => 'Informujemy o zamowieniu czesci do zlecenia naprawy %nazwa_urzadzenia%',
            'brak_urzadzenia' => 'Informujemy o zamowieniu czesci do zlecenia',
        ],
        Status::DZWONIC_PO_ODBIOR_ID => [
            'repeat' => 3,
            'gwarancja' => 'Urzadzenie %producent% jest naprawione i oczekuje na odbior',
            'ubezpieczenie' => '%nazwa_urzadzenia% ze zlecenia %nr_obcy% jest naprawione i oczekuje na odbior',
            'odplatne' => '%nazwa_urzadzenia% jest naprawione i oczekuje na odbior',
            'brak_urzadzenia' => null,
        ],
        Status::DO_ODBIORU_ID => [
            'repeat' => 3,
            'gwarancja' => 'Przypominamy, urzadzenie %producent% jest naprawione i oczekuje na odbior',
            'ubezpieczenie' => 'Przypominamy, %nazwa_urzadzenia% ze zlecenia %nr_obcy% jest naprawione i oczekuje na odbior',
            'odplatne' => 'Przypominamy, %nazwa_urzadzenia% jest naprawione i oczekuje na odbior',
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
                if ($zlecenie->last_sms and $zlecenie->last_sms->zlecenie_status_id == $zlecenie->status_id) continue;
                // if ($zlecenie->id != 53121) continue;

                if ($komorkowy = $zlecenie->klient->komorkowy_numeric) {
                    $message = self::MESSAGES[$zlecenie->status_id] ?? null;

                    if (! $message) continue;

                    if (! $zlecenie->is_urzadzenie) {
                        $message = $message['brak_urzadzenia'];
                    } elseif ($zlecenie->is_gwarancja) {
                        $message = $message['gwarancja'];
                    } elseif ($zlecenie->is_ubezpieczenie) {
                        $message = $message['ubezpieczenie'];
                    } else {
                        $message = $message['odplatne'];
                    }

                    if (! $message) continue;

                    $message = Str::replaceFirst('%producent%', $zlecenie->urzadzenie->producent, $message);
                    $message = Str::replaceFirst('%nr_obcy%', $zlecenie->nr_obcy, $message);
                    $message = Str::replaceFirst('%nazwa_urzadzenia%', $zlecenie->urzadzenie->nazwa, $message);
                    if ($zlecenie->terminarz->is_data_rozpoczecia) {
                        $message = Str::replaceFirst('%data%', $zlecenie->data->format('Y-m-d'), $message);
                    } else {
                        $message = Str::replaceFirst('%data%', '', $message);
                    }

                    $hostedSms->send([$komorkowy], ($message . Sms::FOOTER), [
                        'auto' => true,
                        'zlecenie_id' => $zlecenie->id,
                        'zlecenie_status_id' => $zlecenie->status_id,
                    ]);
                }
            }

            sleep(5 * 60);
        }
    }
}
