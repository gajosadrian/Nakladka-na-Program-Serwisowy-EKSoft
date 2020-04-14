<template>
    <div>
        <!-- <b-button @click="fetchZdjecia()" class="push">fetch zdjecia</b-button> -->

        <ZdjecieShow
            v-for="(_, i) in ZdjecieShow" :key="i"
            v-if="_.show && _.show2"
            :title="_.title"
            :required="_.required"
            :type="_.type"
            :zdjecia="_.zdjecia"
            :save_to="_.save_to"
            :urzadzenie_id="_.urzadzenie_id"
            :zlecenie_id="_.zlecenie_id"
            @fetchZdjecia="fetchZdjecia()"
        />
    </div>
</template>

<script>
export default {
    props: {
        _token: String,
        zlecenie_id: Number,
    },
    data() {
        return {
            zlecenie: null,
            urzadzenie: null,
            zdjecia: [],
        }
    },
    computed: {
        ZdjecieShow() {
            if (! this.zlecenie) return []
            
            return [
                {
                    title: 'Tabliczka',
                    show: Boolean(this.urzadzenie),
                    show2: true,
                    required: this.zlecenie.is_gwarancja || this.zlecenie.is_ubezpieczenie,
                    type: 'tabliczka',
                    zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'tabliczka'),
                    save_to: 'urzadzenie',
                    urzadzenie_id: this.urzadzenie_id,
                    zlecenie_id: this.zlecenie_id,
                },
                {
                    title: 'Gwarancja',
                    show: Boolean(this.urzadzenie),
                    show2: this.zlecenie.is_gwarancja,
                    required: this.zlecenie.is_gwarancja,
                    type: 'gwarancja',
                    zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'gwarancja'),
                    save_to: 'urzadzenie',
                    urzadzenie_id: this.urzadzenie_id,
                    zlecenie_id: this.zlecenie_id,
                },
                {
                    title: 'Polisa',
                    show: Boolean(this.urzadzenie),
                    show2: this.zlecenie.is_gwarancja || this.zlecenie.is_ubezpieczenie,
                    required: this.zlecenie.is_ubezpieczenie,
                    type: 'polisa',
                    zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'polisa'),
                    save_to: 'urzadzenie',
                    urzadzenie_id: this.urzadzenie_id,
                    zlecenie_id: this.zlecenie_id,
                },
                {
                    title: 'Dowód zakupu',
                    show: Boolean(this.urzadzenie),
                    show2: this.zlecenie.is_gwarancja || this.zlecenie.is_ubezpieczenie,
                    required: this.zlecenie.is_gwarancja || this.zlecenie.is_ubezpieczenie,
                    type: 'dowod_zakupu',
                    zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'dowod_zakupu'),
                    save_to: 'urzadzenie',
                    urzadzenie_id: this.urzadzenie_id,
                    zlecenie_id: this.zlecenie_id,
                },
                {
                    title: 'Urządzenie',
                    show: Boolean(this.urzadzenie),
                    show2: true,
                    required: this.zlecenie.is_ubezpieczenie,
                    type: 'urzadzenie',
                    zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'urzadzenie'),
                    save_to: 'urzadzenie',
                    urzadzenie_id: this.urzadzenie_id,
                    zlecenie_id: this.zlecenie_id,
                },
                {
                    title: 'Inne',
                    show: true,
                    show2: true,
                    required: false,
                    type: 'inne',
                    zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'inne'),
                    save_to: 'zlecenie',
                    zlecenie_id: this.zlecenie_id,
                },
            ]
        },
        urzadzenie_id() {
            return this.urzadzenie && this.urzadzenie.id || 0
        },
    },
    methods: {
        fetchZdjecia() {
            axios.get(route('zlecenia.pokazZdjecia', {
                zlecenie_id: this.zlecenie_id,
            }), {
                _token: this._token,
            })
                .then(res => {
                    this.zlecenie = res.data.zlecenie
                    this.urzadzenie = res.data.urzadzenie
                    this.zdjecia = res.data.zdjecia
                })
                .catch(err => {
                    console.log(err);
                })
        },
    },
    mounted() {
        this.fetchZdjecia()
    },
}
</script>