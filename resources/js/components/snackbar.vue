<template>
    <v-snackbar
      v-model="$store.state.snackbar.show"
      :bottom="y === 'bottom'"
      :left="x === 'left'"
      :multi-line="mode === 'multi-line'"
      :right="x === 'right'"
      :timeout="$store.state.snackbar.success ? 2000 : timeout"
      :top="y === 'top'"
      :vertical="mode === 'vertical'"
      class="white--text"
      :color="$store.state.snackbar.success ? 'green' : 'pink'"
    >
      {{ $store.state.snackbar.message }}
      <v-btn
        color="white"
        flat
        @click="$store.state.snackbar.show = false"
      >
        Close
      </v-btn>
    </v-snackbar>
</template>

<script>
export default {
    props: {
      success: {
        type: Boolean
      }
    },

    data() {
        return {
            y: 'top',
            x: null,
            mode: '',
            timeout: 5000,
        }
    }
}
</script>

