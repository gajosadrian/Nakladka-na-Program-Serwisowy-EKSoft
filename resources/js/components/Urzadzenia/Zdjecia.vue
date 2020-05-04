<template>
    <div>
        <h4>Strona: {{ page }}</h4>

        <b-row v-for="zlecenie in zlecenia" :key="zlecenie.id">
            <b-col cols="12" lg="6" xl="4">
                <b-block>
                    <template slot="content">
                        <Inputs :urzadzenie="zlecenie.urzadzenie" />
                    </template>
                </b-block>
            </b-col>
            <b-col cols="12" lg="6" xl="8">
                <b-block full>
                    <template slot="content">
                        <div
                            v-for="zdjecie in zlecenie.zdjecia_do_urzadzenia.filter(zdjecie => zdjecie.type == 'tabliczka')" :key="zdjecie.id"
                            class="text-center"
                        >
                            <Zdjecie :url="zdjecie.url" />
                        </div>
                    </template>
                </b-block>
            </b-col>
        </b-row>

        <div class="push">
            <b-button disabled>Strona: {{ page }}</b-button>
            <b-button @click="prevPage()" variant="danger" :disabled="(page <= 1)">Cofnij</b-button>
            <b-button @click="nextPage()" variant="success">Dalej</b-button>
        </div>
    </div>
</template>

<script>
import Inputs from './Inputs.vue'
import Zdjecie from './Zdjecie.vue'

export default {
    components: {
        Inputs,
        Zdjecie,
    },
    props: {
        _token: String,
    },
    data() {
        return {
            zlecenia: [],
            page: 1,
        }
    },
    methods: {
        fetchZlecenia() {
            axios.get(route('urzadzenie.zdjecia', {
                page: this.page,
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
        prevPage() {
            this.page--
            this.page = this.page || 1
            this.fetchZlecenia()
            window.scrollTo(0, 0)
        },
        nextPage() {
            this.page++
            this.fetchZlecenia()
            window.scrollTo(0, 0)
        },
    },
    mounted() {
        this.fetchZlecenia()
    },
}
</script>
