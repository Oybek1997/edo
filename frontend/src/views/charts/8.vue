<template>
  <div>
    <v-card class="heightFull">
      <apexchart
        type="polarArea"
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
      series: [14, 23, 21, 17, 15, 10, 12, 17, 21],
          chartOptions: {
            chart: {
              type: 'polarArea',
            },
            stroke: {
              colors: ['#fff']
            },
            fill: {
              opacity: 0.8
            },
            responsive: [{
              breakpoint: 480,
              options: {
                chart: {
                  width: 200
                },
                legend: {
                  position: 'bottom'
                }
              }
            }]
          },
      ///////////////////
    };
  },
};
</script>

<style scoped>
/* Add your component's styles here */
</style>
