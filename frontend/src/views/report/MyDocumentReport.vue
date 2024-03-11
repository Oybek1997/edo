<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2" v-if="$route.params.menu_item == 'my'">{{
          $t("my_documents")
        }}</span>
        <span
          class="headerTitle mb-2"
          v-if="$route.params.menu_item == 'all'"
          >{{ $t("all_documents") }}</span
        >
        <span
          class="headerTitle mb-2"
          v-if="$route.params.menu_item == 'selected'"
          >{{ $t("Template report") }}</span
        >
        <span class="headerTitle mb-2" v-else>{{
          $t("Отчеты по документов")
        }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search.txt"
            prepend-inner-icon="mdi-magnify"
            class="txt_search1"
            placeholder="Поиск"
            @keyup.enter="getList"
            dense
            disabled
            hide-details
            solo
          ></v-text-field>
          <v-text-field
            class="txt_search1"
            v-model="search.from_date"
            label="Sanadan"
            type="date"
            outlined
            dense
            style="max-width: 15%; border-left: 0px; border-radius: 0px"
            clearable
            single-line
            hide-details
            @change="getList"
          ></v-text-field>
          <v-text-field
            class="txt_search1"
            v-model="search.to_date"
            label="Sanagacha"
            type="date"
            outlined
            dense
            style="max-width: 15%; border: border-left: 0px; border-radius: 0px;"
            clearable
            single-line
            hide-details
            @change="getList"
          ></v-text-field>
          <!-- <v-btn @click="getList()" class="filterBtn px-2 mr-2" style="background: #fff; height: 34px;">
              Izlash
            </v-btn> -->
          <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
            v-if="$store.getters.checkPermission('organizations-create')"
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
                <!-- <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;" @click="newItem">   
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                    </v-list-item-title
                  ></v-list-item> -->
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title @click="getDocsheetsExcel()">
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
            id="table"
            class="doc-template_data-table pb-10"
            dense
            fixed-header
            style="width: 100%; height: 100%; border-radius: 10px"
          >
            <thead>
              <tr>
                <th
                  style="
                    background: #f6f9fb;
                    text-align: center;
                    border: 1px solid #dce5ef;
                  "
                >
                  #
                </th>
                <th
                  style="
                    background: #f6f9fb;
                    text-align: left;
                    border: 1px solid #dce5ef;
                  "
                >
                  Hujjat turi
                </th>
                <th
                  style="
                    background: #f6f9fb;
                    text-align: center;
                    border: 1px solid #dce5ef;
                  "
                >
                  Barchasi
                </th>
                <th
                  style="
                    background: #f6f9fb;
                    text-align: center;
                    border: 1px solid #dce5ef;
                  "
                >
                  Yakunlangan
                </th>
                <th
                  style="
                    background: #f6f9fb;
                    text-align: center;
                    border: 1px solid #dce5ef;
                  "
                >
                  Ijroda
                </th>
                <th
                  style="
                    background: #f6f9fb;
                    text-align: center;
                    border: 1px solid #dce5ef;
                  "
                >
                  Muddati o'tgan
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in inbox['all']">
                <td
                  v-if="index == 0"
                  :rowspan="inbox['all'].length"
                  style="border: 1px solid #dce5ef"
                >
                  Kiruvchi
                </td>
                <td style="border: 1px solid #dce5ef">
                  {{ item.name_uz_latin }}
                </td>
                <td style="border: 1px solid #dce5ef">
                  <v-tab
                    :to="
                      '/documentsidebar/my-document-report-item/' +
                      '1' +
                      '&' +
                      item.type +
                      '&' +
                      '1' +
                      '&' +
                      search.from_date +
                      '&' +
                      search.to_date
                    "
                    >{{ item.cnt }}</v-tab
                  >
                </td>
                <td style="border: 1px solid #dce5ef">
                  <v-tab
                    :to="
                      '/documentsidebar/my-document-report-item/' +
                      '1' +
                      '&' +
                      item.type +
                      '&' +
                      '2' +
                      '&' +
                      search.from_date +
                      '&' +
                      search.to_date
                    "
                  >
                    {{
                      inbox["done"] != 0
                        ? inbox["done"].filter((v) => v.type == item.type)[0]
                          ? inbox["done"].filter((v) => v.type == item.type)[0]
                              .cnt
                          : 0
                        : 0
                    }}</v-tab
                  >
                </td>
                <td style="border: 1px solid #dce5ef">
                  <v-tab
                    :to="
                      '/documentsidebar/my-document-report-item/' +
                      '1' +
                      '&' +
                      item.type +
                      '&' +
                      '3' +
                      '&' +
                      search.from_date +
                      '&' +
                      search.to_date
                    "
                  >
                    {{
                      inbox["pending"] != 0
                        ? inbox["pending"].filter((v) => v.type == item.type)[0]
                          ? inbox["pending"].filter(
                              (v) => v.type == item.type
                            )[0].cnt
                          : 0
                        : 0
                    }}</v-tab
                  >
                </td>
                <td style="border: 1px solid #dce5ef">
                  <v-tab
                    :to="
                      '/documentsidebar/my-document-report-item/' +
                      '1' +
                      '&' +
                      item.type +
                      '&' +
                      '4' +
                      '&' +
                      search.from_date +
                      '&' +
                      search.to_date
                    "
                  >
                    {{
                      inbox["pending_out_time"] != 0
                        ? inbox["pending_out_time"].filter(
                            (v) => v.type == item.type
                          )[0]
                          ? inbox["pending_out_time"].filter(
                              (v) => v.type == item.type
                            )[0].cnt
                          : 0
                        : 0
                    }}</v-tab
                  >
                </td>
              </tr>
              <tr v-for="(out, v) in outbox['all']">
                <td
                  v-if="v == 0"
                  :rowspan="outbox['all'].length"
                  style="border: 1px solid #dce5ef"
                >
                  Chiquvchi
                </td>
                <td style="border: 1px solid #dce5ef">
                  {{ out.name_uz_latin }}
                </td>
                <td style="border: 1px solid #dce5ef">
                  <v-tab
                    :to="
                      '/documentsidebar/my-document-report-item/' +
                      '2' +
                      '&' +
                      out.type +
                      '&' +
                      '1' +
                      '&' +
                      search.from_date +
                      '&' +
                      search.to_date
                    "
                  >
                    {{ out.cnt }}</v-tab
                  >
                </td>
                <td style="border: 1px solid #dce5ef">
                  <v-tab
                    :to="
                      '/documentsidebar/my-document-report-item/' +
                      '2' +
                      '&' +
                      out.type +
                      '&' +
                      '2' +
                      '&' +
                      search.from_date +
                      '&' +
                      search.to_date
                    "
                  >
                    {{
                      outbox["done"] != 0
                        ? outbox["done"].filter((v) => v.type == out.type)[0]
                          ? outbox["done"].filter((v) => v.type == out.type)[0]
                              .cnt
                          : 0
                        : 0
                    }}</v-tab
                  >
                </td>
                <td style="border: 1px solid #dce5ef">
                  <v-tab
                    :to="
                      '/documentsidebar/my-document-report-item/' +
                      '2' +
                      '&' +
                      out.type +
                      '&' +
                      '3' +
                      '&' +
                      search.from_date +
                      '&' +
                      search.to_date
                    "
                  >
                    {{
                      outbox["pending"] != 0
                        ? outbox["pending"].filter((v) => v.type == out.type)[0]
                          ? outbox["pending"].filter(
                              (v) => v.type == out.type
                            )[0].cnt
                          : 0
                        : 0
                    }}</v-tab
                  >
                </td>
                <td style="border: 1px solid #dce5ef">
                  <v-tab
                    :to="
                      '/documentsidebar/my-document-report-item/' +
                      '2' +
                      '&' +
                      out.type +
                      '&' +
                      '4' +
                      '&' +
                      search.from_date +
                      '&' +
                      search.to_date
                    "
                  >
                    {{
                      outbox["pending_out_time"] != 0
                        ? outbox["pending_out_time"].filter(
                            (v) => v.type == out.type
                          )[0]
                          ? outbox["pending_out_time"].filter(
                              (v) => v.type == out.type
                            )[0].cnt
                          : 0
                        : 0
                    }}</v-tab
                  >
                </td>
              </tr>
            </tbody>
          </v-simple-table>
        </v-col>
      </v-row>
    </v-card>
    <!-- downloading excel dialog qismi boshlandi -->
    <v-dialog
      v-model="downloadExcel"
      hide-overlay
      persistent
      width="290"
      @keydown.esc="downloadExcel = false"
    >
      <v-card class="pa-2" style="border-radius: 1px">
        <span class="dialog-head_title">Excelga yuklab olish</span>
        <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
        <v-card-text class="pa-0 ma-0">
          <v-btn
            color="#3FCB5D"
            right
            small
            dark
            elevation="0"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            @click="uploadExcel()"
          >
          {{ $t("download") }} <v-icon right>mdi-download</v-icon>
          </v-btn>
          <v-btn
            class
            color="#EB4034"
            right
            small
            dark
            elevation="0"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
          >
            {{ $t("Отменить") }}<v-icon right>mdi-close-box-outline</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <!-- downloading excel dialog qismi tugadi -->
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
import TableToExcel from "@linways/table-to-excel";
import Template from "../blankTemplate/Template.vue";
const moment = require("moment");
const axios = require("axios").default;
export default {
  components: { Template },
  data() {
    return {
      loading: false,
      downloadExcel: false,
      search: "",
      items: [],
      punkt_excel: [],
      headers: [],
      templates: [],
      inbox: [],
      outbox: [],
      search: {
        from_date: moment().subtract(1, "months").format("YYYY-MM-DD"),
        to_date: moment(new Date()).format("YYYY-MM-DD"),
      },
      template_id: null,
      selected_reaction: [2, 3, 6],
      has_employee: 0,
      tabno: null,
      table_menu: null,
      today: moment().format("YYYY-MM-DD"),
      reaction_status: [
        {
          text: this.$t("document.process"),
          value: 2,
        },
        {
          text: this.$t("document.ok"),
          value: 3,
        },
        {
          text: this.$t("document.cancel"),
          value: 6,
        },
        // {
        //   text: this.$t("substantiate"),
        //   value: 4,
        // },
      ],
      pagination: {
        page: 1,
        per_page: 20,
      },
      items_count: null,
      arrow1: true,
      arrow2: true,
      arrow3: true,
      arrow4: true,
      last_page: null,
      from: 0,
      total: 0,
      to: 0,
      date: null,
      menu: false,
      menu2: false,
      date2: null,
      minDate: "2023-01-01",
      maxDate: "2023-12-10",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"), {
        name: "MyDocumentsReport_" + this.today + ".xlsx",
      });
    },
    getDocsheetsExcel() {
      this.downloadExcel = true;
    },
    getList() {
      this.loading = true;
      this.items = [];
      let template = this.templates.filter((v) => v.id == this.template_id);
      if (template[0] && template[0].has_employee == 1) {
        this.has_employee = 1;
      } else {
        this.has_employee = 0;
      }
      axios
        .post("https://b-edo.uzautomotors.com/api/report/my-reports", {
          search: this.search,
        })
        .then((response) => {
          this.inbox = response.data[0];
          this.outbox = response.data[1];
          this.punkt_excel = response.data.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    // getTemplates() {
    //   this.loading = true;
    //   let url = "";
    //   if(this.$route.params.menu_item == 'selected'){
    //     url = 'https://b-edo.uzautomotors.com/api/selected-templates-for-report'
    //   }
    //   else{
    //     url = 'https://b-edo.uzautomotors.com/api/all-templates-for-vayvooo'

    //   }
    //   axios
    //     .get(url)
    //     .then((response) => {
    //       this.templates = response.data;
    //       this.loading = false;
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //       this.loading = false;
    //     });
    // },

    screenWidth() {
      return window.innerWidth;
    },
  },
  mounted() {
    // this.getTemplates();
    this.getList();
  },
};
</script>
<style>
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 93vh;
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
.headerSearch .v-text-field--outlined fieldset {
  border: none;
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
.headerSearch
  .v-text-field--enclosed.v-input--dense:not(.v-text-field--solo).v-text-field--outlined
  .v-input__append-inner {
  margin-top: 4px;
  font-size: 18px;
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
  /* border-left: 0px; */
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
}
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > thead > tr > th {
  background: #f6f9fb !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > tbody > tr > td {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
</style>
