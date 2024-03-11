<template>
  <div>
    <h2 class="text-center mb-6">{{ $t('document_chart') }}</h2>
    <canvas ref="canvas"></canvas>
  </div>
</template>
<script>
import { Doughnut } from "vue-chartjs";
export default {
  name: "Doughnut",
  extends: Doughnut,
  props: ["data", "options"],
  mounted() {
    this.renderChart(this.data, this.options);
  }
};
</script>
