<template>
    <v-layout row justify-center>
        <v-dialog id="pollDialog" v-model="show" max-width="500px" scrollable persistent>
            <v-card>
                <v-card-title class="headline grey lighten-2">
                    Помогите нам сделать приложение лучше
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-form ref="form">
                                <v-flex xs12>
                                    <v-radio-group v-model="form.sex" label="Пол">
                                        <v-radio color="primary" label="Мужской" value="man"></v-radio>
                                        <v-radio color="primary" label="Женский" value="woman"></v-radio>
                                    </v-radio-group>
                                </v-flex>
                                <v-flex xs12>
                                    <v-slider v-model="form.age"
                                            label="Возраст"
                                            min="5"
                                            max="100"
                                            thumb-label="always"
                                    ></v-slider>
                                </v-flex>
                                <v-flex xs12>
                                    <v-select
                                            v-model="form.personType"
                                            :items="form.personTypes"
                                            label="Кто вы?"
                                    ></v-select>
                                </v-flex>
                                <v-flex xs12>
                                        <p class="v-label theme--light">Оцените приложение</p>
                                        <v-rating v-model="form.rating"></v-rating>
                                </v-flex>
                                <v-flex xs12>
                                    <p class="v-label theme--light">Какой вид навигации вам удобнее?</p>
                                    <v-checkbox color="primary" v-model="form.preferredNavType" label="Текстовые подсказки" value="text"></v-checkbox>
                                    <v-checkbox color="primary" v-model="form.preferredNavType" label="Голосовые подсказки" value="voice"></v-checkbox>
                                    <v-checkbox color="primary" v-model="form.preferredNavType" label="Виртуальная стрелка" value="3d-object"></v-checkbox>
                                </v-flex>
                            </v-form>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="blue darken-1" flat @click="submit">
                        <v-icon>$vuetify.icons.ok</v-icon>
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click="show = false">
                        <v-icon>$vuetify.icons.close</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
  export default {
    name: "Poll",
    props: [
      'value',
      'goalId',
    ],
    data() {
      return {
        form: {
          goalId: this.$props['goalId'],
          sex: 'man',
          age: 18,
          personType: 'Студент',
          rating: 1,
          preferredNavType: [],
          personTypes: [
            'Студент',
            'Преподаватель',
            'Абитуриент',
            'Родитель',
            'Прохожий',
          ]
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
    methods: {
      submit() {
        const pollData = {
          goalId: this.$props['goalId'],
          sex: this.form.sex,
          age: this.form.age,
          personType: this.getExportPersonType(),
          rating: this.form.rating,
          preferredNavType: this.form.preferredNavType,
        };

        this.$emit('poll-submit', pollData);
        this.show = false;
      },
      getExportPersonType() {
        switch (this.form.personType) {
          case 'Студент':
            return 'student';
          case 'Преподаватель':
            return 'teacher';
          case 'Абитуриент':
            return 'applicant';
          case 'Родитель':
            return 'parent';
          case 'Прохожий':
            return 'stranger';
        }
      },
    },
  }
</script>

<style scoped>

</style>