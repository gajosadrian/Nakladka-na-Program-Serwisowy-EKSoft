<template>
    <div>
        <h1>Mobile App</h1>

        <div v-if="terminy.length == 0">
            Ładowanie zleceń...
        </div>
        <div v-else v-show="! zlecenie_id" v-for="(termin, index) in terminy" class="block block-rounded block-fx-shadow">
            <div :class="{'bg-gray': !termin.zlecenie}" class="block-content block-content-full" style="cursor:pointer;">
                <div class="clearfix">
                    <div class="float-left">
                        <div v-if="termin.zlecenie">
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
                    <div class="float-right">
                        <div>marka, urządzenie</div>
                        <div>model</div>
                        <div>nr seryjny</div>
                    </div>
                </div>
            </div>
        </div>
        <div v-show="zlecenie_id">
            zlecenie_id
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            zlecenie_id: 0,
            terminy: [],
        }
    },

    computed: {
    },

    methods: {
        fetchZlecenia(date = false) {
            let self = this;
            axios.get(route('zlecenia.api.getFromTerminarz', {
                date_string: date,
            })).then(response => {
                let data = response.data;
                self.terminy = data.terminy;
            });
        },
    },

    mounted() {
        this.fetchZlecenia('2019-02-01');
    }
}
</script>
