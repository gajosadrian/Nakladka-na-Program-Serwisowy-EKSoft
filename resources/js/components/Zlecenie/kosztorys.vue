<template>
  <div>
    <b-table-simple small caption-top responsive>
      <b-thead>
        <b-tr>
          <b-th class="font-w700" nowrap style="width:180px;">Symbol dost.</b-th>
          <b-th class="font-w700" nowrap style="width:130px;">Symbol</b-th>
          <b-th class="font-w700" nowrap style="width:500px;">Nazwa</b-th>
          <b-th class="font-w700" nowrap>Opis</b-th>
          <b-th class="font-w700" style="width:90px;">Cena<br>netto</b-th>
          <b-th class="font-w700" nowrap style="width:50px;">VAT</b-th>
          <b-th class="font-w700" style="width:90px;">Cena brutto</b-th>
          <b-th class="font-w700" nowrap style="width:50px;">Ilość</b-th>
          <b-th class="font-w700" style="width:90px;">Wartość brutto</b-th>
          <b-th></b-th>
        </b-tr>
      </b-thead>
      <b-tbody>
        <KosztorysPozycja
          v-for="(pozycja, index) in pozycje" :key="pozycja.id"
          ref="kosztorys"
          :index="index"
          :pozycja="pozycja"
          :is_technik="is_technik"
          :technik_symbols="technik_symbols"
          @remove="remove"
          @keydown="handleKeydown"
        />
        <b-tr>
          <b-td colspan="3">
            <b-form @submit.prevent="submit()" inline>
              <b-input
                v-model="newSymbolDostawcy"
                :state="symbolDostawcyState"
                :list="`symbol_dostawcyList${_uid}`"
                size="sm"
                autocomplete="off"
                style="width:172px;"
              />
              <datalist :id="`symbol_dostawcyList${_uid}`">
                <option v-for="symbol in symbol_dostawcyList" :key="symbol">{{ symbol }}</option>
              </datalist>

              <b-input
                v-model="newSymbol"
                :state="symbolState"
                :list="`symbolList${_uid}`"
                class="ml-2"
                size="sm"
                autocomplete="off"
                style="width:122px;"
              />
              <datalist :id="`symbolList${_uid}`">
                <option v-for="symbol in symbolList" :key="symbol">{{ symbol }}</option>
              </datalist>

              <b-button type="submit" size="sm" variant="primary" class="ml-1" :disabled="(! isValidSymbol() && ! isValidSymbolDostawcy())">
                Dodaj
              </b-button>
            </b-form>
          </b-td>
          <b-td></b-td>
          <b-td></b-td>
          <b-td></b-td>
          <b-td></b-td>
          <b-td></b-td>
          <b-td></b-td>
          <b-td></b-td>
        </b-tr>
      </b-tbody>
      <b-tfoot>
        <b-tr>
          <b-th colspan="10" class="text-right" nowrap>
            <span>Suma kosztów: {{ sumaBrutto.toFixed(2) }} zł</span>
            <span class="ml-4">Do dopłaty: {{ wartoscBrutto.toFixed(2) }} zł</span>
          </b-th>
        </b-tr>
      </b-tfoot>
    </b-table-simple>
  </div>
</template>

<script>
import { debounce } from 'debounce'
import KosztorysPozycja from './KosztorysPozycja'

export default {
  components: {
    KosztorysPozycja
  },

  props: {
    _token: String,
    zlecenie_id: Number,
    is_technik: Boolean,
    technik_symbols: Array,
  },

  data() {
    return {
      pozycje: [],
      newSymbol: '',
      symbolList: [],
      newSymbolDostawcy: '',
      symbol_dostawcyList: [],
    }
  },

  computed: {
    wartoscBrutto() {
      return this.pozycje.reduce((acc, pozycja) => acc += Number(pozycja.wartosc_brutto.toFixed(2)), 0)
    },

    sumaBrutto() {
      return this.pozycje.reduce((acc, pozycja) => {
        if (pozycja.wartosc_brutto > 0) {
          return acc += Number(pozycja.wartosc_brutto.toFixed(2))
        }
        return acc
      }, 0)
    },

    symbolState() {
      const length = this.symbolList.length
      if (length === 0 || this.isValidSymbol()) {
        return null
      }
      return false
    },

    symbolDostawcyState() {
      const length = this.symbol_dostawcyList.length
      if (length === 0 || this.isValidSymbolDostawcy()) {
        return null
      }
      return false
    },
  },

  watch: {
    newSymbol(val) {
      this.fetchProp('symbol', val)
    },

    newSymbolDostawcy(val) {
      this.fetchProp('symbol_dostawcy', val)
    },
  },

  methods: {
    getPozycja(id) {
      return this.pozycje.find(pozycja => pozycja.id == id)
    },

    fetchKosztorys() {
      axios.get(route('zlecenia.api.getKosztorys', this.zlecenie_id))
        .then(response => {
          this.pozycje = response.data
        })
    },

    remove(pozycjaId) {
      const pozycja = this.getPozycja(pozycjaId)

      axios.post(route('kosztorys.destroyPozycja', pozycja.id), {
        _method: 'delete',
        _token: this._token,
      })
        .then(() => {
          this.pozycje.splice(this.pozycje.indexOf(pozycja), 1)
        })
    },

    add(type, symbol) {
      return new Promise((resolve, reject) => {
        axios.post(route('kosztorys.storePozycja'), {
          _token: this._token,
          zlecenieId: this.zlecenie_id,
          type,
          symbol,
        })
          .then(() => {
            resolve()
          })
          .catch(() => {
            reject()
          })
      })
    },

    submit() {
      let symbol, type

      if (this.isValidSymbol()) {
        type = 'symbol'
        symbol = this.newSymbol
      } else if (this.isValidSymbolDostawcy()) {
        type = 'symbol_dostawcy'
        symbol = this.newSymbolDostawcy
      } else {
        return
      }

      this.add(type, symbol)
        .then(() => {
          this.fetchKosztorys()
        })
        .catch(() => {
          console.log('error')
        })

      this.newSymbol = ''
      this.newSymbolDostawcy = ''
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
          if (callback) callback(res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    isValidSymbol() {
      const symbolList = this.symbolList.map(symbol => symbol.toLowerCase())
      return symbolList.includes( this.newSymbol.toLowerCase() )
    },

    isValidSymbolDostawcy() {
      const symbolDostawcyList = this.symbol_dostawcyList.map(symbol => symbol.toLowerCase())
      return symbolDostawcyList.includes( this.newSymbolDostawcy.toLowerCase() )
    },

    handleKeydown(e, index, key, keyLeft, keyRight) {
      const KEY_DOWN = 40
      const KEY_UP = 38
      const KEY_LEFT = 37
      const KEY_RIGHT = 39

      if (key.substring(0, 10) == 'kosztorys_') {
        let vector
        if (e.keyCode == KEY_DOWN) vector = 1
        else if (e.keyCode == KEY_UP) vector = -1
        else if (e.keyCode == KEY_LEFT) vector = -10
        else if (e.keyCode == KEY_RIGHT) vector = 10
        else return

        let item
        if (vector == -10 && keyLeft) {
          item = this.$refs.kosztorys[index]
          if (item) item.focus(keyLeft)
          e.preventDefault()

        } else if (vector == 10 && keyRight) {
          item = this.$refs.kosztorys[index]
          if (item) item.focus(keyRight)
          e.preventDefault()

        } else if ([1, -1].includes(vector)) {
          item = this.$refs.kosztorys[index + vector]
          if (item) item.focus(key)
          e.preventDefault()
        }
      }
    },
  },

  created() {
    this.fetchProp = debounce(this.fetchProp, 300)
  },

  mounted() {
    this.fetchKosztorys()
  },
}
</script>
