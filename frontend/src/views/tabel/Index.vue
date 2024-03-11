<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        
        <span class="mx-4">{{ $t("tabel") }}</span>
        <v-spacer></v-spacer>
        <v-col
        class="d-flex"
        cols="12"
        sm="4"
      >
        <!-- <v-select
          :items="employees"
          v-if="employees.length>0"
          label="Hodimni tanlang"
          dense
          v-model = "selected_employee"
          hide-details
          class="mx-3"
          item-text = "fio"
          item-value = "tabel"
          outlined
          @change="getList"
        ></v-select> -->
        <v-autocomplete
          :items="employees"
          v-model = "selected_employee"
          v-if="employees.length>0"
          :label="$t('Hodimni tanlang')"
          item-text = "fio"
          item-value = "tabel"
          outlined
          class="pa-0"
          :menu-props="{ bottom: true, offsetY: true }"
          clearable
          hide-details
          dense
          @change="getList"
        ></v-autocomplete>
      </v-col>
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
          <v-card class="ma-2" style="max-width: 500px;">
                  <v-simple-table dense fixed-header
                    ref="table"
                    id="table"
                  >
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <td class="text-left" style="background-color:#222; color:white;text-align:center;">
                            Sana
                          </td>
                          <td class="text-left" style="background-color:#222; color:white;text-align:center;">
                            Soat
                          </td>
                          <td class="text-left" style="background-color:#222; color:white;text-align:center;">
                            Vaqt
                          </td>
                          <td class="text-left" style="background-color:#222; color:white;text-align:center;">
                            Amal
                          </td>
                        </tr>
                      </thead>
                      <tbody v-for="(item, key) in items" :key="key">
                        <tr  v-if="item.skud.length == 0" :style="weekend(currentDate +'-' +(key + 1).toString().padStart(2, '0')) ? 'background-color:#eee;' : ''">
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
                          <td style="text-align:right;">{{ skud.ztype == 0 ? $t("Kirish") : $t("Chiqish") }}</td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
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
      employees: [],
      selected_employee: ''
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
    getEmployees(){
      axios
        .get(
          this.$store.state.backend_url + "api/employees/get-skud"
        )
        .then((response) => {
          if(response.data !=1){

            // this.employees = response.data;
            this.employees = Object.values(response.data).map((v) => {
            v.fio = v.lastname_uz_latin + " " + v.firstname_uz_latin + " " + v.middlename_uz_latin + " " + v.tabel;
            return v;
          });
          }
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getList() {
      this.loading = true;
      let user = this.$store.getters.getUser();
      let tabel = "";
      if(this.selected_employee){
        tabel = this.selected_employee;
      }
      else{
        tabel = user.employee.tabel;
      }
      axios
        .post(
          this.$store.state.AS400_url +
            "api/tabel-get/" +
            tabel +
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
    this.getEmployees();
    this.getList();
  },
};
</script>

<style scoped>
table,
th, td {
  border: 1px solid black;
}
</style>
