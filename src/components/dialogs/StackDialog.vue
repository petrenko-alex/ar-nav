<template>
    <v-dialog id="stackDialog" value="true" v-model="show" scrollable max-width="500px" v-if="texts.length">
        <v-card>
            <v-card-title class="headline grey lighten-2" v-if="title || !oneStepDialog">
                {{ title }}
                <v-spacer></v-spacer>
                <v-chip outline v-if="!oneStepDialog">
                    {{ counter }}
                    <v-icon right>$vuetify.icons.stack</v-icon>
                </v-chip>
            </v-card-title>
            <v-card-text>
                {{ currentText }}
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn flat @click="prevText" :disabled="prevButtonDisabled" v-if="!oneStepDialog">
                    <v-icon color="primary">$vuetify.icons.leftAngle</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn flat @click.stop="dialogRead" :disabled="closeButtonDisabled" v-show="!closeButtonDisabled">
                    <v-icon color="primary">$vuetify.icons.ok</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn flat @click="nextText" :disabled="nextButtonDisabled" v-if="!oneStepDialog">
                    <v-icon color="primary">$vuetify.icons.rightAngle</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
  export default {
    name: "StackDialog",
    props: [
      'value',
      'texts',
      'title',
    ],
    data() {
      return {
        // State
        currentStep: 0,
        currentText: '',

        // Controls
        nextButtonDisabled: true,
        prevButtonDisabled: true,
        closeButtonDisabled: true,
      }
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
      },
      counter() {
        return (this.currentStep + 1) + '/' + this.steps;
      },
      oneStepDialog() {
        return this.steps === 1;
      },
    },
    created() {
      this.init();
      this.controlButtons();
    },
    methods: {
      init() {
        this.currentText = this.texts[this.currentStep];
      },
      dialogRead() {
        this.$emit('dialog-read');
        this.show = false;
      },
      nextText() {
        this.nextStep();
        this.setCurrentText(this.currentStep);
        this.controlButtons();
      },
      prevText() {
        this.prevStep();
        this.setCurrentText(this.currentStep);
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
        this.controlCloseButton();
      },
      controlNextButton() {
        this.nextButtonDisabled = this.currentStep === (this.steps - 1);
      },
      controlPrevButton() {
        this.prevButtonDisabled = this.currentStep === 0;
      },
      controlCloseButton() {
        this.closeButtonDisabled = this.currentStep !== (this.steps - 1)
      }
    },
  }
</script>