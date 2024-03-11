<template>
  <v-app>
    <v-card
      style="height:100vh;background-color:#ffc107;"
      class="d-flex flex-column align-center justify-center"
    >
      <v-btn class="text-normal go-back ma-4" @click="goBack" small>
        <v-icon left>mdi-undo-variant</v-icon>Back
      </v-btn>
      <h1 class="font-weight-medium white--text text-size-large">404</h1>
      <h2 class="my-12 font-weight-medium text-size-medium text-uppercase">
        OOPS!
        <span class="ml-3">page not found</span>
      </h2>
      <!-- <p
        class="mb-12 title text-uppercase text-center"
      >Kechirasiz! siz qidirayotgan sahifa mavjud emas.</p>-->
      <v-btn to="/" class="mb-12" depressed color="primary">Go to the home page</v-btn>
    </v-card>
  </v-app>
</template>
<script>
export default {
  data: () => ({}),
  methods: {
    goBack() {
      return this.$router.go(-1);
    }
  }
};
</script>
<style scoped>
.text-size-large {
  font-size: 112px;
}
.text-size-medium {
  font-size: 40px;
}
.go-back {
  position: absolute;
  top: 0;
  left: 0;
}
</style>
