
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



Vue.use(BootstrapVue);
VueClipboard.config.autoSetContainer = true; Vue.use(VueClipboard);
Vue.component('nl2br', Nl2br);
Vue.component('b-block', require('./components/Dashmix/Block/b-block.vue').default);
Vue.component('zlecenie-opis', require('./components/Zlecenie/opis.vue').default);



Vue.mixin({
    methods: {
        route: route,
    }
});
const app = new Vue({
    el: '#app'
});
