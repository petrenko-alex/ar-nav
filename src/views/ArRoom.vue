<template>
    <v-content>
        <div class="ma-5 text-xs-center headline">
            {{ id }}
        </div>
    </v-content>
</template>

<script>
  export default {
    name: "ArRoom",
    props: ['id']
  }
</script>
