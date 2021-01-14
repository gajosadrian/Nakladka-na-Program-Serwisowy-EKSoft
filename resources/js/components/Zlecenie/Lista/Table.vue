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
    @columnresize="onColumnResize"
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
      selectedZlecenie: null,
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
      const selectedZlecenieId = this.selectedZlecenie && this.selectedZlecenie.id || 0
      return this.zlecenia.map(zlecenie => ({ ...zlecenie, selected: zlecenie.id === selectedZlecenieId }))
    },
  },

  methods: {
    onRowClick(e) {
      const zlecenie = e.dataItem
      this.selectedZlecenie = zlecenie
      this.$emit('onZlecenie', zlecenie)
    },
    onRowDoubleClick(e) {
      const zlecenie = e.dataItem
      this.selectedZlecenie = zlecenie

      OpenZlecenie(zlecenie.url, zlecenie.id)
    },
    onCellClick(field, item) {
      // console.log(field, item)
    },
    onColumnResize({columns, end, newWidth, index}) {
      if (! end) return

      const column = columns[index]
    },
    saveUserField() {
      axios.put(route('api.save_field'), {
        _token: this._token,
        _method: 'put',
        name: 'test',
        value,
      })
        .catch((err) => {
          console.log(err)
        })
    },
  }
}
</script>
