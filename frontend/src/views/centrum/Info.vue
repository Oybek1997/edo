<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span v-if="new_count + success_count > 0" class="headerTitle mb-2">{{ new_count + "/" + (new_count + success_count) }}</span>
        <div class="headerSearch d-flex align-center act_report_select">
            <v-text-field
              class="txt_search1"
              append-icon="mdi-magnify"
              v-model="filter.client"
              :placeholder="$t('Client')"
              @keyup.enter="getList()"
              outlined
              dense
              hide-details
            ></v-text-field>
            <v-text-field
              style="border-radius: 0;"
              class="txt_search1"
              append-icon="mdi-magnify"
              v-model="filter.contract"
              :placeholder="$t('Contract')"
              @keyup.enter="getList()"
              outlined
              dense
              hide-details
            ></v-text-field>
            <v-text-field
              style="border-radius: 0;"
              class="txt_search1"
              append-icon="mdi-magnify"
              v-model="filter.region"
              :placeholder="$t('Region')"
              @keyup.enter="getList()"
              outlined
              dense
              hide-details
            ></v-text-field>
            <v-text-field
              style="border-radius: 0;"
              class="txt_search1"
              append-icon="mdi-magnify"
              v-model="filter.area"
              :placeholder="$t('Area')"
              @keyup.enter="getList()"
              outlined
              dense
              hide-details
            ></v-text-field>
            <v-text-field
              style="border-radius: 0;"
              class="txt_search1"
              append-icon="mdi-magnify"
              v-model="filter.address"
              :placeholder="$t('Address')"
              @keyup.enter="getList()"
              outlined
              dense
              hide-details
            ></v-text-field>
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
                    @click="uploadExcel('table', 'Lorem Table')"
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
          <v-simple-table 
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            >
            <template v-slot:default>
              <thead>
                <tr style="background: #f6f9fb!important;">
                  <th style="background: #f6f9fb!important;">#</th>
                  <th style="background: #f6f9fb!important;">Client</th>
                  <th style="background: #f6f9fb!important;">Contract number</th>
                  <th style="background: #f6f9fb!important;">Region</th>
                  <th style="background: #f6f9fb!important;">Area</th>
                  <th style="background: #f6f9fb!important;">Address</th>
                  <th style="background: #f6f9fb!important;">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, key) in listItem" :key="key">
                  <td>{{ key + 1 }}</td>
                  <td style="min-width: 50px; white-space: normal">
                    {{ item.client }}
                  </td>
                  <td style="min-width: 50px; white-space: normal">
                    {{ item.contract_number }}
                  </td>
                  <td style="min-width: 50px; white-space: normal">
                    {{ item.region }}
                  </td>
                  <td style="min-width: 50px; white-space: normal">
                    {{ item.area }}
                  </td>
                  <td style="min-width: 50px; white-space: normal">
                    {{ item.address }}
                  </td>
                  <td style="min-width: 50px; white-space: normal">
                    <v-btn
                      color="success"
                      small
                      text
                      outlined
                      @click="view(item.id)"
                      >View</v-btn
                    >
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="viewDialog"
      scrollable
      fullscreen
      persistent
      :overlay="false"
      transition="dialog-transition"
    >
      <v-card>
        <v-card-title primary-title>
          PDF
          <v-spacer></v-spacer>
          <v-btn color="error" @click="viewDialog = false" icon small
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </v-card-title>
        <v-card-text>
          <iframe
            :src="'data:application/pdf;base64, ' + base64"
            style="width: 100%; height: 80vh"
          ></iframe>
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
import Swal from "sweetalert2";
export default {
  data() {
    return {
      base64: null,
      viewDialog: false,
      loading: false,
      filter: {
        client: null,
        contract: null,
        region: null,
        area: null,
        address: null,
      },
      file: null,
      page: 1,
      from: 0,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      dialog: false,
      editMode: null,
      items: [],
      parts: [],
      part: null,
      form: {},
      item: null,
      dialogHeaderText: "",
      eimzoKeys: [],
      eimzoKey: null,
      eimzoDialog: false,
      new_count: 0,
      success_count: 0,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    listItem() {
      return this.items;
    },
  },
  methods: {
    view(id) {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/post-order/view", { id: id })
        .then((response) => {
          this.base64 = response.data;
          this.viewDialog = true;
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
        .post(this.$store.state.backend_url + "api/post-order/info", {
          filter: this.filter,
        })
        .then((response) => {
          this.items = response.data[0];
          this.new_count = response.data[1];
          this.success_count = response.data[2];
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
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