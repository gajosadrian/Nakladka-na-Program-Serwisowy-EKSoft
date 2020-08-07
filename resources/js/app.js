
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap_laravel');
window.Vue = require('vue');

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'; import 'bootstrap-vue/dist/bootstrap-vue.min.css'
import VueClipboard from 'vue-clipboard2'
import Nl2br from 'vue-nl2br'
import DatePicker from 'vue2-datepicker'



Vue.use(BootstrapVue);
VueClipboard.config.autoSetContainer = true; Vue.use(VueClipboard);
Vue.component('nl2br', Nl2br);
Vue.component('b-block', require('./components/Dashmix/Block/b-block.vue').default);
Vue.component('zlecenie-opis', require('./components/Zlecenie/opis.vue').default);
Vue.component('zlecenie-change-status', require('./components/Zlecenie/change-status.vue').default);
Vue.component('zlecenie-kosztorys', require('./components/Zlecenie/kosztorys.vue').default);
Vue.component('zlecenie-mobile-app', require('./components/Zlecenie/mobile-app.vue').default);
Vue.component('date-picker', DatePicker);
Vue.component('Zdjecia', require('./components/Zdjecia/Zdjecia.vue').default);
Vue.component('ZdjecieShow', require('./components/Zdjecia/Show.vue').default);
Vue.component('UrzadzeniaZdjecia', require('./components/Urzadzenia/Zdjecia.vue').default);
Vue.component('SmsCreate', require('./components/Sms/Create.vue').default);



Vue.mixin({
    methods: {
        route: route,
    }
});
const app = new Vue({
    el: '#app'
});
