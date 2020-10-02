<template>
  <b-img
    @click="rotateRight()"
    :src="url"
    :style="{ transform: `rotate(${rotation}deg)` }"
    fluid
  />
</template>

<script>
export default {
  props: {
    url: String,
  },
  data() {
    return {
      rotation: 0,
    }
  },
  methods: {
    rotateRight() {
      this.rotation += 90
    }
  },
}
</script>