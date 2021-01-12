<template>
  <div>
    <b-row class="no-gutters">
      <b-col cols="12" lg="3" xl="2" class="bg-info-light p-2">
        <b-form-input v-model="scale" type="range" min="1" max="3" step="0.1" />
      </b-col>
    </b-row>
    <b-img
      ref="tabliczka"
      :src="url"
      :style="{
        transform: `rotate(${rotation}deg)`,
        width: size,
      }"
      @click="rotateRight()"
    />
  </div>
</template>

<script>
import ZoomScroll from 'vue-zoom-scroll'

export default {
  components: {
    ZoomScroll,
  },

  props: {
    url: String,
  },

  data() {
    return {
      rotation: 0,
      scale: 1,
      width: null,
      size: 'auto',
    }
  },

  watch: {
    scale() {
      if (! this.$refs.tabliczka) return

      if (! this.width) {
        this.width = this.$refs.tabliczka.clientWidth
      }

      this.size = this.width * this.scale + 'px'
    },
  },

  methods: {
    rotateRight() {
      this.rotation += 90
    }
  },
}
</script>