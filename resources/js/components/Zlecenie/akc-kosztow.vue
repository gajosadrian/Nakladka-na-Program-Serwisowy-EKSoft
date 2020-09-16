<template>
  <div>
    <form v-show="edit" class="row gutters-tiny" @submit.prevent="submit">
      <div class="col-10 col-lg-4">
        <b-input
          v-model="kwota"
          type="number"
          size="sm"
          placeholder="Podaj kwotę brutto"
          ref="kwota"
          required
        />
      </div>
      <div class="col-2 col-lg-8">
        <b-button
          type="submit"
          variant="primary"
          size="sm"
          :disabled="sending"
        >
          Ok
        </b-button>
        <b-button variant="danger" size="sm" @click="setEdit(false)">
          Anuluj
        </b-button>
      </div>
    </form>
    <div v-show="! edit">
      <span v-if="akcKosztow" class="text-success font-w700">{{ akcKosztow }}</span>
      <span v-else class="text-muted">Brak informacji</span>
      <b-button variant="outline-primary" size="sm" @click="setEdit(true)">
        Zmień
      </b-button>
    </div>
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
      akcKosztow: null,
      kwota: null,
      edit: false,
      sending: false,
    }
  },
  methods: {
    submit() {
      this.sending = true

      axios.post(route('zlecenia.api.setAkcKosztow', this.zlecenie_id), {
        'kwota': this.kwota,
      })
        .then(response => {
          this.edit = false
          this.fetchAkcKosztow()

          const opis = response.data.opis
          document.querySelector('textarea#opis').value = opis
        })
        .catch(error => {})
        .finally(() => {
          this.sending = false
        })
    },

    fetchAkcKosztow() {
      axios.get(route('zlecenia.api.getAkcKosztow', this.zlecenie_id))
        .then(response => {
          this.akcKosztow = response.data.akc_kosztow
        })
    },

    setEdit(edit) {
      this.edit = edit
      this.kwota = null

      if (edit) {
        this.$nextTick(() => this.$refs.kwota.$el.focus())
      }
    },
  },

  mounted() {
    this.fetchAkcKosztow()
  },
}
</script>
