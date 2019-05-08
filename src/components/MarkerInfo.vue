<template>
    <v-dialog id="modal" v-model="show" scrollable max-width="500px">
        <v-card>
            <v-card-title class="headline grey lighten-2" primary-title v-if="!!cardTitle">
                {{ cardTitle }}
            </v-card-title>
            <v-card-text>
               <slot></slot>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" flat @click.stop="show = false">
                    <v-icon color="accent">fas fa-times</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
  export default {
    name: "MarkerInfo",
    props: [
      'card-title',
      'value',
    ],
    computed: {
      show: {
        get() {
         return this.value;
        },
        set(value) {
          this.$emit('input', value);
        }
      }
    }
  }
</script>