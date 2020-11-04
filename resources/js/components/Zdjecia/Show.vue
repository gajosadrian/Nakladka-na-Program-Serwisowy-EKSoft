<template>
  <div class="push">
    <!-- <h4 class="mb-0">
      <span :class="{'text-danger': required && !has_zdjecia}">{{ title }}</span>
      <i
        v-if=""
        class="fa fa-star-of-life"
        :class="has_zdjecia && 'text-success' || 'text-danger'"
      />
    </h4>
    <hr class="mt-1"> -->

    <div
      v-for="(state, key) in states" :key="key"
      class="mt-1"
    >
      <div v-if="state.type == 'compressing'">
        <div class="bg-danger text-white font-w600 px-1">
          <i class="fa fa-spinner fa-pulse"></i>
          Kompresowanie...
        </div>
      </div>
      <div v-else-if="state.type == 'sending'">
        <div class="bg-warning text-white font-w600 px-1">
          <i class="fa fa-spinner fa-pulse"></i>
          Wysyłanie...
        </div>
      </div>
      <div v-else-if="state.type == 'sent'">
        <div class="bg-success text-white font-w600 px-1">
          Wysłano!
        </div>
      </div>
      <div v-else-if="state.type == 'error'">
        <div class="bg-danger text-white font-w600 px-1">
          Błąd przy wysyłaniu!
        </div>
      </div>
      <div v-else-if="state.type == 'error_compressing'">
        <div class="bg-danger text-white font-w600 px-1">
          Błąd przy kompresji!
        </div>
      </div>
    </div>

    <div class="mb-1">
      <b-progress
        v-if="required && !has_zdjecia"
        :value="1" :max="1"
        variant="danger"
        height="3px"
      />
      <b-form-file
        v-model="file"
        accept="image/*"
        :state="!required || has_zdjecia"
        :placeholder="title"
        drop-placeholder="Upuść zdjęcie tutaj..."
        browse-text="Wybierz"
        :disabled="disabled"
      />
    </div>

    <b-row class="gutters-tiny">
      <b-col
        cols="6" md="3" lg="2"
        v-for="zdjecie in zdjecia" :key="zdjecie.id"
        class="push ribbon ribbon-danger"
      >
        <div class="ribbon-box">
          <button type="submit" class="btn btn-link p-0 text-white" @click="remove(zdjecie.id)">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <b-link :href="zdjecie.url" target="_blank">
          <b-img :src="show_images && zdjecie.url || no_img_url" fluid />
        </b-link>
      </b-col>

      <b-col
        cols="6" md="3" lg="2"
        v-for="(base64_image, key) in base64_images" :key="key"
        class="push"
      >
        <div class="ribbon ribbon-danger">
          <b-img :src="base64_image[0]" fluid />
          <div @click="submitBase64(key)" class="ribbon-box" style="cursor: pointer;">
            <i class="fa fa-redo-alt mr-1"></i>
            Wyślij
          </div>
        </div>
      </b-col>
    </b-row>
  </div>
</template>

<script>
const imageCompressor = new ImageCompressor

export default {
  props: {
    title: String,
    zdjecia: Array,
    required: Boolean,
    type: String,
    save_to: String,
    urzadzenie_id: Number,
    zlecenie_id: Number,
    no_img_url: String,
    show_images: Boolean,
  },
  data() {
    return {
      disabled: false,
      states: [],
      form: {
        _token: document.getElementById('csrf-token').getAttribute('content'),
        zlecenie_id: this.zlecenie_id,
        urzadzenie_id: this.urzadzenie_id,
        save_to: this.save_to,
        type: this.type,
      },
      file: null,
      base64_images: [],
      base64_images_key: ['base64_zdjecie', this.zlecenie_id, this.urzadzenie_id, this.type].join('#'),
      base64_unsend_keys_key: 'base64_unsend_keys',
    }
  },
  watch: {
    file(file) {
      if (! file) return;

      this.disabled = true
      let state = this.addState('compressing')

      setTimeout(() => {
        this.compressImage(file, compressed_img => {
          this.file = null
          this.submit(compressed_img, null, state)
        }, err => {
          this.addState('error_compressing')
          console.log(err)
        })
      }, 1000);
    },
  },
  computed: {
    has_zdjecia() {
      return this.zdjecia.length > 0
    },
    has_base64_images() {
      return this.base64_images.length > 0
    },
  },
  methods: {
    submit(image, from_restore = false, state) {
      this.disabled = false

      if (! state) {
        state = this.addState('sending')
      } else {
        state.type = 'sending'
      }

      let formData = new FormData()
      formData.append('_token', this.form._token)
      formData.append('zlecenie_id', this.form.zlecenie_id)
      formData.append('urzadzenie_id', this.form.urzadzenie_id)
      formData.append('save_to', this.form.save_to)
      formData.append('type', this.form.type)
      formData.append('image', image, image.name)

      return new Promise((resolve, reject) => {
        axios.post(route('zlecenie-zdjecie.store'), formData)
          .then(res => {
            state.type = 'sent'
            this.fetchZdjecia()
            resolve()
          })
          .catch(err => {
            console.log(err)
            state.type = 'error'
            if (! from_restore) {
              this.addImagetoBase64Images(image)
            }
            reject()
          })
      })
    },
    submitBase64(index) {
      let base64_image = this.base64_images[index]
      let image = this.dataURLtoFile(base64_image[0], base64_image[1])
      this.submit(image, true)
        .then(() => {
          this.removeBase64Image(index)
        })
        .catch(() => {})
    },
    addState(state) {
      let key = Math.random().toString(36).substring(7)
      let obj = {
        key,
        type: state,
      }
      this.states.push(obj)
      return obj
    },
    fetchZdjecia() {
      this.$emit('fetchZdjecia')
    },
    compressImage(img, callback, err_callback) {
      imageCompressor.compress(img, {
        maxWidth: 1000,
        maxHeight: 1000,
        quality: 1.0,
      })
        .then(compressed_img => {
          if (callback) {
            callback(compressed_img)
          }
        })
        .catch(err => {
          if (err_callback) {
            err_callback(err)
          }
        })
    },
    getBase64(file, callback) {
      let reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = () => {
        callback(reader.result)
      }
      reader.onerror = (err) => {
        console.log(err)
      }
    },
    dataURLtoFile(dataurl, filename) {
      let arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n)
      while (n--) {
        u8arr[n] = bstr.charCodeAt(n)
      }
      return new File([u8arr], filename, {type:mime})
    },
    addImagetoBase64Images(image) {
      this.getBase64(image, base64_image => {
        this.base64_images.push([base64_image, image.name, this.zlecenie_id, this.urzadzenie_id, this.form.save_to, this.form.type])
        this.updateBase64Images()
      })
    },
    removeBase64Image(index) {
      this.base64_images.splice(index, 1)
      this.updateBase64Images()
    },
    fetchBase64Images() {
      let base64_images = localStorage[this.base64_images_key]
      return base64_images && JSON.parse(base64_images) || []
    },
    updateBase64Images() {
      const key = this.base64_images_key
      if (this.has_base64_images) {
        localStorage[key] = null
      }
      localStorage[key] = JSON.stringify(this.base64_images)

      const base64_unsend_keys = JSON.parse(localStorage[this.base64_unsend_keys_key] || false) || []
      if (this.has_base64_images && ! base64_unsend_keys.includes(key)) {
        base64_unsend_keys.push(key)
      }
      localStorage[this.base64_unsend_keys_key] = JSON.stringify(base64_unsend_keys)
    },
    remove(zdjecie_id) {
      axios.post(route('zlecenie-zdjecie.destroy', zdjecie_id), {
        _method: 'delete',
        _token: this.form._token,
      })
        .then(() => {
          this.fetchZdjecia()
        })
    },
  },
  mounted() {
    this.base64_images = this.fetchBase64Images()
  },
}
</script>