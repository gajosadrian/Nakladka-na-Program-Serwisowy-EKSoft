<template>
  <div>
    <b-form-group
      :label="label"
      label-cols="12" label-cols-lg="4"
    >
      <b-form-input
        :state="state ? null : false"
        :list="`datalist${_uid}`"
        :value="value"
        @input.native="updateValue($event.target.value)"
      />
      <div v-if="text_danger" class="text-danger mt-1">{{ text_danger }}</div>
      <datalist :id="`datalist${_uid}`">
        <option v-for="data in datalist">{{ data }}</option>
      </datalist>
    </b-form-group>
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    value: String,
    state: Boolean,
    datalist: Array,
    text_danger: String,
  },
  methods: {
    updateValue(val) {
      this.$emit('input', val)
    },
  },
}
</script>