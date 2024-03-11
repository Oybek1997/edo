<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("Hisobot (Part nomer bo'yicha)") }}</span>
        <v-spacer></v-spacer>
        <v-btn
          outlined
          small
          @click="
            getInventoryExcel(1);
            items_excel = [];
          "
          class="mr-2"
          color="success"
        >
          <!-- <span style="color: #4caf50">MC EXCEL</span> -->
          Excelga yuklab olish
        </v-btn>
      </v-card-title>
      <v-card-text
        class="pb-1"
        v-if="
          true || $store.getters.checkRole('inventory_report')
        "
      >
        <v-data-table
          dense
          fixed-header
          :loading-text="$t('loadingText')"
          :no-data-text="$t('noDataText')"
          :height="screenHeight + 75"
          :loading="loading"
          :headers="headers"
          :items="items"
          :search="search"
          class="mainTable ma-1"
          style="border: 1px solid #aaa"
          item-key="id"
          :server-items-length="server_items_length"
          :options.sync="dataTableOptions"
          :disable-pagination="true"
          :footer-props="{
            itemsPerPageOptions: [20, 50, 100],
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
          <template v-slot:body.prepend>
            <tr class="py-0 my-0 prepend_height">
              <td class="py-0 my-0 dense">
                <v-autocomplete
                  class="py-0"
                  clearable
                  v-model="filter.warehouse_id"
                  :items="filter_warehouses"
                  dense
                  hide-details
                  item-value="id"
                  @change="getList()"
                >
                  <template v-slot:selection="{ item }">{{
                    item.name
                  }}</template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </td>
              <td class="py-0 my-0 dense">
                <v-text-field
                  v-model="filter.part_name"
                  hide-details
                  @keyup.native.enter="getList()"
                  clearable
                ></v-text-field>
              </td>
              <td class="py-0 my-0 dense">
                <v-text-field
                  v-model="filter.part_number"
                  hide-details
                  @keyup.native.enter="getList()"
                  clearable
                ></v-text-field>
              </td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </template>
          <template v-slot:item.bajarildi="{ item }">
            <span
              :style="
                (item.real_stock > 0 && item['stock_1c'] > 0
                  ? ((100 * item.real_stock) / item['stock_1c']).toFixed(2)
                  : '') > 100
                  ? 'background-color:#fcc; display:block;'
                  : ''
              "
            >
              {{
                item.real_stock > 0 && item["stock_1c"] > 0
                  ? ((100 * item.real_stock) / item["stock_1c"]).toFixed(2)
                  : ""
              }}
            </span>
          </template>
          <template v-slot:item.real_stock="{ item }">
            {{ item.real_stock > 0 ? parseInt(item.real_stock).toFixed(0) : "" }}
          </template>
          <template v-slot:item.stock_1c="{ item }">
            {{ item["stock_1c"] > 0 ? parseInt(item["stock_1c"]).toFixed(0) : "" }}
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>

    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-title class="py-1 px-3">
          <v-btn
            color="success"
            class=""
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="items_excel"
              :name="'Inv_ruyxati_' + today + '.xls'"
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
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import Template from "../document/Template.vue";
export default {
  components: { Template },
  data() {
    return {
      warehouses: [],
      inventoryAddresses: [],
      commissions: [],
      search: "",
      isLoading: false,
      loading: false,
      items: [],
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      errorEmpMessage: true,
      unitMeasures: ["sht", "kg", "litr", "metr", "p.metr"],
      datatime: "",
      filter: {},
      filter_warehouses: [],
      inventory_excel: [],
      downloadExcel: false,
      today: moment().format("YYYY-MM-DD"),
      items_excel: [],
      downloadExcel: false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 250;
    },
    headers() {
      return [
        {
          text: this.$t("Ombor"),
          value: "warehouse.name",
          sortable: false,
        },
        {
          text: this.$t("Tovar"),
          value: "product_name",
          sortable: false,
        },
        {
          text: this.$t("Part nomer"),
          value: "part_number",
          sortable: false,
        },
        {
          text: this.$t("Aniqlangan soni"),
          value: "real_stock",
          align: "right",
          sortable: false,
        },
        {
          text: this.$t("1C dagi soni"),
          value: "stock_1c",
          align: "right",
          sortable: false,
        },
        {
          text: this.$t("Bajarildi (%)"),
          value: "bajarildi",
          align: "right",
          sortable: false,
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
        .post(this.$store.state.backend_url + "api/inventory/report1", {
          pagination: this.dataTableOptions,
          filter: this.filter,
        })
        .then((res) => {
          this.items = res.data.data;
          this.from = res.data.from;
          this.server_items_length = res.data.total;
          // this.items.map((v) => {
          //   v.blank_date = moment(v.blank_date).format("DD.MM.YYYY");
          //   v.created_at = moment(v.created_at).format("DD.MM.YYYY hh:mm");
          //   v.checked_at = v.checked_at
          //     ? moment(v.checked_at).format("DD.MM.YYYY hh:mm")
          //     : v.checked_at;
          // });
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          // this.loading = false;
        });
    },
    getRef() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/inventory/get-ref")
        .then((res) => {
          this.filter_warehouses = res.data.warehouses;
          this.inventoryAddresses = res.data.addresses.map((v) => {
            return { value: v.id, text: v.address_name };
          });
          this.commissions = res.data.commissions.map((v) => {
            return { value: v.id, text: v.commission_number };
          });
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    getProduct() {
      if (this.formInv.part_number && this.formInv.part_number.length == 8) {
        this.loading = true;
        axios
          .get(
            this.$store.state.backend_url +
              "api/inventory/get-part/" +
              this.formInv.part_number +
              "/" +
              this.formInv.warehouse_id
          )
          .then((res) => {
            if (res.data.id) {
              this.formInv.product_name = res.data.product_name;
              document.getElementById("unit_measure").focus();
            } else {
              Swal.fire({
                icon: "error",
                title: "Maxsulot topilmadi!",
              });
              this.formInv.part_number = "";
              document.getElementById("part_number").focus();
            }
            this.loading = false;
          })
          .catch((err) => {
            console.error(err);
            this.errorEmpMessage = false;
            this.loading = false;
          });
      }
    },
    getAddresses() {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url +
            "api/inventory/get-address/" +
            this.formInv.warehouse_id
        )
        .then((res) => {
          this.inventoryAddresses = res.data.map((v) => {
            return { value: v.id, text: v.address_name };
          });
          this.loading = false;
        })
        .catch((err) => {
          console.error(err);
          this.loading = false;
        });
    },
    getWarehouses() {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url +
            "api/inventory/get-warehouses/" +
            this.formInv.inventory_commission_id
        )
        .then((res) => {
          this.warehouses = res.data;
          this.loading = false;
          document.getElementById("inventory_commission_id").focus();
        })
        .catch((err) => {
          console.error(err);
          this.loading = false;
        });
    },
    getInventoryExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/inventory/report1", {
          filter: this.filter,
          pagination: {
            page: page,
            itemsPerPage: 1000,
          },
        })
        .then((response) => {
          response.data.data.map((v, index) => {
            new_array.push({
              "№": index + page*1000-999,
              Ombor: v.warehouse.name,
              Tovar: v.product_name,
              "Part NO": v.part_number,
              "Aniqlangan soni": v.real_stock ? v.real_stock : '',
              "1C dagi soni": v["stock_1c"] ? v["stock_1c"] : "",
              "Bajarildi (%)":
                v.real_stock > 0 && v["stock_1c"] > 0
                  ? ((100 * v.real_stock) / v["stock_1c"]).toFixed(3)
                  : "",
            });
          });
          // new_array = response.data.data;

          this.items_excel = this.items_excel.concat(new_array);
          if (response.data.data.length == 1000) {
            this.getInventoryExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    editItem(item) {
      this.changeProductsCount(1);
      this.formInv.id = item.id;
      this.formInv.product_count = item.id;
      this.formInv.blank_number = item.blank_number;
      this.formInv.warehouse_id = item.warehouse_id;
      this.formInv.part_number = item.part_number;
      this.formInv.inventory_commission_id = item.inventory_commission_id;
      this.formInv.products[0].real_stock = item.real_stock;
      this.formInv.products[0].inventory_address_id = item.inventory_address_id;
      this.getWarehouses();
      this.getProduct();
    },
    check(item) {
      Swal.fire({
        title: "Ma'lumotlar to'g'riligini tasdiqlaysizmi?",
        text: "Tasdiqlaganingizdan so'ng o'zgartirish imkoniyati yo'q.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ha, Tasdiqlayman!",
        cancelButtonText: "Yo'q!",
      }).then((result) => {
        if (result.isConfirmed) {
          axios
            .get(
              this.$store.state.backend_url + "api/inventory/check/" + item.id
            )
            .then((res) => {
              Swal.fire(
                "Tasdiqlandi!",
                "Siz ushbu ma'lumotni tasdiqladingiz.",
                "success"
              );
              item = res.data;
              this.items = this.items.map((v) => {
                if (v.id == item.id) return item;
                else return v;
              });
              this.items.map((v) => {
                v.blank_date = moment(v.blank_date).format("DD.MM.YYYY");
                v.created_at = moment(v.created_at).format("DD.MM.YYYY hh:mm");
                v.checked_at = v.checked_at
                  ? moment(v.checked_at).format("DD.MM.YYYY hh:mm")
                  : v.checked_at;
              });
            })
            .catch((err) => {
              Swal.fire("Xatolik!", "Serverda hatolik bor.", "error");
            });
        }
      });
    },
    save() {
      if (this.$refs.inventorCreateForm.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/inventory/update",
            this.formInv
          )
          .then((res) => {
            if (res.data.code == 100)
              Swal.fire({
                icon: "error",
                title: res.data.message,
              });
            else {
              this.formInv.id = 0;
              this.formInv.part_number = null;
              this.formInv.product_name = null;
              this.formInv.blank_number = null;
              this.changeProductsCount(this.formInv.products.length);
              // this.formInv.products[0].real_stock = null;
              // this.formInv.products[0].inventory_address_id = null;
              // this.formInv.products[1].real_stock = null;
              // this.formInv.products[1].inventory_address_id = null;
              // this.formInv.products[2].real_stock = null;
              // this.formInv.products[2].inventory_address_id = null;
              // this.formInv.products[3].real_stock = null;
              // this.formInv.products[3].inventory_address_id = null;
              // this.formInv.products[4].real_stock = null;
              // this.formInv.products[4].inventory_address_id = null;
              // this.formInv.products[5].real_stock = null;
              // this.formInv.products[5].inventory_address_id = null;
            }
            this.$refs.inventorCreateForm.resetValidation();
            this.getList();
            this.loading = false;
          })
          .catch((err) => {
            console.log(err);
            this.loading = false;
          });
      }
      console.log(this.formInv);
    },
    changeProductsCount(count) {
      this.$refs.inventorCreateForm.resetValidation();
      this.formInv.products = [];
      for (let i = 0; i < count; i++) {
        this.formInv.products.push({
          real_stock: null,
          inventory_address_id: null,
        });
      }
    },
    changeAddress(id) {
      let address = this.inventoryAddresses.filter((v) => {
        if (v.value == id) return v;
      });
      this.inventoryAddresses = this.inventoryAddresses.filter((v) => {
        if (v.value != id) return v;
      });
      this.inventoryAddresses = address.concat(this.inventoryAddresses);

      console.log(address);
    },
  },
  mounted() {
    this.getRef();
    this.getList();
  },
};
</script>
<style scoped>
.v-content .v-card.v-sheet.theme--light {
  background-color: #ffffff !important;
  border-left: 5px solid red !important;
}
.v-item--active {
  background-color: #fff !important;
}
.dense {
  padding: 0px;
  height: 10px !important;
}

.dense .v-text-field__details {
  display: none !important;
}

.dense .v-text-field {
  padding: 0px;
  margin: 0px;
}

.dense div div div {
  margin-bottom: 0px !important;
}
</style>
