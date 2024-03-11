@@ -0,0 +1,461 @@
<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("inventory.product") }}</span>
      </v-card-title>
      <v-card-text class="pb-1" v-show="false">
        <v-form ref="productCreateForm">
          <v-row>
            <v-col class="pa-1" cols="3">
              <v-autocomplete
                :label="$t('Ombor')"
                class="pa-0"
                clearable
                v-model="form.warehouse_id"
                :items="warehouses"
                item-value="id"
                item-text="search"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
              >
                <template v-slot:selection="{ item }">
                  <v-chip color="white" class="pa-1 ma-0">
                    <span v-text="item.name"></span>
                  </v-chip>
                </template>
                <template v-slot:item="{ item }">
                  <v-list-item-content>
                    <v-list-item-title v-text="item.name"></v-list-item-title>
                    <v-list-item-subtitle
                      v-text="item.code"
                    ></v-list-item-subtitle>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col class="pa-1" cols="3">
              <v-text-field
                :label="$t('inventory.partNumber')"
                v-model="form.part_number"
                :rules="[(v) => !!v || $t('input.required')]"
                type="number"
                hide-details
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col class="pa-1" cols="3">
              <v-text-field
                :label="$t('inventory.productName')"
                v-model="form.product_name"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col class="pa-1" cols="3">
              <v-text-field
                :label="$t('inventory.stock')"
                v-model="form.stock"
                type="number"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col class="pa-1" cols="3">
              <v-text-field
                :label="$t('inventory.unitMeasure')"
                v-model="form.unit_measure"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col class="pa-1" cols="3">
              <v-btn color="success" @click="save()">Saqlash</v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
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
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :footer-props="{
          itemsPerPageOptions: [50, 100, 200, -1],
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
                item-value="item.name"
                hide-details
                dense
                @change="getList()"
              >
                <template v-slot:selection="{ item }">{{ item.name }}</template>
                <template v-slot:item="{ item }">
                  <v-list-item-content>
                    <v-list-item-title
                      v-text="item.code + '-' + item.name"
                    ></v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
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
                v-model="filter.product_name"
                hide-details
                @keyup.native.enter="getList()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.stock"
                hide-details
                @keyup.native.enter="getList()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.unit_measure"
                hide-details
                @keyup.native.enter="getList()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense"></td>
          </tr>
        </template>
        <template v-slot:item.id="{ item }">
          {{
            items
              .map(function (x) {
                return x.id;
              })
              .indexOf(item.id) +
            from +
            1
          }}
        </template>
        <template v-slot:item.warehouse_id="{ item }">
          {{ item.warehouse ? item.warehouse.name : "" }}
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
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
export default {
  data() {
    return {
      form: {
        id: 0,
        address_name: null,
        warehouse_id: null,
      },
      filter: {},
      warehouses: [],
      search: "",
      isLoading: false,
      loading: false,
      items: [],
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      errorEmpMessage: true,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 220;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30, sortable: false },
        {
          text: this.$t("Ombor"),
          value: "warehouse.name",
        },
        {
          text: this.$t("inventory.partNumber"),
          value: "part_number",
        },
        {
          text: this.$t("inventory.productName"),
          value: "product_name",
        },
        {
          text: this.$t("inventory.stock"),
          value: "stock",
        },
        {
          text: this.$t("inventory.unitMeasure"),
          value: "unit_measure",
        },
        // {
        //   text: this.$t("Amallar"),
        //   value: "actions",
        //   width: 150,
        //   align: "left",
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
      axios
        .post(this.$store.state.backend_url + "api/inventory-products", {
          pagination: this.dataTableOptions,
          filter: this.filter,
        })
        .then((response) => {
          this.items = response.data.products.data;
          this.from = response.data.products.from;
          this.server_items_length = response.data.products.total;
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
        .get(this.$store.state.backend_url + "api/inventory/get-ref")
        .then((res) => {
          this.warehouses = res.data.warehouses;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    editItem(item) {
      this.form.id = item.id;
      this.form.warehouse_id = item.warehouse_id;
      this.form.part_number = item.part_number;
      this.form.product_name = item.product_name;
      this.form.stock = item.stock;
      this.form.unit_measure = item.unit_measure;
    },
    save() {
      if (this.$refs.productCreateForm.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/inventory-products/update",
            {
              id: this.form.id,
              warehouse_id: this.form.warehouse_id,
              part_number: this.form.part_number,
              product_name: this.form.product_name,
              stock: this.form.stock,
              unit_measure: this.form.unit_measure,
            }
          )
          .then(() => {
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
            });
            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation"),
            });

            this.form.warehouse_id = null;
            this.form.part_number = null;
            this.form.product_name = null;
            this.form.stock = null;
            this.form.unit_measure = null;
            this.loading = false;
            this.$refs.productCreateForm.resetValidation();
            this.getList();
          })
          .catch(function (error) {
            console.log(error);
            this.loading = false;
          });
      }
    },
    deleteItem(item) {
      Swal.fire({
        title: this.$t("delete"),
        text: this.$t("inventory.delete"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("delete"),
        cancelButtonText: this.$t("close"),
      }).then((result) => {
        if (result.value) {
          this.loading = true;
          axios
            .delete(
              this.$store.state.backend_url +
                "api/inventory-products/delete/" +
                item.id
            )
            .then((res) => {
              this.items = this.items.filter((v) => v.id != item.id);
              Swal.fire({
                position: "top-end",
                toast: true,
                icon: "success",
                title: this.$t("swal_deleted"),
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
              });
              this.getList();
              this.loading = false;
            })
            .catch((error) => {
              console.error(error);
              this.loading = false;
              Swal.fire({
                position: "center",
                icon: "error",
                width: "250px",
                title: this.$t("swal_error_text"),
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
              });
            });
        }
      });
    },
  },
  mounted() {
    this.getRef();
    this.getList();
  },
};
</script>
