<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span>{{ $t("Inventarizatsiya") }}</span>

        <v-spacer></v-spacer>

        <div v-if="$store.getters.checkRole('inventory_operator')">
          <span>{{ $t("Soni: ") }}</span>

          <v-btn class="mx-1" outlined @click="changeProductsCount(1)" small
            >1</v-btn
          >

          <v-btn class="mx-1" outlined @click="changeProductsCount(2)" small
            >2</v-btn
          >

          <v-btn class="mx-1" outlined @click="changeProductsCount(3)" small
            >3</v-btn
          >

          <v-btn class="mx-1" outlined @click="changeProductsCount(4)" small
            >4</v-btn
          >

          <v-btn class="mx-1" outlined @click="changeProductsCount(5)" small
            >5</v-btn
          >

          <v-btn class="mx-1" outlined @click="changeProductsCount(6)" small
            >6</v-btn
          >
        </div>
      </v-card-title>

      <v-card-text
        class="pb-1"
        v-if="$store.getters.checkRole('inventory_operator')"
      >
        <v-form ref="inventorCreateForm">
          <v-row class="ma-0">
            <v-col class="pa-1 inventory_input" cols="2">
              <v-autocomplete
                :label="$t('Komissiya')"
                class="pa-0"
                v-model="formInv.inventory_commission_id"
                @change="getWarehouses"
                :items="commissions"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
                id="inventory_commission_id"
              >
              </v-autocomplete>
            </v-col>

            <v-col class="pa-1 inventory_input" cols="6">
              <v-autocomplete
                :label="$t('Ombor')"
                class="pa-0"
                v-model="formInv.warehouse_id"
                :items="
                  formInv.inventory_commission_id
                    ? warehouses.filter((v) =>
                        commissions

                          .find(
                            (c) => c.value == formInv.inventory_commission_id
                          )

                          .warehouse_ids.includes(v.id)
                      )
                    : []
                "
                item-value="id"
                item-text="name"
                :auto-select-first="true"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                :disabled="formInv.inventory_commission_id ? false : true"
                outlined
                id="idWarehouses"
              >
                <template v-slot:item="{ item }">
                  <div class="display-flex">
                    <span class="font-weight-black">{{ item.name }}</span
                    ><br />

                    <span class="">{{ item.code }}</span>
                  </div>
                </template>
                <template v-slot:selection="{ item }">
                  <div class="display-flex">
                    <span class="">{{ item.name }}</span
                    ><br />

                    <span class="">{{ item.code }}</span>
                  </div>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col class="pa-1 inventory_input" cols="2">
              <v-text-field
                :label="$t('Blanka sanasi')"
                v-model="formInv.blank_date"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                outlined
                type="date"
                id="blank_date"
                dense
              ></v-text-field>
            </v-col>
            <v-col class="pa-1 inventory_input" cols="2">
              <v-text-field
                :label="$t('Blanka nomeri')"
                v-model="formInv.blank_number"
                :rules="status == 1 ? [(v) => !!v || $t('input.required')] : []"
                hide-details
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col class="pa-1 inventory_input" cols="3">
              <v-text-field
                :label="$t('Part nomeri')"
                v-model="formInv.part_number"
                @change="getProduct()"
                :rules="[
                  (v) => !!v || $t('input.required'),
                  (v) =>
                    (v && v.length > 7) || 'Part NO 8 hona bo\'lishi shart!!!',
                  (v) =>
                    (v && v.length < 15) || 'Part NO 8 hona bo\'lishi shart!!!',
                ]"
                hide-details="auto"
                max="8"
                outlined
                :disabled="formInv.warehouse_id ? false : true"
                dense
                id="part_number"
              ></v-text-field>
            </v-col>
            <v-col class="pa-1 inventory_input" cols="6">
              <v-text-field
                :label="$t('Material(butlovchi qism)')"
                v-model="formInv.product_name"
                hide-details
                :disabled="true"
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col class="pa-1 inventory_input" cols="3">
              <v-text-field
                disabled
                :label="$t('O\'lchov')"
                class="pa-0"
                v-model="formInv.unit_measure"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
                id="unit_measure"
              >
              </v-text-field>
              <v-autocomplete
                v-if="false"
                :label="$t('O\'lchov')"
                class="pa-0"
                v-model="formInv.unit_measure"
                :items="unitMeasures"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
                id="unit_measure"
              >
              </v-autocomplete>
            </v-col>
          </v-row>
          <v-row class="ma-0">
            <v-col
              cols="4"
              class="pa-1"
              v-for="(product, index) in formInv.products"
              :key="index"
            >
              <v-card class="pa-1" outlined>
                <v-card-text>
                  <v-row class="ma-0">
                    <!-- v-if="
                        [1,2,3,4,5,6,7,8,9,10,11,12,13, 113, 319,320].includes(
                          formInv.inventory_commission_id
                        )
                      " -->
                    <v-col class="py-0 pr-1 inventory_input">
                      <v-autocomplete
                        :label="$t('Adres ') + (index + 1)"
                        class="pa-0"
                        clearable
                        v-model="product.inventory_address_id"
                        :items="inventoryAddresses"
                        :rules="[
                          (v) =>
                            !!product.manual_address ||
                            product.inventory_address_id ||
                            $t('input.required'),
                        ]"
                        hide-details
                        dense
                        @change="changeAddress(product.inventory_address_id)"
                        outlined
                      >
                      </v-autocomplete>
                    </v-col>
                    <v-col class="py-0 pl-1 inventory_input">
                      <v-text-field
                        :label="$t('Ручной адрес ') + (index + 1)"
                        v-model="product.manual_address"
                        :rules="[
                          (v) =>
                            !!product.manual_address ||
                            product.inventory_address_id ||
                            $t('input.required'),
                        ]"
                        hide-details
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col class="py-0 pl-1 inventory_input" cols="5">
                      <v-text-field
                        :label="$t('Aniqlangan soni ') + (index + 1)"
                        v-model="product.real_stock"
                        :rules="[(v) => !!v || $t('input.required')]"
                        hide-details
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
          <v-row class="mx-0 mt-3">
            <v-btn 
                  color="#3FCB5D"
                  right
                  small
                  dark
                  @click="save()"
                  elevation="0"
                  style="text-transform: none; border-radius: 5px; padding: 5px 20px"
                  >Saqlash</v-btn>
            <v-spacer></v-spacer>
            <v-btn
              outlined
              @click="
                getInventoryExcel(1);
                inventory_excel = [];
              "
              class="mr-2"
              color="success"
              small
              dark
              elevation="0"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            >
              <!-- <span style="color: #4caf50">MC EXCEL</span> -->
              Excelga yuklab olish1
            </v-btn>
            <v-btn
              outlined
              @click="
                getSapExcel(1);
                inventory_excel = [];
              "
              class="mr-2"
              color="success"
              small
              dark
              elevation="0"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            >
              <!-- <span style="color: #4caf50">MC EXCEL</span> -->
              SAP FILE
            </v-btn>
          </v-row>
        </v-form>
      </v-card-text>
      <v-row class="mx-0 mt-4">
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
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense">
                  <v-autocomplete
                    class="py-0"
                    clearable
                    v-model="filter.warehouse_id"
                    :items="warehouses"
                    dense
                    hide-details
                    item-value="id"
                    @change="getList()"
                  >
                    <template v-slot:selection="{ item }"
                      >{{ item.name }}
                    </template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title v-text="item.name"></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0 dense">
                  <v-autocomplete
                    class="py-0"
                    clearable
                    v-model="filter.commission_id"
                    :items="commissions"
                    dense
                    hide-details
                    @change="getList()"
                  >
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.blank_date"
                    type="date"
                    hide-details
                    @keyup.native.enter="getList()"
                    clearable
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.blank_number"
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
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.real_stock"
                    hide-details
                    @keyup.native.enter="getList()"
                    clearable
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-autocomplete
                    clearable
                    v-model="filter.inventory_address_id"
                    :items="inventoryAddresses"
                    hide-details
                    dense
                    @change="getList()"
                  >
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.created_employee"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                    clearable
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.checked_employee"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                    clearable
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense"></td>
              </tr>
            </template>
            <template v-slot:item="{ item }">
              <tr :class="item.blank_status == 1 ? '' : 'red lighten-4 '">
                <td>
                  {{
                    items
                      .map(function (x) {
                        return x.id;
                      })
                      .indexOf(item.id) + from
                  }}
                </td>

                <td style="max-width: 200px;">{{ item.warehouse.name }}<br />{{ item.warehouse.code }}</td>

                <td style="max-width: 70px;">{{ item.commission.commission_number }}</td>

                <td style="max-width: 70px;">{{ item.blank_date }}</td>

                <td style="max-width: 70px;">{{ item.blank_number }}</td>

                <td
                  style="max-width: 70px;"
                  :style="
                    item.product && item.product.with_error
                      ? 'background-color:#F00; color:white;'
                      : ($store.getters.checkRole('inventory_blank_status_change') ? 'font-weight:bold;' : '')
                  "
                >
                  {{ item.part_number }}
                </td>

                <td style="max-width: 70px;" :style="$store.getters.checkRole('inventory_blank_status_change') ? '' : ''"><b>{{ item.real_stock}}</b> ({{item.unit_measure}})</td>

                <td
                  style="max-width: 70px;"
                  :style="
                    item.address && item.address.id == 27779
                      ? 'background-color:#F00; color:white;'
                      : ($store.getters.checkRole('inventory_blank_status_change') ? '' : '')
                  "
                >
                  {{ item.address ? item.address.address_name : "" }}<br />
                  {{ item.manual_address ? item.manual_address : "" }}
                </td>

                <td style="max-width: 70px;">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <span v-bind="attrs" v-on="on">{{
                        item.created_employee
                      }}</span>
                    </template>
                    <span>
                      {{ item.created_at }}
                    </span>
                  </v-tooltip>
                </td>
                <td style="max-width: 70px;">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <span v-bind="attrs" v-on="on">{{
                        item.checked_employee
                      }}</span>
                    </template>
                    <span>{{ item.checked_at }}</span>
                  </v-tooltip>
                </td>
                <td style="max-width: 10  0px;">
                  <v-btn
                    v-if="
                      $store.getters.checkRole('edit_inventory') ||
                      ($store.getters.checkRole('inventory_operator') &&
                        !item.checked_at &&
                        item.blank_status == 1)
                    "
                    class="px-0"
                    color="blue"
                    style="min-width: 25px"
                    small
                    icon
                    @click="editItem(item)"
                  >
                    <v-icon size="18">mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="
                      $store.getters.checkRole('inventory_controller') &&
                      !item.checked_at &&
                      item.blank_status == 1
                    "
                    class="px-0"
                    color="green"
                    style="min-width: 25px"
                    small
                    icon
                    @click="check(item)"
                  >
                    <v-icon size="18">mdi-check</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="
                      $store.getters.checkRole('inventory_blank_status_change') &&
                      item.blank_status == 1
                    "
                    class="pl-0 pr-2"
                    color="red"
                    style="min-width: 25px"
                    small
                    icon
                    @click="changeStatusItem(item)"
                  >
                    <v-icon size="18">mdi-close</v-icon>
                  </v-btn>
                </td>
              </tr>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>

    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="py-1 px-3">
          <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="inventory_excel"
              :name="'Inv_ruyxati_' + today + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
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
      formInv: {
        id: 0,
        blank_number: null,
        blank_date: moment().format("YYYY-MM-DD"),
        unit_measure: "sht",
        warehouse_id: null,
        product_count: 6,
        part_number: null,
        commission_id: null,
        product_name: null,
        products: [
          {
            real_stock: null,
            inventory_address_id: null,
            manual_address: null,
          },
          {
            real_stock: null,
            inventory_address_id: null,
            manual_address: null,
          },
          {
            real_stock: null,
            inventory_address_id: null,
            manual_address: null,
          },
          {
            real_stock: null,
            inventory_address_id: null,
            manual_address: null,
          },
          {
            real_stock: null,
            inventory_address_id: null,
            manual_address: null,
          },
          {
            real_stock: null,
            inventory_address_id: null,
            manual_address: null,
          },
        ],
      },
      warehouses: [],
      inventoryAddresses: [],
      commissions: [],
      search: "",
      status: null,
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
      warehouses: [],
      selectedProduct: null,
      inventory_excel: [],
      downloadExcel: false,
      today: moment().format("YYYY-MM-DD"),
    };
  },
  computed: {
    newAddressess() {
      let arr = [
        {
          text: "Xato adres",
          value: 27779,
        },
      ];
      return arr.concat(
        this.selectedProduct ? this.selectedProduct.addresses : []
      );
    },
    screenHeight() {
      return window.innerHeight - 250;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30, sortable: false },
        {
          text: this.$t("Ombor"),
          value: "warehouse.name",
          sortable: false,
        },
        {
          text: this.$t("Komissiya"),
          value: "commission.commission_number",
          sortable: false,
        },
        {
          text: this.$t("Blanka sanasi"),
          value: "blank_date",
          sortable: false,
        },
        {
          text: this.$t("Blanka raqami"),
          value: "blank_number",
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
          text: this.$t("Adres"),
          value: "inventory_address_id",
          sortable: false,
        },
        {
          text: this.$t("Kiritivchi"),
          value: "created_employee",
          sortable: false,
        },
        {
          text: this.$t("Tasdiqlovchi"),
          value: "checked_employee",
          sortable: false,
        },
        {
          text: "",
          value: "actions",
          width: 30,
          align: "center",
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
        .post(this.$store.state.backend_url + "api/inventory", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          type: 0,
        })
        .then((res) => {
          this.items = res.data.data;
          this.from = res.data.from;
          this.server_items_length = res.data.total;
          this.items.map((v) => {
            v.blank_date = moment(v.blank_date).format("DD.MM.YYYY");
            v.created_at = moment(v.created_at).format("DD.MM.YYYY hh:mm");
            v.checked_at = v.checked_at
              ? moment(v.checked_at).format("DD.MM.YYYY hh:mm")
              : v.checked_at;
          });
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    getRef() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/inventory/get-ref1", {
          status: this.status ? this.status : this.$route.params.status,
        })
        .then((res) => {
          this.warehouses = res.data.warehouses;
          this.inventoryAddresses = res.data.addresses.map((v) => {
            return { value: v.id, text: v.address_name };
          });
          this.commissions = res.data.commissions.map((v) => {
            let warehouse_ids = v.warehouses.map((v) => v.id);
            return {
              value: v.id,
              text: v.commission_number,
              warehouse_id: v.warehouse_id,
              warehouse_ids: warehouse_ids,
            };
          });
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    getProduct() {
      if (this.formInv.part_number && this.formInv.part_number.length > 7) {
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
            this.formInv.product_name = res.data.product_name;
            this.formInv.unit_measure = res.data.unit_measure;
            this.selectedProduct = res.data;
            document.getElementById("part_number").focus();
            if (res.data.with_error) {
              Swal.fire({
                icon: "error",
                title: "Maxsulot topilmadi!",
              }).then(function () {
                //Confirmed
                document.getElementById("part_number").focus();
              });
              // this.formInv.part_number = "";
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
    getWarehouses($event) {
      let selectedComission = this.commissions.find((v) => v.value == $event);
      if (selectedComission) {
        this.formInv.warehouse_id = selectedComission.warehouse_id;
      }
      document.getElementById("blank_date").focus();
    },
    getInventoryExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/inventory", {
          filter: this.filter,
          type: 1,
          pagination: {
            page: page,
            itemsPerPage: 1000,
          },
        })
        .then((response) => {
          response.data.data.map((v, index) => {
            new_array.push({
              "№": index + page,
              Ombor: v.warehouse.name,
              Code: v.warehouse.code,
              Komissiya: v.commission.commission_number,
              "Komissiya turi":
                v.commission.status == 1
                  ? "Asosiy"
                  : v.commission.status == 2
                  ? "Yordamchi"
                  : "Asosiy va yordamchi",
              "Blanka sanasi": moment(v.blank_date).format("DD.MM.YYYY"),
              "Blanka raqami": v.blank_number,
              "Part NO": v.part_number,
              "Part status":
                v.product && v.product.with_error ? "Xato Part nomer" : "",
              "Aniqlangan soni": v.real_stock,
              "O'lchov birligi": v.unit_measure,
              Adres: v.address ? v.address.address_name : "",
              Container: v.manual_address ? v.manual_address : "",
              Kiritunchi: v.created_employee,
              "Kiritilgan vaqt": moment(v.created_at).format(
                "DD.MM.YYYY hh:mm"
              ),
              Tasdiqlovchi: v.checked_employee,
              "Tasqidlangan vaqt": v.checked_at
                ? moment(v.checked_at).format("DD.MM.YYYY hh:mm")
                : "",
              Tasdiqlovchi: v.checked_employee,
              "Blanka holati": v.blank_status ? "" : "Bekor qilindi",
            });
          });
          // new_array = response.data.data;

          this.inventory_excel = this.inventory_excel.concat(new_array);
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
    getSapExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/inventory", {
          filter: this.filter,
          type: 2,
          pagination: {
            page: page,
            itemsPerPage: 1000,
          },
        })
        .then((response) => {
          this.inventory_excel = this.inventory_excel.concat(
            response.data.data.map((v, index) => ({
              Code: v.warehouse.code,
              Komissiya: v.commission.commission_number,
              part_no: v.gm ? "GM" + v.part_number : v.part_number,
              gm: v.gm,
              aniqlangan_soni: v.real_stock,
              ulchov_birligi: v.unit_measure,
            }))
          );
          // new_array = response.data.data;

          // this.inventory_excel = this.inventory_excel.concat(new_array);
          if (response.data.data.length == 1000) {
            this.getSapExcel(++page);
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
      this.formInv.products[0].manual_address = item.manual_address;
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
              // Swal.fire(
              //   "Tasdiqlandi!",
              //   "Siz ushbu ma'lumotni tasdiqladingiz.",
              //   "success"
              // );
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
    changeStatusItem(item) {
      Swal.fire({
        title: "Blanka qatorini bekor qilish?",
        text: "Ushbu Blanka raqamiga tegishli tanlangan qator ma'lumotlari bekor qilinadi.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ha!",
        cancelButtonText: "Yo'q!",
      }).then((result) => {
        if (result.isConfirmed) {
          axios
            .post(
              this.$store.state.backend_url + "api/inventory/change-status",
              {
                id: item.id,
              }
            )
            .then((res) => {
              if (res.data == 200) {
                Swal.fire("Bekor qilindi!", "", "success");
                this.getList();
              } else {
                Swal.fire("Ma'lumot topilmadi!", "", "error");
              }
            })
            .catch((err) => {
              Swal.fire("Xatolik!", "Serverda hatolik bor.", "error");
            });
        }
      });
    },
  },
  watch: {
    $route(to, from) {
      this.status = to.params.status;
      this.getRef();
    },
  },
  mounted() {
    this.status = this.$route.params.status;
    this.getRef();
    this.getList();
  },
};
</script>
<style scoped>
/* .v-content .v-card.v-sheet.theme--light {
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
} */
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
