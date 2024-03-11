<template>
  <div>
    <v-card class="heightFull">
      <v-card-title primary-title>
        <h3>Dashboard#</h3>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="6">
            <v-card>
              <v-card-title primary-title>
                title
              </v-card-title>
              <v-card-text>
                <apexchart type="bar" :options="chartOptions" height="350" :series="series"></apexchart>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>

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
      items: [{}],
      ///////////////////
      series: [{
        data: []
      }],
      chartOptions: {
        chart: {
          type: 'bar',
          height: 380
        },
        plotOptions: {
          bar: {
            barHeight: '100%',
            distributed: true,
            // horizontal: true,
            dataLabels: {
              position: 'center'
            },
          }
        },
        colors: ['#33b2df', '#546E7A', '#d4526e', '#13d8aa', '#A5978B', '#2b908f', '#f9a3a4', '#90ee7e',
          '#f48024', '#69d2e7'
        ],
        dataLabels: {
          enabled: true,
          textAnchor: 'start',
          style: {
            colors: ['#fff']
          },
          // formatter: function (val, opt) {
          //   return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
          // },
          offsetX: 0,
          dropShadow: {
            enabled: true
          }
        },
        stroke: {
          width: 1,
          colors: ['#fff']
        },
        // xaxis: {
        //   categories: ['South Korea', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan',
        //     'United States', 'China', 'India'
        //   ],
        // },
        yaxis: {
          labels: {
            show: true
          }
        },
        title: {
          text: 'Custom DataLabels',
          align: 'center',
          floating: true
        },
        subtitle: {
          text: 'Category Names as DataLabels inside bars',
          align: 'center',
        },
        // tooltip: {
        //   theme: 'dark',
        //   x: {
        //     show: false
        //   },
        //   y: {
        //     title: {
        //       formatter: function () {
        //         return ''
        //       }
        //     }
        //   }
        // }
      },

      ///////////////////
    };
  },
  computed: {
    chart1() {
      return this.charts1;
    },
  },
  methods: {
    getChart1() {
      axios
        .post(this.$store.state.backend_url + "api/kpi/charts1/", {
          number: 5
        })
        .then((response) => {
          this.series = [
            {
              name: "series-1",
              data: response.data.map((v) => v.head_count)
            },
          ],
            this.chartOptions.xaxis = {
              categories: response.data.map((v) => v.head_count)
            }
          console.log(this.chartOptions);
        })
        .catch((error) => {
          console.log(error);
        });
    }
  },
  mounted() {
    // this.getChart(0);
    this.getChart1();
  },

};
</script>

<style scoped>
/* Add your component's styles here */
</style>
