<template>
  <div>
    <b-row class="gutters-tiny row-deck" style="margin-top: 10px; margin-bottom: 10px;">
      <b-col cols="2">
        <SearchBlock title="Klient">
          <b-input v-model="search.customerName" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Miejscowość">
          <b-input v-model="search.customerCity" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Adres">
          <b-input v-model="search.customerAddress" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Marka">
          <b-input v-model="search.deviceBrand" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Nr Seryjny">
          <b-input v-model="search.deviceSerial" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock>
          <b-form-radio v-model="search.serviceOpen" value="1">Otwarte zlecenia</b-form-radio>
          <!-- <b-form-radio v-model="search.serviceOpen" value="2">Zamknięte</b-form-radio> -->
          <b-form-radio v-model="search.serviceOpen" value="3">Wszystkie zlecenia</b-form-radio>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Status">
          <multiselect :options="[]" :searchable="false" :close-on-select="false" :show-labels="false" size="sm" placeholder="Wszystko" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Technik">
          <multiselect :options="[]" :searchable="false" :close-on-select="false" :show-labels="false" placeholder="Wszystko" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Zleceniodawca" disabled>
          <multiselect :options="[]" :searchable="false" :close-on-select="false" :show-labels="false" placeholder="Wszystko" disabled />
        </SearchBlock>
      </b-col>
      <b-col cols="6">
        <div class="block">
          <div class="block-content p-1 opis">
            <span v-if="selectedZlecenie">{{ selectedZlecenie.opis }}</span>
          </div>
        </div>
      </b-col>
    </b-row>

    <Table :zlecenia="zlecenia" @onZlecenie="onZlecenie" />
  </div>
</template>

<script>
import SearchBlock from './SearchBlock'
import Table from './Table'

export default {
  components: {
    SearchBlock,
    Table,
  },

  props: {
    _token: String,
    _search: Object,
    _tableWidths: Object,
  },

  data() {
    return {
      selectedZlecenie: null,
      zlecenia: [],
      search: this._search,
    }
  },

  methods: {
    onZlecenie(zlecenie) {
      this.selectedZlecenie = zlecenie
    },
    fetchData() {
      axios.get(route('zlecenia.api.getList'), {
        search: this.search,
      })
        .then(response => {
          const data = response.data

          this.zlecenia = data.zlecenia
        })
    },
  },

  created() {
    this.fetchData()
  },
}
</script>

<style scoped>
div.opis {
  height: 5em;
  overflow: hidden;
}
</style>
