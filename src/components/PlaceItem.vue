<template>
    <v-card>
        <v-img class="white--text" :src="place.image"/>
        <v-card-title>
            <div>
                <span class="grey--text">{{ place.name }}</span><br>
                <span>{{ place.description }}</span><br>
            </div>
        </v-card-title>
        <v-card-actions>
            <v-layout row justify-center mb-1>
                    <v-btn flat color="orange">Исследовать</v-btn>
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
  }
</script>