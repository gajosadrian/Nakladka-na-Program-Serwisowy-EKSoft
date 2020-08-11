<template>
  <div>
    <b-table-simple small caption-top responsive>
      <b-thead>
        <b-tr>
          <b-th class="font-w700" nowrap>Symbol dost.</b-th>
          <b-th class="font-w700" nowrap style="width:70px;">Symbol</b-th>
          <b-th class="font-w700" nowrap>Nazwa</b-th>
          <b-th class="font-w700" nowrap>Opis</b-th>
          <b-th class="font-w700" nowrap style="width:1%;">Cena netto</b-th>
          <b-th class="font-w700" nowrap style="width:50px;">VAT</b-th>
          <b-th class="font-w700" nowrap style="width:1%;">Cena brutto</b-th>
          <b-th class="font-w700" nowrap style="width:50px;">Ilość</b-th>
          <b-th class="font-w700 text-right" nowrap style="width:1%;">Wartość brutto</b-th>
        </b-tr>
      </b-thead>
      <b-tbody>
        <KosztorysPozycja
          v-for="pozycja in pozycje" :key="pozycja.id"
          :pozycja="pozycja"
        />
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
    }
  },

  computed: {
    wartoscBrutto() {
      return this.pozycje.reduce((acc, pozycja) => acc += pozycja.wartosc_brutto, 0)
    },
  },

  methods: {
    fetchKosztorys() {
      axios.get(route('zlecenia.api.getKosztorys', this.zlecenie_id))
        .then(response => {
          this.pozycje = response.data
        })
    },
  },

  mounted() {
    this.fetchKosztorys()
  },
}
</script>
