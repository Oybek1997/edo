<template>
  <div>
    <v-card class="heightFull">
      <apexchart
        type="heatmap"
        height="350"
        :options="chartOptions"
        :series="series"
      ></apexchart>
    </v-card>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";

export default {
  components: {
    apexchart: VueApexCharts,
  },
  data() {
    return {
      series: this.generateMetrics(9, 18),
      chartOptions: {
        chart: {
          height: 350,
          type: 'heatmap',
        },
        dataLabels: {
          enabled: true,
          style: {
                colors: ['indigo']
              }
        },
        colors: ["#E53935"],
        title: {
          text: 'HeatMap Chart (Single color)'
        },
      },
    };
  },
  methods: {
    generateMetrics(numMetrics, dataPoints) {
      const metrics = [];
      for (let i = 1; i <= numMetrics; i++) {
        metrics.push({
          name: `Цех${i}`,
          data: this.generateData(dataPoints, { min: 0, max: 90 }),
        });
      }
      return metrics;
    },
    generateData(count, range) {
      const data = [];
      for (let i = 0; i < count; i++) {
        data.push({
          x: `${i + 1}-кун`,
          y: Math.floor(Math.random() * (range.max - range.min + 1)) + range.min,
        });
      }
      return data;
    },
  },
};
</script>


<style scoped>
/* Add your component's styles here */
</style>
