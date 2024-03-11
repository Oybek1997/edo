<template>
  <div class="fullHeight">
    <v-card class="heightFull" style="border-radius: 10px; border: 1px solid #dce5ef;" elevation="0">
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("control_punkt.report") }}</span>
        <div
            class="headerSearch d-flex align-center"
          >
          <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            class="txt_search1"
            style=""
            :placeholder="$t('search')"
            @keyup.enter="getList"
            dense
            hide-details
            solo
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
            v-if="$store.getters.checkPermission('organizations-create')"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn class="txt_searchBtn  ml-2" outlined v-bind="attrs" v-on="on">
                <v-icon size="18" color="white">mdi-format-list-bulleted</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;" @click="newItem">
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                    </v-list-item-title
                  ></v-list-item>
                <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;" @click="0"> 
                  <v-list-item-title @click="getEmployeeExcel(1);
            punkt_excel = [];">
                    <v-icon color='#107C41' size="18">mdi-microsoft-excel</v-icon>
                    Скачать таблицу Excel
                    </v-list-item-title
                  ></v-list-item>
              </v-list>
            </v-card>
          </v-menu>
      
        </div>
        <!-- <v-btn class="mx-3" @click="uploadExcel('table', 'Lorem Table')">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn> -->
        <!-- <v-col cols="12" sm="6" md="2">
          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                dense
                v-model="date"
                label="Sana boshi"
                outlined
                hide-details
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker v-model="date" @input="menu = false"></v-date-picker>
          </v-menu>
        </v-col>
        <v-col cols="12" sm="6" md="2">
          <v-menu
            v-model="menu2"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                dense
                v-model="date2"
                label="Sana oxiri"
                outlined
                hide-details
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="date2"
              @input="menu2 = false"
            ></v-date-picker>
          </v-menu>
        </v-col>
        <v-btn depressed dense color="success" @click="getList()">
          Izlash
        </v-btn> -->
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
        <v-data-table
        id="table"
          dense
            class="doc-template_data-table"
            style="
                  width: 100%;
                  height: 100%;
                  border-radius: 10px;
                "
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="items"
            single-expand
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
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
          <!-- <template v-slot:item.idc="{ item }">{{
            items.indexOf(item) + 1
          }}</template> -->
          <template v-slot:item="{ item, index }">
            <tr>
                <td style="max-width: 50px">{{ index + 1 }}</td>
                <td style="max-width: 150px">{{ item.id }}</td>
                <td style="max-width: 200px" :to="'/document/' + item.pdf_file_name">
                    {{  item.document_number }}
                </td>
                <td style="max-width: 300px">{{ item.fio }}</td>
                <td style="max-width: 300px">{{ item.Bulim }}</td>
                <td style="max-width: 300px">{{ item.Lavozim }}</td>
                <td style="max-width: 300px">{{ item.due_date }}</td>
                <td style="max-width: 300px">{{ item.kun }}</td>
                <td style="max-width: 150px">
                  <v-btn
                    x-small
                      block
                      rounded
                      dark
                      v-if="item.status==6"
                      class="px-1 error"
                      style="border: 1px solid; border-radius: 15px;"
                    >
                      Rad etilgan
                    </v-btn>
                    <v-btn
                      x-small
                      block
                      dark
                      v-if="item.status==3 ||item.status==4||item.status==5"
                      class="px-1 primary"
                      style="border: 1px solid; border-radius: 15px;"
                    >
                      Jarayonda
                    </v-btn>
                    <v-btn
                      x-small
                      block
                      rounded
                      dark
                      v-if="item.status==1||item.status==2"
                      class="px-1 primary"
                      style="border: 1px solid; border-radius: 15px;"
                    >
                      Jarayonda
                    </v-btn>
                    <v-btn
                      x-small
                      block
                      rounded
                      dark
                      v-if="item.status==0"
                      class="px-1 black"
                      style="border: 1px solid; border-radius: 15px;"
                    >
                      Yangi
                    </v-btn>
                </td>
            </tr>
          </template>
          <!-- <template v-slot:item.document_number="{ item }">
            <v-btn
              outlined
              small
              rounded
              :to="'/document/' + item.pdf_file_name"
              >{{
                item.document_number
              }}</v-btn
            ></template> -->

          <!-- <template #item.status="{ item }">
            <v-btn
            x-small
              block
              rounded
              dark
              v-if="item.status==6"
              class="px-1 error"
            >
              Rad etilgan
            </v-btn>
            <v-btn
              x-small
              block
              dark
              v-if="item.status==3 ||item.status==4||item.status==5"
              class="px-1 primary"
            >
              Jarayonda
            </v-btn>
            <v-btn
              x-small
              block
              rounded
              dark
              v-if="item.status==1||item.status==2"
              class="px-1 primary"
            >
              Jarayonda
            </v-btn>
            <v-btn
              x-small
              block
              rounded
              dark
              v-if="item.status==0"
              class="px-1 black"
            >
              Yangi
            </v-btn>
          </template> -->
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
              :data="punkt_excel"
              :name="'Ijro_nazorati_ruyxati_' + today + '.xls'"
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
import Swal from "sweetalert2";
import TableToExcel from "@linways/table-to-excel";
const moment = require("moment");
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
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      downloadExcel: false,
      punkt_excel: [],
      today: moment().format("YYYY-MM-DD"),
      table_menu : false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight-140;
    },
    headers() {
      return [
        { text: "#", value: "idc"},
        { text: "ID", value: "id"},
        { text: this.$t("document.document_number"), value: "document_number" },
        { text: this.$t("document.fio"), value: "fio" },
        { text: this.$t("department_id"), value: "Bulim" },
        { text: this.$t("employee.position"), value: "Lavozim" },
        { text: this.$t("document.due_date"), value: "due_date" },
        { text: this.$t("control_punkt.due_day"), value: "kun" },
        { text: this.$t("document.execution_status"), value: "status" },
      ];
    },
  },
  methods: {
    newItem(){},
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .post("https://b-edo.uzautomotors.com/api/control-punkt-report", {
          // startdate: this.date,
          // enddate: this.date2,
        })
        .then((response) => {
          this.items = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getEmployeeExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/control-punkt-report", {
          locale: this.$i18n.locale,
          page: page,
          perPage: 1000,
        })
        .then((response) => {
          new_array = response.data;
          this.punkt_excel = this.punkt_excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getEmployeeExcel(++page);
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
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"), {name: "control-punkt-report"+ ".xlsx"});
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
<style scoped>
.fullHeight {
  height: calc(100% - 10px);
}
.heightFull {
  border-radius: 10px 10px 10px 10px;
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
  background: #FF9F0E;
  border: 0.20px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px!important;
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

</style>
