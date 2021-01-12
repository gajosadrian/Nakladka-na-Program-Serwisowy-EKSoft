<template>
  <div>
    <span class="d-none">{{ refresher }}</span>

    <b-row class="no-gutters">
      <b-col lg="5">
        <b-form @submit.prevent="submit">

          <!-- Customer -->
          <b-form-group
            v-if="! _predefined"
            label="Kontrahent"
          >
            <b-form-input v-model="form.customer" />
            <b-list-group>
              <b-list-group-item
                href="javascript:;"
                v-show="! customer.selected || customer.selected.klient_id == kh.klient_id"
                v-for="kh in customer.list" :key="kh.klient_id"
                :active="customer.selected && customer.selected.klient_id == kh.klient_id"
                @click="selectCustomer(kh)"
              >
                {{ kh.pelna_nazwa }}, {{ kh.adres }}, {{ kh.kod_pocztowy }} {{ kh.miejscowosc }}
              </b-list-group-item>
            </b-list-group>
          </b-form-group>

          <!-- Phones -->
          <b-form-group
            v-if="customer.selected || _predefined"
            label="Wybierz telefon"
          >
            <b-list-group>
              <b-list-group-item
                href="javascript:;"
                v-for="(phone, index) in telefony" :key="index"
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
      </b-col>
      <b-col lg="7">
        <h6>Historia</h6>

        <div v-for="sms in smses" :key="sms.id">
          <div class="clearfix">
            <div class="float-left">
              <span
                class="font-w600"
                :class="! sms.auto ? 'text-info' : (sms.type == 'error') ? 'bg-danger text-white px-1' : 'text-success'"
              >
                {{ ! sms.auto ? sms.user.name : (sms.type == 'error') ? 'Error' : 'Automat' }}
              </span>
              <span v-if="sms.phones.length > 0">
                -> {{ sms.phones.join(', ') }}
              </span>
              <b-button
                v-if="sms.type == 'error'"
                variant="outline-success"
                size="sm"
                class="ml-1"
                @click="resolveError(sms)"
              >
                <i class="fa fa-check text-succes"></i>
              </b-button>
              <span
                v-if="! zlecenie_id && sms.zlecenie"
                class="ml-3"
              >
                <a href="javascript:;" :onclick="sms.zlecenie.popup_link"><u>{{ sms.zlecenie.nr_or_obcy }}</u></a>
              </span>
            </div>
            <div class="float-right text-muted">
              <span v-if="sms.sms_amount > 1">
                {{ sms.sms_amount }} smsy
              </span>
              <span class="ml-3">{{ sms.date_formatted }}</span>
            </div>
          </div>
          <nl2br tag="p" :text="sms.message_formatted" />
          <hr>
        </div>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { debounce } from 'debounce'

export default {
  props: {
    _token: String,
    _predefined: Boolean,
    _telefony: Array,
    _footer: String,
    zlecenie_id: Number,
    zlecenie_status_id: Number,
    smses: Array,
  },

  data() {
    return {
      refresher: 0,
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

      message = [...message].map(letter => {
        if (polishCharacters[letter]) {
          return polishCharacters[letter]
        } else if (polishCharacters[letter.toLowerCase()]) {
          return polishCharacters[letter.toLowerCase()].toUpperCase()
        }

        return letter
      }).join('')

      return `${message}${this._footer}`
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

    telefony() {
      if (this.customer.selected) {
        return this.customer.selected.telefony_array
      } else if (this._predefined) {
        return this._telefony
      }
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
        zlecenie_id: this.zlecenie_id,
        zlecenie_status_id: this.zlecenie_status_id,
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

    resolve(sms) {
      axios.post(route('sms.resolve', sms.id))
        .then(response => {
          sms.type += '_resolved'
          this.refresh()
        })
        .catch(error => {
          console.log(error)
        })
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

    refresh() {
      return this.refresher++
    },
  },

  created() {
    this.fetchCustomers = debounce(this.fetchCustomers, 300)
  },
}
</script>