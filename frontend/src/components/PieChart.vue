<template>
  <div>
    <h2 class="text-center mb-6">{{ $t('document_chart') }}</h2>
    <canvas ref="canvas"></canvas>
  </div>
</template>
<script>
const axios = require("axios").default;
import { Pie } from "vue-chartjs";

export default {
  name: "PieChart",
  extends: Pie,
  props: ["data", "options"],
  mounted() {
    this.renderChart(this.data, this.options);
  }
};
</script>
