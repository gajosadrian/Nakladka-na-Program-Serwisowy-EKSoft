<template>
  <div>
    <b-block full>
      <template slot="content">
        <b-form @submit.prevent="submit">

          <!-- Customer -->
          <b-form-group
            label="Kontrahent"
          >
            <b-form-input v-model="form.customer" />
            <b-list-group>
              <b-list-group-item
                href="javascript:;"
                v-for="kh in customer.list" :key="customer.klient_id"
                v-if="! customer.selected || customer.selected.klient_id == kh.klient_id"
                :active="customer.selected && customer.selected.klient_id == kh.klient_id"
                @click="selectCustomer(kh)"
              >
                {{ kh.pelna_nazwa }}, {{ kh.adres }}, {{ kh.kod_pocztowy }} {{ kh.miejscowosc }}
              </b-list-group-item>
            </b-list-group>
            <b-list-group v-if="customer.selected">
              <b-list-group-item
                href="javascript:;"
                v-for="(phone, index) in customer.selected.telefony_array" :key="index"
                class="border border-primary"
                @click="usePhone(phone)"
              >
                {{ phone }}
              </b-list-group-item>
            </b-list-group>
          </b-form-group>

          <!-- Phones -->
          <b-form-group
            label="Telefony"
            description="Podawać po przecinku"
          >
            <b-form-input v-model.trim="form.phones" />
          </b-form-group>

          <!-- Message -->
          <b-form-group
            label="Treść wiadomości"
            :description="`Ilość SMS: ${smsCount}, Znaków: ${message.length}/${maxSmsLength}`"
          >
            <b-form-textarea
              v-model="form.message"
              rows="6"
            />
            <b-select v-model="predefinedMessage.selected" :options="predefinedMessage.list" />
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
import { debounce } from 'debounce'

export default {
  props: {
    _token: String,
  },

  data() {
    return {
      sending: false,
      customer: {
        selected: null,
        list: [],
      },
      predefinedMessage: {
        selected: null,
        list: [
          { text: '-- Wiadomości predefiniowane --', value: null },
          'Urządzenie gotowe do odbioru',
          `Dane przelewu:\n"DAR-GAZ" Dariusz Gajos\nNr konta: 50 1940 1076 3097 3581 0000 0000\n\nProszę o zadatek w kwocie `,
        ],
      },
      form: {
        customer: null,
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
      let message = this.form.message.replace(/ +(?= )/g, '').trim()

      message = [...message].map((letter) => polishCharacters[letter] || letter).join('')
      return `${message}\n\n--\nSerwis DAR-GAZ\nSamsonowicza 18K\nOstrowiec Sw.\ntel. 412474575`
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
      return Math.ceil(this.message.length / this.maxSmsLength) * this.phones.length
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
    },
  },

  watch: {
    'predefinedMessage.selected': function (val) {
      if (! val) return;

      this.form.message = val
      setTimeout(() => {
        this.predefinedMessage.selected = null
      }, 10);
    },

    'form.customer': function (val) {
      if (! val) {
        this.customer.list = []
        return;
      }

      this.fetchCustomers(val)
    },
  },

  methods: {
    submit() {
      this.sending = true

      axios.post(route('sms.store'), {
          phones: this.phones,
          message: this.message,
        })
        .then(response => {
          this.clearForm()
          this.swal('success', 'Wysłano!')
        })
        .catch(error => {
          this.swal('error', 'Wystąpił problem')
        })
        .then(() => {
          this.sending = false
        })
    },

    clearForm() {
      this.form.phones = ''
      this.form.message = ''

      this.form.customer = ''
      this.customer.selected = null
    },

    fetchCustomers(search) {
      this.customer.list = []
      this.customer.selected = null

      axios.post(route('klient.apiFind'), {
          search,
        })
        .then(response => {
          this.customer.list = response.data
        })
        .catch(error => {
          console.log(error)
        })
    },

    selectCustomer(kh) {
      this.customer.selected = kh
    },

    usePhone(phone) {
      this.form.phones = phone
    },

    swal(type, text) {
      swal({
        position: 'center',
        type: type,
        title: text,
        showConfirmButton: false,
        timer: 1500
      });
    },
  },

  created() {
    this.fetchCustomers = debounce(this.fetchCustomers, 300)
  },
}
</script>