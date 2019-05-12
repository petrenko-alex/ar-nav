<template>
    <v-dialog id="selectGoalDialog" v-model="show" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title class="headline grey lighten-2">Выбор цели</v-card-title>
            <v-card-text >
                <v-container text-xs-center>
                    <v-radio-group v-model="goalId">
                        <v-radio v-for="goal in goals"
                                 :key="goal.id"
                                 :label="goal.title"
                                 :value="goal.id"
                                 color="primary"
                        ></v-radio>
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
    ],
    data() {
      return {
        goalId: null,
      }
    },
    mounted() {
      this.goalId = null;
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