<template>
    <v-dialog id="dialog" value="true" v-model="show" scrollable max-width="500px">
        <v-card>
            <v-card-title class="headline grey lighten-2" primary-title>
                {{ currentTitle }}
            </v-card-title>
            <v-card-text>
                {{ currentText }}
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn flat @click="prevText" :disabled="prevButtonDisabled">
                    <v-icon color="accent">fas fa-angle-left</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn flat @click.stop="show = false" :disabled="closeButtonDisabled">
                    <v-icon color="accent">fas fa-times</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn flat @click="nextText" :disabled="nextButtonDisabled">
                    <v-icon color="accent">fas fa-angle-right</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
  export default {
    name: "StartDialog",
    props: [
      'value',
    ],
    data() {
      return {
        // State
        currentStep: 0,
        currentTitle: '',
        currentText: '',

        // Data
        titles: [
          'AR-Nav',
        ],
        texts: [
          'Приветствуем вас в приложении AR-Nav!',
          'Приветствуем вас в приложении AR-Nav 2',
          'Приветствуем вас в приложении AR-Nav 3',
        ],

        // Controls
        nextButtonDisabled: true,
        prevButtonDisabled: true,
        closeButtonDisabled: false,
      }
    },
    created() {
      this.init();
      this.controlButtons();
    },
    computed: {
      show: {
        get() {
          return this.value;
        },
        set(value) {
          this.$emit('input', value);
        }
      },
      steps() {
        return this.texts.length;
      }
    },
    methods: {
      init() {
        this.currentTitle = this.titles[this.currentStep];
        this.currentText = this.texts[this.currentStep];
      },
      nextText() {
        this.nextStep();
        this.setCurrentText(this.currentStep);
        this.controlButtons();
      },
      prevText() {
        this.prevStep();
        this.setCurrentText(this.currentStep)
        this.controlButtons();
      },
      setCurrentText(stepNumber) {
        const text = this.texts[stepNumber];
        if (text) {
          this.currentText = text;
        }
      },
      nextStep() {
        this.currentStep++;
        if (this.currentStep >= this.steps) {
          this.currentStep = (this.steps - 1);
        }
      },
      prevStep() {
        this.currentStep--;
        if (this.currentStep < 0) {
          this.currentStep = 0;
        }
      },
      controlButtons() {
        this.controlNextButton();
        this.controlPrevButton();
      },
      controlNextButton() {
        this.nextButtonDisabled = this.currentStep === (this.steps - 1);
      },
      controlPrevButton() {
        this.prevButtonDisabled = this.currentStep === 0;
      },
    },
  }
</script>

<style scoped>

</style>