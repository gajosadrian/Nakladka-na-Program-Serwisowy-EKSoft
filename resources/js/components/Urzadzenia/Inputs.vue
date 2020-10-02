<template>
  <div>
    <Input
      label="Producent"
      v-model.trim="urzadzenie.producent"
      :small="small"
      :state="Boolean(urzadzenie.producent && urzadzenie.producent != '!')"
      :datalist="producent_list"
    />
    <Input
      label="Nazwa"
      v-model.trim="urzadzenie.nazwa"
      :small="small"
      :state="Boolean(urzadzenie.nazwa)"
      :datalist="nazwa_list"
    />
    <Input
      label="Model"
      v-model.trim="urzadzenie.model"
      :small="small"
      :state="Boolean(urzadzenie.model && urzadzenie.model != '!' && urzadzenie.nr_seryjny_raw.substr(0, 3) != 'FK0')"
      :datalist="model_list"
    />
    <Input
      label="Nr seryjny"
      v-model.trim="urzadzenie.nr_seryjny_raw"
      :small="small"
      :state="Boolean(urzadzenie.nr_seryjny_raw.substr(0, 3) != 'FK0' && isValidSerialNo)"
      :datalist="nr_seryjny_raw_list"
      :text_danger="(! isValidSerialNo) ? 'Nr seryjny już istnieje!' : ''"
    />
    <Input
      label="Kod wyrobu"
      v-model.trim="urzadzenie.kod_wyrobu"
      :small="small"
      :state="true"
      :datalist="kod_wyrobu_list"
    />

    <b-form-group class="text-right" :class="{'mt-2': small}">
      <b-button
        :size="small ? 'sm' : null"
        :variant="savable ? 'danger' : 'light'"
        :disabled="(! savable || sending)"
        @click="submit()"
      >
        Zapisz
      </b-button>
    </b-form-group>
  </div>
</template>

<script>
import { debounce } from 'debounce'
import Input from './Input.vue'

export default {
  components: {
    Input
  },
  props: {
    _urzadzenie: Object,
    small: Boolean,
  },
  data() {
    return {
      urzadzenie: Object.assign({}, this._urzadzenie),
      sending: false,
      unsaved: false,
      producent_list: [],
      nazwa_list: [],
      model_list: [],
      nr_seryjny_raw_list: [],
      kod_wyrobu_list: [],
      nr_seryjny_original: this._urzadzenie.nr_seryjny_raw,
      nr_seryjny_searched: null,
    }
  },
  watch: {
    'urzadzenie.producent': function (val) {
      this.unsaved = true
      this.fetchProp('producent', val)
    },
    'urzadzenie.nazwa': function (val) {
      this.unsaved = true
      this.fetchProp('nazwa', val)
    },
    'urzadzenie.model': function (val) {
      this.unsaved = true
      this.fetchProp('model', val)
    },
    'urzadzenie.nr_seryjny_raw': function (val) {
      this.unsaved = true
      this.fetchProp('nr_seryjny_raw', val)
      this.fetchSerialNo(val)
    },
    'urzadzenie.kod_wyrobu': function (val) {
      this.unsaved = true
      this.fetchProp('kod_wyrobu', val)
    },
  },
  computed: {
    isValidSerialNo() {
      return ! this.nr_seryjny_searched || this.nr_seryjny_searched.trim() == this.nr_seryjny_original.trim()
    },
    savable() {
      return this.isValidSerialNo && this.unsaved
    },
  },
  methods: {
    submit() {
      this.sending = true

      axios.post(route(`urzadzenie.putUrzadzenie`, this.urzadzenie.id), {
        _token: this._token,
        _method: 'put',
        ...this.urzadzenie,
      })
        .then(res => {
          this.unsaved = false
        })
        .catch(err => {
          console.log(err)
        })
        .then(() => {
          this.sending = false
        })
    },
    fetchProp(prop, search) {
      axios.post(route(`urzadzenie.apiProps`, {
        prop,
      }), {
        _token: this._token,
        search,
      })
        .then(res => {
          this[`${prop}_list`] = res.data
        })
        .catch(err => {
          console.log(err)
        })
    },
    fetchSerialNo(search) {
      axios.post(route(`urzadzenie.apiSerialNo`), {
        _token: this._token,
        search,
      })
        .then(res => {
          this.nr_seryjny_searched = res.data.serial_no
        })
        .catch(err => {
          console.log(err)
        })
    },
  },
  created() {
    this.fetchProp = debounce(this.fetchProp, 300)
    // this.fetchSerialNo = debounce(this.fetchSerialNo, 300)
  },
}
</script>