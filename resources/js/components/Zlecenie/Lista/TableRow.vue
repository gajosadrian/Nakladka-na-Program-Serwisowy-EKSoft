<template>
  <td v-if="field == 'customer'" :class="className" nowrap @click="onClick(field, dataItem)">
    <span v-if="klient">
      {{ klient.nazwa }}<br>
      <span class="text-muted">{{ klient.symbol }}</span>
    </span>
  </td>

  <td v-else-if="field == 'address'" :class="className" nowrap @click="onClick(field, dataItem)">
    {{ klient.adres }}<br>
    {{ klient.kod_pocztowy }} {{ klient.miasto_short }}
  </td>

  <td v-else-if="field == 'service'" :class="className" nowrap @click="onClick(field, dataItem)">
    <span :class="`font-w600 text-${zlecenie.znacznik.color}`">
      <i :class="zlecenie.znacznik.icon"></i>
      {{ zlecenie.znacznik_formatted }}
    </span><br>
    {{ zlecenie.nr_or_obcy }}
    <a href="javascript:void(0)" class="ml-2" v-clipboard:copy="zlecenie.nr_or_obcy">
      <i class="far fa-copy"></i>
    </a>
  </td>

  <td v-else-if="field == 'device'" :class="className" nowrap @click="onClick(field, dataItem)">
    <span v-if="urzadzenie">
      {{ urzadzenie.nazwa }}<br>
      {{ urzadzenie.producent }}
    </span>
  </td>

  <td v-else-if="field == 'status'" :class="className" class="p-1" nowrap @click="onClick(field, dataItem)">
    <span v-if="status" class="rounded p-2" :class="`table-${status.color}`" style="color: #495057;">
      <i :class="`${status.icon} text-${status.color}`"></i>
      {{ status.nazwa }}
    </span>
  </td>

  <td v-else-if="field == 'dateRegister'" :class="className" nowrap @click="onClick(field, dataItem)">
    {{ zlecenie.data_przyjecia_formatted }}<br>
    <small class="text-muted">{{ data_przyjecia_when }}</small>
  </td>

  <td v-else-if="field == 'dateCalendar'" :class="className" nowrap @click="onClick(field, dataItem)">
    <span v-if="zlecenie.is_termin">{{ zlecenie.data_zakonczenia_formatted }}</span>
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
        znacznik: this.getNestedValue('znacznik', this.dataItem),
        znacznik_formatted: this.getNestedValue('znacznik_formatted', this.dataItem),
        data_przyjecia_formatted: this.getNestedValue('data_przyjecia_formatted', this.dataItem),
        data_zakonczenia_formatted: this.getNestedValue('data_zakonczenia_formatted', this.dataItem),
        dni_od_przyjecia: this.getNestedValue('dni_od_przyjecia', this.dataItem),
        is_termin: this.getNestedValue('is_termin', this.dataItem),
      }
    },
    klient() {
      return this.getNestedValue('klient', this.dataItem)
    },
    urzadzenie() {
      return this.getNestedValue('urzadzenie', this.dataItem)
    },
    status() {
      return this.getNestedValue('status', this.dataItem)
    },
    data_przyjecia_when() {
      if (this.zlecenie.dni_od_przyjecia > 0) {
        if (this.zlecenie.dni_od_przyjecia >= 2) {
          return `${this.zlecenie.dni_od_przyjecia} dni temu`
        } else {
          return 'wczoraj'
        }
      }
      return 'dzisiaj'
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
