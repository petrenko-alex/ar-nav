<template>
  <v-app>
    <v-navigation-drawer fixed app v-model="showSideMenu">
      <v-list dense>
          <v-list-tile>
              <v-list-tile-action>
                  <v-icon>home</v-icon>
              </v-list-tile-action>
              <v-list-tile-content>
                  <v-list-tile-title>Домой</v-list-tile-title>
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

    <v-content>
      <PlaceList></PlaceList>
    </v-content>
  </v-app>
</template>

<script>
  import PlaceList from '../components/PlaceList';

  export default {
      name: 'home',
      components: {
          PlaceList
      },
      data() {
          return {
            appName: 'ar-nav',
            showSideMenu: false,
            routes: this.$router.options.routes,
          }
      }
  }
</script>
