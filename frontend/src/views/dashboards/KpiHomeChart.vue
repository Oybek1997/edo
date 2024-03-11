<template>
    <div>
      <v-card class="heightFull">
        <v-card-title primary-title>
          <h3>Dashboard</h3>
          <v-spacer></v-spacer>
  
          <span style="max-width: 100px">
            <v-autocomplete
              label="Yil"
              v-model="year"
              :items="years"
              hide-details
              @change="getChartAll"
              dense
              outlined
              item-value="value"
              item-text="text"
              class="mx-1"
            ></v-autocomplete>
          </span>
          <span style="max-width: 130px">
            <v-autocomplete
              label="Chorak"
              v-model="quarter"
              :items="quarters"
              hide-details
              dense
              outlined
              @change="getChartAll"
              item-value="value"
              item-text="text"
              class="mx-1"
            ></v-autocomplete>
          </span>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="6">
              <v-card>
                <v-card-title primary-title
                  >Eng ko`p foiz olgan bo`limlar(TOP 10)</v-card-title
                >
                <v-card-text>
                  <apexchart
                    v-if="chart0.visible"
                    height="300"
                    type="bar"
                    :options="chart0.options"
                    :series="chart0.series"
                  ></apexchart>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="6">
              <v-card>
                <v-card-title primary-title
                  >Eng kam foiz olgan bo`limlar(TOP 10)</v-card-title
                >
                <v-card-text>
                  <apexchart
                    v-if="chart1.visible"
                    height="300"
                    type="bar"
                    :options="chart1.options"
                    :series="chart1.series"
                  ></apexchart>
                </v-card-text>
              </v-card>
            </v-col>
            <!-- <v-col cols="6">
              <v-card>
                <v-card-title primary-title>Korxona xodimlari soni</v-card-title>
                <v-card-text>
                  <apexchart
                    v-if="chart2.visible"
                    height="350"
                    type="pie"
                    :options="chart2.options"
                    :series="chart2.series"
                  ></apexchart>
                </v-card-text>
              </v-card>
            </v-col>          -->
          </v-row>
        </v-card-text>
      </v-card>
    </div>
  </template>
  
  <script>
  import VueApexCharts from "vue-apexcharts";
  const axios = require("axios").default;
  const moment = require("moment");
  
  export default {
    components: {
      apexchart: VueApexCharts,
    },
    data() {
      return {
        year:  2023,
        quarter: 1,
        years: [2023,2024],
        // years: [
        //   {
        //     text: moment().subtract(1, "years").format("YYYY"),
        //     value: moment().subtract(1, "years").format("YYYY"),
        //   },
        //   { text: moment().format("YYYY"), value: moment().format("YYYY") },
        // ],
        quarters: [
          { text: this.$t("1-chorak"), value: 1 },
          { text: this.$t("2-chorak"), value: 2 },
          { text: this.$t("3-chorak"), value: 3 },
          { text: this.$t("4-chorak"), value: 4 },
        ],
        itemBirthday: [],
        countBirthday: 0,
        charts0: {
          visible: false,
        },
        charts1: {
          visible: false,
        },
        charts2: {
          visible: false,
        },
      };
    },
    computed: {
      chart0() {
        return this.charts0;
      },
      chart1() {
        return this.charts1;
      },
      chart2() {
        return this.charts2;
      },
    },
    methods: {
      getChart(number) {
        axios
          .post(this.$store.state.backend_url + "api/kpi/charts/", {
            number: number,
            year: this.year,
            quarter: this.quarter,
          })
          .then((response) => {
            // console.log(response);
            if (number == 0) {
              this.charts0 = {
                visible: true,
                options: {
                  chart: {
                    id: "vuechart-example",
                  },
                  plotOptions: {
                    bar: {
                      barHeight: "100%",
                      distributed: true,
                      // horizontal: true,
                      dataLabels: {
                        position: "center",
                      },
                    },
                  },
                  xaxis: {
                    categories: response.data.map((v) => v.department_code),
                  },
                },
                series: [
                  {
                    name: "Mukofot",
                    data: response.data.map((v) => v.mukofot),
                  },
                ],
              };
            } else if (number == 1) {
              this.charts1 = {
                visible: true,
                options: {
                  chart: {
                    id: "vuechart-example",
                  },
                  plotOptions: {
                    bar: {
                      barHeight: "100%",
                      distributed: true,
                      // horizontal: true,
                      dataLabels: {
                        position: "center",
                      },
                    },
                  },
                  xaxis: {
                    categories: response.data.map((v) => v.department_code),
                  },
                },
                series: [
                  {
                    name: "Mukofot",
                    data: response.data.map((v) => v.mukofot),
                  },
                ],
              };
              // this.charts1 = {
              //   visible: true,
              //   animations: {
              //     enabled: true,
              //     easing: "easeinout",
              //     speed: 800,
              //     animateGradually: {
              //       enabled: true,
              //       delay: 150,
              //     },
              //     dynamicAnimation: {
              //       enabled: true,
              //       speed: 350,
              //     },
              //   },
              //   options: {
              //     chart: {
              //       type: "bar",
              //       height: 380,
              //     },
              //     plotOptions: {
              //       bar: {
              //         barHeight: "100%",
              //         distributed: false,
              //         horizontal: true,
              //         dataLabels: {
              //           position: "bottom",
              //         },
              //       },
              //     },
              //     dataLabels: {
              //       enabled: true,
              //       textAnchor: "start",
              //       style: {
              //         colors: ["#FFF"],
              //       },
              //       // formatter: function (val, opt) {
              //       //   return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
              //       // },
              //       offsetX: 0,
              //       dropShadow: {
              //         enabled: false,
              //       },
              //     },
              //     stroke: {
              //       width: 1,
              //       colors: ["#fff"],
              //     },
              //     xaxis: {
              //       categories: response.data.map((v) => v.name),
              //     },
              //     yaxis: {
              //       labels: {
              //         show: true,
              //       },
              //     },
              //   },
              //   series: [
              //     {
              //       name: "Miqdori:",
              //       data: response.data.map((v) => v.ct),
              //     },
              //   ],
              // };
            } else if (number == 2) {
              this.charts2 = {
                visible: true,
                options: {
                  // labels: response.data.map((v) => v.name+' ('+v.ct+' дона)'),
                  responsive: [
                    {
                      breakpoint: 480,
                      options: {
                        chart: {
                          width: 200,
                        },
                        legend: {
                          position: "bottom",
                        },
                      },
                    },
                  ],
                },
  
                series: response.data.map((v) => v.ct),
              };
            }
            // window.dispatchEvent(new Event("resize"));
          })
          .catch((error) => {
            console.log(error);
          });
      },
      getChartAll() {
        this.getChart(1);
        this.getChart(0);
      },
    },
    mounted() {
      console.log(moment().subtract(1, 'years').format('YYYY'),)
      // this.getChart(3);
      // this.getChart(2);
      this.getChartAll();
    },
  };
  </script>
  
  <style scoped>
  /* Add your component's styles here */
  </style>
  