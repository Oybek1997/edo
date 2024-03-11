<template>
  <div>
    <v-card class="heightFull">
      <v-card-title primary-title>
        <h3>Dashboard#</h3>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12">
            <v-card>
              <v-card-title primary-title>
                title
              </v-card-title>
              <v-card-text>
                <apexchart
                v-if="chart0.visible"
                  
                  type="bar"
                  :options="chart0.options"
                  :series="chart0.series"
                ></apexchart>
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
      charts: {
       
        
          
      },
    };
  },
  computed: {
    chart0() {
      return this.charts;
    },
  },
  methods: {
    getChart1() {
      axios
        .post(this.$store.state.backend_url + "api/kpi/charts1/",{
          number:5
        })
        .then((response) => {
          console.log(response)
          this.charts = {
            visible:true,

            options: {
              plotOptions: {
              bar: {
                barHeight: '100%',
           
                horizontal: true,
                dataLabels: {
                  position: 'bottom'
                },
              }
            },
            dataLabels: {
              // enabled: true,
              // textAnchor: 'start',
              style: {
                colors: ['#fff']
              },
              
              offsetX: 0,
              dropShadow: {
                enabled: true
              }
            },
            stroke: {
              width: 1,
              colors: ['#fff']
            },
              chart: {
                id: "vuechart-example",
              },
              xaxis: {
                categories: response.data.map((v)=>v.department_code),
              },
            },
            series: [
              {
                name: "series-1",
                data: response.data.map((v)=>v.head_count),
              },
            ],
          };
          // window.dispatchEvent(new Event("resize"));
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getChart(number) {
      axios
        .post(this.$store.state.backend_url + "api/charts/" + number)
        .then((response) => {
          console.log(response)
          this.charts = {
            visible:true,
            options: {
              chart: {
                id: "vuechart-example",
              },
              xaxis: {
                categories: Object.keys(response.data),
              },
            },
            series: [
              {
                name: "series-1",
                data: Object.values(response.data),
              },
            ],
          };
          window.dispatchEvent(new Event("resize"));
        })
        .catch((error) => {
          console.log(error);
        });
    },
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
