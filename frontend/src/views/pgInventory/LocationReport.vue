<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Manzillar bo'yicha hisobot") + " " }} </span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            class="txt_search1"
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-responsive max-width="200" v-if="false">
            <v-select
              :items="[{text:'Asosiy', value:1},{text:'Yordamchi', value:2}]"
              v-model="status"
              label="Status"
              clearable
              @change="getList"
            ></v-select>
          </v-responsive>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
            <v-icon color="#00B950" left>mdi-filter-outline</v-icon>Фильтр
          </v-btn>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
              Столбцы <v-icon color="#00B950" right>mdi-checkbox-marked-outline</v-icon>
          </v-btn>
          <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="txt_searchBtn ml-2"
                outlined
                v-bind="attrs"
                v-on="on"
              >
                <v-icon size="18" color="white"
                  >mdi-format-list-bulleted</v-icon
                >
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title
                  @click="
                    getExcel(1);
                    excel = [];
                  "
                  >
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    Скачать таблицу Excel
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu> 
        </div>        
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
            <v-data-table
              class="doc-template_data-table"
              dense
              style="width: 100%; height: 100%; border-radius: 10px"
              fixed-header
              :loading-text="$t('loadingText')"
              :no-data-text="$t('noDataText')"
              :height="screenHeight"
              :loading="loading"
              :headers="headers"
              :items="items"
              :search="search"
              item-key="id"
              :server-items-length="server_items_length"
              :options.sync="dataTableOptions"
              :disable-pagination="true"
              :footer-props="{
                itemsPerPageOptions: [50, 100, 200, 500, 1000, 5000],
                itemsPerPageAllText: $t('itemsPerPageAllText'),
                itemsPerPageText: $t('itemsPerPageText'),
                showFirstLastPage: true,
                firstIcon: 'mdi-arrow-collapse-left',
                lastIcon: 'mdi-arrow-collapse-right',
                prevIcon: 'mdi-arrow-left',
                nextIcon: 'mdi-arrow-right',
              }"
              @update:page="updatePage"
              @update:items-per-page="updatePerPage"
            >
              <template v-slot:item.id="{ item }">
                {{
                  items
                  .map(function (x) {
                    return x.stock;
                  })
                  .indexOf(item.stock) +
                1
                }}
              </template>
              <template v-slot:[`body.prepend`]>
              <tr class="py-0 my-0 prepend_height">
                
                <td class="py-0 my-0 dense" style="width: 50px">
                </td>
                <td class="py-0 my-0 dense" >
                  <v-text-field
                    v-model="filter.address"
                    type="text"
                    hide-details
                    dense
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  
                </td>
              
                <td class="py-0 my-0 dense">
                  
                </td>
              </tr>
            </template>
            </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog v-model="loading" width="300">
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
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card class="pa-5">
        <v-card-title class="py-1 px-3">
          <v-btn
            color="success"
            class=""
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="excel"
              :name="'Location_report' + today + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-title>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import PieChart from "@/components/PieChartInventory";
export default {
  components: {
    PieChart
  },
  data() {
    return {
      chartData1: null,
      chartData2: null,
      form: {
        id: 0,
        address_name: null,
        warehouse_id: null,
      },
      status:null,
      stock_1c: 0,
      real_stock: 0,
      stock_1c1: 0,
      real_stock1: 0,
      checkedReport: 0,
      filter: {},
      warehouses: [],
      pagination: {},
      filter: {},
      search: "",
      isLoading: false,
      loading: false,
      items: [],
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      page: 1,
      from: 0,
      server_items_length: -1,
      errorEmpMessage: true,
      summ: {
        qoldiq: 0,
        topildi: 0,
      },
      today: moment().format("YYYY-MM-DD"),
      excel: [],
      downloadExcel: false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 150;
    },
    headers() {
      return [
        {
          text: "#",
          value: "id",
          align: "center",
          width: 30,
          sortable: false,
        },
        // {
        //   text: this.$t("Address"),
        //   value: "address.address_name",
        // },
        // {
        //   text: this.$t("Joylashuv"),
        //   value: "wh_name",
        //   align: "right",
        // },
        {
          text: this.$t("Manzil"),
          value: "address_name",
          align: "right",
        },
        {
          text: this.$t("Qoldiq"),
          value: "stock",
          align: "right",
        },
        {
          text: this.$t("Topildi"),
          value: "fact",
          align: "right",
        },
        // {
        //   text: this.$t("Bajarildi (%)"),
        //   value: "bajarildi",
        //   align: "right",
        // },
      ];
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getExcel() {
      let new_array = [];
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/pg_inventory/get-location-report",
          {
            filter: this.filter,
            excel : 1
          }
        )
        .then((response) => {
          new_array = response.data;
          // console.log(new_array);
          new_array.forEach(element => {
            element.address_name = this.replaceDashesWithCommas(element.address_name);
          });
          this.excel = this.excel.concat(new_array);
            this.loading = false;
            this.downloadExcel = true;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/pg_inventory/get-location-report",
        {
          filter: this.filter
        })
        .then((response) => {
          this.items = response.data
          // this.server_items_length = response.data[0].total;
          // this.from = response.data[0].from;
          // this.stock_1c = response.data[1];
          // this.real_stock = response.data[2];
          // this.stock_1c1 = response.data[4];
          // this.real_stock1 = response.data[5];
          // this.chartData1 = {
          //   labels: ['Kiritilgan','Kiritilmagan'],
          //   datasets: [
          //     {
          //       backgroundColor: ["#77F", "#5f5", "grey","teal", "indigo"],
          //       data: [
          //         Math.round(this.real_stock / this.stock_1c * 100),
          //         100-(Math.round(this.real_stock / this.stock_1c * 100)),
          //       ],
          //     },
          //   ],
          // };
          // this.chartData2 = {
          //   labels: ['Kiritilgan','Kiritilmagan'],
          //   datasets: [
          //     {
          //       backgroundColor: ["#77F", "#5f5", "grey","teal", "indigo"],
          //       data: [
          //         Math.round(this.real_stock1 / this.stock_1c1 * 100),
          //         100-(Math.round(this.real_stock1 / this.stock_1c1 * 100)),
          //       ],
          //     },
          //   ],
          // };
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    replaceDashesWithCommas(str) {
    // Count the number of dashes in the string
        const dashCount = (str.match(/-/g) || []).length;
        const onlyIntegers = /^[\d-]+$/.test(str);
        // Replace dashes with commas only if there are more than one dash
        if (dashCount == 2 && onlyIntegers) {
            return 'gm' + str;
        }

        return str;
    },
    checkedReportFun() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/inventory/checkedReport")
        .then((response) => {
          this.checkedReport = response.data;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
<style scoped>
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialogTitle {
  color: #000;
  font-size: 16px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.labelTitle {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
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
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > tbody > tr > td {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropdown-list .v-list-item .v-list-item__title {
  color: #000;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialog-head_title {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-dialog > .v-card > .v-card__text {
  padding: 0px 0px 0px 0px;
}
.doc-template_data-table table > tbody > tr > td {
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