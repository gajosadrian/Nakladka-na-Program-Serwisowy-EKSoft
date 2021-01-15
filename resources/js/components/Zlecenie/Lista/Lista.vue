<template>
  <div>
    <b-row class="gutters-tiny row-deck" style="margin-top: 10px; margin-bottom: 10px;">
      <b-col cols="1">
        <SearchBlock title="Klient">
          <b-input v-model="search.customerName" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Adres">
          <b-input v-model="search.customerAddress" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="1">
        <SearchBlock title="Miejscowość">
          <b-input v-model="search.customerCity" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Nr zlecenia">
          <b-input v-model="search.serviceNo" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="1">
        <SearchBlock title="Urządzenie">
          <b-input v-model="search.deviceType" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="1">
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
        <SearchBlock title="Część">
          <b-input v-model="search.partSymbol" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Status">
          <MultiSelect
            v-model="search.serviceStatuses"
            :data-items="statusy"
            text-field="nazwa"
            data-item-key="id"
            placeholder="Wszystko"
            :tags="(search.serviceStatuses && search.serviceStatuses.length > 0) ? [{text: `${search.serviceStatuses.length} status(y)`, data: [...search.serviceStatuses]}] : []"
          />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Technik">
          <ComboBox
            v-model="search.serviceTechnician"
            :data-items="technicy"
            text-field="nazwa"
            data-item-key="id"
            placeholder="Wszystko"
          />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Zleceniodawca" disabled>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock>
          <b-form-radio v-model="search.serviceOpen" value="1">Otwarte</b-form-radio>
          <!-- <b-form-radio v-model="search.serviceOpen" value="2">Zamknięte</b-form-radio> -->
          <b-form-radio v-model="search.serviceOpen" value="3">Wszystkie</b-form-radio>
        </SearchBlock>
      </b-col>
      <b-col cols="4">
        <div class="block">
          <div class="block-content p-1 opis">
            <span v-if="selectedZlecenie">{{ selectedZlecenie.opis_last }}</span>
          </div>
        </div>
      </b-col>
    </b-row>

    <Table :zlecenia="zlecenia" :columnWidths="columnWidths" @onZlecenie="onZlecenie" />
  </div>
</template>

<script>
import { MultiSelect, DropDownList, ComboBox } from '@progress/kendo-vue-dropdowns'
import SearchBlock from './SearchBlock'
import Table from './Table'

export default {
  components: {
    MultiSelect, DropDownList, ComboBox,
    SearchBlock,
    Table,
  },

  props: {
    statusy: Array,
    technicy: Array,
    _token: String,
    _search: Object,
    _columnWidths: Object,
  },

  data() {
    return {
      selectedZlecenie: null,
      zlecenia: [],
      search: this._search,
      columnWidths: this._columnWidths,
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
  height: 3.5em;
  overflow: hidden;
}

.k-combobox {
  width: 100%;
}
</style>
