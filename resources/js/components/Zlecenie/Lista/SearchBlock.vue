<template>
  <div class="block">
    <div class="block-content p-1">
      <div v-if="title" class="text-center">
        <label class="m-0">{{ title }}</label>
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
  },
}
</script>
