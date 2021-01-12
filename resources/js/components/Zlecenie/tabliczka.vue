<template>
  <div>
    <div v-for="zdjecie in zdjecia" :key="zdjecie.id" class="push">
      <Zdjecie :url="zdjecie.url" />
    </div>
  </div>
</template>

<script>
import Zdjecie from '../Urzadzenia/Zdjecie'

export default {
  components: {
    Zdjecie,
  },
  props: {
    zlecenie_id: Number,
    zdjecia: Array,
  },
}
</script>
