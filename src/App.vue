<template>
  <div id="app">
    <v-app>
      <v-navigation-drawer fixed app v-model="showSideMenu">
        <v-list dense>
          <v-list-tile v-for="route in menuRoutes" :key="route.name" :to="{path: route.path}">
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
        <v-spacer></v-spacer>
        <v-btn icon :href="appRepoUrl" target="_blank">
          <v-icon>$vuetify.icons.github</v-icon>
        </v-btn>
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
      appRepoUrl: '//github.com/gafk/ar-nav',
      routes: this.$router.options.routes,
    }
  },
  computed: {
    menuRoutes() {
      return this.routes.filter(
          (route) => {
            return route.meta.showInMenu;
          }
      )
    }
  },
}
</script>
