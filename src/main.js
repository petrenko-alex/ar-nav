import Vue from 'vue';
import App from './App.vue';
import Resource from 'vue-resource';
import Vuetify from 'vuetify';
import router from './router';
import './plugins/vuetify';
import './registerServiceWorker';
import 'roboto-fontface/css/roboto/roboto-fontface.css';
import 'material-design-icons-iconfont/dist/material-design-icons.css';
import '@fortawesome/fontawesome-free/css/all.css';

Vue.config.productionTip = false;
Vue.use(Resource);
Vue.use(Vuetify, {
  iconfont: 'fa5',
  icons: {
    'github' : 'fab fa-github'
  }
});

new Vue({
  router,
  Resource,
  data: {
    baseApiUrl: 'http://192.168.1.91:8090/api/',
  },
  render: h => h(App)
}).$mount('#app');
