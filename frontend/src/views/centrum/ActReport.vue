<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">Aktlar ro'yxati</span>
        <div class="headerSearch d-flex align-center act_report_select">
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
          <v-select
            color="#e6e6e6"
            class="txt_search2"
            outlined
            dense
            v-model="act_month"
            :items = "months"
            hide-details
            @change="getList()"
            clearable
          ></v-select>
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
                  >mdi-format-list-bulleted</v-icon>
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
          <v-data-table
              id="table"
              class="doc-template_data-table"
              dense
              style="width: 100%; height: 100%; border-radius: 10px"
              fixed-header
              :height="screenHeight"
              :loading="loading"
              :headers="headers"
              :disable-pagination="true"
              :items="items"
            >
            <template v-slot:item.id="{ item }">{{
              items.indexOf(item) + 1
            }}</template>
            <template #item.allsum="{ item }">
              {{ item.allsum ? item.allsum.toString().replace(".", ",") : 0 }}
              <!-- {{ item.allsum ? item.allsum.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ").replace(".", ",") : 0 }} -->
            </template>
            <template #item.document_date="{ item }">
              {{ item.document_date? item.document_date.substr(0,10):'' }}
            </template>
            <template #item.status="{ item }">
              {{ item.status==1||item.status== 2 ? "Processing" : item.status ==3||item.status ==4||item.status ==5 ? "Approved": "Cancelled" }}
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
import TableToExcel from "@linways/table-to-excel";
export default {
  data() {
    return {
      loading: false,
      search: "",
      items: [],
      date: null,
      menu: false,
      menu2: false,
      date2: null,
      act_month: "",
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      months:[
        {text: "Yanvar, 2023", value: "2023-01"},
        {text: "Fevral, 2023", value: "2023-02"},
        {text: "Mart, 2023", value: "2023-03"},
        {text: "Aprel, 2023", value: "2023-04"},
        {text: "May, 2023", value: "2023-05"},
        {text: "Iyun, 2023", value: "2023-06"},
        {text: "Iyul, 2023", value: "2023-07"},
        {text: "Avgust, 2023", value: "2023-08"},
        {text: "Sentyabr, 2023", value: "2023-09"},
        {text: "Oktyabr, 2023", value: "2023-10"},
        {text: "Noyabr, 2023", value: "2023-11"},
        {text: "Dekabr, 2023", value: "2023-12"}
      ],
    //   today: moment().format("YYYY-MM-DD"),
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: "ACT", value: "title" },
        { text: "ACT DATE", value: "max(sign_date)" },
        { text: "DATE UPLOADED", value: "document_date" },
        { text: "ARRIVAL DATE", value: "max(arrival)" },
        { text: "CENTRAL BANK RATE", value: "max(bank_rate)" },
        { text: "ACT AMOUNT", value: "allsum" },
        { text: "STATUS", value: "status" },
      ];
    },
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .post("https://b-edo.uzautomotors.com/api/centrum/testRep", {act_month: this.act_month})
        .then((response) => {
          this.items = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"), {name: "ActReport"+ ".xlsx"});
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