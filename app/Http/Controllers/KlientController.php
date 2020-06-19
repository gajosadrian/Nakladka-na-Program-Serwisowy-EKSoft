<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subiekt\KontrahentEwidencja;

class KlientController extends Controller
{
    public function apiFind(Request $request)
    {
        $limit = $request->limit ?? 10;
        $words = explode(' ', preg_replace('/\s+/', ' ', $request->search));
        $words_length = count($words);
        $collection = collect();

        for ($step = 0; $step < $words_length; $step++) {
            $length = $level = $words_length - $step;

            $query = KontrahentEwidencja::limit($limit)->orderBy('adr_NazwaPelna')->where('adr_TypAdresu', 1)
                ->where(function ($query) use ($step, $words, $length) {
                    for ($offset = 0; $offset <= $step; $offset++) {
                        $search_words = implode(' ', array_slice($words, $offset, $length));

                        $query = $query
                            ->orWhere('adr_Nazwa', 'like', "%{$search_words}%")
                            ->orWhere('adr_NazwaPelna', 'like', "%{$search_words}%")
                            ->orWhere('adr_Adres', 'like', "%{$search_words}%")
                            ->orWhere('adr_Miejscowosc', 'like', "%{$search_words}%")
                            ->orWhere('adr_Kod', $search_words)
                            ->orWhere('adr_Telefon', $search_words)
                        ;
                    }
                });

            $customers = $query->get();

            $customers = $customers->map(function ($customer) use ($level) {
                $customer->_level = $level;
                return $customer->only('_level', 'klient_id', 'nazwa', 'pelna_nazwa', 'adres', 'miejscowosc', 'kod_pocztowy', 'telefony_array');
            });

            $collection = $collection->merge($customers);
        }

        return response()->json(
            $collection->unique('klient_id')->take($limit)
        );
    }
}
