<template>
  <div class="block">
    <div class="block-content p-1">
      <div v-if="title" class="text-center">
        <label class="m-0" :class="{'text-danger': active}">
          {{ title }}
        </label>
      </div>
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    disabled: Boolean,
    active: Boolean,
  },
}
</script>
