<template>
    <v-content id="arRoomRoot">
        <a-scene embedded artoolkit="sourceType: webcam;" arjs="debugUIEnabled: false;">
            <!--<a-entity geometry="primitive: plane"-->
            <!--scale="3 4 1"-->
            <!--position="0.8 1.5 0"-->
            <!--rotation="-90 0 0"-->
            <!--draw=" background: #ff9801;"-->
            <!--htmltexture="asset: #list"-->
            <!--&gt;</a-entity>-->

            <a-marker preset='hiro' id='hiroMarker' registerevents>
                <a-gltf-model :src="goal.directions.object.src"
                              :color="goal.directions.color"
                              :position="goal.directions.object.position"
                              :rotation="goal.directions.object.rotation"
                              :scale="goal.directions.object.scale"
                ></a-gltf-model>
                <a-text :value="currentText"
                        :color="goal.directions.color"
                        :rotation="goal.directions.text.rotation"
                        :position="goal.directions.text.position"
                        :scale="goal.directions.text.scale"
                ></a-text>
            </a-marker>
            <a-entity camera></a-entity>
        </a-scene>

        <qrcode-stream :track="false" @decode="onDecode" @init="onInit"/>

        <StackDialog v-if="showWelcomeDialog" v-model="showWelcomeDialog"
                     :texts="welcomeDialogTexts" :title="welcomeDialogTitle"
                     v-on:dialog-read="welcomeDialogRead"
        />

        <SelectGoalDialog v-model="showSelectGoalDialog"
                          :goals="goals"
                          v-on:goal-selected="goalSelected"
        />

        <MarkerInfo v-model="showMarkerInfo"
                    :card-title="currentPlaceObjectTitle"
        >
            {{ currentPlaceObjectDescription }}
        </MarkerInfo>

        <div id="uiElements" v-show="marker.visible">
            <v-btn id="placeObjectInfoBtn" fab color="primary" @click="showMarkerInfo = true" v-show="currentPlaceObject">
                <v-icon>$vuetify.icons.info</v-icon>
            </v-btn>
            <v-btn id="changeGoalBtn" fab color="primary" @click="showSelectGoalDialog = true" v-show="currentPlaceObject">
                <v-icon>$vuetify.icons.changeGoal</v-icon>
            </v-btn>
        </div>
    </v-content>
</template>

<script>
  import {QrcodeCapture, QrcodeDropZone, QrcodeStream} from 'vue-qrcode-reader';
  import MarkerInfo from '../components/MarkerInfo';
  import StackDialog from '../components/dialogs/StackDialog';
  import SelectGoalDialog from '../components/dialogs/SelectGoalDialog';

  export default {
    name: "ArRoom",
    props: ['id'],
    components: {
      QrcodeStream,
      MarkerInfo,
      StackDialog,
      SelectGoalDialog,
    },
    data() {
      return {
        apiUrl: {
          markersUrl: 'getplacemarkers.php',
          pathUrl: 'getpathtoplaceobject.php',
        },
        roomId: 0,
        currentText: 'Hello World',

        goals: {},
        goal: {
          current: null,
          path: null,
          directions: {
            text: {
              scale: '2 0.7 0',
              position: '-1.5 0 0.8',
              rotation: '270 0 0',
            },
            object: {
              src: '/gltf/arrow/scene.gltf',
              scale: '0.5 0.5 0.5',
              position: '1 0.5 0.5',
              rotation: '90 90 90',
            },
            color: '#2196F3',
          },
        },

        // Welcome dialog
        welcomeDialogTitle: 'AR-Nav',
        welcomeDialogTexts: [],

        // Dialogs visibility
        showMarkerInfo: false,
        showSelectGoalDialog: false,
        showWelcomeDialog: false,

        // Markers
        markers: {},
        // Current Marker info
        marker: {
          visible: false,
          current: null,
        },
      }
    },
    computed: {
      currentPlaceObject() {
        const currentMarker = this.marker.current;
        if (currentMarker
          && currentMarker['placeObject']
          && currentMarker['placeObject']['type'] !== 'service'
        ) {
          return currentMarker['placeObject'];
        }
        return null;
      },
      currentPlaceObjectDescription() {
        if (this.currentPlaceObject && this.currentPlaceObject.hasOwnProperty('description')) {
          return this.currentPlaceObject['description'];
        } else {
          return '';
        }
      },
      currentPlaceObjectTitle() {
        if (this.currentPlaceObject && this.currentPlaceObject.hasOwnProperty('title')) {
          return this.currentPlaceObject['title'];
        } else {
          return '';
        }
      },
      arNavInit() {
        return localStorage.getItem('ARNav.init') === 'true';
      },
      arNavSessionInit() {
        return sessionStorage.getItem('ARNav.activeSession') === 'true';
      },
    },
    created() {
      this.initAFrameComponents();
      this.roomId = this.$route.params['id'];

      // Get markers
      this.$http.get(
        this.$root.baseApiUrl + this.apiUrl.markersUrl,
        {params: {placeId: this.roomId}}
      ).then(
        result => {
          if (result.ok) {
            this.markers = result.body;

            this.init();
          } else {
            console.log('Error getting goals for place. Status text: ' + result.statusText);
          }
        },
        error => {
          console.log('Error getting goals for place. Status text: ' + error.statusText);
        }
      );
    },
    methods: {
      init() {
        this.initGoals();
        this.initStartDialog();
      },

      initAFrameComponents() {
        /* globals AFRAME */
        const self = this;
        AFRAME.registerComponent('registerevents', {
          init: function () {
            const marker = this.el;
            marker.addEventListener('markerFound', function() {
              self.onMarkerFound(marker);
            });
            marker.addEventListener('markerLost', function() {
              self.onMarkerLost(marker);
            });
          }
        });
      },

      /**
       * Обработчик события "Цель выбрана".
       *
       * @param {number} goalId id цели
       */
      goalSelected(goalId) {
        let self = this;
        if (this.goals.hasOwnProperty(goalId)) {
          this.goal.current = this.goals[goalId];

          // Get path
          this.$http.get(
            this.$root.baseApiUrl + this.apiUrl.pathUrl,
            {
              params: {
                placeId: this.roomId,
                targetPlaceObjectId: this.goal.current['id'],
                startMarkerId: this.marker.current['id']
              }
            }
          ).then(
            result => {
              if (result.ok) {
                this.goal.path = result.body;
                this.showDirections();
              } else {
                console.log('Error getting path for goal. Status text: ' + result.statusText);
              }
            },
            error => {
              console.log('Error getting path for goal. Status text: ' + error.statusText);
            }
          );
        }

        setTimeout(function () {
          self.showSelectGoalDialog = false;
        }, 300);
      },

      goalReached() {
        const msg = 'Congratulations! You\'ve reach the goal';
        this.currentText = msg;
        console.log(msg);
      },

      showDirections() {
        if (!this.goal.current && !this.goal.path) {
          return;
        }

        // Get current directions
        const currentMarkerId = this.marker.current['id'];
        const path = this.goal.path;

        const currentPathNode = path['m_' + currentMarkerId]
        if (currentPathNode) {
          const next = currentPathNode['next'];
          if(next) {
            this.showDirectionsInfo(next.path.directions);
          }

          const pathEnd = currentPathNode['pathEnd'];
          if (pathEnd) {
            this.goalReached();
          }
        }
      },

      showDirectionsInfo(directionsInfo) {
        this.currentText = directionsInfo;

        var directionsRegex = /(\[\d+\])(\(.*\))?/;
        var parseResult = directionsInfo.match(directionsRegex);

        let degrees = parseResult[1];
        if (degrees) {
          degrees = degrees.replace('[', '');
          degrees = degrees.replace(']', '');
        }

        let directionsText = parseResult[2];
        if (directionsText) {
          directionsText = directionsText.replace('(', '');
          directionsText = directionsText.replace(')', '');
        }

        // Текст
        // Стрелка
        // Голос
      },

      /**
       * Обработчик события "Приветственный диалог прочитан"
       */
      welcomeDialogRead() {
        this.rememberUser();
      },

      /**
       * Инициализирует объект целей на основе объекта маркеров
       */
      initGoals() {
          for(let markerId in this.markers) {
            let marker = this.markers[markerId];
            if(marker.hasOwnProperty('placeObject')) {
              let markerPlaceObject = marker['placeObject'];

              // PlaceObject типа service не является целью
              if (markerPlaceObject.hasOwnProperty('type') && markerPlaceObject['type'] === 'service') {
                continue;
              }

              if (!this.goals.hasOwnProperty(markerPlaceObject['id'])) {
                this.goals[markerPlaceObject['id']] = markerPlaceObject;
              }
            }
          }
      },

      /**
       * Инициализирует начальный диалог в зависимости
       * от "статуса пользователя"
       * "Статус пользователя" - данные об использовании приложения
       */
      initStartDialog() {
        if(!this.arNavInit) {
          // First time ever user
          this.welcomeDialogTexts = this.getDialogTextForFirstTimeEverUser();
          this.showWelcomeDialog = true;
        } else if(this.arNavInit && !this.arNavSessionInit) {
          // Returnee user (after closing app)
          this.welcomeDialogTexts = this.getDialogTextForReturnedUser();
          this.showWelcomeDialog = true;
        }
      },

      /**
       * Записывает "статус пользователя".
       * "Статус пользователя" - данные об использовании приложения
       */
      rememberUser() {
        if (!this.arNavInit) {
          this.initArNav();
        }

        if (!this.arNavSessionInit) {
          this.initArNavSession();
        }
      },

      initArNav() {
        localStorage.setItem('ARNav.init', 'true');
      },

      initArNavSession() {
        sessionStorage.setItem('ARNav.activeSession', 'true');
      },

      /**
       * Возвращает привественный текст для пользователя, который впервые запустил приложение.
       *
       * @returns {string[]}
       */
      getDialogTextForFirstTimeEverUser() {
        return [
          'Приветствуем вас в приложении AR-Nav!',
          'Для начала вам необходимо выбрать цель. Цель - это объект, который вы хотите найти.',
        ];
      },

      /**
       * Возвращает привественный текст для вернувшегося пользователя.
       * (второй и более раз запустил приложение)
       *
       * @returns {string[]}
       */
      getDialogTextForReturnedUser() {
        return [
          'С возвращением в приложение AR-Nav! Давайте выберем цель.',
        ];
      },

      onMarkerFound(marker) {
        this.marker.visible = true;
      },

      onMarkerLost(marker) {
        this.marker.visible = false;
      },

      onMarkerScanned(markerId) {
        if (this.markers.hasOwnProperty(markerId)) {
          this.marker.current = this.markers[markerId];

          if (!this.goal.current || !this.goal.path) {
            // Show select goal dialog if goal is not selected yet
            this.showSelectGoalDialog = true;
          } else {
            this.showDirections();
          }
        }
      },

      onDecode(result) {
        console.log('QR scan result: ' + result);
        this.onMarkerScanned(+result);
      },

      async onInit(promise) {
        try {
          await promise;
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
    #changeGoalBtn {
        left: 5px;
    }

    #placeObjectInfoBtn {
        left: 5px;
    }

    #uiElements {
        z-index: 3;
        position: absolute;
        bottom: 50px;
    }

    #ar-js-video {
        z-index: 0 !important;
    }

    a-scene {
        z-index: 1 !important;
    }
</style>