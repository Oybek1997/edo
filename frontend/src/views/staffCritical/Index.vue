<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("critical.index") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
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
                  @click="newItem"
                >
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>Добавить новую строку
                  </v-list-item-title>
                </v-list-item>
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title
                    @click="
                      getEmployeeExcel(1);
                      employee_excel = [];
                    "
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
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :headers="headers"
            :items="items"
            :single-expand="singleExpand"
            :options.sync="dataTableOptions"
            :expanded.sync="expanded"
            item-key="id"
            show-expand
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, -1],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
          >
            <template v-slot:item.id="{ item }">
              {{
                items
                  .map(function (x) {
                    return x.id;
                  })
                  .indexOf(item.id) + 1
              }}
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-3">
                <v-card class="my-2">
                  <div>
                    <v-system-bar window color="#eee">
                      <b>{{ $t("") }}</b>
                      <v-spacer></v-spacer>
                      <v-icon size="18" color="success" medium @click="newReserveEmployeeItem(item)"
                        >mdi-plus</v-icon
                      >
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-1">
                  <table class="doc-template_data-table ma-0 pa-0"
                      style="width: 100%" v-if="item.reserves.length">
                    <!-- <thead> -->
                      <tr>
                        <td class="font-weight-bold">{{ $t("#") }}</td>
                        <td class="font-weight-bold">
                          {{ $t("critical.employee") }}
                        </td>
                        <td class="font-weight-bold" style="max-width: 50px">
                          {{ $t("actions") }}
                        </td>
                      </tr>
                    <!-- </thead> -->
                    <!-- <tbody> -->
                      <tr v-for="(reserv, ind) in item.reserves" :key="ind">
                        <td>{{ ind + 1 }}</td>
                        <td>
                          <router-link
                            :to="'/personcontrol/profile/' + reserv.employee_id"
                            style="text-decoration: none"
                          >
                            <span v-if="$i18n.locale == 'uz_latin'">
                              {{ reserv.employee.firstname_uz_latin }}
                              {{ reserv.employee.lastname_uz_latin }}
                              {{ reserv.employee.middlename_uz_latin }}
                            </span>
                            <span v-else>
                              {{ reserv.employee.firstname_uz_cyril }}
                              {{ reserv.employee.lastname_uz_cyril }}
                              {{ reserv.employee.middlename_uz_cyril }}
                            </span>
                          </router-link>
                        </td>
                        <td class style="max-width: 40px">
                          <v-btn
                            v-if="$store.getters.checkPermission('critical-delete')"
                            class="pl-0 pr-2"
                            color="error"
                            style="min-width: 25px"
                            x-small
                            text
                            @click="deleteReserveEmployeeItem(reserv)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    <!-- </tbody> -->
                  </table>
                  
                <!-- </v-container> -->
                <span
                  v-else
                  style="display: block; text-align: center; color: red"
                  >{{ $t("noDataText") }}</span>
                  </v-container>
                </v-card>
              </td>
            </template>
            <template v-slot:item.employee="{ item }">
              <td style="max-width: 300px">
                <span v-if="$i18n.locale == 'uz_latin'">
                  {{ item.employee.firstname_uz_latin }}
                  {{ item.employee.lastname_uz_latin }}
                  {{ item.employee.middlename_uz_latin }}
                </span>
                <span v-else>
                  {{ item.employee.firstname_uz_cyril }}
                  {{ item.employee.lastname_uz_cyril }}
                  {{ item.employee.middlename_uz_cyril }}
                </span>
              </td>
            </template>
            <template v-slot:item.staff="{ item }">
              <td style="max-width: 300px">
                <span v-if="$i18n.locale == 'uz_latin'">
                  {{ item.staff.department.name_uz_latin }},
                  {{ item.staff.position.name_uz_latin }}
                </span>
                <span v-else-if="$i18n.locale == 'uz_cyril'">
                  {{ item.staff.department.name_uz_cyril }},
                  {{ item.staff.position.name_uz_cyril }}
                </span>
                <span v-else
                  >{{ item.staff.department.name_ru }},
                  {{ item.staff.position.name_ru }}</span
                >
              </td>
            </template>
            <template v-slot:item.description="{ item }">
              <td style="max-width: 200px">
                {{ item.description }}
              </td>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                v-if="$store.getters.checkPermission('critical-update')"
                class="pl-0 pr-0"
                color="blue"
                style="min-width: 25px"
                small
                text
                @click="newReserveEmployeeItem(item)"
              >
                <v-icon size="18">mdi-account-plus-outline</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("critical.employee") }}</label>
                <v-autocomplete
                  outlined
                  clearable
                  v-model="reserveEmployeeForm.employee_id"
                  :items="employees"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  class="mt-1"
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveReserveEmployee"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="dialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
              :data="employee_excel"
              :name="'Kritik_hodimlar_ruyxati_' + today + '.xls'"
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data() {
    return {
      loading: false,
      search: "",
      dialog: false,
      items: [],
      form: {},
      dialogHeaderText: "",
      page: 1,
      employees: [],
      from: 0,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      expanded: [],
      singleExpand: false,
      dialogHeaderText: "",
      reserveEmployeeForm: {},
      downloadExcel: false,
      employee_excel: [],
      today: moment().format("YYYY-MM-DD"),
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    headers() {
      return [
        {
          text: "",
          value: "data-table-expand",
          width: 30,
        },
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("critical.employee"),
          value: "employee",
        },
        { text: this.$t("critical.position"), value: "staff" },
        { text: this.$t("critical.begin_date"), value: "begin_date", width: 70 },
        { text: this.$t("critical.end_date"), value: "end_date", width: 70 },
        { text: this.$t("critical.description"), value: "description", width: 100 },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 120,
          align: "center",
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("critical-update") ||
          this.$store.getters.checkPermission("critical-delete")
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
    getRef() {
      let locale = this.$i18n.locale;
      locale = locale == "uz_latin" ? "uz_latin" : "uz_cyril";
      axios
        .get(
          this.$store.state.backend_url +
            "api/staff-criticals/get-ref/" +
            locale
        )
        .then((response) => {
          this.employees = response.data.map((v) => ({
            value: v.id,
            text:
              v.tabel +
              " " +
              v["lastname_" + locale] +
              " " +
              (v["firstname_" + locale]
                ? v["firstname_" + locale].substr(0, 1) + ". "
                : "") +
              " " +
              (v["middlename_" + locale]
                ? v["middlename_" + locale].substr(0, 1) + ". "
                : ""),
            // text: v.lastname_uz_cyril + " " + v.firstname_uz_cyril
          }));
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/staff-criticals", {
          pagination: this.dataTableOptions,
          search: this.search,
          locale: this.$i18n.locale,
        })
        .then((response) => {
          this.items = response.data;
          console.log("11111", this.items);
          this.from = response.data.from;
          this.server_items_length = response.data.total;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newReserveEmployeeItem(item) {
      this.dialogHeaderText = this.$t("critical.add_reserve_employee");
      this.reserveEmployeeForm = {
        id: Date.now(),
        critical_staff_id: item.id,
        employee_id: "",
      };
      this.dialog = true;
    },
    editReserveEmployeeItem(item) {
      this.dialogHeaderText = this.$t("Tahrirlash");
      this.reserveEmployeeForm = {
        id: item.id,
        critical_staff_id: item.critical_staff_id,
        employee_id: item.employee_id,
      };
      this.dialog = true;
    },
    saveReserveEmployee() {
      axios
        .post(
          this.$store.state.backend_url + "api/reserves/update",
          this.reserveEmployeeForm
        )
        .then((res) => {
          this.getList();
          this.dialog = false;
          const Toast = Swal.mixin({
            toast: true,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: (toast) => {
              toast.addEventListener("mouseenter", Swal.stopTimer);
              toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
          });
          Toast.fire({
            icon: "success",
            title: this.$t("create_update_operation"),
          });
        })
        .catch((err) => {
          console.log(err);
        });
    },
    deleteReserveEmployeeItem(item) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete"),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url + "api/reserves/delete/" + item.id
            )
            .then((res) => {
              this.getList();
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
    getEmployeeExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/staff-criticals/get-excel", {
          locale: this.$i18n.locale,
          page: page,
          perPage: 1000,
        })
        .then((response) => {
          new_array = response.data;
          this.employee_excel = this.employee_excel.concat(new_array);
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
  },
  mounted() {
    this.getList();
    this.getRef();
  },
};
</script>
<style scoped>
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
</style>
