import Vue from 'vue';
import Router from './router';
import Resource from 'vue-resource';
import App from './App.vue';
import './plugins/vuetify';

import './registerServiceWorker';
import 'roboto-fontface/css/roboto/roboto-fontface.css';
import 'material-design-icons-iconfont/dist/material-design-icons.css';

Vue.config.productionTip = false;
Vue.use(Resource);

new Vue({
  Router,
  Resource,
  data: {
    baseApiUrl: 'http://192.168.1.91:8090/api/',
  },
  render: h => h(App)
}).$mount('#app');
