<template>
  <b-tr>
    <b-td nowrap>{{ pozycja.symbol_dostawcy }}</b-td>
    <b-td nowrap>
      <b-input
        v-model="pozycja.symbol"
        size="sm"
        :list="`symbolList${_uid}`"
        :state="symbolState"
        @focus="$event.target.select()"
        :disabled="Boolean(pozycja.id)"
        required
      />
      <datalist :id="`symbolList${_uid}`">
        <option v-for="symbol in symbolList" :key="symbol">{{ symbol }}</option>
      </datalist>
    </b-td>
    <b-td nowrap>
      <i class="fa fa-shopping-cart text-danger" v-if="pozycja.is_zamowione"></i>
      {{ pozycja.nazwa.substring(0, 50) }}<span v-if="pozycja.nazwa.length > 50">...</span>
    </b-td>
    <b-td nowrap>
      <b-input
        v-model="pozycja.opis"
        size="sm"
      />
    </b-td>
    <b-td nowrap>
      <b-input
        v-model="cenaFixed"
        type="number"
        size="sm"
        @keyup="updateCenaBrutto()"
        @focus="$event.target.select()"
        required
      />
    </b-td>
    <b-td nowrap>
      <b-input
        v-model="pozycja.vat_procent"
        type="number"
        size="sm"
        @keyup="updateCenaBrutto()"
        @focus="$event.target.select()"
        required
      />
    </b-td>
    <b-td nowrap>
      <b-input
        v-model="cenaBruttoFixed"
        type="number"
        size="sm"
        @keyup="updateCena()"
        @focus="$event.target.select()"
        required
      />
    </b-td>
    <b-td nowrap>
      <b-input
        v-model="pozycja.ilosc"
        type="number"
        size="sm"
        @keyup="updateCena()"
        @focus="$event.target.select()"
        required
      />
    </b-td>
    <b-td class="text-right" nowrap>
      <b-input
        :value="wartoscBrutto.toFixed(2)"
        size="sm"
        disabled
      />
    </b-td>
    <b-td>
      <i
        class="fa fa-times text-danger"
        @click="remove()"
      ></i>
    </b-td>
  </b-tr>
</template>

<script>
import { debounce } from 'debounce'

export default {
  props: {
    _token: String,
    pozycja: Object,
  },

  data() {
    return {
      symbolList: [null],
    }
  },

  watch: {
    'pozycja.symbol': function (val) {
      this.symbolList = []
      this.fetchProp('symbol', val, () => {
        this.update()
      })
    },
    'pozycja.opis': function (val) {
      this.update()
    },
    'pozycja.cena': function (val) {
      this.update()
    },
    'pozycja.var_procent': function (val) {
      this.update()
    },
    'pozycja.cena_brutto': function (val) {
      this.update()
    },
    'pozycja.ilosc': function (val) {
      this.update()
    },
  },

  computed: {
    vatRate() {
      return this.pozycja.vat_procent / 100 + 1
    },

    wartoscBrutto() {
      return this.pozycja.cena_brutto * this.pozycja.ilosc
    },

    symbolState() {
      return this.symbolList.length === 1 ? null : false
    },

    cenaFixed: {
      get() {
        return Math.round((this.pozycja.cena + Number.EPSILON) * 100) / 100
        // return this.pozycja.cena.toFixed(2)
      },
      set(val) {
        this.pozycja.cena = Number(val)
      },
    },

    cenaBruttoFixed: {
      get() {
        return Math.round((this.pozycja.cena_brutto + Number.EPSILON) * 100) / 100
        // return this.pozycja.cena_brutto.toFixed(2)
      },
      set(val) {
        this.pozycja.cena_brutto = Number(val)
      },
    },
  },

  methods: {
    updateCena() {
      const cenaBrutto = this.pozycja.cena_brutto
      const cena = cenaBrutto / this.vatRate

      this.pozycja.cena = cena
      this.pozycja.wartosc = cena * this.pozycja.ilosc
      this.pozycja.wartosc_brutto = cenaBrutto * this.pozycja.ilosc
    },

    updateCenaBrutto() {
      const cena = this.pozycja.cena
      const cenaBrutto = cena * this.vatRate

      this.pozycja.cena_brutto = cenaBrutto
      this.pozycja.wartosc = cena * this.pozycja.ilosc
      this.pozycja.wartosc_brutto = cenaBrutto * this.pozycja.ilosc
    },

    fetchProp(prop, search, callback) {
      axios.post(route(`czesci.apiProps`, {
        prop,
      }), {
        _token: this._token,
        search,
      })
        .then(res => {
          this[`${prop}List`] = res.data
          callback(res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    update() {
      if (! this.isValidSymbol()) return;

      axios.post(route('kosztorys.updatePozycja', this.pozycja.id), {
        _method: 'PUT',
        _token: this._token,
        symbol: this.pozycja.symbol,
        opis: this.pozycja.opis,
        cena: this.pozycja.cena,
        vat: this.pozycja.vat_procent,
        ilosc: this.pozycja.ilosc,
      })
        .then(res => {})
        .catch(err => {
          console.log(err)
        })
    },

    remove() {
      if (! this.pozycja.id) return;
      this.$emit('remove', this.pozycja.id)
    },

    isValidSymbol() {
      return this.symbolState === null ? true : false
    },
  },

  created() {
    this.fetchProp = debounce(this.fetchProp, 300)
    this.update = debounce(this.update, 300)
  },
}
</script>

<style scoped>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}

.text-danger {
  cursor: pointer;
}
</style>
