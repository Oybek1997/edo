<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("Inventarizatsiya hisoboti") + " " }} </span>
        <v-spacer></v-spacer>
        <div v-if="real_stock">{{ Math.round(Math.round(real_stock+real_stock1) / Math.round(stock_1c+stock_1c1) * 10000)/100 }} % bajarildi.</div>
        <v-spacer></v-spacer>
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
      <v-card-text class="pb-1">
        <v-row>
          <v-col cols="6">
            <v-simple-table class="ma-2">
              <template v-slot:default>
                <tbody>
                  <tr>
                    <td rowspan="3">Asosiy</td>
                  </tr>
                  <tr>
                    <td style="background-color:white !important;">Kiritilgan</td>
                    <td class="text-right">{{ new Intl.NumberFormat(['ban', 'id']).format(Math.round(real_stock)) }}</td>
                    <td>{{ Math.round(real_stock / stock_1c * 100) }} %</td>
                  </tr>
                  <tr>
                    <td>Kiritilmagan</td>
                    <td class="text-right">{{ new Intl.NumberFormat(['ban', 'id']).format(Math.round(stock_1c)) }}</td>
                    <td>{{ 100 - Math.round(real_stock / stock_1c * 100) }} %</td>
                  </tr>
                  <tr>
                    <td rowspan="3">Yordamchi</td>
                  </tr>
                  <tr>
                    <td>Kiritilgan</td>
                    <td class="text-right">{{ new Intl.NumberFormat(['ban', 'id']).format(Math.round(real_stock1)) }}</td>
                    <td>{{ Math.round(real_stock1 / (stock_1c1)*100) }} %</td>
                  </tr>
                  <tr>
                    <td>Kiritilmagan</td>
                    <td class="text-right">{{ new Intl.NumberFormat(['ban', 'id']).format(Math.round(stock_1c1)) }}</td>
                    <td>{{ 100 - Math.round(real_stock1 / (stock_1c1)*100) }} %</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-col>
          <v-col cols="3">
            <PieChart v-if="chartData1" :data="chartData1" />
          </v-col>
          <v-col cols="3">
            <PieChart v-if="chartData2" :data="chartData2" />
          </v-col>
        </v-row>
        <v-data-table
          dense
          fixed-header
          :loading-text="$t('loadingText')"
          :no-data-text="$t('noDataText')"
          :height="screenHeight"
          :loading="loading"
          :headers="headers"
          :items="items"
          :search="search"
          style="border: 1px solid #aaa"
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
              items.map(x => x.part_number).indexOf(item.part_number) + from
            }}
          </template>
          <template v-slot:item.actions="{ item }">
            <v-btn color="blue" small text @click="editItem(item)">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn color="red" small text @click="deleteItem(item)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-card-text>
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
        {
          text: this.$t("Part nomer"),
          value: "part_number",
          align: "right",
        },
        {
          text: this.$t("Qoldiq"),
          value: "stock_1c",
          align: "right",
        },
        {
          text: this.$t("Topildi"),
          value: "topildi",
          align: "right",
        },
        {
          text: this.$t("Blanka Soni"),
          value: "blanka_soni",
          align: "right",
        },
        {
          text: this.$t("Bajarildi (%)"),
          value: "bajarildi",
          align: "right",
        },
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
      axios
        .post(this.$store.state.backend_url + "api/inventory/report2", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          status: this.status,
        })
        .then((response) => {
          this.items = response.data[0].data.map(v => {
            v.topildi = v.inventory_blank_lists.reduce(((acc, curr) => acc + (Math.round(curr.real_stock ))) , 0);
            v.bajarildi = Math.round(v.topildi / v.stock_1c * 100);
            v.blanka_soni = v.inventory_blank_lists.length
            return v;
          });
          this.server_items_length = response.data[0].total;
          this.from = response.data[0].from;
          this.stock_1c = response.data[1];
          this.real_stock = response.data[2];
          this.stock_1c1 = response.data[4];
          this.real_stock1 = response.data[5];
          this.chartData1 = {
            labels: ['Kiritilmagan ('+(Math.round(this.stock_1c * 100) / 100 - this.real_stock)+')','Kiritilgan ('+this.real_stock+")"],
            datasets: [
              {
                backgroundColor: ["#77F", "#5f5", "grey","teal", "indigo"],
                data: [
                  Math.round(this.stock_1c * 100) / 100 - this.real_stock,
                  this.real_stock,
                ],
              },
            ],
          };
          this.chartData2 = {
            labels: ['Kiritilmagan ('+(Math.round(this.stock_1c * 100) / 100 - this.real_stock)+')','Kiritilgan ('+this.real_stock+")"],
            datasets: [
              {
                backgroundColor: ["#77F", "#5f5", "grey","teal", "indigo"],
                data: [
                  Math.round(this.stock_1c * 100) / 100 - this.real_stock,
                  this.real_stock,
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
