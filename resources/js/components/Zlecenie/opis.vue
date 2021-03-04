<template>
  <div>
    <b-textarea
      v-if="! is_technik"
      v-model="opis"
      id="opis"
      :rows="14"
      class="mb-3"
      @input="updateNotatka"
    />
    <div v-if="is_technik" class="mb-3">
      <div v-html="opis_formatted"></div>
    </div>
    <textarea v-model.trim="new_opis" @keydown.enter="appendNotatka()" class="form-control form-control-alt mb-2" placeholder="Dodaj opis.."></textarea>
    <b-button size="sm" variant="primary" :disabled="disable_button" @click="appendNotatka">Dodaj opis</b-button>
  </div>
</template>

<script>
import { debounce } from 'debounce'

export default {
  props: {
    zlecenie_id: String,
    is_technik: Number,
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
      let opis = this.opis.split('>>').join('<span class="font-w600 text-danger"><u>')
      opis = opis.split('<<').join('</u></span>')
      opis = opis.split("\n").join('<br>')
      return opis
    },

    opisRows() {
      const length = this.opis.split('\n').length
      return length + 2
    },
  },

  methods: {
    appendNotatka() {
      if (this.new_opis == '') return false;

      this.disable_button = true
      axios.post(route('zlecenia.api.append_opis', this.zlecenie_id), {
        opis: this.new_opis,
      }).then(response => {
        this.disable_button = false
        this.opis = response.data
        this.new_opis = ''

        this.$nextTick(() => {
          let $opis = $('textarea#opis')
          $opis.scrollTop($opis[0].scrollHeight)
          removeBladHtml()
        })
      })
    },

    updateNotatka() {
      axios.post(route('zlecenia.api.updateOpis', this.zlecenie_id), {
        opis: this.opis,
      })
    },
  },

  mounted: function () {
    axios.get(route('zlecenia.api.get_opis', {
      id: this.zlecenie_id,
    }))
      .then(response => {
        this.opis = response.data

        this.$nextTick(() => {
          let $opis = $('textarea#opis')
          $opis.scrollTop($opis[0].scrollHeight)
        })
      })
  },

  created() {
    this.updateNotatka = debounce(this.updateNotatka, 500)
  },
}
</script>
