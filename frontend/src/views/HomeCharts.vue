<template>
  <div>
    <v-card class="heightFull">
      <v-card-title primary-title>
        <h3>Dashboard</h3>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="6">
            <v-card>
              <v-card-title primary-title
                >Korxona xodimlari soni [Filiallar bo'yicha]</v-card-title
              >
              <v-card-text>
                <apexchart
                  v-if="chart2.visible"
                  height="315"
                  type="pie"
                  :options="chart2.options"
                  :series="chart2.series"
                ></apexchart>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="6">
            <v-card>
              <v-card-title primary-title
                >Korxona xodimlari soni [Bo'limlar bo'yicha]</v-card-title
              >
              <v-card-text>
                <apexchart
                  v-if="chart4.visible"
                  height="300"
                  type="bar"
                  :options="chart4.options"
                  :series="chart4.series"
                ></apexchart>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="6">
            <v-card>
              <v-card-title primary-title
                >EDO da kiritilgan xujjarlar [TOP 10]</v-card-title
              >
              <v-card-text>
                <apexchart
                  v-if="chart5.visible"
                  height="300"
                  type="bar"
                  :options="chart5.options"
                  :series="chart5.series"
                ></apexchart>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="6">
            <v-card min-height="350">
              <v-card-title primary-title
                >Tavallud ayyom. [Xodimlar soni {{ countBirthday }}]
              </v-card-title>
              <v-simple-table
                class="doc-template_data-table"
                dense
                height="330"
                id="table"
                fixed-header
              >
                <template v-slot:default>
                  <tbody>
                    <tr v-for="(item, index) in itemBirthday" :key="item.fio">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.fio }}</td>
                      <td>
                        {{ item.department }} <br /><span>{{ item.position }}</span>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card>
          </v-col>

          <!-- ////////Xodimga tegishli//////////// -->
          <v-col cols="6">
            <v-card>
              <v-card-title primary-title>Qatnashgan hujjatlarim</v-card-title>
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
              <v-card-title primary-title>Kiritgan hujjatlarim</v-card-title>
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
          <!-- //////////////////// -->
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
      itemBirthday: [],
      countBirthday: 0,
      charts0: {
        visible: false,
      },
      charts4: {
        visible: false,
      },
      charts5: {
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
    chart4() {
      return this.charts4;
    },
    chart5() {
      return this.charts5;
    },
  },
  methods: {
    getChart(number) {
      axios
        .post(this.$store.state.backend_url + "api/charts/" + number)
        .then((response) => {
          if (number == 0) {
            this.charts0 = {
              visible: true,
              options: {
                chart: {
                  id: "vuechart-example",
                },
                xaxis: {
                  categories: Object.keys(response.data),
                },
                plotOptions: {
                  bar: {
                    borderRadius: 5,
                    distributed: true,
                    dataLabels: {
                      position: "top", // top, center, bottom
                    },
                  },
                },
                dataLabels: {
                  enabled: true,
                  offsetY: -20,
                  style: {
                    fontSize: "12px",
                    colors: ["#304758"],
                  },
                },
              },
              series: [
                {
                  name: "Count",
                  data: Object.values(response.data),
                },
              ],
            };
          } else if (number == 4) {
            this.charts4 = {
              visible: true,
              options: {
                chart: {
                  id: "vuechart-example",
                },
                xaxis: {
                  categories: Object.keys(response.data),
                },
                plotOptions: {
                  bar: {
                    barHeight: "100%",
                    borderRadius: 5,
                    distributed: false,
                    horizontal: false,
                    dataLabels: {
                      position: "top",
                    },
                  },
                },
                dataLabels: {
                  enabled: true,
                  offsetY: -20,
                  style: {
                    fontSize: "12px",
                    colors: ["#304758"],
                  },
                },
              },
              series: [
                {
                  name: "Count",
                  data: Object.values(response.data),
                },
              ],
            };
          } else if (number == 5) {
            this.charts5 = {
              visible: true,
              animations: {
                enabled: true,
                easing: "easeinout",
                speed: 800,
                animateGradually: {
                  enabled: true,
                  delay: 150,
                },
                dynamicAnimation: {
                  enabled: true,
                  speed: 350,
                },
              },
              options: {
                chart: {
                  type: "bar",
                  height: 380,
                },
                plotOptions: {
                  bar: {
                    barHeight: "100%",
                    distributed: true,
                    horizontal: true,
                    dataLabels: {
                      position: "top",
                    },
                  },
                },
                dataLabels: {
                  enabled: true,
                  offsetX: 40,
                  style: {
                    fontSize: "12px",
                    colors: ["#304758"],
                  },
                },
                stroke: {
                  width: 1,
                  colors: ["#fff"],
                },
                xaxis: {
                  categories: response.data.map((v) => v.name),
                },
                yaxis: {
                  labels: {
                    show: true,
                  },
                },
              },
              series: [
                {
                  name: "Miqdori",
                  data: response.data.map((v) => v.ct),
                },
              ],
            };
            console.log("this.charts4=", this.charts4);
          } else if (number == 1) {
            this.charts1 = {
              visible: true,
              animations: {
                enabled: true,
                easing: "easeinout",
                speed: 800,
                animateGradually: {
                  enabled: true,
                  delay: 150,
                },
                dynamicAnimation: {
                  enabled: true,
                  speed: 350,
                },
              },
              options: {
                chart: {
                  type: "bar",
                  height: 380,
                },
                plotOptions: {
                  bar: {
                    barHeight: "100%",
                    distributed: true,
                    horizontal: true,
                    dataLabels: {
                      position: "top",
                    },
                  },
                },
                dataLabels: {
                  enabled: true,
                  offsetX: 20,
                  style: {
                    fontSize: "12px",
                    colors: ["#304758"],
                  },
                },
                stroke: {
                  width: 1,
                  colors: ["#fff"],
                },
                xaxis: {
                  categories: response.data.map((v) => v.name),
                },
                yaxis: {
                  labels: {
                    show: true,
                  },
                },
              },
              series: [
                {
                  name: "Miqdori",
                  data: response.data.map((v) => v.ct),
                },
              ],
            };
          } else if (number == 2) {
            this.charts2 = {
              visible: true,
              options: {
                labels: response.data.map((v) => v.name + " (" + v.ct + " дона)"),
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
          } else if (number == 3) {
            this.itemBirthday = response.data.data;
            this.countBirthday = response.data.count;
          }
          // window.dispatchEvent(new Event("resize"));
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getbirthday(number) {
      axios
        .get(this.$store.state.backend_url + "api/employees/birthday")
        .then((response) => {
          console.log(response);
        });
    },
  },
  mounted() {
    this.getChart(5);
    this.getChart(4);
    this.getChart(3);
    this.getChart(2);
    this.getChart(1);
    this.getChart(0);
  },
};
</script>

<style scoped>
/* Add your component's styles here */
</style>
