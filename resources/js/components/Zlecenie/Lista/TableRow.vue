<template>
  <td v-if="field == 'customer'" :class="className" nowrap @click="onClick(field, dataItem)">
    {{ klient.nazwa }}<br>
    <small class="text-muted">{{ klient.symbol }}</small>
  </td>
  <td v-else-if="field == 'service'" :class="className" nowrap @click="onClick(field, dataItem)">
    {{ zlecenie.nr_or_obcy }}
  </td>
  <td v-else-if="field == 'status'" :class="className" class="p-1" nowrap @click="onClick(field, dataItem)">
    <span class="rounded p-2 table-info" style="color: #495057;">
      <i class="fa fa-file-signature text-info"></i>
      {{ status }}test
    </span>
  </td>
  <td v-else :class="className" nowrap>
    {{ getNestedValue(field, dataItem)}}
  </td>
</template>

<script>
export default {
  props: {
    field: String,
    dataItem: Object,
    format: String,
    className: String,
    columnIndex: Number,
    columnsCount: Number,
    rowType: String,
    level: Number,
    expanded: Boolean,
    editor: String,
  },

  computed: {
    zlecenie() {
      return {
        nr: this.getNestedValue('nr', this.dataItem),
        nr_obcy: this.getNestedValue('nr_obcy', this.dataItem),
        nr_or_obcy: this.getNestedValue('nr_or_obcy', this.dataItem),
      }
    },
    klient() {
      return this.getNestedValue('klient', this.dataItem)
    },
    status() {
      return this.getNestedValue('status', this.dataItem)
    },
  },

  methods: {
    getNestedValue: function(field, dataItem) {
      const path = field.split('.')
      let data = dataItem
      path.forEach((p) => {
        data = data ? data[p] : undefined
      })
      return data
    },
    onClick(field, dataItem) {
      this.$emit('onCellClick', field, dataItem)
    },
  },
}
</script>

<style scoped>
td {
  user-select: none; /* supported by Chrome and Opera */
  -webkit-user-select: none; /* Safari */
  -khtml-user-select: none; /* Konqueror HTML */
  -moz-user-select: none; /* Firefox */
  -ms-user-select: none; /* Internet Explorer/Edge */
}
</style>
