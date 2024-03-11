<template>
  <div class="fullHeight">
     <v-card class="pa-0 heightFull" elevation="0">
      <!-- ------------- -->
      <v-card-title class="px-4 py-3">
          <span class="headerTitle mb-2">{{ $t("department.myokd") }}</span>
          <div class="headerSearch d-flex align-center">
            <v-text-field
            v-model="search.from_date"
            class="txt_search1"
              label="Sanadan"
              type="date"
              dense
              hide-details
              solo
            ></v-text-field>
            <v-text-field
            v-model="search.to_date"
            class="txt_search2"
              label="Sanagacha"
              type="date"
              dense
              hide-details
              solo
            ></v-text-field>
            
            <v-btn
              class="filterBtn px-2"
              style="background: #fff; height: 34px"
              @click="getAll()"
            >
              <v-icon color="info" center>mdi-magnify</v-icon>
            </v-btn>           
            <v-btn
            v-if="$store.getters.getUser().employee.dr_employee_id != null"
            v-show="!status_report"
              class="filterBtn px-2"
              style="background: #fff; height: 34px"
              @click="getListDr()" 
            > {{$t("rep.dr_rep")}}
              <v-icon color="info" center>mdi-magnify</v-icon>
            </v-btn>           
            <v-btn
            v-show="status_report"
              class="filterBtn px-2"
              style="background: #fff; height: 34px"
              @click="getListMy()" 
            > {{$t("rep.my_rep")}}
              <v-icon color="info" center>mdi-magnify</v-icon>
            </v-btn>           
            <v-btn
              class="filterBtn px-2"
              style="background: #fff; height: 34px"
              @click="uploadExcel('table', 'Lorem Table')"
            >
            {{ $t("Excel") }}
              <v-icon color="green" right>mdi-download-multiple</v-icon>
            </v-btn>           
          </div>
        </v-card-title>
       <!-- ------------- -->
      <!-- <v-card-title class="pa-1">
        <span>{{ $t("department.myokd") }}</span>
        <v-row class="pl-10">
          <v-col cols="3">
            <v-text-field
              class="mt-4"
              v-model="search.from_date"
              label="Sanadan"
              type="date"
              outlined
              background-color="#FFFFFa"
              dense
              clearable
            ></v-text-field>
          </v-col>
          <v-col cols="3">
            <v-text-field
              class="mt-4"
              v-model="search.to_date"
              background-color="#FFFFFa"
              label="Sanagacha"
              type="date"
              outlined
              dense
              clearable
            ></v-text-field>
          </v-col>
          <v-col>
            <v-btn @click="getAll()" class="ml-1 mt-4" color="info">
              <v-icon>mdi-magnify</v-icon>
            </v-btn>
          </v-col>
          <v-col
            v-if="$store.getters.getUser().employee.dr_employee_id != null"
            v-show="!status_report"
          >
            <v-btn @click="getListDr()" class="ml-1 mt-4" color="info">{{
              $t("rep.dr_rep")
            }}</v-btn>
          </v-col>
          <v-col v-show="status_report">
            <v-btn @click="getListMy()" class="ml-1 mt-4" color="info">{{
              $t("rep.my_rep")
            }}</v-btn>
          </v-col>
          <v-col>
            <v-btn
              class="ml-1 mr-0 mt-4"
              @click="uploadExcel('table', 'Lorem Table')"
            >
              <v-icon color="green">mdi-download-multiple</v-icon>
              <span style="color: green">{{ $t("excel") }}</span>
            </v-btn>
          </v-col>
        </v-row>
      </v-card-title> -->
      <v-simple-table class="mainTable" dense fixed-header id="table">
        <template v-slot:default>
          <tbody style="text-align: center">
            <tr>
              <td rowspan="3" style="color: #0b198f">
                {{ $t("rep.resalution_name") }}
              </td>
              <td rowspan="3" style="color: #0b198f">
                {{ $t("rep.params_0") }}
              </td>
              <td colspan="8" style="color: #0b198f">
                {{ $t("rep.done_from_them") }}
              </td>
            </tr>
            <tr>
              <td rowspan="2" style="color: #0b198f">
                {{ $t("rep.params_1") }}
              </td>
              <td colspan="2" style="color: #0b198f">
                {{ $t("rep.done_from_them") }}
              </td>
              <td rowspan="2" style="color: #0b198f">
                {{ $t("rep.params_4") }}
              </td>
              <td colspan="3" style="color: #0b198f">
                {{ $t("rep.to_be_executed") }}
              </td>
              <td rowspan="2" style="color: #0b198f">
                {{ $t("rep.params_8") }}
              </td>
            </tr>
            <tr>
              <td style="color: #0b198f">{{ $t("rep.params_2") }}</td>
              <td style="color: #0b198f">{{ $t("rep.params_3") }}</td>
              <td style="color: #0b198f">{{ $t("rep.params_5") }}</td>
              <td style="color: #0b198f">{{ $t("rep.params_6") }}</td>
              <td style="color: #0b198f">{{ $t("rep.params_7") }}</td>
            </tr>
          </tbody>
          <tbody>
            <tr
              style="color: white; background-color: DimGrey"
              v-for="(okMy, q) in okdMy"
              :key="q + 'a'"
            >
              <td>{{ $t("my_documents") }}</td>
              <td
                class="hover_s"
                v-for="(counts, k) in okMy[1]"
                :key="counts.id"
              >
                <v-tab
                  :to="
                    'document-report-my-item/' +
                    '&' +
                    k +
                    '&' +
                    search.from_date +
                    '&' +
                    search.to_date +
                    '&' +
                    status_report
                  "
                  >{{ counts }}</v-tab
                >
              </td>
            </tr>
            <tr v-for="(ok, i) in okd" :key="i">
              <td>
                {{
                  ok[0][0]["firstname_" + $i18n.locale] +
                  " " +
                  ok[0][0]["lastname_" + $i18n.locale] +
                  " " +
                  ok[0][0]["middlename_" + $i18n.locale]
                }}
              </td>
              <td class="hover" v-for="(count, t) in ok[1]" :key="count.id">
                <v-tab
                  :to="
                    'document-report-employee-item/' +
                    ok[0][0].id +
                    '&' +
                    t +
                    '&' +
                    search.from_date +
                    '&' +
                    search.to_date +
                    '&' +
                    status_report
                  "
                  >{{ count }}</v-tab
                >
              </td>
            </tr>

            <tr>
              <td>{{ $t("all_documents") + ":" }}</td>
              <td v-for="summ in okdsumm" :key="summ.id" class="hover">
                <v-tab>{{ summ }}</v-tab>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>

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

      <v-dialog v-model="errormodal" width="300" hide-overlay>
        <v-card color="primary" dark>
          <v-card-text>{{ $t("error date range!") }}</v-card-text>
        </v-card>
      </v-dialog>
    </v-card>
  </div>
</template>

<script>
const axios = require("axios").default;
import TableToExcel from "@linways/table-to-excel";
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data() {
    return {
      search: {
        from_date: "",
        to_date: "",
      },
      okd: [],
      okdsumm: [],
      okdMy: [],
      status_report: 0,
      loading: false,
      errormodal: false,
    };
  },
  computed: {
    user() {
      return this.$store.getters.getUser();
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center" },
        {
          text: this.$t("dealers.name"),
          value: "name",
        },
      ];
    },
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    },
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    },
    getListDr() {
      this.status_report = 1;
      this.getList();
      this.getMyDoc();
    },
    getListMy() {
      this.status_report = 0;
      this.getList();
      this.getMyDoc();
    },
    getAll() {
      this.getList();
      this.getMyDoc();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/document-report-employee", {
          search: this.search,
          status_report: this.status_report,
        })
        .then((response) => {
          this.okd = response.data;

          let a = 0;
          let b = 0;
          let c = 0;
          let d = 0;
          let e = 0;
          let f = 0;
          let g = 0;
          let h = 0;
          let i = 0;

          for (let item in this.okd) {
            a = a + this.okd[item][1][0];
            this.okdsumm[0] = a;
            b = b + this.okd[item][1][1];
            this.okdsumm[1] = b;
            c = c + this.okd[item][1][2];
            this.okdsumm[2] = c;
            d = d + this.okd[item][1][3];
            this.okdsumm[3] = d;
            e = e + this.okd[item][1][4];
            this.okdsumm[4] = e;
            f = f + this.okd[item][1][5];
            this.okdsumm[5] = f;
            g = g + this.okd[item][1][6];
            this.okdsumm[6] = g;
            h = h + this.okd[item][1][7];
            this.okdsumm[7] = h;
            i = i + this.okd[item][1][8];
            this.okdsumm[8] = i;
          }
          this.loading = false;
        })
        .catch((error) => {
          this.errormodal = true;
          console.log(error);
          this.loading = false;
        });
    },
    cartTotalAmount(total) {
      let summ = 0;
      for (let item in total) {
        summ = summ + total[0];
      }
      return summ;
    },
    getMyDoc() {
      // this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/document-report-my", {
          search: this.search,
          status_report: this.status_report,
        })
        .then((response) => {
          this.okdMy = response.data;
          // this.loading = false;
        })
        .catch((error) => {
          this.errormodal = true;
          console.log(error);
          // this.loading = false;
        });
    },
  },
  mounted() {
    this.getList();
    this.getMyDoc();
  },
};
</script>
<style>
.fullHeight {
  height: calc(100% - 10px);
}
.heightFull {
  border-radius: 10px 10px 10px 10px;
}

.hover :hover {
  font-size: 20px;
  color: #0a73bb;
}
.hover_s :hover {
  font-size: 20px;
  color: white;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.headerSearch {
  width: 100%;
  height: 34px;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_search2 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_searchBtn {
  background: #ff9f0e;
  border: 0.2px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px !important;
  height: 34px !important;
  border-radius: 1px;
  width: 25px;
  padding: 0 13px;
}

.filterBtn {
  color: #000;
  font-size: 12px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  border: 1px solid #e6e6e6;
  /* border-right: 0px; */
  border-left: 0px;
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
}
</style>
