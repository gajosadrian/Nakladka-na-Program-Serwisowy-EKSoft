<template>
    <div>
        UrzadzeniaZdjecia
    </div>
</template>

<script>
export default {
    props: {
        _token: String,
    },
    data() {
        return {
            urzadzenia: [],
        }
    },
    computed: {
    },
    methods: {
        fetchUrzadzenia() {
            axios.get(route('urzadzenie.zdjecia'), {
                _token: this._token,
            })
                .then(res => {
                    this.urzadzenia = res.data.urzadzenia
                })
                .catch(err => {
                    console.log(err)
                })
        },
    },
    mounted() {
        this.fetchUrzadzenia()
    },
}
</script>
