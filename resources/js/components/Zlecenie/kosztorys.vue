<template>
  <div>
    <b-table-simple small caption-top responsive>
      <b-thead>
        <b-tr>
          <b-th class="font-w700" nowrap>Symbol dost.</b-th>
          <b-th class="font-w700" nowrap style="width:130px;">Symbol</b-th>
          <b-th class="font-w700" nowrap>Nazwa</b-th>
          <b-th class="font-w700" nowrap>Opis</b-th>
          <b-th class="font-w700" nowrap style="width:1%;">Cena netto</b-th>
          <b-th class="font-w700" nowrap style="width:50px;">VAT</b-th>
          <b-th class="font-w700" nowrap style="width:1%;">Cena brutto</b-th>
          <b-th class="font-w700" nowrap style="width:50px;">Ilość</b-th>
          <b-th class="font-w700 text-right" nowrap style="width:1%;">Wartość brutto</b-th>
          <b-th></b-th>
        </b-tr>
      </b-thead>
      <b-tbody>
        <KosztorysPozycja
          v-for="pozycja in pozycje" :key="pozycja.id"
          :pozycja="pozycja"
          @remove="remove"
        />
        <b-tr>
          <b-td></b-td>
          <b-td colspan="2">
            <b-form @submit.prevent="submit()" inline>
              <b-input v-model="newSymbol" size="sm" required style="width:122px;"></b-input>
              <b-button type="submit" size="sm" variant="primary" class="ml-1">
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
          <b-th></b-th>
          <b-th></b-th>
          <b-th></b-th>
          <b-th></b-th>
          <b-th></b-th>
          <b-th></b-th>
          <b-th></b-th>
          <b-th></b-th>
          <b-th class="text-right" nowrap>
            {{ Math.round(wartoscBrutto) }} zł
          </b-th>
          <b-th></b-th>
        </b-tr>
      </b-tfoot>
    </b-table-simple>
  </div>
</template>

<script>
import KosztorysPozycja from './KosztorysPozycja'

export default {
  components: {
    KosztorysPozycja
  },

  props: {
    _token: String,
    zlecenie_id: Number,
  },

  data() {
    return {
      pozycje: [],
      newSymbol: '',
    }
  },

  computed: {
    wartoscBrutto() {
      return this.pozycje.reduce((acc, pozycja) => acc += pozycja.wartosc_brutto, 0)
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

    add(symbol) {
      return new Promise((resolve, reject) => {
        axios.post(route('kosztorys.storePozycja'), {
          _token: this._token,
          zlecenieId: this.zlecenie_id,
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
      if (! this.newSymbol) return;

      this.add(this.newSymbol)
        .then(() => {
          this.fetchKosztorys()
        })
        .catch(() => {
          console.log('error')
        })

      this.newSymbol = ''
    },
  },

  mounted() {
    this.fetchKosztorys()
  },
}
</script>
