<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("inventory.blank") }}</span>
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
          itemsPerPageOptions: [50, 100, 200],
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
          <!--<tr class="py-0 my-0">-->
          <!--<td class="py-0 my-0 dense"></td>-->
          <!--<td class="py-0 my-0 dense">-->
          <!--<v-autocomplete-->
          <!--class="py-0"-->
          <!--clearable-->
          <!--v-model="filter.commission_id"-->
          <!--:items="commissions"-->
          <!--hide-details-->
          <!--item-value="id"-->
          <!--item-text="text"-->
          <!--dense-->
          <!--@change="getList()"-->
          <!--&gt;-->
          <!--</v-autocomplete>-->
          <!--</td>-->
          <!--<td class="py-0 my-0 dense">-->
          <!--<v-text-field v-model="filter.blank_number"-->
          <!--hide-details-->
          <!--@keyup.native.enter="getList()"-->
          <!--clearable>-->
          <!--</v-text-field>-->
          <!--</td>-->
          <!--<td class="py-0 my-0 dense"></td>-->
          <!--<td class="py-0 my-0 dense"></td>-->
          <!--<td class="py-0 my-0 dense"></td>-->
          <!--</tr>-->
        </template>
        <!--<template v-slot:item.id="{ item }">-->
        <!--{{-->
        <!--items-->
        <!--.map(function (x) {-->
        <!--return x.key;-->
        <!--})-->
        <!--.indexOf(item.key) + from-->
        <!--}}-->
        <!--</template>-->
        <template v-slot:item.actions="{ item }">
          <v-btn color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-title class="py-1 px-3">
          <v-btn color="success" class="" @click="downloadExcel = false" text>
            <download-excel
              :data="items_excel"
              :name="'Inv_blank_' + today + '.xls'"
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

export default {
  data() {
    return {
      form: {
        id: 0,
        blank_number: null,
        commission_id: null,
      },
      filter: {},
      search: "",
      commissions: [],
      isLoading: false,
      loading: false,
      items: [],
      items_excel: [],
      downloadExcel: false,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      page: 1,
      from: 0,
      today: moment().format("YYYY-MM-DD"),
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
        // {
        //   text: this.$t("inventory.commission"),
        //   value: "n1",
        // },
        {
          text: this.$t("inventory.blank_number"),
          value: "blank_number",
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
        .post(this.$store.state.backend_url + "api/inventory/blanks", {
          pagination: this.dataTableOptions,
          filter: this.filter,
        })
        .then((response) => {
          this.items = response.data.items;
          this.from = response.from;
          this.server_items_length = response.data.total;
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
          this.commissions = res.data.commissions;
          this.commissions = res.data.commissions.map((v) => ({
            text: v.commission_number,
            value: v.id,
          }));

          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    getInventoryExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/inventory/blankReport")
        .then((response) => {
          response.data.map((v, index) => {
            new_array.push({
              "№": index + page * 1000 - 999,
              Blank_nomer: v.blank_number,
            });
          });
          // new_array = response.data.data;

          this.items_excel = this.items_excel.concat(new_array);
          this.downloadExcel = true;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    editItem(item) {
      this.form.id = item.id;
      this.form.blank_number = item.blank_number;
      this.form.commission_id = item.commission_id;
    },
    save() {
      if (this.$refs.addressCreateForm.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/inventory-addresses/update",
            {
              id: this.form.id,
              address_name: this.form.address_name,
              warehouse_id: this.form.warehouse_id,
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

            this.form.address_name = null;
            this.form.warehouse_id = null;
            this.loading = false;
            this.$refs.addressCreateForm.resetValidation();
            this.getList();
          })
          .catch(function (error) {
            console.log(error);
            this.loading = false;
          });
      }
      // console.log(this.formInv);
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
                "api/inventory-addresses/delete/" +
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
    this.getList();
    this.getRef();
  },
};
</script>
