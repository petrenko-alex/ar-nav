<template>
    <v-dialog id="selectGoalDialog" v-model="show" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title class="headline grey lighten-2">Выбор цели</v-card-title>
            <v-card-text >
                <v-container text-xs-center>
                    <v-radio-group v-model="goalId">
                        <v-radio v-for="goal in goalsObj"
                                 :key="goal.id"
                                 :label="goal.title"
                                 :value="goal.id"
                                 color="primary"
                                 :disabled="goal.id === currentGoalObj.id"
                                 :off-icon="goal.id === currentGoalObj.id ? $vuetify.icons.mapMarker : $vuetify.icons.radioOff"
                        >
                        </v-radio>
                    </v-radio-group>
                </v-container>
            </v-card-text>
            <v-divider></v-divider>
        </v-card>
    </v-dialog>
</template>

<script>
  export default {
    name: "SelectGoalDialog",
    props: [
      'value',
      'goals',
      'currentGoal',
    ],
    data() {
      return {
        goalId: null,
        goalsObj: null,
        currentGoalObj: null,
      }
    },
    mounted() {
      this.goalId = null;
      this.goalsObj = this.$props['goals'];
      this.currentGoalObj = this.$props['currentGoal'];
    },
    beforeUpdate() {
      const currentGoalId = this.currentGoalObj ? this.currentGoalObj['id'] : 0;

      // Set disabled prop for goals
      for (let goalId in this.goalsObj) {
        if (this.goalsObj.hasOwnProperty(goalId)) {
          this.goalsObj[goalId].disabled = false;
          if (+goalId === +currentGoalId) {
            this.goalsObj[goalId].disabled = true;
          }
        }
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
    watch: {
      goalId(newValue) {
        if (newValue) {
          this.$emit('goal-selected', newValue);
        }
      }
    },
  }
</script>