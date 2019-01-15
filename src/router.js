import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import MapCreator from './views/MapCreator'

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
      meta: {
        icon: 'home',
        title: 'Домой',
      }
    },
    {
      path: '/map-creator',
      name: 'map_creator',
      component: MapCreator,
      meta: {
        icon: 'fas fa-map-marked-alt',
        title: 'Генератор карт',
      }
    },
  ]
})
