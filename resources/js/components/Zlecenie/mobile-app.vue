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
                                <span v-if="termin.zlecenie.is_dzwonic" class="text-info">Dzwonić</span>
                                <span v-else-if="termin.zlecenie.checkable_umowiono && !termin.zlecenie.is_umowiono" class="text-danger">Nieumówione</span>
                                <span v-else><br></span>
                            </div>
                            <a v-if="!termin.zlecenie.is_soft_zakonczone" :href="termin.zlecenie.google_maps_route_link" class="btn btn-sm btn-light">
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
                    <h4 class="mb-2 text-center">{{ zlecenie.nr }} - {{ zlecenie.nr_obcy }}</h4>
                    <div class="clearfix">
                        <div class="float-left font-w700">Kontrahent:</div>
                        <div class="float-right"><i :class="zlecenie.znacznik_icon"></i> {{ zlecenie.znacznik_formatted }}</div>
                    </div>
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
                    <hr>
                    <div class="font-w700">Uwagi technika:</div>
                    <!-- <textarea v-model.trim="new_opis" class="form-control form-control-alt my-2" placeholder="Dodaj opis.." rows="3"></textarea>
                    <div class="text-right">
                        <button :disabled="disable_OpisButton" type="button" class="btn btn-light">Dodaj opis</button>
                    </div> -->
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
            technik: null,
            zlecenie: null,
            terminy: [],
            disable_OpisButton: false,
            new_opis: null,
        }
    },

    computed: {
    },

    methods: {
        fetchZlecenia(date = 0) {
            let self = this;
            axios.get(route('zlecenia.api.getFromTerminarz', {
                date_string: date,
            })).then(response => {
                let data = response.data;
                self.date_string = data.date_string;
                self.technik = data.technik;
                self.terminy = data.terminy;
            });
        },

		setZlecenie(zlecenie) {
			this.zlecenie = zlecenie;
			window.scrollTo(0, 0);
		}
    },

    mounted() {
        this.fetchZlecenia();
    }
}
</script>

<style scoped>

.border-bold {
    border-width: 3px !important;
}

</style>
