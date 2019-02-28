
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap_laravel');
window.Vue = require('vue');

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap-vue/dist/bootstrap-vue.min.css'



Vue.use(BootstrapVue);
Vue.component('b-block', require('./components/Dashmix/Block/b-block.vue').default);
Vue.component('zlecenie-append-opis', require('./components/Zlecenie/append-opis.vue').default);

Vue.mixin({
    methods: {
        route: route,
    }
});
const app = new Vue({
    el: '#app'
});
