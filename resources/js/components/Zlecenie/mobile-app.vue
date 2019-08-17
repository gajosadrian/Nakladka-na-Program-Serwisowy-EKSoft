<template>
    <div>
        <div v-show="!zlecenie">
            <div v-if="technik">
                <h3>{{ technik.nazwa }} {{ date_string }}</h3>
            </div>

            <div v-if="terminy.length == 0">
                Ładowanie zleceń...
            </div>
            <div v-else v-for="(termin, index) in terminy" :class="{'bg-success-light': termin.zlecenie && termin.zlecenie.is_soft_zakonczone, 'border border-bold border-top-0 border-bottom-0 border-right-0 border-danger': termin.zlecenie && !termin.zlecenie.is_soft_zakonczone}" class="block block-rounded shadow-sm">
                <div @click="setZlecenie(termin.zlecenie)" :class="{'bg-gray': !termin.zlecenie}" class="block-content block-content-full p-2" style="cursor:pointer;">
                    <div class="clearfix">
                        <div class="float-left">
                            <div v-if="termin.zlecenie">
                                <div class="font-size-sm text-muted"><i :class="termin.zlecenie.znacznik_icon"></i> {{ termin.zlecenie.znacznik_formatted }}</div>
                                <div class="font-w700">{{ termin.zlecenie.klient.nazwa }}</div>
                                <div>{{ termin.zlecenie.klient.kod_pocztowy }} {{ termin.zlecenie.klient.miasto }}</div>
                                <div>{{ termin.zlecenie.klient.adres }}</div>
                            </div>
                            <div v-else>
                                {{ termin.temat || 'Zlecenie usunięte z terminarza' }}
                            </div>
                        </div>
                        <div class="float-right text-right">
                            <div>{{ termin.godzina_rozpoczecia }}</div>
                            <div v-if="termin.zlecenie" class="text-muted font-size-sm">{{ termin.przeznaczony_czas_formatted }}</div>
                        </div>
                    </div>
                    <div v-if="termin.zlecenie" class="clearfix">
                        <div class="float-left">
                            <div class="font-w700">
                                <span v-if="termin.zlecenie.is_warsztat" class="text-warning">Warsztat</span>
                                <span v-else-if="termin.zlecenie.is_dzwonic" class="text-info">Dzwonić</span>
                                <span v-else-if="termin.zlecenie.checkable_umowiono && !termin.zlecenie.is_umowiono" class="text-danger">Nieumówione</span>
                                <span v-else><br></span>
                            </div>
                            <a v-if="!termin.zlecenie.is_soft_zakonczone && !termin.zlecenie.is_warsztat" :href="termin.zlecenie.google_maps_route_link" class="btn btn-sm btn-light">
                                <i class="fa fa-map-marker-alt"></i>
                                Mapa
                            </a>
                        </div>
                        <div class="float-right text-right">
                            <div>{{ termin.zlecenie.urzadzenie.producent }}, {{ termin.zlecenie.urzadzenie.nazwa }}</div>
                            <div>{{ termin.zlecenie.urzadzenie.model }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="zlecenie">
            <div class="block block-rounded">
                <div :class="{'border-success': zlecenie.is_soft_zakonczone, 'border-danger': !zlecenie.is_soft_zakonczone}" class="block-content block-content-full border border-bold border-left-0 border-bottom-0 border-right-0">
                    <div class="mb-2 text-center">
                        <h4 class="mb-0">{{ zlecenie.nr }} <span v-if="zlecenie.nr_obcy">- {{ zlecenie.nr_obcy }}</span></h4>
                        <div><i :class="zlecenie.znacznik_icon"></i> {{ zlecenie.znacznik_formatted }}</div>
                    </div>
                    <div class="font-w700">Kontrahent:</div>
                    <div>{{ zlecenie.klient.nazwa }}</div>
                    <div>{{ zlecenie.klient.kod_pocztowy }} {{ zlecenie.klient.miasto }}, {{ zlecenie.klient.adres }}</div>
                    <div class="mt-1">
                        <div v-for="(telefon, t_index) in zlecenie.klient.telefony" class="mt-2">
                            <a :href="'tel:'+telefon" class="btn btn-light">
                                <i class="fa fa-phone text-success"></i>
                                {{ telefon }}
                            </a>
                        </div>
                        <div class="mt-2">
                            <a :href="zlecenie.google_maps_route_link" class="btn btn-light">
                                <i class="fa fa-map-marker-alt text-info"></i>
                                Mapa
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="font-w700">Opis:</div>
                    <nl2br tag="div" :text="zlecenie.opis" />
                    <div class="font-w700 mt-2">Kosztorys:</div>
                    <div class="font-size-sm">
                        <div v-for="(pozycja, index2) in zlecenie.kosztorys_pozycje" v-if="pozycja.ilosc > 0" class="mt-2">
                            <div class="clearfix border border-left-0 border-right-0 border-bottom-0">
                                <div class="float-left">
                                    <div class="font-w700">{{ pozycja.nazwa }}</div>
                                    <div>Symbol: <span class="font-w600">{{ pozycja.symbol }}</span></div>
                                    <div v-if="pozycja.symbol_dostawcy">Symbol dost.: <span class="font-w600">{{ pozycja.symbol_dostawcy }}</span></div>
                                    <div v-if="pozycja.opis">Opis: <span class="font-w600">{{ pozycja.opis }}</span></div>
                                    <div>
                                        Cena:
                                        <span class="font-w600">
                                            <template v-if="pozycja.ilosc === 1">
                                                {{ pozycja.cena_brutto.toFixed(2) }}
                                            </template>
                                            <template v-else>
                                                {{ pozycja.ilosc }} x {{ pozycja.cena_brutto.toFixed(2) }} = {{ pozycja.wartosc_brutto }}
                                            </template>
                                            zł
                                        </span>
                                    </div>
                                </div>
                                <div class="float-right text-right">
                                    <select v-if="pozycja.is_towar" v-model="parts[pozycja.id]" :class="{
                                        'bg-success': parts[pozycja.id] == 'mounted',
                                        'bg-danger': parts[pozycja.id] == 'unmounted',
                                        'bg-warning': parts[pozycja.id] == 'written'
                                    }" class="form-control form-control-sm mt-1" style="width:30px;">
                                        <option value="" disabled>{{ pozycja.nazwa }}</option>
                                        <option value="">---</option>
                                        <option v-for="n in pozycja.ilosc" :key="n" value="mounted">Zamontowane - {{ n }} szt.</option>
                                        <option value="unmounted">Niezamontowane</option>
                                        <option value="written">Rozpisane</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="font-w700">Uwagi technika:</div>
                    <div v-if="!zlecenie.is_zakonczone">
                        <textarea v-model.trim="new_opis" :class="{'border border-danger': is_new_opis}" class="form-control form-control-alt my-2" placeholder="Dodaj opis.." rows="3"></textarea>
                        <div class="text-right">
                            <button @click="addOpis" :disabled="disable_OpisButton" :class="{'btn-light': !is_new_opis, 'btn-danger': is_new_opis}" type="button" class="btn">Dodaj opis</button>
                        </div>
                    </div>
                    <div v-else>
                        <p>Nie można już edytować opisu zlecenia.</p>
                    </div>
                </div>
            </div>

			<div class="pt-5"></div>
            <nav class="fixed-bottom bg-white p-2 text-right" style="box-shadow: 0px 6px 2px 8px rgba(0,0,0,.08);">
                <button @click="setZlecenie(null)" type="button" class="btn bg-white text-muted">
                    <i class="fa fa-reply"></i>
                    Cofnij
                </button>
            </nav>
        </div>
    </div>
</template>

<script>
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};

export default {
    data() {
        return {
            renderComponent: true,
            timer: null,
            date: 0,
            technik: null,
            zlecenie: null,
            terminy: [],
            disable_OpisButton: false,
            new_opis: '',
            parts: [],
        }
    },

    mounted() {
        this.fetchZlecenia();
        this.timer = setInterval(this.fetchZlecenia, 120000); // 2 min
    },

    beforeDestroy() {
        this.cancelAutoUpdate();
    },

    computed: {
        is_new_opis() {
            if (this.new_opis.length > 0) {
                return true;
            }
            return false;
        },
    },

    methods: {
        fetchZlecenia() {
            axios.get(route('zlecenia.api.getFromTerminarz', {
                date_string: this.date,
            })).then(response => {
                let data = response.data;
                this.date_string = data.date_string;
                this.technik = data.technik;
                this.terminy = data.terminy;
            });
            this.updateZlecenieInstance();
        },

        fetchZlecenie() {
            if (! this.zlecenie) return false;
        },

        updateZlecenieInstance() {
            if (! this.zlecenie) return false;
            this.setZlecenie(this.getZlecenieById(this.zlecenie.id), false);
            console.log('updated');
        },

        getZlecenieById(zlecenie_id) {
            let new_zlecenie = false
            this.terminy.forEach(function (termin, index) {
                if (termin.zlecenie.id == zlecenie_id) {
                    new_zlecenie = termin.zlecenie;
                }
            });
            return new_zlecenie;
        },

		setZlecenie(zlecenie, scroll = true) {
			this.zlecenie = zlecenie;
            if (scroll) {
                window.scrollTo(0, 0);
            }
		},

        addOpis() {
            if (this.new_opis == '') return false;
            if (! this.zlecenie) return false;

            this.disable_OpisButton = true;
            axios.post(route('zlecenia.api.append_opis', {
                id: this.zlecenie.id,
                opis: this.new_opis,
            })).then(response => {
                this.disable_OpisButton = false;
                this.new_opis = '';
            });

            this.changeStatus(41);
            this.fetchZlecenia();
        },

        changeStatus(status_id) {
            if (! this.zlecenie) return false;
            axios.post(route('zlecenia.api.change_status', {
                id: this.zlecenie.id,
                status_id: status_id,
                remove_termin: 0,
                terminarz_status_id: '12897956',
            }));
        },

        cancelAutoUpdate() {
            clearInterval(this.timer);
        },
    },
}
</script>

<style scoped>

.border-bold {
    border-width: 3px !important;
}

</style>
