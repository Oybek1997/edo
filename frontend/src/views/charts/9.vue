<template>
  <div>
    <v-card class="heightFull">
      <apexchart
        type="radialBar"
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
      items: [{}],
      ///////////////////
      series: [70],
          chartOptions: {
            chart: {
              height: 350,
              type: 'radialBar',
            },
            plotOptions: {
              radialBar: {
                hollow: {
                  size: '70%',
                }
              },
            },
            labels: ['Cricket'],
          },
      ///////////////////
    };
  },
};
</script>

<style scoped>
/* Add your component's styles here */
</style>
