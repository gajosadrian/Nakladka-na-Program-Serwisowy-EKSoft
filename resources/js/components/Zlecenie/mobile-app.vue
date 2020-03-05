<template>
    <div>
        <div v-show="!zlecenie">
            <div v-if="technik" class="row mb-3">
                <h3 class="col-7">
                    {{ technik.nazwa }}
                    <b-link :href="route('logout')" class="btn btn-light btn-sm border">
                        <i class="fa fa-sign-out-alt"></i>
                    </b-link>
                </h3>
                <date-picker class="col-5" @input="terminy=[];fetchZlecenia()" v-model="date" lang="en" valueType="format" :first-day-of-week="1"></date-picker>
            </div>

            <div v-if="terminy.length == 0">
                Ładowanie zleceń...
            </div>
            <div v-else v-for="(termin, index) in terminy" :class="{'bg-success-light': termin.zlecenie && termin.zlecenie.is_soft_zakonczone && !termin.zlecenie.is_do_wyjasnienia, 'bg-danger-light': termin.zlecenie && termin.zlecenie.is_do_wyjasnienia && !termin.zlecenie.is_soft_zakonczone, 'bg-info-light': termin.zlecenie && termin.zlecenie.is_do_wyjasnienia && termin.zlecenie.is_soft_zakonczone, 'border border-bold border-top-0 border-bottom-0 border-right-0 border-danger': termin.zlecenie && !termin.zlecenie.is_soft_zakonczone}" class="block block-rounded shadow-sm">
                <div @click="setZlecenie(termin.zlecenie, true, true, !termin.zlecenie); plastic_click_001.play();" :class="{'bg-gray': !termin.zlecenie && !termin.klient, 'bg-danger-light': !termin.zlecenie && termin.klient}" class="block-content block-content-full p-2" style="cursor:pointer;">
                    <div class="clearfix">
                        <div class="float-left">
                            <div v-if="termin.zlecenie">
                                <div v-if="termin.temat && !termin.zlecenie.is_dzwonic && !termin.zlecenie.is_soft_zakonczone" class="mb-1 p-1 font-w600 text-danger">
                                    <u>{{ termin.temat }}</u>
                                </div>
                                <div v-if="!termin.zlecenie.is_do_wyjasnienia" class="font-size-sm text-muted"><i :class="termin.zlecenie.znacznik_icon"></i> {{ termin.zlecenie.znacznik_formatted }}</div>
                                <div><span class="font-w700">{{ termin.zlecenie.klient.nazwa }}</span> <span class="font-size-sm">- {{ termin.zlecenie.klient.symbol }}</span></div>
                                <div>{{ termin.zlecenie.klient.kod_pocztowy }} {{ termin.zlecenie.klient.miasto }}</div>
                                <div>{{ termin.zlecenie.klient.adres }}</div>
                            </div>
                            <div v-else>
                                <div v-if="termin.klient" class="mb-1">
                                    <div><span class="font-w700">{{ termin.klient.nazwa }}</span></div>
                                    <div>{{ termin.klient.kod_pocztowy }} {{ termin.klient.miasto }}</div>
                                    <div>{{ termin.klient.adres }}</div>
                                    <div v-for="(telefon, t_index) in termin.klient.telefony">
                                        <a :href="'tel:'+telefon" class="btn btn-light btn-sm mr-1">
                                            <i class="fa fa-phone text-success"></i>
                                            {{ telefon }}
                                        </a>
                                    </div>
                                </div>
                                <div :class="{'font-w700 text-white': !termin.zlecenie && termin.klient}">{{ termin.temat || 'Zlecenie usunięte z terminarza' }}</div>
                            </div>
                        </div>
                        <div class="float-right text-right">
                            <template v-if="termin.zlecenie && !termin.zlecenie.is_do_wyjasnienia">
                                <div>{{ termin.godzina_rozpoczecia }}</div>
                                <div v-if="termin.zlecenie" class="text-muted font-size-sm">{{ termin.przeznaczony_czas_formatted }}</div>
                                <div v-if="termin.zlecenie" class="text-muted font-size-sm">{{ termin.zlecenie.czas_trwania }} dni temu</div>
                            </template>
                            <template v-else-if="termin.zlecenie && termin.zlecenie.is_do_wyjasnienia">
                                <div class="bg-danger text-white font-size-sm font-w600 shadow rounded p-1">Do wyjaśnienia</div>
                            </template>
                        </div>
                    </div>
                    <div v-if="termin.zlecenie" class="clearfix">
                        <div class="float-left">
                            <div class="font-w700">
                                <span v-if="termin.zlecenie.is_warsztat" class="bg-warning px-1">Warsztat</span>
                                <span v-else-if="termin.zlecenie.is_dzwonic" class="bg-info text-white px-1">Dzwonić</span>
                                <span v-else-if="termin.zlecenie.checkable_umowiono && !termin.zlecenie.is_umowiono" class="bg-danger text-white px-1">Nieumówione</span>
                                <span v-else><br></span>
                            </div>
                            <!-- <a v-if="show_map && !termin.zlecenie.is_soft_zakonczone && !termin.zlecenie.is_warsztat && !termin.zlecenie.is_do_wyjasnienia" :href="termin.zlecenie.google_maps_route_link" class="btn btn-sm btn-light">
                                <i class="fa fa-map-marker-alt"></i>
                                Mapa
                            </a> -->
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
                    <div class="mb-3">
                        <div><span class="font-w700">Przyjął:</span> {{ zlecenie.przyjmujacy_nazwa }}</div>
                        <div><span class="font-w700">Trwanie zlecenia:</span> {{ zlecenie.czas_trwania }} dni</div>
                        <div v-if="zlecenie.is_umowiono"><span class="font-w700">Umówił:</span> {{ zlecenie.umowiono_pracownik_nazwa }} {{ zlecenie.umowiono_data }}</div>
                    </div>
                    <div class="font-w700">Kontrahent:</div>
                    <div>{{ zlecenie.klient.nazwa }} <span class="font-size-sm">- {{ zlecenie.klient.symbol }}</span></div>
                    <div>{{ zlecenie.klient.kod_pocztowy }} {{ zlecenie.klient.miasto }}, {{ zlecenie.klient.adres }}</div>
                    <div class="mt-1">
                        <div v-if="show_map" class="push">
                            <a :href="zlecenie.google_maps_route_link" class="btn btn-light">
                                <i class="fa fa-map-marker-alt text-info"></i>
                                Mapy Google
                            </a>
                            <a href="https://play.google.com/store/apps/details?id=pl.neptis.yanosik.mobi.android&launch=true" class="btn btn-light"
                                v-clipboard:copy="address_formatted"
                            >
                                <i class="fa fa-map-marker-alt text-danger"></i>
                                Yanosik
                            </a>
                        </div>
                        <div v-for="(telefon, t_index) in zlecenie.klient.telefony" class="mt-2">
                            <a :href="'tel:'+telefon" class="btn btn-light">
                                <i class="fa fa-phone text-success"></i>
                                {{ telefon }}
                            </a>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-w700">{{ zlecenie.urzadzenie.producent }}, {{ zlecenie.urzadzenie.nazwa }}</div>
                        <div style="font-family:consolas;">{{ zlecenie.urzadzenie.model }}</div>
                        <div style="font-family:consolas;">{{ zlecenie.urzadzenie.nr_seryjny }}</div>
                        <div>
                            <a :href="zlecenie.zdjecia_url" class="btn btn-sm btn-rounded" :class="{ 'btn-danger': true }" target="_blank">
                                <i class="fa fa-camera"></i> Zdjęcia
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="font-w700">Opis:</div>
                    <div v-html="opis_formatted"></div>
                    <!-- <nl2br tag="div" :text="opis_formatted" /> -->
                    <div v-if="dodawanie_opisu" class="font-w600 text-danger">
                        <i class="fa fa-spinner fa-pulse"></i>
                        Dodawanie opisu...
                    </div>
                    <template v-if="zlecenie.kosztorys_pozycje.length > 0">
                        <div class="mt-2">
                            <span class="font-w700">Suma kosztorysu:</span>
                            <span>{{ suma_kosztorysu.toFixed(2) }} zł</span>
                        </div>
                        <!-- <div class="font-w700">Kosztorys:</div> -->
                        <div class="font-size-sm">
                            <div v-for="(pozycja, index2) in zlecenie.kosztorys_pozycje" v-if="(pozycja.ilosc > 0 || (pozycja.naszykowana_czesc && pozycja.naszykowana_czesc.is_editable))" class="mt-2">
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
                                                    {{ pozycja.ilosc }} x {{ pozycja.cena_brutto.toFixed(2) }} = {{ pozycja.wartosc_brutto.toFixed(2) }}
                                                </template>
                                                zł
                                            </span>
                                        </div>
                                    </div>
                                    <div v-if="pozycja.is_towar && !pozycja.is_ekspertyza && !pozycja.is_zamowione && Number.isInteger(pozycja.ilosc) && (!pozycja.naszykowana_czesc || pozycja.naszykowana_czesc.is_editable)" class="float-right text-right">
                                        <select v-model="parts[pozycja.id]" @change="mountPart(pozycja, parts[pozycja.id])" :class="{
                                            'bg-success': String(parts[pozycja.id]).includes('zamontowane') && !String(parts[pozycja.id]).includes('niezamontowane'),
                                            'bg-danger': String(parts[pozycja.id]).includes('niezamontowane'),
                                            'bg-warning': String(parts[pozycja.id]).includes('rozpisane'),
                                            'bg-secondary': ! parts[pozycja.id],
                                        }" class="form-control form-control-sm mt-1" style="width:30px;">
                                            <option value="" disabled>{{ pozycja.nazwa }}</option>
                                            <!-- <option value="">---</option> -->
                                            <option v-for="n in (pozycja.naszykowana_czesc && pozycja.naszykowana_czesc.ilosc || pozycja.ilosc)" :key="n" :value="'zamontowane#' + n">Zamontowane - {{ n }} szt.</option>
                                            <option v-for="n in (pozycja.naszykowana_czesc && pozycja.naszykowana_czesc.ilosc || pozycja.ilosc)" :key="n" :value="'rozpisane#' + n">Rozpisane - {{ n }} szt.</option>
                                            <option value="niezamontowane">Niezamontowane</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <hr>
                    <div class="font-w700">Uwagi technika:</div>
                    <div v-if="!zlecenie.is_zakonczone">
                        <textarea v-model.trim="new_opis" :class="{'border border-danger': is_new_opis}" class="form-control form-control-alt my-2" placeholder="Dodaj opis.." rows="6"></textarea>
                        <div class="text-right">
                            <button @click="addOpis" :disabled="disable_OpisButton" :class="{'btn-light': !is_new_opis, 'btn-danger': is_new_opis}" type="button" class="btn">Dodaj opis</button>
                        </div>
                    </div>
                    <div v-else>
                        <p>Nie można już edytować opisu zlecenia.</p>
                    </div>
                </div>
            </div>

            <div class="d-none d-md-block">
    			<div class="pt-5"></div>
                <nav class="fixed-bottom bg-white p-2 text-right" style="box-shadow: 0px 6px 2px 8px rgba(0,0,0,.08);">
                    <button @click="setZlecenie(null, true, false); plastic_click_002.play();" type="button" class="btn bg-white text-muted">
                        <i class="fa fa-reply"></i>
                        Cofnij
                    </button>
                </nav>
            </div>
        </div>
        <div class="pb-3 text-center">
            <hr>
            © Adrian Gajos 2019
        </div>
    </div>
</template>

<script>
// if (window.location.href == route('zlecenia.mobileApp')) {
//     history.pushState(null, null, location.href);
//     window.onpopstate = function () {
//         history.go(1);
//     };
// }

export default {
    data() {
        return {
            click_21: null,
            plastic_click_001: null,
            plastic_click_002: null,
            _token: '',
            scroll_pos: 0,
            timer: null,
            date: new Date().toJSON().slice(0,10),
            show_map: null,
            technik: null,
            zlecenie: null,
            terminy: [],
            disable_OpisButton: false,
            new_opis: '',
            dodawanie_opisu: false,
            parts: [],
        }
    },

    mounted() {
        this.fetchZlecenia();
        this.timer = setInterval(this.fetchZlecenia, 300000); // 5 min
        // this.click_21 = new Audio('/sounds/click_021.mp3');
        this.click_21 = new Audio('/zlecenia/public/sounds/click_021.mp3');
        this.plastic_click_001 = new Audio('/zlecenia/public/sounds/plastic_click_001.mp3');
        this.plastic_click_002 = new Audio('/zlecenia/public/sounds/plastic_click_002.mp3');
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

        opis_formatted() {
            if ( ! this.zlecenie) return false;
            let opis = this.zlecenie.opis.split('>>').join('<span class="font-w600 text-danger"><u>');
            opis = opis.split('<<').join('</u></span>');
            opis = opis.split("\n").join('<br>');
            return opis;
        },

        address_formatted() {
            if ( ! this.zlecenie) return false;
            return this.zlecenie.klient.miasto + ', ' + this.zlecenie.klient.adres;
        },

        suma_kosztorysu() {
            if ( ! this.zlecenie) return 0;
            return this.zlecenie.kosztorys_pozycje.reduce((a, b) => a + (b.wartosc_brutto || 0), 0);
        },
    },

    methods: {
        mountPart(pozycja, value) {
            let [type, ilosc] = value.split('#');

            axios.post( route('czesci.updateZamontuj', {
                kosztorys_pozycja: pozycja.id,
            }), {
                _token: this._token,
                _method: 'patch',
                technik_id: this.technik.id,
                type,
                ilosc,
            })
            .then((response) => {
                console.log('success');
            })
            .catch((error) => {
                this.parts[pozycja.id] = false;
                this.fetchZlecenia();
                swal({
                    position: 'center',
                    type: 'error',
                    title: 'Wystąpił problem',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        },

        fetchZlecenia() {
            axios.get(route('zlecenia.api.getFromTerminarz', {
                date_string: this.date,
            })).then(response => {
                let data = response.data;
                this.date_string = data.date_string;
                this.technik = data.technik;
                this.terminy = data.terminy;
                this.show_map = data.show_map;

                this.parts = [];
                this.terminy.map((termin) => {
                    if (termin.zlecenie) {
                        termin.zlecenie.kosztorys_pozycje.map((pozycja) => {
                            if (pozycja.is_zamontowane) {
                                this.parts[pozycja.id] = 'zamontowane#' + pozycja.ilosc;
                            } else if (pozycja.is_rozpisane) {
                                this.parts[pozycja.id] = 'rozpisane#' + pozycja.ilosc;
                            } else if (pozycja.is_niezamontowane) {
                                this.parts[pozycja.id] = 'niezamontowane';
                            } else {
                                this.parts[pozycja.id] = false;
                            }
                        });
                    }
                });

                this.updateZlecenieInstance();
            });
        },

        fetchZlecenie() {
            if (! this.zlecenie) return false;
        },

        updateZlecenieInstance() {
            if (! this.zlecenie) return false;
            let zlecenie_id = this.getZlecenieById(this.zlecenie.id);
            if (zlecenie_id) {
                this.setZlecenie(zlecenie_id, false);
                // console.log('updated');
            }
        },

        getZlecenieById(zlecenie_id) {
            let new_zlecenie = false
            this.terminy.forEach(function (termin, index) {
                if (termin.zlecenie && termin.zlecenie.id == zlecenie_id) {
                    new_zlecenie = termin.zlecenie;
                }
            });
            return new_zlecenie;
        },

		setZlecenie(zlecenie, scroll = true, scroll_reset = true, blocked = false) {
            if (blocked) return;

            if (zlecenie) {
                this.rememberScroll();
            }
			this.zlecenie = zlecenie;
            if (scroll) {
                this.doScroll(scroll_reset);
            }
            navigator.vibrate(50);
		},

        doScroll(reset = false) {
            if (reset) {
                window.scrollTo(0, 0);
            } else {
                setTimeout(() => {
                    console.log('scroll to', this.scroll_pos);
                    window.scrollTo(0, this.scroll_pos);
                }, 100);
            }
        },

        rememberScroll() {
            // this.scroll_pos = window.scrollY;
        },

        addOpis() {
            if (this.new_opis == '') return false;
            if (! this.zlecenie) return false;

            this.disable_OpisButton = true;
            this.dodawanie_opisu = true;
            axios.post(route('zlecenia.api.append_opis', {
                id: this.zlecenie.id,
                opis: this.new_opis,
            }), {
                _token: this._token,
            })
            .then(response => {
                this.disable_OpisButton = false;
                this.dodawanie_opisu = false;
                this.new_opis = '';
                // swal({
                //     position: 'center',
                //     type: 'success',
                //     title: 'Dodano opis',
                //     showConfirmButton: false,
                //     timer: 1500
                // });

                this.changeStatus(41);
                this.fetchZlecenia();
            })
            .catch((error) => {
                this.disable_OpisButton = false;
                this.dodawanie_opisu = false;
                swal({
                    position: 'center',
                    type: 'error',
                    title: 'Wystąpił problem',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
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

        handleScroll () {
            if (this.zlecenie) return;
            this.scroll_pos = window.scrollY;
        },
    },

    created() {
        let self = this;

        window.addEventListener('scroll', this.handleScroll);

        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
            self.setZlecenie(null, true, false);
            self.plastic_click_002.play();
        };
    },

    destroyed () {
        window.removeEventListener('scroll', this.handleScroll);
    },
}
</script>

<style scoped>

.border-bold {
    border-width: 3px !important;
}

</style>
