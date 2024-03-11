<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span class="mx-4">{{ $t("tabel") }}</span>
        <v-spacer></v-spacer>
        <v-form ref="myForm">
          <v-menu
            ref="menu"
            v-model="menu"
            :close-on-content-click="false"
            :return-value.sync="currentDate"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="auto"
            nudge-left="170"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-responsive max-width="120">
                <v-text-field
                  v-model="currentDate"
                  outlined
                  dense
                  readonly
                  v-bind="attrs"
                  v-on="on"
                  hide-details
                ></v-text-field>
              </v-responsive>
            </template>
            <v-date-picker
              v-model="currentDate"
              type="month"
              no-title
              scrollable
              @change="
                $refs.menu.save(currentDate);
                getList();
              "
            >
            </v-date-picker>
          </v-menu>
        </v-form>
        <v-btn
          class="mx-3"
          color="indigo"
          x-small
          dark
          fab
          @click="tableToExcel('table', 'Lorem Table')"
        >
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn>
      </v-card-title>
      <v-sheet>
        <template>
          <v-card>
            <v-tabs horizontal>
              <v-tab>
                <v-icon left>
                  mdi-account
                </v-icon>
                Skud SWOD
              </v-tab>
              <v-tab>
                <v-icon left>
                  mdi-clock
                </v-icon>
                Skud Time
              </v-tab>

              <v-tab-item>
                <v-card class="ma-2" style="max-width: 500px;">
                  <v-simple-table dense fixed-header
                    ref="table"
                    id="table"
                  >
                    <template v-slot:default>
                      <thead>
                      <tr>
                        <td class="text-left" style="background-color:#2196F3; color:white;text-align:center;">
                          Дата
                        </td>
                        <td class="text-left" style="background-color:#2196F3; color:white;text-align:center;">
                          Hour
                        </td>
                        <td class="text-left" style="background-color:#2196F3; color:white;text-align:center;">
                          Время
                        </td>
                        <td class="text-left" style="background-color:#2196F3; color:white;text-align:center;">
                          Событие
                        </td>
                      </tr>
                      </thead>
                      <tbody v-for="(item, key) in items" :key="key">
                      <tr  v-if="item.skud.length == 0" :style="weekend(currentDate +'-' +(key + 1).toString().padStart(2, '0')) ? 'background-color:#efe;' : ''">
                        <td style="text-align:center;">
                          {{
                            currentDate +
                            "-" +
                            (key + 1).toString().padStart(2, "0")
                          }}
                        </td>
                        <td style="text-align:center;">
                          {{ item.hour }}
                        </td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"></td>
                      </tr>
                      <tr v-for="(skud, k) in item.skud" :key="k" style="background-color:#fff;">
                        <td style="text-align:center;" v-if="k == 0" :rowspan="item.skud.length">
                          {{
                            currentDate +
                            "-" +
                            (key + 1).toString().padStart(2, "0")
                          }}
                        </td>
                        <td style="text-align:center;" v-if="k == 0" :rowspan="item.skud.length">
                          {{ item.hour }}
                        </td>
                        <td style="text-align:right;">{{ skud.ztime.padStart(6, "0").substr(0,2) +':'+ skud.ztime.padStart(6, "0").substr(2,2) }}</td>
                        <td style="text-align:right;">{{ skud.ztype == 0 ? $t("In") : $t("Out") }}</td>
                      </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card>
              </v-tab-item>
              <!--              Second Tab-->
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-calendar
                      :start="currentDate + '-01'"
                      :weekdays="[1, 2, 3, 4, 5, 6, 0]"
                      color="primary"
                      style="text-align: center;"
                    >
                      <template v-slot:day="{ past, date, day, month }">
                        <div
                          v-if="
                            currentDate == date.substring(0, 7) &&
                            items[day - 1]
                          "
                        >
                          <div>
                            {{ items[day - 1].hour ? items[day - 1].hour : "" }}
                          </div>
                          <v-row>
                            <v-col
                              v-for="item in items[day - 1].skud"
                              class="text-left"
                              cols="6"
                            >
                              <span
                                :style="
                                  item.ztype == '0'
                                    ? 'color:green;'
                                    : 'color:red;'
                                "
                              >
                                {{ item.zsysdt.toString().substr(11, 5) }}
                              </span>
                            </v-col>
                          </v-row>
                        </div>

                        <div
                          v-if="false"
                          v-for="(item, index) in items"
                          class="pa-2 d-flex justify-space-between align-center"
                        >
                          <span>{{ item.hour }}</span
                          ><br />

                          <div class="d-flex flex-column">
                            <!--              <span  v-for="(skud,index) in item.skud"  >{{ skud.ztime }}<br></span>-->

                            <span
                              :style="[
                                index % 2 == 0
                                  ? { color: 'blue' }
                                  : { color: 'red' },
                              ]"
                              v-if="
                                skud.ztime.length == 5
                                  ? (skud.ztime = 0 + skud.ztime)
                                  : skud.ztime
                              "
                              v-for="(skud, index) in item.skud"
                            >{{ skud.ztime.substr(0, 2) }}:{{
                                skud.ztime.substr(2, 2)
                              }}<br
                              /></span>

                            <!--                 <span v-else-if="day == 3 && items.z12mm == date.substr(5, 2)">{{ items.z12d3 }}</span>-->
                          </div>
                        </div>
                      </template>
                    </v-calendar>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs>
          </v-card>
        </template>
      </v-sheet>
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>    </v-dialog>
  </div>
</template>
<script>
import TableToExcel from "@linways/table-to-excel";
import skud from '@/router/skud'
const axios = require("axios").default;
const moment = require("moment");
export default {
  data() {
    return {
      loading: false,
      employee: {},
      currentDate: new Date().toISOString().substr(0, 7),
      newMonth: new Date().getMonth() + 1,
      menu: false,
      modal: false,
      items: [],
      focus: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
  },
  methods: {
    weekend(date){
      return ["0","6"].includes(moment(date).format('d'));
    },
    getList() {
      this.loading = true;
      let user = this.$store.getters.getUser();
      let tabel = user.employee.tabel;
      axios
      .get(
        this.$store.state.AS400_url +
        "api/get-skud-full/" +
        skud +
        "/" +
        this.currentDate
      )
      .then((response) => {
        this.items = Object.values(response.data).map((v) => {
          v.skud = Object.values(v.skud);
          return v;
        });
        this.loading = false;
      })
      .catch((error) => {
        console.log(error);
        this.loading = false;
      });
    },
    tableToExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    }
  },
  mounted() {
    this.getList();
  },
};
</script>

<style scoped>
table,
th,
td {
  border: 1px solid black;
}
</style>
