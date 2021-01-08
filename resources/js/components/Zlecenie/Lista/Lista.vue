<template>
  <div>
    <b-row class="gutters-tiny row-deck" style="margin-top: 10px; margin-bottom: 10px;">
      <b-col cols="2">
        <SearchBlock title="Klient">
          <b-input v-model="searchCustomerName" type="text" size="sm"></b-input>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Miejscowość">
          <b-input v-model="searchCustomerCity" type="text" size="sm"></b-input>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Adres">
          <b-input v-model="searchCustomerAddress" type="text" size="sm"></b-input>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Marka">
          <b-input v-model="searchDeviceBrand" type="text" size="sm"></b-input>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Nr Seryjny">
          <b-input v-model="searchDeviceSerial" type="text" size="sm"></b-input>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock>
          <b-form-radio v-model="searchServiceOpen" value="1">Otwarte zlecenia</b-form-radio>
          <!-- <b-form-radio v-model="searchServiceOpen" value="2">Zamknięte</b-form-radio> -->
          <b-form-radio v-model="searchServiceOpen" value="3">Wszystkie zlecenia</b-form-radio>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Status">
          <multiselect :options="[]" :searchable="false" :close-on-select="false" :show-labels="false" size="sm" placeholder="Wszystko"></multiselect>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Technik">
          <multiselect :options="[]" :searchable="false" :close-on-select="false" :show-labels="false" placeholder="Wszystko"></multiselect>
        </SearchBlock>
      </b-col>
      <b-col cols="2">
        <SearchBlock title="Zleceniodawca" disabled>
          <multiselect :options="[]" :searchable="false" :close-on-select="false" :show-labels="false" placeholder="Wszystko" disabled></multiselect>
        </SearchBlock>
      </b-col>
    </b-row>

    <Table />
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
  },

  data() {
    return {
      searchCustomerName: '',
      searchCustomerCity: '',
      searchCustomerAddress: '',
      searchDeviceBrand: '',
      searchDeviceSerial: '',
      searchServiceOpen: 1,
      serviceStatuses: [],
      serviceTechnician: 0,
      serviceBuyer: '',
    }
  },
}
</script>
