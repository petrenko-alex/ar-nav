<template>
    <v-content>
        <v-btn id="toggleMarkerInfoBtn" round @click.stop="showMarkerInfo = true">Show marker info</v-btn>
        <v-btn id="toggleDialogBtn" round @click.stop="showDialog = true">Show dialog</v-btn>


        <a-scene embedded artoolkit="sourceType: webcam;" arjs="debugUIEnabled: false;">
            <!--<a-gltf-model src="/gltf/arrow/scene.gltf" position="1 0.5 0.5" rotation="0 90 90"></a-gltf-model>-->
            <a-text :value="currentText" rotation="-90 0 0" color="#ff9800" position="0.8 0 -0.5"></a-text>

            <!--<a-entity geometry="primitive: plane"-->
                      <!--scale="3 4 1"-->
                      <!--position="0.8 1.5 0"-->
                      <!--rotation="-90 0 0"-->
                      <!--draw=" background: #ff9801;"-->
                      <!--htmltexture="asset: #list"-->
            <!--&gt;</a-entity>-->

            <a-marker-camera preset='hiro'></a-marker-camera>
            <!--<div style="visibility: hidden">-->
                <!--<qrcode-stream :track="false" @decode="onDecode" @init="onInit"/>-->
            <!--</div>-->
        </a-scene>

        <StackDialog v-model="showDialog"></StackDialog>
        <MarkerInfo v-model="showMarkerInfo" :card-title="markerInfoTitle">{{ markerInfoText }}</MarkerInfo>
    </v-content>
</template>

<script>
  import {QrcodeCapture, QrcodeDropZone, QrcodeStream} from 'vue-qrcode-reader';
  import MarkerInfo from '../components/MarkerInfo';
  import StackDialog from '../components/dialogs/StackDialog';

  export default {
    name: "ArRoom",
    props: ['id'],
    components: {
      QrcodeStream,
      MarkerInfo,
      StackDialog,
    },
    data() {
      return {
        markersUrl: 'getplacemarkers.php',
        roomId: 0,
        goals: {},
        currentText: 'Hello World',

        // Dialog
        showDialog: false,

        // Markers
        markers: {},

        // Marker info
        markerInfoText: '',
        markerInfoTitle: '',
        showMarkerInfo: false,
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
              this.markers = result.body;

              // Get random place object (imitate qr code decoded)
              //const randomMarkerId = this.testScanMarker();
              const randomMarkerId = 2;
              this.onMarkerScanned(randomMarkerId);


            } else {
              console.log('Error getting goals for place. Status text: ' + result.statusText);
            }
          },
          error => {
            console.log('Error getting goals for place. Status text: ' + error.statusText);
          })
    },
    methods: {
      // TODO: Test method (delete later)
      testScanMarker() {
        const markerIds = Object.keys(this.markers);
        return markerIds[Math.floor(Math.random() * markerIds.length)];
      },

      onMarkerScanned(markerId) {
        if (this.markers.hasOwnProperty(markerId)) {
          const marker = this.markers[markerId];
          const placeObject = marker['placeObject'];
          this.markerInfoText = placeObject['description'];
          this.markerInfoTitle = placeObject['title'];
        }
      },

      onDecode(result) {
        // TODO: Remove 2 lines below
        this.currentText = result;
        console.log(result);
        alert(result);

        // const markerId = +result;
        // if(this.goals.hasOwnProperty(markerId)) {
        //   this.currentText = this.goals[markerId].title;
        //   //this.show = true;
        // }
      },

      async onInit(promise) {
        try {
          console.log('before init qr');

          await promise;

          console.log('after init qr');
        } catch (error) {
          alert(error);
          this.currentText = error.name;
          this.show = true;

          if (error.name === 'NotAllowedError') {
            this.currentText = 'ERROR: you need to grant camera access permisson';
            this.show = true;

            console.log('ERROR: you need to grant camera access permisson');
          } else if (error.name === 'NotFoundError') {
            this.currentText = 'ERROR: no camera on this device';
            this.show = true;

            console.log('ERROR: no camera on this device');
          } else if (error.name === 'NotSupportedError') {
            this.currentText = 'ERROR: secure context required (HTTPS, localhost)';
            this.show = true;

            console.log('ERROR: secure context required (HTTPS, localhost)');
          } else if (error.name === 'NotReadableError') {
            this.currentText = 'ERROR: is the camera already in use?';
            this.show = true;

            console.log('ERROR: is the camera already in use?');
          } else if (error.name === 'OverconstrainedError') {
            this.currentText = 'ERROR: installed cameras are not suitable';
            this.show = true;

            console.log('ERROR: installed cameras are not suitable');
          } else if (error.name === 'StreamApiNotSupportedError') {
            this.currentText = 'ERROR: Stream API is not supported in this browser';
            this.show = true;

            console.log('ERROR: Stream API is not supported in this browser');
          }
        }
      }
    }
  }
</script>

<style>
    #toggleDialogBtn {
        z-index: 3;
        position: absolute;
        top: 60px;
        left: 180px;
    }

    #toggleMarkerInfoBtn {
        z-index: 3;
        position: absolute;
        top: 10px;
        left: 180px;
    }

    #ar-js-video {
        z-index: 0 !important;
    }

    a-scene {
        z-index: 1 !important;
    }
</style>