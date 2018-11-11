import Vue from 'vue';
import Router from './router';
import App from './App.vue';
import './plugins/vuetify';

import './registerServiceWorker';
import 'roboto-fontface/css/roboto/roboto-fontface.css';
import 'material-design-icons-iconfont/dist/material-design-icons.css';

Vue.config.productionTip = false;

new Vue({
  Router,
  render: h => h(App)
}).$mount('#app')
