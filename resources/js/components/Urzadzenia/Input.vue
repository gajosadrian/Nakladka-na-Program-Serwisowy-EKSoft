<template>
  <div>
    <!-- Default variant -->
    <b-form-group
      v-if="! small"
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
    </b-form-group>

    <!-- Small variant -->
    <div v-else>
      <div class="font-w700">{{ label }}:</div>
      <b-input
        size="sm"
        :state="state ? null : false"
        :list="`datalist${_uid}`"
        :value="value"
        maxlength="25"
        @input.native="updateValue($event.target.value)"
      />
      <div v-if="text_danger" class="text-danger mt-1">{{ text_danger }}</div>
    </div>

    <!-- Shared -->
    <datalist :id="`datalist${_uid}`">
      <option v-for="(data, key) in datalist" :key="key">{{ data }}</option>
    </datalist>
  </div>
</template>

<script>
export default {
  props: {
    small: Boolean,
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