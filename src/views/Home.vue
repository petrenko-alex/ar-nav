<template>
    <v-content>
      <PlaceList></PlaceList>
    </v-content>
</template>

<script>
  import PlaceList from '../components/PlaceList';

  export default {
      name: 'home',
      components: {
          PlaceList
      },
  }
</script>
