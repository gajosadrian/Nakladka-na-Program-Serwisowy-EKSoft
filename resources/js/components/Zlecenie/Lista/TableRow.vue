<template>
  <td v-if="field == 'customer'" :class="className" nowrap @click="onClick(field, dataItem)">
    {{ customer[0] }}<br>
    <small class="text-muted">{{ customer[1] }}</small>
  </td>
  <td v-else-if="field == 'status'" :class="className" class="p-1" nowrap @click="onClick(field, dataItem)">
    <b-badge class="bg-info d-block">
      <i class="fa fa-file-signature mr-1"></i>
      {{ status }}
    </b-badge>
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
    customer() {
      return this.getNestedValue('customer', this.dataItem)
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

  created() {},
}
</script>
