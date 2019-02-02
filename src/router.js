import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import MapCreator from './views/MapCreator'
import ArRoom from './views/ArRoom'

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
      meta: {
        showInMenu: true,
        icon: 'home',
        title: 'Домой',
      }
    },
    {
      path: '/map-creator',
      name: 'map_creator',
      component: MapCreator,
      meta: {
        showInMenu: true,
        icon: 'fas fa-map-marked-alt',
        title: 'Генератор карт',
      }
    },
    {
      path: '/ar-room/:id',
      name: 'ar_room',
      component: ArRoom,
      meta: {
        showInMenu: false,
      }
    },
  ]
})
