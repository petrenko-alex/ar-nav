<template>
    <v-content>
        <a-scene embedded artoolkit="sourceType: webcam;">
            <a-box position='0 0.5 0' material='opacity: 0.5;'></a-box>
            <a-marker-camera preset='hiro'></a-marker-camera>
        </a-scene>
    </v-content>
</template>

<script>
  export default {
    name: "ArRoom",
    props: ['id'],
  }
</script>
