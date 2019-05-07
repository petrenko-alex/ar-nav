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
                <v-btn color="primary" flat @click="prevText">
                    <v-icon class="text-main-color">fas fa-angle-left</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" flat @click.stop="show = false">
                    <v-icon class="text-main-color">fas fa-times</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" flat @click="nextText">
                    <v-icon class="text-main-color">fas fa-angle-right</v-icon>
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
        currentStep: 0,
        currentTitle: '',
        currentText: '',

        titles: [
          'AR-Nav',
        ],
        texts: [
          'Приветствуем вас в приложении AR-Nav!',
          'Приветствуем вас в приложении AR-Nav 2',
          'Приветствуем вас в приложении AR-Nav 3',
        ],
      }
    },
    created() {
        this.currentTitle = this.titles[this.currentStep];
        this.currentText = this.texts[this.currentStep]
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
      nextText() {
        this.nextStep();
        this.setCurrentText(this.currentStep)
      },
      prevText() {
        this.prevStep();
        this.setCurrentText(this.currentStep)
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
    },
  }
</script>

<style scoped>

</style>