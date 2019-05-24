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
import '@/css/main.css'

Vue.config.productionTip = false;
Vue.use(Resource);
Vue.use(Vuetify, {
  iconfont: 'fa5',
  icons: {
    'github' : 'fab fa-github',
    'leftAngle': 'fas fa-angle-left',
    'rightAngle': 'fas fa-angle-right',
    'leftCaret': 'fas fa-caret-left',
    'rightCaret': 'fas fa-caret-right',
    'close': 'fas fa-times',
    'stack': 'fas fa-layer-group',
    'ok': 'fas fa-check',
    'info': 'fas fa-info',
    'changeGoal': 'fas fa-map-signs',
    'audio': 'fas fa-volume-up',
    'mapMarker': 'fas fa-map-marker-alt'
  },
});
Vue.config.ignoredElements = [
  'a-marker',
  'a-marker-camera'
];

new Vue({
  router,
  Resource,
  data: {
    baseApiUrl: 'https://' + process.env.VUE_APP_DOMAIN + '/api/',
  },
  render: h => h(App)
}).$mount('#app');
