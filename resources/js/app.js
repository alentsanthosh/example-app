require('./bootstrap');

import Vue from 'vue';
import ElementUI from 'element-ui';
import ExampleComponent from './components/ExampleComponent.vue';
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(ElementUI);
Vue.component('example-component', ExampleComponent);

const app = new Vue({
    el: '#app',
});

