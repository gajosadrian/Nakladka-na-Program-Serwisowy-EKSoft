<template>
  <Grid
    :style="{height: 'calc(100vh - 180px)'}"
    :data-items="items"
    :cell-render="cellTemplate"
    :selected-field="selectedZlecenieField"
    :resizable="true"
    :columns="columns"
    @rowclick="onRowClick"
    @rowdblclick="onRowDoubleClick"
    @onCellClick="onCellClick"
  />
</template>

<script>
import { Grid } from '@progress/kendo-vue-grid'
import TableRow from './TableRow'

export default {
  components: {
    Grid,
    TableRow,
  },

  data() {
    return {
      cellTemplate: TableRow,
      selectedZlecenieField: 'selected',
      selectedZlecenieId: 0,
      products: this.createRandomData(50),
      columns: [
        // { field: 'lp', title: 'Lp.', width: '55px' },
        { field: 'customer', title: 'Klient', width: '250px' },
        { field: 'address', title: 'Adres', width: '80px' },
        { field: 'serviceNo', title: 'Nr zlecenia', width: '80px' },
        { field: 'device', title: 'Urządzenie', width: '80px' },
        { field: 'status', title: 'Status', width: '80px' },
        { field: 'dateAccept', title: 'Przyjęcie', width: '80px' },
        { field: 'dateCalendar', title: 'Termin', width: '80px' },
      ]
    }
  },

  computed: {
    items() {
      return this.products.map((item) => ({ ...item, selected: item.id === this.selectedZlecenieId }))
    },
  },

  methods: {
    onRowClick(e) {
      const item = e.dataItem
      this.selectedZlecenieId = item.id
    },
    onRowDoubleClick(e) {
      const item = e.dataItem
      this.selectedZlecenieId = item.id
      console.log(item.id)
    },
    onCellClick(field, item) {
      // console.log(field, item)
    },
    createRandomData(count) {
      const productNames = ["Aniseed Syrup", "Chef Anton's Cajun Seasoning", "Chef Anton's Gumbo Mix", "Grandma's Boysenberry Spread", "Uncle Bob's Organic Dried Pears", "Northwoods Cranberry Sauce", "Mishi Kobe Niku"];
      const unitPrices = [12.5, 10.1, 5.3, 7, 22.53, 16.22, 20, 50, 100, 120]

      return Array(count).fill({}).map((_, idx) => ({
        id: idx + 1,
        lp: idx + 1,
        customer: [productNames[Math.floor(Math.random() * productNames.length)], idx],
        address: unitPrices[Math.floor(Math.random() * unitPrices.length)],
        status: 'test',
      }));
    }
  }
}
</script>
