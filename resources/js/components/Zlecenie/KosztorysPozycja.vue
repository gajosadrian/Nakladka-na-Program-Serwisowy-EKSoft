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
        required
      />
      <datalist :id="`symbolList${_uid}`">
        <option v-for="symbol in symbolList" :key="symbol">{{ symbol }}</option>
      </datalist>
    </b-td>
    <b-td nowrap>
      {{ pozycja.nazwa.substring(0, 30) }}
    </b-td>
    <b-td nowrap>
      <i class="fa fa-shopping-cart text-danger" v-if="pozycja.is_zamowione"></i>
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
        @focus="$event.target.select()"
        required
      />
    </b-td>
    <b-td class="text-right" nowrap>{{ pozycja.wartosc_brutto.toFixed(2) }}</b-td>
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
      this.fetchProp('symbol', val)
    },
  },

  computed: {
    vatRate() {
      return this.pozycja.vat_procent / 100 + 1
    },

    symbolState() {
      return this.symbolList.length === 1 ? null : false
    },

    cenaFixed: {
      get() {
        return Math.round((this.pozycja.cena + Number.EPSILON) * 100) / 100
      },
      set(val) {
        this.pozycja.cena = Number(val)
      },
    },

    cenaBruttoFixed: {
      get() {
        return Math.round((this.pozycja.cena_brutto + Number.EPSILON) * 100) / 100
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
      this.pozycja.cena = Math.round((cena + Number.EPSILON) * 10000) / 10000
    },

    updateCenaBrutto() {
      const cena = this.pozycja.cena
      const cenaBrutto = cena * this.vatRate
      this.pozycja.cena_brutto = Math.round((cenaBrutto + Number.EPSILON) * 10000) / 10000
    },

    fetchProp(prop, search) {
      axios.post(route(`czesci.apiProps`, {
        prop,
      }), {
        _token: this._token,
        search,
      })
        .then(res => {
          this[`${prop}List`] = res.data
        })
        .catch(err => {
          console.log(err)
        })
    },

    isValidSymbol() {
      return this.symbolState === null ? true : false
    },
  },

  created() {
    this.fetchProp = debounce(this.fetchProp, 300)
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
</style>
