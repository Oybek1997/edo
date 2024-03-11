<template>
  <v-app>
    <v-card class="error-page">
      <div class="content">
        <h1 class="animate-404">Ushbu sahifa jarayonda..</h1>
        <h2 class="animate-oops">
          Tez orada ishga tushadi! <span></span>
        </h2>
      </div>
    </v-card>
  </v-app>
</template>

<script>
export default {
  methods: {
    goBack() {
      this.$router.go(-1);
    }
  }
};
</script>


<style scoped>
.error-page {
  height: 100vh;
  background-color: #ffffff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.content h1 {
  font-size: 112px;
  color: rgb(34, 2, 2);
  font-weight: medium;
  opacity: 0;
  transform: translateY(-20px);
  animation: fadeInUp 0.8s forwards;
}

.content h2 {
  margin: 48px 0;
  font-size: 40px;
  font-weight: medium;
  text-transform: uppercase;
  color: rgb(0, 0, 0);
  opacity: 0;
  transform: translateY(-20px);
  animation: fadeInUp 0.8s forwards 0.5s;
}

.content h2 span {
  margin-left: 12px;
}

.go-back {
  position: absolute;
  top: 20px;
  left: 20px;
}

.home-page-btn {
  opacity: 0;
  animation: fadeIn 1s forwards 1s;
}

.home-page-btn:hover {
  transform: scale(1.05);
  transition: transform 0.3s ease;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  to {
    opacity: 1;
  }
}
</style>
