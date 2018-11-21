<template>
    <v-container grid-list-lg>
        <v-layout row wrap>
            <v-flex xs12 sm6 md4 lg2 v-for="place in places" :key="place.id">
                <PlaceItem v-bind:place="place"></PlaceItem>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
  import places from '../stubs/places.js';
  import PlaceItem from './PlaceItem';

  export default {
    name: "PlaceList",
    components: {
      PlaceItem
    },
    data() {
      return {
        places
      }
    }
  }
</script>