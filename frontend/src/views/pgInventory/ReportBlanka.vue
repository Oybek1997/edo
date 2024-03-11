<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Inventarizatsiya hisoboti") + " " }} </span>
        
        <div class="headerTitle" v-if="real_stock">{{ Math.round(Math.round(real_stock) / Math.round(stock_1c) * 10000)/100 }} % bajarildi.</div>
        
        <v-responsive max-width="200" v-if="false">
          <v-select
            :items="[{text:'Asosiy', value:1},{text:'Yordamchi', value:2}]"
            v-model="status"
            label="Status"
            clearable
            @change="getList"
          ></v-select>
        </v-responsive>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" cols="12">
            <v-row class="mx-0">
              <v-col cols="6">
                <!-- <span style="text-align:center">Umumiy</span> -->
                <v-simple-table class="ma-2 doc-template_data-table" dense style="width: 100%; height: 100%; border-radius: 10px">
                  <template v-slot:default>
                    <tbody >
                      <tr>
                        <td style="text-align: right;">Jami</td>
                        <td style="text-align: left;">{{ stock_1c.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ").split(".")[0] }}</td>
                        <td style="text-align: left;"></td>
                        <!-- <td style="text-align: center;">{{ Math.round(real_stock / stock_1c * 100) }} %</td> -->
                      </tr>
                      <tr>
                        <td style="text-align: right;">Kiritilgan</td>
                        <td style="text-align: left;">{{ real_stock.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ").split(".")[0] }}</td>
                        <td style="text-align: left;">{{ Math.round(real_stock / stock_1c * 100) }} %</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">Kiritilmagan</td>
                        <td style="text-align: left;">{{ (stock_1c - real_stock).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ").split(".")[0] }}</td>
                        <td style="text-align: left;">{{ 100 - Math.round(real_stock / stock_1c * 100) }} %</td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-col>
              <v-col  cols="6" style="max-width: 30%;">
                    <PieChart v-if="chartData1" :data="chartData1" />                
              </v-col>
            </v-row>  
        </v-col>
        <v-col class="ma-0 pa-0" cols="12">               
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
                items.map(x => x.code).indexOf(item.code) + 1
              }}
            </template>
            <template v-slot:item.wh_number="{ item }">
              {{
                item.wh_number ? item.wh_number : 'Ombor biriktirilmagan'
              }}
            </template>
            <template v-slot:item.stock="{ item }">
              {{
                item.stock ? item.stock : '0'
              }}
            </template>
            <template v-slot:item.fact="{ item }">
              {{
                item.fact ? item.fact : 0
              }}
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
      chartData3: null,
      form: {
        id: 0,
        address_name: null,
        warehouse_id: null,
      },
      status:null,
      stock_1c: 0,
      real_stock: 0,
      allAsaka: 0,
      realAsaka: 0,
      allXorazm: 0,
      realXorazm: 0,
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
      inventoryData: [],
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      page: 1,
      from: 0,
      server_items_length: -1,
      errorEmpMessage: true,
      summ: {
        qoldiq: 0,
        topildi: 0,
      },
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
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
        //   text: this.$t("Ombor"),
        //   value: "name",
        //   width: "800",
        //   align: "left",
        // },
        {
          text: this.$t("Ombor"),
          value: "code",
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
        //   text: this.$t("Blanka Soni"),
        //   value: "blanka_soni",
        //   align: "right",
        // },
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
    getList() {
      this.loading = true;
      let all = 0;
      let fact = 0;
      let allAsaka = 0;
      let factAsaka = 0;
      let allXorazm = 0;
      let factXorazm = 0;
      axios
        .get(this.$store.state.backend_url + "api/pg_inventory/get-status-blanka")
        .then((response) => {
          this.items = response.data;
          response.data.forEach(element => {
            // let k = element.fact ? element.fact.split(".")[0] : 0;
            all += parseInt(element.stock?element.stock:0);
            fact += parseInt(element.fact ? element.fact: 0);
          });
          this.stock_1c = all;
          this.real_stock = fact;

          this.chartData1 = {
            labels: ['Kiritilgan','Kiritilmagan'],
            datasets: [
              {
                backgroundColor: ["#2ed557", "#ff4f16", "grey","teal", "indigo"],
                data: [
                  Math.round(this.real_stock / this.stock_1c * 100),
                  100-(Math.round(this.real_stock / this.stock_1c * 100)),
                ],
              },
            ],
          };
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
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
  width: 20%;
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
  height: 30px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>