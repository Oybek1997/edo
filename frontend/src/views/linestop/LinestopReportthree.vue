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
const axios = require("axios").default;
export default {
  components: {
    apexchart: VueApexCharts,
  },
  data() {
    return {
      series: [],
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
          text: 'Ежедневный отчет'
        },
      },
    };
  },
  methods: {
    getList() {
      axios
        .post(this.$store.state.backend_url + "api/linestops/dailyreport", {
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          this.processData(response.data);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    processData(data) {
      const transformedSeries = data.reduce((acc, item) => {
        let seriesIndex = acc.findIndex(s => s.name === item.linename);
        if (seriesIndex === -1) {
          acc.push({
            name: item.linename,
            data: [{ x: item.date, y: item.ticketcount }]
          });
        } else {
          acc[seriesIndex].data.push({ x: item.date, y: item.ticketcount });
        }
        return acc;
      }, []);
      this.series = transformedSeries;
    }
  },
  mounted() {
    this.getList();
  },
};
</script>
<style scoped>
</style>
