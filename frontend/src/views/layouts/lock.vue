<template>
  <v-app>
    <v-card
      style="height:100vh;background-color:#ffc107;"
      class="d-flex flex-column align-center justify-center"
    >
      <h2 class="my-6 font-weight-medium text-size-medium text-uppercase">
        <b><span class="ml-3">Sizda muddati o'tgan dokumentlar mavjud.</span></b><br>
        <span class="ml-3">Iltimos muddati o'tgan dokumentlaringizni yopib qo'ying!</span>
      </h2>
      <!-- <p
        class="mb-12 title text-uppercase text-center"
      >Kechirasiz! siz qidirayotgan sahifa mavjud emas.</p>-->
      <v-btn to="/" class="mb-12" depressed color="primary">Muddati o'tgan dokumentlarni ko'rish</v-btn>
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
