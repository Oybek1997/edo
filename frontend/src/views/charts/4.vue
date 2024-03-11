<template>
  <div>
    <v-card class="heightFull">
      <apexchart
        type="rangeBar"
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
      series: [
        {
          data: [
            {
              x: 'Team A',
              y: [10, 40]
            },
            {
              x: 'Team B',
              y: [20, 60]
            },
            {
              x: 'Team C',
              y: [30, 70]
            },
            {
              x: 'Team D',
              y: [40, 80]
            }
          ]
        }
      ],
      chartOptions: {
        chart: {
          height: 350,
          type: 'rangeBar'
        },
        plotOptions: {
          bar: {
            horizontal: true,
            barHeight: '80%'
          }
        },
        xaxis: {
          type: 'datetime'
        },
        title: {
          text: 'Range Bar Chart',
          align: 'center'
        }
      }

      ///////////////////
    };
  },
};
</script>

<style scoped>
/* Add your component's styles here */
</style>
