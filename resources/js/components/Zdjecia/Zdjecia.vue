<template>
  <div>
    <!-- <b-button @click="fetchZdjecia()" class="push">fetch zdjecia</b-button> -->

    <div class="border bg-white shadow-sm p-2 push">
      <b-form-checkbox v-model="show_images" switch>
        <b>Pokaż zdjęcia</b>
      </b-form-checkbox>
    </div>

    <ZdjecieShow
      v-show="(_.show && _.show2)"
      v-for="(_, i) in ZdjecieShowList" :key="i"
      :title="_.title"
      :required="_.required"
      :type="_.type"
      :zdjecia="_.zdjecia"
      :save_to="_.save_to"
      :urzadzenie_id="_.urzadzenie_id"
      :zlecenie_id="_.zlecenie_id"
      :no_img_url="no_img_url"
      :show_images="show_images"
      @fetchZdjecia="fetchZdjecia()"
    />
  </div>
</template>

<script>
export default {
  props: {
    _token: String,
    zlecenie_id: Number,
    required_photos: Array,
    zlecenie: Object,
    urzadzenie: Object,
    zdjecia: Array,
    no_img_url: String,
  },
  data() {
    return {
      show_images: false,
      // zlecenie: null,
      // urzadzenie: null,
      // zdjecia: [],
      // no_img_url: null,
    }
  },
  computed: {
    ZdjecieShowList() {
      if (! this.zlecenie) return []

      return [
        {
          title: 'Tabliczka',
          show: Boolean(this.urzadzenie && this.urzadzenie.id),
          show2: true,
          required: this.required_photos.includes('tabliczka'),
          type: 'tabliczka',
          zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'tabliczka'),
          save_to: 'urzadzenie',
          urzadzenie_id: this.urzadzenie_id,
          zlecenie_id: this.zlecenie_iid,
        },
        {
          title: 'Gwarancja',
          show: Boolean(this.urzadzenie && this.urzadzenie.id),
          show2: this.zlecenie.is_gwarancja,
          required: this.required_photos.includes('gwarancja'),
          type: 'gwarancja',
          zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'gwarancja'),
          save_to: 'urzadzenie',
          urzadzenie_id: this.urzadzenie_id,
          zlecenie_id: this.zlecenie_iid,
        },
        {
          title: 'Polisa',
          show: Boolean(this.urzadzenie && this.urzadzenie.id),
          show2: this.zlecenie.is_gwarancja || this.zlecenie.is_ubezpieczenie,
          required: this.required_photos.includes('polisa'),
          type: 'polisa',
          zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'polisa'),
          save_to: 'urzadzenie',
          urzadzenie_id: this.urzadzenie_id,
          zlecenie_id: this.zlecenie_iid,
        },
        {
          title: 'Dowód zakupu',
          show: Boolean(this.urzadzenie && this.urzadzenie.id),
          show2: this.zlecenie.is_gwarancja || this.zlecenie.is_ubezpieczenie,
          required: this.required_photos.includes('dowod_zakupu'),
          type: 'dowod_zakupu',
          zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'dowod_zakupu'),
          save_to: 'urzadzenie',
          urzadzenie_id: this.urzadzenie_id,
          zlecenie_id: this.zlecenie_iid,
        },
        {
          title: 'Urządzenie',
          show: Boolean(this.urzadzenie && this.urzadzenie.id),
          show2: true,
          required: this.required_photos.includes('urzadzenie'),
          type: 'urzadzenie',
          zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'urzadzenie'),
          save_to: 'urzadzenie',
          urzadzenie_id: this.urzadzenie_id,
          zlecenie_id: this.zlecenie_iid,
        },
        {
          title: 'Inne',
          show: true,
          show2: true,
          required: false,
          type: 'inne',
          zdjecia: this.zdjecia.filter(zdjecie => zdjecie.type == 'inne'),
          save_to: 'zlecenie',
          zlecenie_id: this.zlecenie_iid,
        },
      ]
    },
    urzadzenie_id() {
      return this.urzadzenie && this.urzadzenie.id || 0
    },
    zlecenie_iid() {
      return this.zlecenie && this.zlecenie.id || this.zlecenie_id
    },
  },
  methods: {
    fetchZdjecia() {
      if (this.zlecenie_id) {
        axios.get(route('zlecenia.pokazZdjecia', {
          zlecenie_id: this.zlecenie_id,
        }), {
          _token: this._token,
        })
          .then(res => {
            let data = res.data

            this.zlecenie = data.zlecenie
            this.urzadzenie = data.urzadzenie
            this.zdjecia = data.zdjecia
            this.no_img_url = data.no_img_url
          })
          .catch(err => {
            console.log(err)
          })
      } else {
        this.$emit('fetchZdjecia')
      }
    },
  },
  mounted() {
    this.fetchZdjecia()

    // history.pushState(null, null, location.href)
    // window.onpopstate = () => {
    //   history.go(1)
    // }
  },
}
</script>