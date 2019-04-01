<template>
    <v-content>
        <qrcode-stream :track="false" @decode="onDecode" @init="onInit"/>
        <a-scene embedded artoolkit="sourceType: webcam;">
            <!--<a-assets>-->
                <!--<img id="boxTexture" src="https://i.imgur.com/mYmmbrp.jpg">-->
                <!--<img id="groundTexture" src="https://cdn.aframe.io/a-painter/images/floor.jpg">-->
            <!--</a-assets>-->

            <!--<a-box src="#boxTexture" position="0 -0.5 0" rotation="0 45 45" material='opacity: 1;' scale="0.5 0.5 0.5">-->
                <!--<a-animation attribute="position"-->
                             <!--to="0 -0.5 0.5"-->
                             <!--direction="alternate"-->
                             <!--dur="1000"-->
                             <!--repeat="indefinite"-->
                <!--&gt;</a-animation>-->
            <!--</a-box>-->
            <!--<a-text :value="currentText"-->
                    <!--color="red"-->
                    <!--position="-0.9 0.2 -3"-->
                    <!--scale="1.5 1.5 1.5"-->
            <!--&gt;</a-text>-->
            <a-entity console-log="message: TEST;"></a-entity>


            <a-marker-camera preset='hiro'>
            </a-marker-camera>
        </a-scene>
    </v-content>
</template>

<script>
  import {QrcodeStream, QrcodeDropZone, QrcodeCapture} from 'vue-qrcode-reader';
  import ArameComponents from '../components/aframe/components';

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
        currentText: 'Hello!',
      }
    },
    created() {
      // AFRAME.registerComponent('my-test-component', {
      //   tick: function () {
      //     console.log(this);
      //     var el = this.el;
      //     el.setAttribute("value", "other text");
      //   }
      // });



      this.roomId = this.$route.params['id'];

      this.$http.get(
        this.$root.baseApiUrl + this.markersUrl,
        {params: {placeId: this.roomId, XDEBUG_SESSION_START:'PHPSTORM'}}
      )
        .then(
          result => {
            if (result.ok) {
              this.markers = result.body;
            } else {
              console.log('Error getting markers for place. Status text: ' + result.statusText);
            }
          },
          error => {
            console.log('Error getting markers for place. Status text: ' + error.statusText);
          })
    },
    methods: {
      onDecode(result) {
        const markerId = +result;
        if(this.markers.hasOwnProperty(markerId)) {
          this.currentText = this.markers[markerId].info;
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

    .qrcode-stream {
        position: absolute;
        left: 0;
        top: 0;
        width: 1px;
        height: 1px;
    }
</style>