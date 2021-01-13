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

  props: {
    zlecenia: Array,
  },

  data() {
    return {
      cellTemplate: TableRow,
      selectedZlecenieField: 'selected',
      selectedZlecenieId: 0,
      columns: [
        // { field: 'lp', title: 'Lp.', width: '55px' },
        { field: 'customer', title: 'Klient', width: '80px' },
        { field: 'address', title: 'Adres', width: '80px' },
        { field: 'service', title: 'Nr zlecenia', width: '80px' },
        { field: 'device', title: 'Urządzenie', width: '80px' },
        { field: 'status', title: 'Status', width: '80px' },
        { field: 'dateAccept', title: 'Przyjęcie', width: '80px' },
        { field: 'dateCalendar', title: 'Termin', width: '80px' },
      ]
    }
  },

  computed: {
    items() {
      return this.zlecenia.map((item) => ({ ...item, selected: item.id === this.selectedZlecenieId }))
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
  }
}
</script>
