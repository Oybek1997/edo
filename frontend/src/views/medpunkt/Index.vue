<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t('Med Punkt') }}</span>
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
            ></v-date-picker>
          </v-menu>
        </v-form>
      </v-card-title>
      <v-row class="mx-0 mb-10" style="display: flex; align-items: flex-end">
        <v-col class="pa-0" md="12" sm="12" xs="12">
          <v-card class="d-flex align-content-start justify-space-between" flat tile>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2"
                  width="20%"
                  :class="`elevation-${hover ? 16 : 3}`"
                  style="border-radius: 5px"
                >
                  <v-card-title class="card_title px-2">{{ $t("Murojaatlar ") + ' ' + currentDate}}</v-card-title>
                  <v-card-title class="card_title-number pt-0 pb-6">
                    {{
                    medReportsThisMonth
                    }}
                  </v-card-title>
                  <v-divider class="ml-3 mr-10" color="black"></v-divider>
                  <v-card-title class="card_title-sub--number pt-3 pb-0">
                    {{
                    medReports
                    }}
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title py-2 mx-0"
                  >{{ $t("Barchasi") }}</v-card-subtitle>
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2"
                  width="20%"
                  :class="`elevation-${hover ? 16 : 3}`"
                  style="border-radius: 5px"
                >
                  <v-card-title class="card_title px-2">{{ $t("Ruxsatnoma olganlar") }}</v-card-title>
                  <v-card-title class="card_title-number pt-0 pb-6">
                    {{
                    permissionsThisMonth
                    }}
                  </v-card-title>
                  <v-divider class="ml-3 mr-10" color="black"></v-divider>
                  <v-card-title class="card_title-sub--number pt-3 pb-0">
                    {{
                    permissions
                    }}
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title py-2 mx-0"
                  >{{ $t("Barchasi") }}</v-card-subtitle>
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2"
                  width="20%"
                  :class="`elevation-${hover ? 16 : 3}`"
                  style="border-radius: 5px"
                >
                  <v-card-title class="card_title px-2">{{ $t("Yo'llanma olganlar") }}</v-card-title>
                  <v-card-title class="card_title-number pt-0 pb-6">
                    {{
                    referralsThisMonth
                    }}
                  </v-card-title>
                  <v-divider class="ml-3 mr-10" color="black"></v-divider>
                  <v-card-title class="card_title-sub--number pt-3 pb-0">
                    {{
                    referrals
                    }}
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title py-2 mx-0"
                  >{{ $t("Barchasi") }}</v-card-subtitle>
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2"
                  width="20%"
                  :class="`elevation-${hover ? 16 : 3}`"
                  style="border-radius: 5px"
                >
                  <v-card-title class="card_title px-2">{{ $t("Ruxsatnoma olganlar") }}</v-card-title>
                  <v-card-title class="card_title-number pt-0 pb-6">
                    {{
                    5
                    }}
                  </v-card-title>
                  <v-divider class="ml-3 mr-10" color="black"></v-divider>
                  <v-card-title class="card_title-sub--number pt-3 pb-0">
                    {{
                    6
                    }}
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title py-2 mx-0"
                  >{{ $t("Barchasi") }}</v-card-subtitle>
                </v-card>
              </template>
            </v-hover>
          </v-card>
        </v-col>

        <v-col class="pa-0 mt-6" md="12" sm="12" xs="12">
          <v-card class="d-flex align-content-start justify-space-between" flat tile>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2"
                  width="20%"
                  :class="`elevation-${hover ? 16 : 3}`"
                  style="border-radius: 5px"
                >
                  <v-card-title
                    class="card_title px-2"
                  >{{ $t("TTYo chaqiruvlar soni ") + ' ' + currentDate}}</v-card-title>
                  <v-card-title class="card_title-number pt-0 pb-6">
                    {{
                    call_allThisMonth
                    }}
                  </v-card-title>
                  <v-divider class="ml-3 mr-10" color="black"></v-divider>
                  <v-card-title class="card_title-sub--number pt-3 pb-0">
                    {{
                    call_all
                    }}
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title py-2 mx-0"
                  >{{ $t("Barchasi") }}</v-card-subtitle>
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2"
                  width="20%"
                  :class="`elevation-${hover ? 16 : 3}`"
                  style="border-radius: 5px"
                >
                  <v-card-title class="card_title px-2">{{ $t("Joyida") }}</v-card-title>
                  <v-card-title class="card_title-number pt-0 pb-6">
                    {{
                    call_type1ThisMonth
                    }}
                  </v-card-title>
                  <v-divider class="ml-3 mr-10" color="black"></v-divider>
                  <v-card-title class="card_title-sub--number pt-3 pb-0">
                    {{
                    call_type1
                    }}
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title py-2 mx-0"
                  >{{ $t("Barchasi") }}</v-card-subtitle>
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2"
                  width="20%"
                  :class="`elevation-${hover ? 16 : 3}`"
                  style="border-radius: 5px"
                >
                  <v-card-title class="card_title px-2">{{ $t("Tibbiy qisimga olib kelinganlar") }}</v-card-title>
                  <v-card-title class="card_title-number pt-0 pb-6">
                    {{
                    call_type2ThisMonth
                    }}
                  </v-card-title>
                  <v-divider class="ml-3 mr-10" color="black"></v-divider>
                  <v-card-title class="card_title-sub--number pt-3 pb-0">
                    {{
                    call_type2
                    }}
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title py-2 mx-0"
                  >{{ $t("Barchasi") }}</v-card-subtitle>
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2"
                  width="20%"
                  :class="`elevation-${hover ? 16 : 3}`"
                  style="border-radius: 5px"
                >
                  <v-card-title class="card_title px-2">{{ $t("SHTYoBga olib ketilganlar") }}</v-card-title>
                  <v-card-title class="card_title-number pt-0 pb-6">
                    {{
                    call_type3ThisMonth
                    }}
                  </v-card-title>
                  <v-divider class="ml-3 mr-10" color="black"></v-divider>
                  <v-card-title class="card_title-sub--number pt-3 pb-0">
                    {{
                    call_type3
                    }}
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title py-2 mx-0"
                  >{{ $t("Barchasi") }}</v-card-subtitle>
                </v-card>
              </template>
            </v-hover>
          </v-card>
        </v-col>
      </v-row>
    </v-card>
    <div class="pa-5">
      <template>
        <v-card-title class="pa-1">
        <span>{{ $t("Xisobotlar") }}</span>
        <v-spacer></v-spacer>
        <v-form ref="myForm">
          <v-menu
            ref="menu"
            v-model="menu1"
            :close-on-content-click="false"
            :return-value.sync="currentReportDate"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="auto"
            nudge-left="170"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-responsive max-width="120">
                <v-text-field
                  v-model="currentReportDate"
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
              v-model="currentReportDate"
              type="month"
              no-title
              scrollable
              @change="
                $refs.menu.save(currentReportDate);
                getReportPeriodIllnesMonth();
              "
            ></v-date-picker>
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
        <v-simple-table fixed-header class="med_data-table" ref="table"
          id="table">
          <template v-slot:default>
            <thead>
              <tr>
                <th rowspan="3" class="text-center" style="max-width: 50px;">№</th>
                <th rowspan="3" class="text-center" style="max-width: 200px;">MKBX</th>
                <th colspan="4" class="text-center" style="max-width: 200px;">Absolut raqamlar</th>
              </tr>
              <tr>
                <th colspan="2" class="text-center" style="max-width: 200px;">2022</th>
                <th colspan="2" class="text-center" style="max-width: 200px;">2023</th>
              </tr>
              <tr>
                <th class="text-center" style="max-width: 200px;">Soni</th>
                <th class="text-center" style="max-width: 200px;">Kuni</th>
                <th class="text-center" style="max-width: 200px;">Soni</th>
                <th class="text-center" style="max-width: 200px;">Kuni</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(report, index) in repoertMonthIllnes" :key="index">
                <td style="max-width: 50px;"> {{ index + 1 }} </td>
                <td style="max-width: 200px; white-space: wrap;"> {{ report.name }} </td>
                <td style="max-width: 200px; text-align: center;">  </td>
                <td style="max-width: 200px; text-align: center;">  </td>
                <td style="max-width: 200px; text-align: center;"> {{ report.Soni }} </td>
                <td style="max-width: 200px; text-align: center;"> {{ report.Summa }} </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </template>
    </div>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t('loadingText') }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import TableToExcel from "@linways/table-to-excel";
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
      index: 0,
      staticInfo: [],
      dialog: false,
      editMode: null,
      currentDate: new Date().toISOString().substr(0, 7),
      currentReportDate: new Date().toISOString().substr(0, 7),
      newMonth: new Date().getMonth() + 1,
      menu: false,
      menu1: false,
      items: [],
      repoertIllnes: [],
      repoertMonthIllnes: [],
      medReports: [],
      medReportsThisMonth: [],
      permissions: [],
      permissionsThisMonth: [],
      referrals: [],
      referralsThisMonth: [],
      treatment: [],
      connection: [],
      call_all: [],
      call_allThisMonth: [],
      call_type1: [],
      call_type1ThisMonth: [],
      call_type2: [],
      call_type2ThisMonth: [],
      call_type3: [],
      call_type3ThisMonth: [],
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [{ text: "№", value: "id", align: "center", width: 30 }];
    }
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    weekend(date) {
      return ["0", "6"].includes(moment(date).format("d"));
    },
    getList() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/report/" +
            this.currentDate
        )
        .then(response => {
          this.items = response.data;
          this.medReports = response.data.medReports;
          this.medReportsThisMonth = response.data.medReportsThisMonth;

          this.permissions = response.data.permissions;
          this.permissionsThisMonth = response.data.permissionsThisMonth;

          this.referrals = response.data.referrals;
          this.referralsThisMonth = response.data.referralsThisMonth;

          this.call_all = response.data.call_all;
          this.call_allThisMonth = response.data.call_allThisMonth;

          this.call_type1 = response.data.call_type1;
          this.call_type1ThisMonth = response.data.call_type1ThisMonth;

          this.call_type2 = response.data.call_type2;
          this.call_type2ThisMonth = response.data.call_type2ThisMonth;

          this.call_type3 = response.data.call_type3;
          this.call_type3ThisMonth = response.data.call_type3ThisMonth;

          // this.treatment = response.data.treatment.map((v) => ({
          //   sanasi: v.sanasi,
          //   soni: v.soni,
          // }));
          // this.connection = response.data.connection.map((v) => ({
          //   sanasi: v.sanasi,
          //   soni: v.soni,
          // }));
          // this.call_all = response.data.call_all.map((v) => ({
          //   sanasi: v.sanasi,
          //   soni: v.soni,
          // }));
          // this.call_type1 = response.data.call_type1.map((v) => ({
          //   sanasi: v.sanasi,
          //   soni: v.soni,
          // }));
          // this.call_type2 = response.data.call_type2.map((v) => ({
          //   sanasi: v.sanasi,
          //   soni: v.soni,
          // }));
          // this.call_type3 = response.data.call_type3.map((v) => ({
          //   sanasi: v.sanasi,
          //   soni: v.soni,
          // }));
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getReportPeriodIllnes() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/report-period-illness"
        )
        .then(response => {
          this.repoertIllnes = response.data.illness;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getReportPeriodIllnesMonth() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/report-period-illness/" +
            this.currentReportDate
        )
        .then(response => {
          this.repoertMonthIllnes = response.data.illnessMonth;
          console.log('456', this.repoertMonthIllnes);
          this.loading = false;
        })
        .catch(error => {
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
    this.getReportPeriodIllnes();
    this.getReportPeriodIllnesMonth();
  }
};
</script>
<style scoped>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100% !important;
}

#customers td,
#customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even) {
  background-color: #f2f2f2;
}

#customers tr:hover {
  background-color: #ddd;
}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
}
.dashboard_bg {
  background: #fff;
}
.dashboard__title {
  color: #205c7c;
  font-size: 40px;
  font-weight: 700;
  text-shadow: 0 2px 3px rgba(60, 60, 60, 0.25);
}
.dashboard__togglers {
  display: flex;
  justify-content: center;
}
.dashborad_date_toggler {
  background: transparent;
  /* border: 1px solid #205c7c; */
  /* border-radius: 18px; */
  pointer-events: initial;
  overflow: hidden;
}
.dashboard_manager_toggler {
  background: transparent;
  /* border: 1px solid #205c7c; */
  /* border-radius: 18px; */
  pointer-events: initial;
  overflow: hidden;
  display: flex;
  flex-direction: row;
}
.dashboard__togglers-toggler-item {
  color: #205c7c;
  height: 34px;
  text-transform: capitalize;
  font-family: "Montserrat", Sans-serif;
}
.v-input__slot fieldset {
  border: none !important;
}
.v-text-field__slot {
  color: #fff !important;
}
.card_title {
  color: #000;
  font-size: 12px;
  line-height: 1;
  letter-spacing: 0.03em;
  font-weight: 700;
  text-transform: uppercase;
  display: block;
  display: -webkit-box;
  max-width: 100%;
  height: 41px;
  margin: 0 auto;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 8px;
}
.card_sub--title {
  font-size: 13px;
  line-height: 10px;
  margin-top: 8px;
  white-space: nowrap;
  overflow: hidden;
  color: #000;
  line-height: 1.2;
}
.card_title-number {
  font-size: 43px;
  font-weight: 600;
  color: #8275ff;
}
.card_title-sub--number {
  font-size: 19px;
  font-weight: 600;
  color: #00d669;
}
.text-ellipsis {
  max-width: 100%;
  display: inline-block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.top-chart__pipeline {
  margin-right: 15px;
}
.top-chart__pipelines-item {
  text-overflow: ellipsis;
  overflow: hidden;
  max-width: 100%;
  display: inline-block;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  text-transform: uppercase;
  line-height: 18px;
  color: #205c7c;
  text-decoration: none;
}
.top-chart_title {
  color: #fff;
  text-align: center;
  font-size: 14px;
  line-height: 1;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
  /* text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25); */
}
.top-chart_sub--title {
  color: #205c7c !important;
  font-size: 12px;
  line-height: 1;
  font-weight: 600;
  text-align: center;
  cursor: pointer;
}
.top-chart-number {
  display: block;
  color: #8275ff;
  text-align: center;
  font-weight: 600;
  font-size: 45px;
  /* text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25); */
  margin-top: -10px;
}
.top-chart-sub--number {
  /* color: #000 !important; */
  text-align: center;
  font-weight: 700;
  font-size: 13px;
  /* text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25); */
}
.top-chart_btn {
  color: #205c7c;
  text-align: center;
  font-size: 14px;
  line-height: 1;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
}
#top-chart-number {
  color: #205c7c;
}
#top-chart-sub--number {
  color: #205c7c;
  text-align: center;
  font-weight: 700;
  font-size: 13px;
  /* text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25); */
}
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
}
.fontSize35 {
  font-size: 30px;
}
.med_data-table tr>th, .med_data-table tr>td {
  border: 1px solid #000;
  border-collapse: collapse;
}
.med_data-table tbody > tr > td {
  white-space: normal;
  max-width: 50px;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
