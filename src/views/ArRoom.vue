<template>
    <v-content>
        <qrcode-stream :track="false" @decode="onDecode" @init="onInit"/>
        <a-scene embedded artoolkit="sourceType: webcam;">
            <a-box position='0 0.5 -1' material='opacity: 0.5;'></a-box>
            <a-marker-camera preset='hiro'></a-marker-camera>
        </a-scene>
    </v-content>
</template>

<script>
  import {QrcodeStream, QrcodeDropZone, QrcodeCapture} from 'vue-qrcode-reader'

  export default {
    name: "ArRoom",
    props: ['id'],
    components: {
      QrcodeStream,
    },
    data() {
      return {
        markersUrl: 'markers/getmarkersforplace.php',
        roomId: 0,
        markers: {},
      }
    },
    created() {
      this.roomId = this.$route.params['id'];

      this.$http.get(
        this.$root.baseApiUrl + this.markersUrl,
        {params: {placeId: this.roomId, XDEBUG_SESSION_START:'PHPSTORM'}}
      )
        .then(
          response => {
            if (response.ok) {
              this.markers = response.body;
            } else {
              console.log('Error getting markers for place. Status text: ' + response.statusText);
            }
          },
          response => {
            console.log('Error getting markers for place. Status text: ' + response.statusText);
          })
    },
    methods: {
      onDecode(result) {
        const markerId = +result;
        if(this.markers.hasOwnProperty(markerId)) {
          console.log(this.markers[markerId].info);
        }
      },

      async onInit(promise) {
        try {
          await promise
        } catch (error) {
          if (error.name === 'NotAllowedError') {
            console.log('ERROR: you need to grant camera access permisson');
          } else if (error.name === 'NotFoundError') {
            console.log('ERROR: no camera on this device');
          } else if (error.name === 'NotSupportedError') {
            console.log('ERROR: secure context required (HTTPS, localhost)');
          } else if (error.name === 'NotReadableError') {
            console.log('ERROR: is the camera already in use?');
          } else if (error.name === 'OverconstrainedError') {
            console.log('ERROR: installed cameras are not suitable');
          } else if (error.name === 'StreamApiNotSupportedError') {
            console.log('ERROR: Stream API is not supported in this browser');
          }
        }
      }
    }
  }
</script>

<style>
    video {
        z-index: 0 !important;
    }

    a-scene {
        z-index: 1 !important;
    }
</style>