<template>
    <v-content>
        <a-scene embedded artoolkit="sourceType: webcam;">
            <a-gltf-model src="/gltf/arrow/scene.gltf" position="1 1 0.5" rotation="0 90 90"></a-gltf-model>
            <a-text :value="currentText" rotation="-90 0 0" color="#ff9800" position="0.8 0 -0.5"></a-text>

            <a-marker-camera preset='hiro'></a-marker-camera>
            <div style="visibility: hidden">
                <qrcode-stream :track="true" @decode="onDecode" @init="onInit"/>
            </div>
        </a-scene>

        <MarkerInfo v-model="show" card-title="">{{ currentText }}</MarkerInfo>
    </v-content>
</template>

<script>
  import {QrcodeStream, QrcodeDropZone, QrcodeCapture} from 'vue-qrcode-reader';
  import ArameComponents from '../components/aframe/components';
  import MarkerInfo from '../components/MarkerInfo';

  export default {
    name: "ArRoom",
    props: ['id'],
    components: {
      QrcodeStream,
      MarkerInfo,
    },
    data() {
      return {
        markersUrl: 'getplaceobjects.php',
        roomId: 0,
        goals: {},
        currentText: 'Hello World',

        counter: 0,

        show: false
      }
    },
    created() {
      this.roomId = this.$route.params['id'];

      this.$http.get(
        this.$root.baseApiUrl + this.markersUrl,
        {params: {placeId: this.roomId, XDEBUG_SESSION_START:'PHPSTORM'}}
      )
        .then(
          result => {
            if (result.ok) {
              this.goals = result.body;
            } else {
              console.log('Error getting goals for place. Status text: ' + result.statusText);
            }
          },
          error => {
            console.log('Error getting goals for place. Status text: ' + error.statusText);
          })
    },
    methods: {
      onDecode(result) {
        // TODO: Remove 2 lines below
        this.currentText = result;

        const markerId = +result;
        if(this.goals.hasOwnProperty(markerId)) {
          this.currentText = this.goals[markerId].title;
          //this.show = true;
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
    #ar-js-video {
        z-index: 0 !important;
    }

    a-scene {
        z-index: 1 !important;
    }
</style>