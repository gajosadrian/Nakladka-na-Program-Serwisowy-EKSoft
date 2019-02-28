<template>
    <div>
        <textarea v-model="opis" class="form-control form-control-alt mb-2" placeholder="Dodaj opis.."></textarea>
        <b-button size="sm" :disabled="disable_button" @click="appendNotatka">Dodaj opis</b-button>
    </div>
</template>

<script>
export default {
    props: {
        zlecenie_id: String,
    },

    data() {
        return {
            opis: '',
            disable_button: false,
        }
    },

    methods: {
        appendNotatka() {
            this.disable_button = true;

            axios.post(route('zlecenia.api.append_opis', {
                id: this.zlecenie_id,
                opis: this.opis,
            })).then(response => {
                location.reload();
            });
        }
    }
}
</script>
