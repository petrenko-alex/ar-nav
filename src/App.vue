<template>
  <v-app>
    <v-navigation-drawer fixed app v-model="showSideMenu">
      <v-list dense></v-list>
    </v-navigation-drawer>
    <v-toolbar app>
      <v-toolbar-side-icon @click.stop="showSideMenu = !showSideMenu"></v-toolbar-side-icon>
      <v-toolbar-title class="headline text-uppercase">
        <span>{{ appName }}</span>
      </v-toolbar-title>
    </v-toolbar>

    <v-content>
      <PlaceItem></PlaceItem>
    </v-content>
  </v-app>
</template>

<script>
import PlaceItem from './components/PlaceItem'

export default {
  name: 'App',
  components: {
    PlaceItem
  },
  data () {
    return {
      appName: 'ar-nav',
      showSideMenu: false,
    }
  }
}
</script>
