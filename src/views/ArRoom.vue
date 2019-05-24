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
                              :rotation="directionsObjectRotation"
                              :scale="goal.directions.object.scale"
                              v-if="goal.directions.object.rotateDegrees > 0"
                ></a-gltf-model>
                <a-text :value="goal.directions.text.value"
                        :color="goal.directions.color"
                        :rotation="goal.directions.text.rotation"
                        :position="goal.directions.text.position"
                        :scale="goal.directions.text.scale"
                        v-if="goal.directions.text.value"
                        font="/aframe_font/arial.fnt"
                        width="2"
                        wrap-count="25"
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
            <v-btn id="sayDirections" fab color="primary" @click="sayDirections" v-show="goal.directions.text.value">
                <v-icon>$vuetify.icons.audio</v-icon>
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
  import FuzzyLogic from 'es6-fuzz';
  import Triangle from 'es6-fuzz/lib/curve/triangle';
  import Voca from 'voca';

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

        goals: {},
        goal: {
          current: null,
          path: null,
          directions: {
            text: {
              value: '',
              goalReached: 'Вы достигли цели!',
              scale: '1 1 0',
              position: '-0.5 0 1',
              rotation: '270 0 0',
            },
            object: {
              src: '/gltf/arrow/scene.gltf',
              scale: '0.5 0.5 0.5',
              position: '0 0.5 0',
              initialRotation: '90 90 90',
              rotateDegrees: 0,
            },
            color: '#2196F3',
          },
        },

        voice: {
          utterance: null,
        },
        fuzzyLogic: null,

        // Welcome dialog
        welcomeDialogTitle: 'AR-Nav',
        welcomeDialogTexts: [],
        isWelcomeDialogRead: false,

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
      directionsObjectRotation() {
        let initialRotation = this.goal.directions.object.initialRotation;
        initialRotation = initialRotation.split(' ');
        initialRotation[0] = (initialRotation[0] - this.goal.directions.object.rotateDegrees);
        return initialRotation.join(' ');
      },
      isGoalSelected() {
        return this.goal.current && this.goal.path;
      },
    },
    created() {
      this.initFuzzy();
      this.initVoiceApi();
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

      initFuzzy() {
        this.fuzzyLogic = new FuzzyLogic();
        this.fuzzyLogic
          .init('повернитесь немного правее', new Triangle(0, 22.5, 45))
          .or('повернитесь направо', new Triangle(45, 90, 135))
          .or('развернитесь направо', new Triangle(135, 157.5, 180))
          .or('развернитесь налево', new Triangle(180, 202.5, 225))
          .or('повернитесь налево', new Triangle(225, 270, 315))
          .or('повернитесь немного левее', new Triangle(315, 337.5, 360));
      },

      initVoiceApi() {
        this.voice.utterance = new window.SpeechSynthesisUtterance();
        this.voice.utterance.lang = 'ru-RU';
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
        this.goal.directions.text.value = this.goal.directions.text.goalReached;
        this.sayDirections();

        // TODO: Show questionary
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
        // Parse directions info
        const directionsRegex = /(\[\d+\])(\(.*\))?/;
        const parseResult = directionsInfo.match(directionsRegex);

        let degrees = parseResult[1];
        if (degrees) {
          degrees = Voca.trimLeft(degrees, '[');
          degrees = Voca.trimRight(degrees, ']');

          if (+degrees < 0) degrees = 0;
          if (+degrees > 360) degrees = 360;

          // Set rotate degrees
          this.goal.directions.object.rotateDegrees = degrees;

          // Get directions text using FuzzyLogic
          let fuzzyResult = this.fuzzyLogic.defuzzify(degrees);
          if (fuzzyResult) {
            this.goal.directions.text.value = Voca.capitalize(fuzzyResult.defuzzified);
          }
        }

        // Add hint for directions text
        let directionsText = parseResult[2];
        if (directionsText) {
          directionsText = Voca.trimLeft(directionsText, '(');
          directionsText = Voca.trimRight(directionsText, ')');
          directionsText = Voca.capitalize(directionsText);

          if (this.goal.directions.text.value) {
            this.goal.directions.text.value += '.\n'
          }
          this.goal.directions.text.value += directionsText;
        }

        this.sayDirections();
      },

      sayDirections() {
        this.sayText(this.goal.directions.text.value);
      },

      /**
       * Обработчик события "Приветственный диалог прочитан"
       */
      welcomeDialogRead() {
        this.isWelcomeDialogRead = true;
        if (!this.isGoalSelected && this.marker.visible) {
          // Show select goal dialog if goal is not selected yet
          this.showSelectGoalDialog = true;
        }

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
        } else {
          this.isWelcomeDialogRead = true;
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

          if (this.isWelcomeDialogRead && !this.isGoalSelected) {
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
      },

      sayText(text) {
        if (text) {
          try {
            this.voice.utterance.text = text;
            window.speechSynthesis.speak(this.voice.utterance);
          } catch (ex) {
            console.log('speechSynthesis not available', ex);
          }
        }
      },
    }
  }
</script>

<style>
    #testVoice {
        z-index: 3;
    }

    #changeGoalBtn {
        position: absolute;
        right: 5px;
    }

    #placeObjectInfoBtn {
        left: 5px;
    }

    #sayDirections {
        left: 5px;
    }

    #uiElements {
        width: 100%;
        z-index: 3;
        position: absolute;
        bottom: 75px;
    }

    #ar-js-video {
        z-index: 0 !important;
    }

    a-scene {
        z-index: 1 !important;
    }
</style>