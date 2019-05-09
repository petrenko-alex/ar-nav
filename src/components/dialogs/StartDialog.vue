<template>
    <v-dialog id="dialog" value="true" v-model="show" scrollable max-width="500px" v-if="texts.length">
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
                    <v-icon color="accent">$vuetify.icons.leftAngle</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn flat @click.stop="show = false" :disabled="closeButtonDisabled" v-show="!closeButtonDisabled">
                    <v-icon color="accent">$vuetify.icons.ok</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn flat @click="nextText" :disabled="nextButtonDisabled" v-if="!oneStepDialog">
                    <v-icon color="accent">$vuetify.icons.rightAngle</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import {stackDialogMixin} from "@/mixins/stackDialogMixin";

    export default {
    name: "StartDialog",
    props: [
      'value',
    ],
    mixins: [stackDialogMixin],
    data() {
      return {
        // Data
        title: 'AR-Nav',
        texts: [
          'Приветствуем вас в приложении AR-Nav!',
          'Приветствуем вас в приложении AR-Nav 2',
          'Приветствуем вас в приложении AR-Nav 3',
        ],
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
    },
  }
</script>

<style scoped>

</style>