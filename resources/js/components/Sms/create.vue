<template>
  <div>
    <b-block full>
      <template slot="content">
        <b-form @submit.prevent="submit">

          <!-- Phones -->
          <b-form-group
            label="Telefony"
            description="Podawaj po przecinku"
          >
            <b-form-input v-model.trim="form.phones" />
          </b-form-group>

          <!-- Message -->
          <b-form-group
            label="Treść wiadomości"
            :description="`Ilość SMS: ${smsCount}`"
          >
            <b-form-textarea
              v-model.trim="form.message"
              rows="3"
              max-rows="6"
            />
          </b-form-group>

          <!-- Buttons -->
          <b-button type="submit" :variant="hasErrors ? 'danger' : 'success'" :disabled="hasErrors">
            <span v-if="! sending">Wyślij</span>
            <span v-else>Proszę czekać...</span>
          </b-button>

        </b-form>
      </template>
    </b-block>
  </div>
</template>

<script>
export default {
  data() {
    return {
      sending: false,
      form: {
        phones: '',
        message: '',
      },
    }
  },

  computed: {
    message() {
      const polishCharacters = {
        'ą': 'a', 'ć': 'c', 'ę': 'e', 'ł': 'l', 'ń': 'n',
        'ó': 'o', 'ś': 's', 'ź': 'z', 'ż': 'z'
      }
      let message = this.form.message
      message = [...message].map((letter) => polishCharacters[letter] || letter).join('')
      return `Dzien dobry, ${message}\n\n--\nSerwis DAR-GAZ\nul. Samsonowicza 18K\nOstrowiec Sw.\ntel. 412474575`
    },

    isBasicGSM() {
      const gsm = " \n@£$¥èéùìòÇØøÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ!\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà"
      for (const letter of this.message) {
        if (gsm.indexOf(letter) === -1) return false
      }
      return true
    },

    maxSmsLength() {
      return this.isBasicGSM ? 160 : 70
    },

    smsCount() {
      return Math.ceil(this.message.length / this.maxSmsLength)
    },

    phones() {
      return this.form.phones.split(',').map((phone) => phone.replace(/\D/g, ''))
    },

    hasValidPhones() {
      for (const phone of this.phones) {
        if (phone.length != 9) return false
      }
      return true
    },

    hasErrors() {
      return ! this.hasValidPhones || this.form.message.length == 0
    }
  },

  methods: {
    submit() {
      this.sending = true

      axios.post(route('sms.store'), {
          phones: this.phones,
          message: this.message,
        })
        .then((response) => {
          this.clearForm()
          swal({
            position: 'center',
            type: 'success',
            title: 'Wysłano!',
            showConfirmButton: false,
            timer: 1500
          });
        })
        .catch((error) => {
          swal({
            position: 'center',
            type: 'error',
            title: 'Wystąpił problem',
            showConfirmButton: false,
            timer: 1500
          });
        })
        .then(() => {
          this.sending = false
        })
    },

    clearForm() {
      this.form.phones = ''
      this.form.message = ''
    },
  },
}
</script>