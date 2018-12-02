<template>
    <v-container grid-list-lg>
        <div class="ma-5 text-xs-center headline" v-if="isEmpty">
            К сожалению, доступных помещений пока нет :(
        </div>
        <v-layout row wrap>
            <v-flex xs12 sm6 md4 lg2
                    v-for="place in places"
                    :key="place.id"
            >
                <PlaceItem v-bind:place="place"></PlaceItem>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
  import PlaceItem from './PlaceItem';

  export default {
    name: "PlaceList",
    components: {
      PlaceItem
    },
    data() {
      return {
        placesUrl: 'places/getallplaces.php',
        places: []
      }
    },
    computed: {
      isEmpty() {
        return this.places.length === 0;
      }
    },
    created() {
      this.$http.get(this.$root.baseApiUrl + this.placesUrl).then(response => {
        if (response.ok) {
          this.places = response.body;
        } else {
          console.log('Error getting places. Status text: ' + response.statusText);
        }
      }, response => {
        console.log('Error getting places. Status text: ' + response.statusText);
      })
    }
  }
</script>