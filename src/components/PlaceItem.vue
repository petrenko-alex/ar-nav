<template>
    <v-card>
        <v-img class="white--text" :src="place.imagePath"/>
        <v-card-title>
            <div>
                <span class="grey--text">{{ place.title }}</span><br>
                <span>{{ place.description }}</span><br>
            </div>
        </v-card-title>
        <v-card-actions>
            <v-layout row justify-center mb-1>
                <v-btn flat color="orange" :to="{name: 'ar_room', params: {id: place.id}}">
                    Исследовать
                </v-btn>
            </v-layout>
        </v-card-actions>
    </v-card>
</template>

<script>
  export default {
    name: "PlaceItem",
    props: [
      'place',
    ],
    beforeRouteUpdate(to, from, next) {

    },
  }
</script>