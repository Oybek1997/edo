<template>
  <div>
    <v-card class="heightFull">
      <!-- type="pie" -->
      <apexchart
        type="donut"
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
      series: [25, 15, 44, 55, 41, 17],
          chartOptions: {
            chart: {
              width: '100%',
              type: 'pie',
            },
            labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            theme: {
              monochrome: {
                enabled: true
              }
            },
            plotOptions: {
              pie: {
                dataLabels: {
                  offset: -5
                }
              }
            },
            title: {
              text: "Monochrome Pie"
            },
            dataLabels: {
              formatter(val, opts) {
                const name = opts.w.globals.labels[opts.seriesIndex]
                return [name, val.toFixed(1) + '%']
              }
            },
            legend: {
              show: false
            }
          },
      ///////////////////
    };
  },
};
</script>

<style scoped>
/* Add your component's styles here */
</style>
