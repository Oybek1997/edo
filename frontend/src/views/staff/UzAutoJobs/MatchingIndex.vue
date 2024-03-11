<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{
          $t("Xodimlarni moshlashtirish")
        }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="filter.search"
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
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="getList"
            disabled
          >
            <v-icon color="#00B950" left>mdi-magnify</v-icon>Қидириш
          </v-btn>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="
              getDetailExcel();
              inventory_excel = [];
            "
            disabled
          >
            <v-icon color="#107C41" left>mdi-microsoft-excel</v-icon>Юклаб олиш
          </v-btn>         
        </div>
      </v-card-title>
      <!-- :items="staffData.filter((v) => v.employee_staff.length > 0)" -->
        <!-- show-expand
        fixed-header -->
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        class="mainTable"
        style="width: 100%; height: 100%; border-radius: 10px"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
        :disable-pagination="true"
        disable-sort
        :footer-props="{
          itemsPerPageOptions: [50, 100, 200],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-left-box',
          lastIcon: 'mdi-arrow-right-box',
          prevIcon: 'mdi-arrow-left-drop-circle-outline',
          nextIcon: 'mdi-arrow-right-drop-circle-outline',
        }"
      >
        <!-- <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="screenHeight">
            <v-simple-table class="mb-3 mt-3">
              <thead>
                <tr>
                  <th class="text-center blue-grey lighten-5 lign-center">N</th>
                  <th class="text-center blue-grey lighten-5 lign-center">uzautoJobs_id</th>
                  <th class="text-center blue-grey lighten-5 lign-center">Эълон қилнган вақт</th>
                  <th class="text-center blue-grey lighten-5 lign-center">Эълон қилди</th>
                  <th class="text-center blue-grey lighten-5 lign-center">status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="itema in item.choice_status" :key="itema.index">
                  <td class="text-center">{{ item.choice_status.indexOf(itema) + 1 }}</td>                 
                  <td class="text-center"> {{ String(itema.uzautoJobs_id).padStart(6, '0') }}</td>                 
                  <td class="text-center">{{ itema.created_at }}</td>
                  <td class="text-center">{{ itema.created_by }}</td>
                  <td class="text-center">
                    <span
                        v-if="itema.status == 201"
                        class="tdClass1 lign-center"
                        style="background: orange;width: 150px; "
                      >
                        {{ itemStatus.find((v) => v.id == itema.status).text }}
                      </span>
                    <span
                        v-if="itema.status == 202"
                        class="tdClass1  lign-center"
                        style="background: yellow;width: 150px;"
                      >
                        {{ itemStatus.find((v) => v.id == itema.status).text }}
                      </span>
                    <span
                        v-if="itema.status == 200"
                        class="tdClass1  lign-center"
                        style="background: green;width: 150px;"
                      >
                        {{ itemStatus.find((v) => v.id == itema.status).text }}
                      </span>
                    </td>
                </tr>
              </tbody>
            </v-simple-table>
          </td>
        </template> -->

        <template v-slot:item.number="{ item, index }" style>
          {{ from + index + 1 }}
        </template>   
        <template v-slot:item.created_at="{ item, index }" style>
          {{ item.choice_status?item.choice_status[0].created_at:'' }}
        </template> 
        <template v-slot:item.id="{ item, index }" style>    
          <span 
          class="tdClass1 text-center"          
          >
        {{ String(item.id).padStart(6, '0') }}       
        </span>
        </template> 
        <template v-slot:item.action="{ item, index }" style>    
          <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="$router.push('/staffs/employees/matching/'+item.id)"
                >
                  <!-- @click="getSelection(item)" -->
                  <v-icon>mdi-arrange-send-backward</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Vakantlarni moslashtirish") }}</span>
            </v-tooltip>
        </template> 
      </v-data-table>
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
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
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
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
            <download-excel :data="application_excel" :name="'RP_' + today + '.xls'">
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
    <v-dialog
      v-model="rejectDialog"
      hide-overlay
      persistent
      width="300"
      @keydown.esc="rejectDialog = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <v-card-text>
          <span class="dialog-head_title">Bekor bo'lish sababi</span>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-autocomplete
            :items="itemMessages.filter((v) => v.id > 1 && v.id != 9999)"
            v-model="rejectID.type"
            item-text="name"
            item-value="id"
            clearable
            solo
            dense
            hide-details="auto"
            @change="getList"
          ></v-autocomplete>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-btn
            color="success"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            v-if="rejectID.type !== null"
            @click="rejectCanditate(), (rejectDialog = false)"
          >
            <span style="color: success">{{ $t("OK") }}</span>
            <v-icon color="success" height="20">mdi-check</v-icon>
          </v-btn>
          <v-btn
            @click="rejectDialog = false"
            color="red"
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
import Swal from "sweetalert2";
export default {
  data: () => ({
    dateRules: [
      (v) =>
        !v ||
        /^\d{4}[\-\-](0?[1-9]|1[012])[\-\-](0?[1-9]|[12][0-9]|3[01])$/.test(v) ||
        "Date must be valid(YYYY-DD-MM)",
    ],
    items: [],
    staffData: [],
    itemMessages: [
      { id: 9999, name: "Янги", status: 1, type: 0 },
      { id: 0, name: "Muvaffaqiyatsiz", status: 1, type: 2 },
    ],
    filter: {},
    loading: false,
    downloadExcel: false,
    rejectDialog: false,
    rejectID: {
      itemId: null,
      sort: null,
      type: null,
    },
    itemStatus: [
      { id: "201", text: "Jobs га юборилди" },
      { id: "202", text: "Эълон қилинди" },
      { id: "200", text: "Танлов якунланди" },
    ],
    application_excel: [],
    choiceItem: [],
    today: moment().format("YYYY-MM-DD"),
    server_items_length: null,
    dataTableOptions: { page: 1, itemsPerPage: 50 },
    server_items_length: -1,
    page: 1,
    from: 0,
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 205;
    },
    headers() {
      return [
        {
          text: "#",
          value: "number",
          align: "center",
          width: 30,
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t(""),
          value: "action",
          class: "blue-grey lighten-5 lign-center",
          width: 30,
        },
        {
          text: this.$t("Tanlov Index"),
          value: "id",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("created_at"),
          value: "created_at",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("depatament_code"),
          value: "depatament_code",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("depatament_name"),
          value: "depatament_name",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("position_name"),
          value: "position_name",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("status"),
          value: "status",
          class: "blue-grey lighten-5 lign-center",
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("staff-update") ||
          this.$store.getters.checkPermission("staff-delete")
      );
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
      this.filter.excells = 0;
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/matching-index", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          language: this.$i18n.locale,
        })
        .then((response) => {
          this.items = response.data.choice.data;          
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDetailExcel() {
      let new_array = [];
      this.application_excel = [];
      this.filter.excells = 1;
      this.loading = true;
      this.dataTableOptions.itemsPerPage = 500;
      axios
        .post(this.$store.state.backend_url + "api/employe-staff/full", {
          filter: this.filter,
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
        })
        // this.staffData = response.data.data.filter((v) => v.employee_staff.length > 0);
        .then((response) => {
          response.data.map((v, index) => {
            new_array.push({
              "№": index + this.dataTableOptions.page,
              // ID: v.ID,
              Branch: v.Branch,
              FunctionalName: v.FunctionalName,
              FunctionalCode: v.FunctionalCode,
              DepartmentName: v.DepartmentName,
              DepartmentCode: v.DepartmentCode,
              PositionName: v.PositionName,
              PositionCode: v.PositionCode,
              Status: v.Status,
              RangeName: v.RangeName,
              RangeCode: v.RangeCode,
              personalType: v.personalType,
              expenceType: v.expenceType,
              firstname: v.firstname,
              lastname: v.lastname,
              middlename: v.middlename,
              bornDate: v.bornDate,
              Category: v.Category,
              tabel: v.tabel,
              Shift: v.Shift,
              experience: v.experience,
              firstWorkDate: v.firstWorkDate,
              enterOrderNumber: v.enterOrderNumber,
              enterOrderDate: v.enterOrderDate,
              BP: v.BP,
              TS: v.TS,
              XS: v.XS,
              AS: v.AS,
              BPV: v.BPV,
              TSV: v.TSV,
              Coeff: v.Coeff,
              WB: v.WB,
              DirInDir: v.DirInDir,
              Coeff: v.Coeff,
              WB: v.WB,
              DirInDir: v.DirInDir,
            });
          });
          this.application_excel = this.application_excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getDetailExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
          this.filter.excells = 0;
          this.dataTableOptions.itemsPerPage = 50;
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
.staff-s table > thead > tr > th {
  white-space: normal;
  display: block;
  display: -webkit-box;
  max-width: 100%;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.fullHeight {
  height: calc(100% - 100px);
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
.btn_class {
  padding: 20px;
  margin: 0px 0px -19px 20px;
}
.tdClass1 {
  display: block;
  color: #fff;
  color: rgb(3, 3, 3);
  width: 100%;
  height: 20px;
  border-radius: 5px;
  padding: 1px 5px;
}
.tdClass2 {
  display: block;
  color: #fff;
  width: 100px;
  height: 30px;
  border-radius: 15px;
  padding: 0px 5px;
  font-size: 12px;
  font-weight: 400;
  font-style: italic;
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
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
  color: #212529;
  font-size: 14px;
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
  width: 50px;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
</style>
