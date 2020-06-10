<template>
    <div>
        <div class="push">
            <date-picker v-model="dates" type="date" valueType="format" range placeholder="Wybierz zakres" :lang="date_lang" :first-day-of-week="1" />
            <span class="ml-3">
                <span class="font-w600 text-info">{{ zlecenia.length }}</span> urządzeń
            </span>
            <span class="ml-3">
                Zdjęcia w <span class="font-w600 text-info">{{ zdjecia_percent }}%</span> zleceń
            </span>
        </div>

        <b-row v-for="zlecenie in zlecenia" :key="zlecenie.id">
            <b-col cols="12" lg="6" xl="4">
                <b-block>
                    <template slot="content">
                        <Info :zlecenie="zlecenie" class="push" />
                        <Inputs :urzadzenie="zlecenie.urzadzenie" />
                    </template>
                </b-block>
            </b-col>
            <b-col cols="12" lg="6" xl="8">
                <b-block full>
                    <template slot="content">
                        <template v-if="hasZdjecia(zlecenie)">
                            <div
                                v-for="zdjecie in getZdjeciaTabliczka(zlecenie)" :key="zdjecie.id"
                                class="text-center"
                            >
                                <Zdjecie :url="zdjecie.url" />
                            </div>
                        </template>
                        <div v-else>Brak zdjęcia tabliczki</div>
                    </template>
                </b-block>
            </b-col>
        </b-row>
    </div>
</template>

<script>
import Inputs from './Inputs.vue'
import Zdjecie from './Zdjecie.vue'
import Info from './Info.vue'

export default {
    components: {
        Inputs,
        Zdjecie,
        Info,
    },
    props: {
        _token: String,
    },
    data() {
        return {
            zlecenia: [],
            dates: [],
            date_lang: {
                months: ['Styczeń', 'Luty', 'Marzec', 'Kwieceń', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'],
                monthsShort: ['Sty', 'Lut', 'Mar', 'Kwi', 'Maj', 'Cze', 'Lip', 'Sie', 'Wrz', 'Paź', 'Lis', 'Gru'],
                weekdaysShort: ['Niedz', 'Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob'],
                weekdaysMin: ['Ni', 'Po', 'Wt', 'Śr', 'Cz', 'Pi', 'So'],
            },
        }
    },
    watch: {
        dates() {
            if (this.dates.length == 0) return;
            this.fetchZlecenia()
        },
    },
    computed: {
        zlecenia_ze_zdjeciami() {
            return this.zlecenia.filter(zlecenie => {
                return this.getZdjeciaTabliczka(zlecenie).length > 0
            })
        },
        zdjecia_percent() {
            return Math.round(this.zlecenia_ze_zdjeciami.length / this.zlecenia.length * 100) || 0
        },
    },
    methods: {
        fetchZlecenia() {
            axios.get(route('urzadzenie.zdjecia', {
                date_start: this.dates[0],
                date_end: this.dates[1],
            }), {
                _token: this._token,
            })
                .then(res => {
                    this.zlecenia = res.data.zlecenia
                })
                .catch(err => {
                    console.log(err)
                })
        },
        hasZdjecia(zlecenie) {
            return zlecenie.zdjecia_do_urzadzenia.length > 0
        },
        getZdjeciaTabliczka(zlecenie) {
            return zlecenie.zdjecia_do_urzadzenia.filter(zdjecie => zdjecie.type == 'tabliczka')
        },
    },
    mounted() {
        this.fetchZlecenia()
    },
}
</script>
