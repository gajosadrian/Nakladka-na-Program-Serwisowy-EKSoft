<template>
    <div>
        <div class="mb-3">
            <div v-html="opis_formatted"></div>
            <!-- <nl2br tag="div" :text="opis" /> -->
        </div>
        <textarea v-model.trim="new_opis" class="form-control form-control-alt mb-2" placeholder="Dodaj opis.."></textarea>
        <b-button size="sm" variant="primary" :disabled="disable_button" @click="appendNotatka">Dodaj opis</b-button>
    </div>
</template>

<script>
export default {
    props: {
        zlecenie_id: String,
    },

    data() {
        return {
            disable_button: false,
            opis: '',
            new_opis: '',
        }
    },

    computed: {
        opis_formatted() {
            let opis = this.opis.split('>>').join('<span class="font-w600 text-danger"><u>');
            opis = opis.split('<<').join('</u></span>');
            opis = opis.split("\n").join('<br>');
            return opis;
        },
    },

    methods: {
        appendNotatka() {
            if (this.new_opis == '') return false;

            this.disable_button = true;
            axios.post(route('zlecenia.api.append_opis', {
                id: this.zlecenie_id,
                opis: this.new_opis,
            })).then(response => {
                this.disable_button = false;
                this.opis = response.data;
                this.new_opis = '';
            });
        }
    },

    mounted: function () {
        let self = this;

        axios.get(route('zlecenia.api.get_opis', {
            id: this.zlecenie_id,
        })).then(response => {
            self.opis = response.data;
        });
    },
}
</script>
