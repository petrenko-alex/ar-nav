<template>
  <div id="app">
    <v-app>
      <v-navigation-drawer fixed app v-model="showSideMenu">
        <v-list dense>
          <v-list-tile v-for="route in routes" :key="route.name">
            <v-list-tile-action>
              <v-icon>{{ route.meta.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>{{ route.meta.title }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
      </v-navigation-drawer>

      <v-toolbar app>
        <v-toolbar-side-icon @click.stop="showSideMenu = !showSideMenu"></v-toolbar-side-icon>
        <v-toolbar-title class="headline text-uppercase">
          <span>{{ appName }}</span>
        </v-toolbar-title>
      </v-toolbar>

      <router-view/>
    </v-app>
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      showSideMenu: false,
      appName: 'ar-nav',
      routes: this.$router.options.routes,
    }
  }
}
</script>
