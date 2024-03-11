<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      
        <!-- <span :to="'/okd-report-full-toshkent'" target="_blank">OKD</span> -->
        <v-card-title class="px-4 py-3">
          <span class="headerTitle mb-2">{{ $t("OKD") }}</span>
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
              @click="getList()"
            >
              <v-icon color="info" center>mdi-magnify</v-icon>
            </v-btn>
            <v-autocomplete
            class="txt_search2"
              v-model="search.incoming_journal_id"
              :placeholder="'Kiruvchi xujjat jurnallari'"
              :items="incoming_journal"
              item-value="id"
              :item-text="'name_' + $i18n.locale"
              dense
              hide-details
              solo
              multiple
            ></v-autocomplete>
            <v-btn
              class="filterBtn px-2"
              style="background: #fff; height: 34px"
              :to="'/documentsidebar/mailing/list'"
            >
              <v-icon color="info" center>mdi-cog</v-icon>
            </v-btn>
            <v-btn
              class="filterBtn px-2"
              style="background: #fff; height: 34px"
              @click="uploadExcel('table', 'Lorem Table')"
            >
            {{ $t("Excel") }}
              <v-icon color="green" right>mdi-download-multiple</v-icon>
            </v-btn>
            <v-btn
              class="filterBtn px-2"
              style="background: #fff; height: 34px"
              :to="'okd-report-full-toshkent'"
            >
            {{ $t("Tashkent OKD") }}
              <v-icon color="green" right>mdi-cog</v-icon>
            </v-btn>
            <!-- ////////////////////////// -->            
          </div>
        </v-card-title>
   
      <v-simple-table id="table" class="mainTable" dense fixed-header>
        <template v-slot:default>
          <tbody style="text-align: center">
            <tr>
              <td rowspan="3" style="color: #0b198f">
                {{ $t("rep.uz_ak_name") }}
              </td>
              <td rowspan="3" style="color: #0b198f">
                {{ $t("rep.params_0") }}
              </td>
              <td colspan="8" style="color: #0b198f">
                {{ $t("rep.done_from_them") }}
              </td>
              <td rowspan="3" style="color: #0b198f">
                {{ $t("Cancels Document") }}
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
              <td
                class="hover_color"
                rowspan="2"
                style="color: #0b198f; background-color: #b0c4de"
              >
                <v-tab
                  style="font-size: 10px"
                  :to="
                    'okd-report-tab-full/' +
                    8 +
                    '&' +
                    search.from_date +
                    '&' +
                    search.to_date +
                    '&' +
                    search.controlDoc
                  "
                  target="_blank"
                  >{{ $t("rep.params_8") }}
                </v-tab>
              </td>
            </tr>
            <tr>
              <td
                class="hover_color"
                style="color: #0b198f; background-color: #b0c4de"
              >
                <v-tab
                  style="font-size: 10px"
                  :to="
                    'okd-report-tab-full/' +
                    2 +
                    '&' +
                    search.from_date +
                    '&' +
                    search.to_date +
                    '&' +
                    search.controlDoc
                  "
                  target="_blank"
                  >{{ $t("rep.params_2") }}
                </v-tab>
              </td>

              <td
                class="hover_color"
                style="color: #0b198f; background-color: #b0c4de"
              >
                <v-tab
                  style="font-size: 10px"
                  :to="
                    'okd-report-tab-full/' +
                    3 +
                    '&' +
                    search.from_date +
                    '&' +
                    search.to_date +
                    '&' +
                    search.controlDoc
                  "
                  target="_blank"
                  >{{ $t("rep.params_3") }}
                </v-tab>
              </td>
              <td style="color: #0b198f">{{ $t("rep.params_5") }}</td>
              <td style="color: #0b198f">{{ $t("rep.params_6") }}</td>
              <td style="color: #0b198f">{{ $t("rep.params_7") }}</td>
            </tr>
          </tbody>
          <tbody v-for="(ok, i) in okd" :key="i">
            <tr>
              <!-- <td>{{ ok[0][0]["name_" + $i18n.locale] }}</td> -->
              <!-- <td class="hover"> -->
              <td>
                <!-- <v-tab              
                :to="
                  'okd-report-item-full/' +
                  ok.id +
                  '&' +
                  t +
                  '&' +
                  search.from_date +
                  '&' +
                  search.to_date +
                  '&' +
                  search.incoming_journal_id
                "
                target="_blank"
                > -->
                <!-- <template>
                  <v-text-field style="text-align: left;">
                    <span>
                      {{ ok.shtati }}
                    </span>
                  </v-text-field>
                </template>
                </v-tab> -->
                {{ ok.shtati }}
              </td>
              <td>
                <v-tab
                  :to="
                    'report-item-full/' +
                    'all_doc' +
                    '&' +
                    ok.staff_id +
                    '&' +
                    search.from_date +
                    '&' +
                    search.to_date
                  "
                  target="_blank"
                >
                  {{ ok.all_doc }}
                </v-tab>
              </td>
              <td>{{ ok.all_ok }}</td>
              <td>{{ ok.all_ok_in }}</td>
              <td>{{ ok.all_ok_out }}</td>
              <td>{{ ok.all_execution }}</td>
              <td>{{ ok.execution_one }}</td>
              <td>{{ ok.execution_two }}</td>
              <td>{{ ok.execution_three }}</td>
              <td>{{ ok.execution_out }}</td>
              <td>{{ ok.cancel }}</td>
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
          <v-card-text>
            <!--          {{ // $t("errormodal") }}-->
            {{ $t("error date range!") }}
            <!--          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>-->
          </v-card-text>
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
        from_date: moment().subtract(1, "months").format("YYYY-MM-DD"),
        to_date: moment(new Date()).format("YYYY-MM-DD"),
        // from_date: moment().startOf("month").format("YYYY-MM-DD"),
        // to_date: moment(new Date()).format("YYYY-MM-DD"),
        controlDoc: null,
        incoming_journal_id: "",
      },
      okd: [],
      incoming_journal: [],
      types: [
        { text: this.$t("document.all"), value: 1 },
        { text: this.$t("department.incoming"), value: 2 },
        { text: this.$t("department.order"), value: 3 },
        { text: this.$t("department.appeal"), value: 4 },
      ],
      loading: false,
      errormodal: false,
    };
  },
  computed: {
    headers() {
      return [
        { text: "#", value: "id", align: "center" },
        {
          // text: "Organization name",
          text: this.$t("dealers.name"),
          value: "name",
        },
      ];
    },
  },
  methods: {
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    },
    getControlDoc($event) {
      // this.controlDoc = $event;
      // console.log($event);
      this.getList();
    },
    getDoc() {
      this.search.controlDoc = null;
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/report-full", {
          search: this.search,
        })
        .then((response) => {
          // console.log(response.data)
          // if (response.data = 500){
          //   this.errormodal = true;
          // }else{
          this.okd = response.data;
          // }
          // this.staffs = response.data;

          this.loading = false;
        })
        .catch((error) => {
          this.errormodal = true;
          console.log(error);
          this.loading = false;
        });
    },
    getTableList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/directories", {
          pagination: "",
          search: "",
          filter: 1,
        })
        .then((response) => {
          this.incoming_journal = response.data.directories.data;
          // console.log(this.incoming_journal);
        })
        .catch((error) => {
          this.errormodal = true;
          console.log(error);
        });
    },
  },
  mounted() {
    this.getList();
    this.getTableList();
  },
};
</script>
<style scoped>
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
.hover_color :hover {
  color: rgb(0, 0, 0);
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
