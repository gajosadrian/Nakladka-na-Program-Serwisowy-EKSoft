<template>
    <div>
        <h1>Mobile App</h1>

        <div v-show="! zlecenie_id" v-for="(termin, index) in terminy" class="block block-rounded block-fx-shadow">
            <div class="block-content block-content-full">
                <div class="clearfix">
                    <div class="float-left">
                        <div class="font-w700">Kontrahent</div>
                        <div>miasto, adres</div>
                        <div>urządzenie, marka</div>
                    </div>
                    <div class="float-right">
                        <div>godzina</div>
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
            terminy: [true, true],
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
