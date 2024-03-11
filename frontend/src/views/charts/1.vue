<template>
  <div>
    <v-card class="heightFull">
      <apexchart
        height="350"
        type="line"
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
      series: [
        {
          colors: "yellow",
          name: "Target",
          type: "column",
          data: [95.5, 96, 97, 94, 94, 93, 94, 95.5, 96, 97, 92, 91],
        },
        {
          colors: "red",
          name: "DRR",
          type: "line",
          data: [95, 95, 95, 95, 95, 95, 95, 95, 95, 95, 95, 95],
        },
      ],
      chartOptions: {
        chart: {
          height: 350,
          type: "colum",
        },
        stroke: {
          width: [0, 4],
        },
        title: {
          text: "Сифат кўрсаткич бўйича маълумот",
        },
        dataLabels: {
          enabled: true,
          enabledOnSeries: [1],
        },
        colors: ["#FFA726","#78909C"],
        labels: [
          "01 Jan 2001",
          "02 Jan 2001",
          "03 Jan 2001",
          "04 Jan 2001",
          "05 Jan 2001",
          "06 Jan 2001",
          "07 Jan 2001",
          "08 Jan 2001",
          "09 Jan 2001",
          "10 Jan 2001",
          "11 Jan 2001",
          "12 Jan 2001",
        ],
        xaxis: {
          type: "datetime",
        },
        yaxis: [
          {
            
            title: {
              text: "DRR кўрчаткичи",
            },
          },
          {
            opposite: true,
            title: {
              text: "DRR кўрчаткичи",
            },
          },
        ],
      },
    };
  },
};
</script>

<style scoped>
/* Add your component's styles here */
</style>
