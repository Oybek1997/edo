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
          @click="tabelDownload()"
        >
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn>
      </v-card-title>
      <v-sheet>
        <template>
          <v-card>
            <v-tabs horizontal>
              <v-tab>
                <v-icon left> mdi-account </v-icon>
                Skud SWOD
              </v-tab>

              <v-tab-item>
                <v-card class="ma-2" style="max-width: 1800px">
                  <table
                    :style="{ height: screenHeight + 'px' }"
                    class="tablestyle"
                  >
                    <thead style="position: sticky; top: -2px">
                      <tr>
                        <th class="tableMore" rowspan="2">№</th>
                        <th class="tableMore" rowspan="2">Ф.И.О</th>
                        <th class="tableMore" rowspan="2">Таб.№</th>
                        <th class="tableMore" rowspan="2">Код отдела</th>
                        <th class="tableMore" rowspan="2">Смена</th>
                        <th class="tableMore" rowspan="2">Месяц</th>
                        <th class="tableMore" rowspan="2">Год</th>
                        <th
                          class="tableMore"
                          style="text-align: center"
                          colspan="31"
                        >
                          Числа месяца
                        </th>
                        <th class="tableMore degree" rowspan="2">
                          Факт.раб.дней
                        </th>
                        <th class="tableMore" colspan="6">дни неявок</th>
                        <th class="tableMore" colspan="2">"Недор часи"</th>
                        <th class="tableMore" colspan="4">Всего</th>
                        <th class="tableMore" rowspan="2">Итого часов</th>
                      </tr>
                      <tr style="height: 50px">
                        <th v-for="(item, key) in 31" :key="key">
                          {{ key + 1 }}
                        </th>
                        <th>отпуск</th>
                        <th>болезн</th>
                        <th class="tableMore">админ. Отпуск</th>
                        <th>Прогул</th>
                        <th>Дополнител</th>
                        <th class="tableMore">Выxодной</th>
                        <th class="tableMore">Государственний</th>
                        <th class="tableMore">Командировка</th>
                        <th>Основное рабочее время</th>
                        <th>Сверxсрочние</th>
                        <th>Выxодние Праздничние часи</th>
                        <th>Ночные часи</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, key) in items" :key="key">
                        <td class="sticky-column">
                          {{ key + 1 }}
                        </td>
                        <td class="sticky-column">
                          {{ item.z08fio }}
                        </td>
                        <td>
                          {{ item.z12tn }}
                        </td>
                        <td>
                          {{ item.z00no }}
                        </td>
                        <td>
                          {{ item.z12pch }}
                        </td>
                        <td>
                          {{ item.z12mm }}
                        </td>
                        <td>
                          {{ item.z12yy }}
                        </td>
                        <td>
                          {{ item.z12d1 }}
                        </td>
                        <td>
                          {{ item.z12d2 }}
                        </td>
                        <td>
                          {{ item.z12d3 }}
                        </td>
                        <td>
                          {{ item.z12d4 }}
                        </td>
                        <td>
                          {{ item.z12d5 }}
                        </td>
                        <td>
                          {{ item.z12d6 }}
                        </td>
                        <td>
                          {{ item.z12d7 }}
                        </td>
                        <td>
                          {{ item.z12d8 }}
                        </td>
                        <td>
                          {{ item.z12d9 }}
                        </td>
                        <td>
                          {{ item.z12d10 }}
                        </td>
                        <td>
                          {{ item.z12d11 }}
                        </td>
                        <td>
                          {{ item.z12d12 }}
                        </td>
                        <td>
                          {{ item.z12d13 }}
                        </td>
                        <td>
                          {{ item.z12d14 }}
                        </td>
                        <td>
                          {{ item.z12d15 }}
                        </td>
                        <td>
                          {{ item.z12d16 }}
                        </td>
                        <td>
                          {{ item.z12d17 }}
                        </td>
                        <td>
                          {{ item.z12d18 }}
                        </td>
                        <td>
                          {{ item.z12d19 }}
                        </td>
                        <td>
                          {{ item.z12d20 }}
                        </td>
                        <td>
                          {{ item.z12d21 }}
                        </td>
                        <td>
                          {{ item.z12d22 }}
                        </td>
                        <td>
                          {{ item.z12d23 }}
                        </td>
                        <td>
                          {{ item.z12d24 }}
                        </td>
                        <td>
                          {{ item.z12d25 }}
                        </td>
                        <td>
                          {{ item.z12d26 }}
                        </td>
                        <td>
                          {{ item.z12d27 }}
                        </td>
                        <td>
                          {{ item.z12d28 }}
                        </td>
                        <td>
                          {{ item.z12d29 }}
                        </td>
                        <td>
                          {{ item.z12d30 }}
                        </td>
                        <td>
                          {{ item.z12d31 }}
                        </td>
                        <td>
                          {{ item.z12frd }}
                        </td>
                        <td>
                          {{ item.z12otp }}
                        </td>
                        <td>
                          {{ item.z12blz }}
                        </td>
                        <td>
                          {{ item.z12admotp }}
                        </td>
                        <td>
                          {{ item.z12prg }}
                        </td>
                        <td>
                          {{ item.z12dop }}
                        </td>
                        <td>
                          {{ item.z12v }}
                        </td>
                        <td>
                          {{ item.z12gos }}
                        </td>
                        <td>
                          {{ item.z12kom }}
                        </td>
                        <td>
                          {{ item.z12otrch }}
                        </td>
                        <td>
                          {{ item.z12otrsch }}
                        </td>
                        <td>
                          {{ item.z12otrvch }}
                        </td>
                        <td>
                          {{ item.z12otrnch }}
                        </td>
                        <td>
                          {{ item.z12votrch }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
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
                      style="text-align: center"
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
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import TableToExcel from "@linways/table-to-excel";
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
      return window.innerHeight - 205;
    },
  },
  methods: {
    weekend(date) {
      return ["0", "6"].includes(moment(date).format("d"));
    },
    getList() {
      this.loading = true;
      let user = this.$store.getters.getUser();
      let tabel = user.employee.tabel;
      axios
        .get(
          this.$store.state.AS400_url +
            "api/get-skud-full/" +
            tabel +
            "/" +
            this.currentDate
        )
        .then((response) => {
          this.items = response.data;
          console.log(this.items);
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    tabelDownload() {
      this.loading = true;
      let user = this.$store.getters.getUser();
      let tabel = user.employee.tabel;
      axios
        .get(
          this.$store.state.backend_url +
            "api/skudswod/excel-download/" +
            this.currentDate +
            "/" +
            tabel +
            "/" +
            "1",
          {
            responseType: "arraybuffer",
            headers: {
              "Content-Type": "application/json",
            },
          }
        )
        .then((response) => {
          // console.log(response.data.byteLength);
          if (response.data.byteLength != 1) {
            const type = response.headers["content-type"];
            const blob = new Blob([response.data], {
              type: type,
              encoding: "UTF-8",
            });

            const link = document.createElement("a");
            link.href = window.URL.createObjectURL(blob);
            link.download = Date.now() + ".xlsx";
            link.click();
            this.loading = false;
          } else {
            alert("Xatolik yuz berdi");
            this.loading = false;
          }
        });
    },
    tableToExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    },
  },
  mounted() {
    this.getList();
  },
};
</script>

<style scoped>
.tablestyle {
  width: 100%;
  border: 1px solid #78909c;
  border-collapse: collapse;
  table-layout: fixed;
  overflow: auto;
  display: inline-block;
}
table > thead > tr > th {
  padding: 10px;
  position: sticky;
  background-color: #2196f3;
  color: white;
  top: 0;
  z-index: 1;
  /* height: 50px; */
}
table > tbody > tr > td,
table > thead > tr > th {
  padding: 5px;
  border: 1px solid #ddd;
  /* white-space: wrap; */
}
.sticky-column {
  position: sticky;
  position: -webkit-sticky;
  left: 0;
  background: #2196f3;
  color: white;
  z-index: 1;
  width: 100px; /* How can I make this dynamic? */
}
</style>
