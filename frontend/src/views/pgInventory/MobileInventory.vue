<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Инвентаризация") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            readonly
            dense
            hide-details
            solo
            single-line
          ></v-text-field>
          <v-text-field
            style="border-radius: none!important;"
            v-model="filter.menu1"
            class="txt_search1"
            label="Sanadan"
            type="date"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-text-field
            style="border-radius: none;"
            v-model="filter.menu2"
            class="txt_search1"
            label="Sanagacha"
            type="date"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="getList"
          >
            <v-icon color="#00B950" left>mdi-magnify</v-icon>Қидириш
          </v-btn>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="
              getDetailExcel(1);
              inventory_excel = [];
            "
          >
            <v-icon color="#107C41" left>mdi-microsoft-excel</v-icon>Юклаб олиш
          </v-btn>
        </div>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
            single-expand
            :expanded="expanded"
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
            item-key="id"
            show-expand
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            disable-sort
            :footer-props="{
              itemsPerPageOptions: [20, 50, 1000],
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
            <template v-slot:body.prepend="{ item }">
              <tr class="prepend_height">
                <td></td>
                <td></td>
                <td>
                  <v-autocomplete
                    v-model="filter.to_delete"
                    :items="toDeletes"
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                  <v-text-field
                    v-model="filter.partNumer"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td></td>
                <td>
                  <v-text-field
                    v-model="filter.edoNumber"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-text-field
                    v-model="filter.whNumner"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-text-field
                    v-model="filter.addresName"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td></td>
                <td></td>
                <td>
                  <v-text-field
                    v-model="filter.unNumber"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td></td>
                <td style="width: 50px">
                  <v-autocomplete
                    v-model="filter.dubl"
                    :items="switchItem"
                    item-text="text"
                    item-value="id"
                    clearable
                    hide-details
                    dense
                    @keyup.enter="getList"
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td style="width: 50px">
                  <v-autocomplete
                    v-model="filter.smesh"
                    :items="switchItem"
                    item-text="text"
                    item-value="id"
                    clearable
                    hide-details
                    dense
                    @keyup.enter="getList"
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                  <v-text-field
                    v-model="filter.camNum"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-text-field
                    v-model="filter.name"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-autocomplete
                    v-model="filter.status"
                    :items="statuses"
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
              </tr>
            </template>
            <template v-slot:item.id="{ item, index }">
              {{ from + index }}
            </template>

            <template v-slot:item.quartera="{ item }">
              <td style="max-width: 150px">
                <span style="white-space: normal; max-width: 100px">
                  {{
                    item.quarter
                      ? item.quarter.year + "/" + item.quarter.quarter
                      : ""
                  }}
                </span>
              </td>
            </template>
            <template v-slot:item.createdby="{ item }">
              <!-- <td style="max-width: 150px"> -->
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: normal; max-width: 100px"
                  >
                    {{
                      item.createdby.firstname.substring(0, 1) +
                      ". " +
                      item.createdby.lastname
                    }}
                  </span>
                </template>
                <template>
                  <span style="white-space: normal; max-width: 100px">
                    {{
                      "(" +
                      item.createdby.tab +
                      ") " +
                      item.createdby.firstname +
                      "\n" +
                      item.createdby.lastname
                    }}
                  </span>
                </template>
              </v-tooltip>
                <!-- </td> -->
            </template>
            <template v-slot:[`item.duplicate`]="{ item }">
              <v-switch
                hide-details
                class="mt-0"
                readonly
                :input-value="item.is_duplicate"
                size="18"
              >
                <!-- :label="`${item.switch ? 'Фаол' : 'Блок'}`" -->
              </v-switch>
            </template>
            <template v-slot:[`item.to_delete`]="{ item }">
              <td :style="item.to_delete ? 'background-color:red;' : ''"></td>
            </template>
            <template v-slot:[`item.smesh`]="{ item }">
              <v-switch
                class="mt-0"
                hide-details
                readonly
                :input-value="item.is_smesh"
                size="18"
              >
              </v-switch>
            </template>
            <template v-slot:[`item.facts`]="{ item }">
              <span
                :class="
                  item.fact != item.quantity ? 'success white--text py-1' : ''
                "
              >
                {{ item.fact }}
              </span>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                class="pl-0 pr-2"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)"
                v-if="item.status && $store.getters.checkPermission('delete-mobile-record')"
              >
                <v-icon size="18">mdi-trash-can-outline</v-icon>
              </v-btn>
              <v-btn
                color="error"
                outlined
                x-small
                v-else-if="!item.status"
              >
                Deleted
              </v-btn>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-3">
                <v-card class="my-2">
                  <v-container fluid class="pa-1 blue-grey lighten-5">
                    <table style="width: 100%">
                      <tr>
                        <th>{{ $t("Full QR code") }}</th>
                        <th style="max-width: 100px; white-space: normal">
                          {{ $t("Scan date") }}
                        </th>
                        <th style="max-width: 100px; white-space: normal">
                          {{ $t("created_at") }}
                        </th>
                        <th style="max-width: 100px; white-space: normal">
                          {{ $t("updated_at") }}
                        </th>
                      </tr>
                      <tr>
                        <td
                          style="
                            max-width: 200px;
                            white-space: normal;
                            text-align: center;
                          "
                        >
                          {{ item.full_qr }}
                        </td>
                        <td
                          style="
                            max-width: 200px;
                            white-space: normal;
                            text-align: center;
                          "
                        >
                          {{ item.scan_date }}
                        </td>
                        <td
                          style="
                            max-width: 200px;
                            white-space: normal;
                            text-align: center;
                          "
                        >
                          {{ item.created_at }}
                        </td>
                        <td
                          style="
                            max-width: 200px;
                            white-space: normal;
                            text-align: center;
                          "
                        >
                          {{ item.updated_at }}
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>
              </td>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
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
    <v-dialog
      v-model="downloadExcel"
      hide-overlay
      persistent
      width="300"
      @keydown.esc="downloadExcel = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <v-card-text>
          <span class="dialog-head_title">Excel га юклаб олиш</span>
          <v-divider
            class="ma-0 pa-0"
            style="border-color: #dce5ef"
          ></v-divider>
          <v-btn
            color="success"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
          >
            <download-excel
              :data="application_excel"
              :name="'Inv_' + today + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn
            color="red"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            ><span style="color: red">{{ $t("close") }}</span>
            <v-icon color="red" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
// import Swal from "sweetalert2";
export default {
  data: () => ({
    items: [],
    expanded: [],
    switchItem: [
      { id: null, text: "All" },
      { id: true, text: "Yes" },
      { id: false, text: "No" },
    ],
    toDeletes: [
      { value: null, text: "All" },
      { value: true, text: "Yes" },
      { value: false, text: "No" },
    ],
    statuses: [
      { value: null, text: "All" },
      { value: true, text: "Active" },
      { value: false, text: "deleted" },
    ],
    filter: {
      to_delete: null,
      status: null,
      excells: null,
    },
    search: "",
    from: 0,
    loading: false,
    application_excel: [],
    downloadExcel: false,
    today: moment().format("YYYY-MM-DD"),
    page: 1,
    server_items_length: -1,
    dataTableOptions: { page: 1, itemsPerPage: 20 },
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 205;
    },
    headers() {
      return [
        {
          text: "",
          value: "data-table-expand",
          class: "blue-grey lighten-5",
          width: 30,
        },
        {
          text: "#",
          value: "id",
          align: "center",
          class: "blue-grey lighten-5",
          width: 30,
        },

        {
          text: this.$t("To Delete"),
          align: "center",
          value: "to_delete",
          class: "blue-grey lighten-5",
          width: 100,
        },
        {
          text: this.$t("Part number"),
          align: "center",
          value: "product.part_number",
          class: "blue-grey lighten-5",
          width: 100,
        },
        {
          text: this.$t("Product name"),
          value: "product.name",
          class: "blue-grey lighten-5",
          align: "center",
        },
        {
          text: this.$t("EO number"),
          value: "eo.eo_number",
          class: "blue-grey lighten-5",
          width: 50,
        },
        {
          text: this.$t("Warehouse"),
          value: "address.warehouse.wh_number",
          align: "center",
          class: "blue-grey lighten-5",
          width: 30,
        },
        {
          text: this.$t("address/name"),
          value: "address.address_name",
          class: "blue-grey lighten-5",
          // width: 30,
        },
        {
          text: this.$t("Scaner fact"),
          value: "quantity",
          class: "blue-grey lighten-5",
          align: "center",
          width: 30,
        },
        {
          text: this.$t("Manual fact"),
          value: "facts",
          class: "blue-grey lighten-5",
          align: "center",
          width: 30,
        },
        {
          text: this.$t("un_number"),
          value: "un_number",
          class: "blue-grey lighten-5",
          // width: 30,
        },
        {
          text: this.$t("scan_date"),
          value: "scan_date",
          class: "blue-grey lighten-5",
          // width: 30,
        },
        {
          text: this.$t("Duplicate"),
          value: "duplicate",
          class: "blue-grey lighten-5",
          // width: 30,
        },
        {
          text: this.$t("Smesh"),
          value: "smesh",
          class: "blue-grey lighten-5",
          // width: 30,
        },
        {
          text: this.$t("Commissions"),
          value: "createdby.commission_number",
          class: "blue-grey lighten-5",
          width: 130,
        },
        {
          text: this.$t("createdby"),
          value: "createdby",
          class: "blue-grey lighten-5",
          width: 130,
        },
        // {
        //   text: this.$t("quarter"),
        //   value: "quartera",
        //   class: "blue-grey lighten-5",
        //   width: 60,
        // },
        {text: this.$t("Status"),value: "actions",width: 70,align: "center",}
        ,
      ];
      // .filter(
      //   (v) =>
      //     v.value != "actions" ||
      //     this.$store.getters.checkPermission("department-update") ||
      //     this.$store.getters.checkPermission("department-delete")
      // );
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    deleteItem(item) {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/pg_inventory/change-status",
          item
        )
        .then((res) => {
          // this.getList();
          this.items = this.items.map(v => {
            if(v.id==item.id){
              v.status = false;
              return v;
            }
            return v;
          });
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },

    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/get-info", {
          filter: this.filter,
          pagination: this.dataTableOptions,
        })
        .then((res) => {
          this.items = res.data.data;
          this.server_items_length = res.data.total;
          this.from = res.data.from;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDetailExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/get-info/get-excel", {
          filter: this.filter,
          search: this.search,
          type: 1,
          pagination: {
            page: page,
            itemsPerPage: 1000,
          },
        })
        .then((response) => {
          response.data.map((v, index) => {
            new_array.push({
              "№": index + page,
              PartNumber: v.PartNumber,
              PartName: v.PartName,
              EOName: v.EOName,
              WHID: v.WHID,
              AddressName: v.AddressName,
              ScanerFact: v.ScanerFact,
              ManualFact: v.ManualFact,
              UniqNumber: v.UniqNumber,
              FullQRCode: v.FullQRCode,
              Duplicat: v.Duplicat,
              Smesh: v.Smesh,
              ScanDate: v.ScanDate,
              CreatedAT: v.CreatedAT,
              CreatedBy: v.CreatedBy,
              Commissions: v.Commissions,
              Quater: v.Quater,
              "To Delete": v.to_delete, // ? "true" : "false",
              "Is Deleted": v.status, // ? "Active" : "Deleted",
            });
          });
          this.application_excel = this.application_excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getDetailExcel(++page);
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
  },
  mounted() {
    var date = new Date();
    this.filter.menu1 = moment(
      new Date(date.getFullYear(), date.getMonth() - 1, 1)
    ).format("YYYY-MM-DD");
    this.filter.menu2 = moment(new Date()).format("YYYY-MM-DD");
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