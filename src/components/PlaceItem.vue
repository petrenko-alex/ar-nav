<template>
    <v-layout row>
        <v-flex xs12 sm6 offset-lg3>

            <v-card v-for="place in places" :key="place.id">
                <v-img class="white--text"  :src="place.image"/>
                <v-card-title>
                    <div>
                        <span class="grey--text">{{ place.name }}</span><br>
                        <span>{{ place.description }}</span><br>
                    </div>
                </v-card-title>
                <v-card-actions>
                    <v-btn flat color="orange">Исследовать</v-btn>
                </v-card-actions>
            </v-card>


        </v-flex>
    </v-layout>
</template>

<script>
  import places from '../stubs/places.js';

  export default {
    name: "PlaceItem",
    data() {
      return {
        places
      }
    }
  }
</script>

<style scoped>

</style>