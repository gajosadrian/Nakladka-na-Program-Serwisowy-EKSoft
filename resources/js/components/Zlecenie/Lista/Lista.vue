<template>
  <div>
    <b-row class="gutters-tiny row-deck" style="margin-top: 10px; margin-bottom: 10px;">
      <b-col cols="2">
        <SearchBlock title="Klient">
          <b-input v-model="search.customerName" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Adres">
          <b-input v-model="search.customerAddress" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Miejscowość">
          <b-input v-model="search.customerCity" type="text" size="sm" />
        </SearchBlock>
      </b-col>
      <b-col cols="1">
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
      <b-col cols="1">
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
          <ComboBox
            v-model="search.serviceBuyer"
            :data-items="zleceniodawcy"
            text-field="name"
            data-item-key="key"
            placeholder="Wszystko"
          />
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Okres zleceń">
          <ComboBox
            v-model="search.serviceScope"
            :data-items="serviceScopes"
            text-field="name"
            data-item-key="id"
            :placeholder="serviceScopes[0].name"
          />
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

    <Table ref="ZleceniaTable" :zlecenia="zleceniaData" :columnWidths="columnWidths" @onZlecenie="onZlecenie" />

    <SearchBlock
      v-if="hasZlecenia"
      class="mt-2 mb-0"
    >
      <div class="clearfix">
        <div class="float-left">
          <b-pagination
            v-show="zlecenia.last_page > 1"
            v-model="zlecenia.current_page"
            :total-rows="zlecenia.total"
            :per-page="zlecenia.per_page"
            class="m-0"
            @input="onPage"
          />
        </div>
        <div class="float-right">
          <span class="mr-4">{{ zlecenia.total }} zleceń</span>
        </div>
      </div>
    </SearchBlock>
  </div>
</template>

<script>
import { debounce } from 'debounce'
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
    zleceniodawcy: Array,
    serviceScopes: Array,
    _token: String,
    _search: Object,
    _columnWidths: Object,
  },

  data() {
    return {
      selectedZlecenie: null,
      zlecenia: {
        current_page: localStorage.zlecenia2_page,
      },
      search: this._search,
      columnWidths: this._columnWidths,
    }
  },

  watch: {
    'search.customerName': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchData()
    },
    'search.customerAddress': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchData()
    },
    'search.customerCity': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchData()
    },
    'search.serviceNo': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchData()
    },
    'search.serviceScope': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchDataInstant()
    },
    'search.serviceStatuses': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchDataInstant()
    },
    'search.serviceTechnician': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchDataInstant()
    },
    'search.serviceBuyer': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchDataInstant()
    },
    'search.deviceBrand': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchData()
    },
    'search.deviceType': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchData()
    },
    'search.deviceSerial': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchData()
    },
    'search.partSymbol': function () {
      this.saveUserField('zlecenia2.search', this.search)
      this.fetchData()
    },
  },

  computed: {
    hasZlecenia() {
      return this.zlecenia && this.zlecenia.data
    },
    zleceniaData() {
      return this.hasZlecenia || []
    },
  },

  methods: {
    onZlecenie(zlecenie) {
      this.selectedZlecenie = zlecenie
    },
    onPage(page) {
      localStorage.zlecenia2_page = page
      this.fetchDataInstant(true)
    },
    fetchData(keepPage = false, keepScroll = false) {
      if (! keepScroll && this.$refs.ZleceniaTable) {
        this.$refs.ZleceniaTable.scrollTop()
      }

      if (! keepScroll) this.zlecenia.data = []

      axios.post(route('zlecenia.api.getList'), {
        page: keepPage && this.zlecenia.current_page || 1,
        search: this.search,
      })
        .then(response => {
          const data = response.data
          this.zlecenia = data.zlecenia

          if (keepScroll == 2) {
            setTimeout(() => {
              this.$refs.ZleceniaTable.restoreScrollPos()
            }, 1000)
          }
        })
    },
    saveUserField(field, value) {
      axios.put(route('api.save_field'), {
        _token: this._token,
        _method: 'put',
        name: field,
        value,
      })
        .catch((err) => {
          console.log(err)
        })
    },
  },

  mounted() {
    this.fetchDataInstant = this.fetchData
    this.fetchData = debounce(this.fetchData, 500)
    this.saveUserField = debounce(this.saveUserField, 500)

    this.fetchDataInstant(true, 2)
    setInterval(() => {
      this.fetchData(true, true)
    }, 5 *60*1000)
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
