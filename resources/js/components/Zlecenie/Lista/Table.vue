<template>
  <Grid
    ref="grid"
    :style="{height: 'calc(100vh - 220px)'}"
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
    columnWidths: Object,
  },

  data() {
    return {
      cellTemplate: TableRow,
      selectedZlecenieField: 'selected',
      selectedZlecenie: null,
      columns: [
        { field: 'lp', title: 'Lp.', width: `${this.columnWidths.lp || 55}px` },
        { field: 'customer', title: 'Klient', width: `${this.columnWidths.customer || 80}px` },
        { field: 'address', title: 'Adres', width: `${this.columnWidths.address || 80}px` },
        { field: 'service', title: 'Nr zlecenia', width: `${this.columnWidths.service || 80}px` },
        { field: 'device', title: 'Urządzenie', width: `${this.columnWidths.device || 80}px` },
        { field: 'status', title: 'Status', width: `${this.columnWidths.status || 80}px` },
        { field: 'dateRegister', title: 'Przyjęcie', width: `${this.columnWidths.dateRegister || 80}px` },
        { field: 'dateCalendar', title: 'Termin', width: `${this.columnWidths.dateCalendar || 80}px` },
      ]
    }
  },

  computed: {
    items() {
      const selectedZlecenieId = this.selectedZlecenie && this.selectedZlecenie.id || 0
      return this.zlecenia.map((zlecenie, idx) => ({
        ...zlecenie,
        selected: (zlecenie.id === selectedZlecenieId),
        lp: idx + 1,
      }))
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
      this.columnWidths[column.field] = newWidth
      this.saveUserField('zlecenia2.columnWidths', this.columnWidths)
    },
    scrollTop(pos = 0) {
      this.$el.querySelector('.k-grid-content').scrollTop = pos
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
}
</script>
