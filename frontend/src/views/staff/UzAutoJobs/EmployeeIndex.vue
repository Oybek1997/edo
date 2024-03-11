<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >

      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Штатное расстановка") }}*</span>
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
          >
            <v-icon color="#107C41" left>mdi-microsoft-excel</v-icon>Юклаб олиш
          </v-btn>
        </div>
      </v-card-title>
      <!-- :items="staffData.filter((v) => v.employee_staff.length > 0)" -->
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
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
        <template v-slot:[`body.prepend`]>
          <tr>
            <td></td>
            <!-- <td></td> -->
            <td>
              <v-text-field
                v-model="filter.branchName"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.function_code"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td></td>
            <td>
              <v-text-field
                v-model="filter.department_code"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.position_name"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.position_id"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.diapazon"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.expence_type"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.personal_type"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.shift"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.gender"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td></td>
            <td>
              <v-text-field
                v-model="filter.tabel"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.staj"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td></td>
            <td></td>
            <td>
              <v-text-field
                v-model="filter.kat_number"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.buyruq_number"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.buyruq_date"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
              <v-text-field
                v-model="filter.koef_code"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.dirInDir"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.bw"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
          </tr>
          <template v-for="(item, key) in staffData">
            <tr v-for="(itema, i_key) in item.employee_staff">
              <td class="text-center">
                {{ from + key }}
              </td>
              <td class="text-left">
                {{
                  item.department
                    ? item.department.branch
                      ? item.department.branch.name
                      : ""
                    : ""
                }}
              </td>
              <td class="text-left">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      v-bind="attrs"
                      v-on="on"
                      style="white-space: normal; max-width: 100px"
                    >
                      {{
                        item.department
                          ? item.department.functional_department
                            ? item.department.functional_department
                                .functional_department_code
                            : ""
                          : ""
                      }}
                    </span>
                  </template>
                  <template>
                    <span style="white-space: normal; max-width: 100px">
                      {{
                        item.department
                          ? item.department.functional_department
                            ? item.department.functional_department[
                                "name_" + $i18n.locale
                              ]
                            : ""
                          : ""
                      }}
                    </span>
                  </template>
                </v-tooltip>
              </td>
              <td class="text-left">
                {{ item.department ? item.department["name_" + $i18n.locale] : "" }}
              </td>
              <td class="text-center">
                {{ item.department ? item.department.department_code : "-" }}
              </td>
              <td class="text-left">
                {{ item.position ? item.position["name_" + $i18n.locale] : "" }}
              </td>
              <td class="text-center">
                {{ item.position ? item.position.code : "" }}
              </td>
              <td class="text-center">
                {{ item.range ? item.range.code : "" }}
              </td>
              <td>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      v-bind="attrs"
                      v-on="on"
                      style="white-space: normal; max-width: 100px"
                    >
                      {{
                        item.expence_type
                          ? parseInt(
                              item.expence_type["name_" + $i18n.locale].split("-")[0]
                            )
                          : ""
                      }}
                    </span>
                  </template>
                  <template>
                    <span style="white-space: normal; max-width: 100px">
                      {{
                        item.expence_type ? item.expence_type["name_" + $i18n.locale] : ""
                      }}
                    </span>
                  </template>
                </v-tooltip>
              </td>
              <td>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      v-bind="attrs"
                      v-on="on"
                      style="white-space: normal; max-width: 100px"
                    >
                      {{
                        item.personal_type
                          ? parseInt(
                              item.personal_type["name_" + $i18n.locale].split("-")[0]
                            )
                          : ""
                      }}
                    </span>
                  </template>
                  <template>
                    <span style="white-space: normal; max-width: 100px">
                      {{
                        item.personal_type
                          ? item.personal_type["name_" + $i18n.locale]
                          : ""
                      }}
                    </span>
                  </template>
                </v-tooltip>
              </td>
              <td class="text-center">
                {{ itema.shift ? itema.shift.name : "" }}
              </td>
              <td class="text-center">
                {{ itema.employee ? itema.employee.gender : "" }}
              </td>
              <td
                :style="
                  itema.employee
                    ? i_key >= item.rate_count
                      ? itema.is_main_staff == 0
                        ? 'background-color: yellow; color: black;'
                        : 'background-color: red; color: white;'
                      : itema.is_main_staff == 0
                      ? 'background-color: orange; color: black;'
                      : ''
                    : 'background-color: #A5D6A7; color: black;'
                "
              >
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      v-bind="attrs"
                      v-on="on"
                      style="white-space: normal; max-width: 100px"
                    >
                      {{
                        itema.employee
                          ? i_key +
                            1 +
                            ". " +
                            itema.employee["lastname_" + $i18n.locale] +
                            " " +
                            itema.employee["firstname_" + $i18n.locale].substring(0, 1) +
                            ". " +
                            (itema.employee["middlename_" + $i18n.locale].length > 1
                              ? itema.employee["middlename_" + $i18n.locale].substring(
                                  0,
                                  1
                                ) + ". "
                              : "")
                          : i_key + 1 + ". " + itema["lastname_" + $i18n.locale]
                      }}
                    </span>
                  </template>
                  <template>
                    <span style="white-space: normal; max-width: 100px">
                      {{
                        itema.employee
                          ? i_key +
                            1 +
                            ". " +
                            itema.employee["lastname_" + $i18n.locale] +
                            " " +
                            itema.employee["firstname_" + $i18n.locale] +
                            " " +
                            (itema.employee["middlename_" + $i18n.locale].length > 1
                              ? itema.employee["middlename_" + $i18n.locale] + " "
                              : "")
                          : i_key + 1 + ". " + itema["lastname_" + $i18n.locale]
                      }}
                    </span>
                  </template>
                </v-tooltip>
              </td>
              <!-- <td
            :style="itema.employee?
            (i_key >= item.rate_count ? 
              (itema.is_main_staff==0?'background-color: yellow; color: black;':'background-color: red; color: white;') 
              : (itema.is_main_staff==0?'background-color: orange; color: black;':'')):
            ('background-color: #A5D6A7; color: black;')"
            >
              {{itema.employee?itema.employee["firstname_" + $i18n.locale]:itema["firstname_" + $i18n.locale]}}
            </td> -->
              <td
                :style="
                  itema.employee
                    ? i_key >= item.rate_count
                      ? itema.is_main_staff == 0
                        ? 'background-color: yellow; color: black;'
                        : 'background-color: red; color: white;'
                      : itema.is_main_staff == 0
                      ? 'background-color: orange; color: black;'
                      : ''
                    : 'background-color: #A5D6A7; color: black;'
                "
              >
                {{ itema.employee ? itema.employee.tabel : "" }}
              </td>
              <td>
                {{ itema.employee ? itema.employee.experience : "" }}
              </td>
              <td>
                {{ itema.employee ? itema.employee.born_date : "" }}
              </td>
              <td>
                {{ itema.employee ? itema.employee.first_work_date : "" }}
              </td>
              <td>
                {{
                  itema.employee
                    ? itema.employee.tariff_scale
                      ? itema.employee.tariff_scale.category
                      : ""
                    : ""
                }}
              </td>

              <td>
                {{ itema.employee ? itema.employee.enter_order_number : "" }}
              </td>
              <td>
                {{ itema.employee ? itema.employee.enter_order_date : "" }}
              </td>
              <td class="text-left">
                {{ item.rate_count_bp }}
              </td>
              <td class="text-left">
                {{ item.rate_count }}
              </td>
              <td
                class="text-left"
                :style="
                  item.perconFactCount > item.rate_count
                    ? 'background-color:red ; color:black;'
                    : ''
                "
              >
                {{ item.perconFactCount }}
              </td>
              <td
                class="text-left"
                :style="
                  item.perconCount > item.rate_count
                    ? 'background-color:red ; color:black;'
                    : ''
                "
              >
                {{ item.perconCount }}
              </td>
              <td class="text-left">
                {{ item.rate_count_bp - item.perconFactCount }}
              </td>
              <td class="text-left">
                {{ item.rate_count - item.perconFactCount }}
              </td>
              <td class="text-left">
                <template
                  v-if="
                    itema.employee &&
                    itema.employee.employee_coefficients &&
                    itema.employee.employee_coefficients.length > 0
                  "
                >
                  <template v-for="cofItem in coefficients">
                    <template v-for="empItem in itema.employee.employee_coefficients">
                      <tr v-if="empItem.coefficient_id === cofItem.id">
                        <td class="text-center px-2">
                          {{ cofItem.code }}
                        </td>
                        <td class="text-center px-2">
                          {{ empItem.percent + "%" }}
                        </td>
                      </tr>
                    </template>
                  </template>
                </template>
              </td>

              <td class="text-left">
                {{
                  (() => {
                    const getNumber = (data) =>
                      data ? parseInt(data["name_" + $i18n.locale].split("-")[0]) : 0;
                    const expenceType = getNumber(item.expence_type);
                    const personalType = getNumber(item.personal_type);
                    if (expenceType === 1 && personalType === 5) {
                      return "Dir";
                    } else if (expenceType === 9 && personalType === 6) {
                      return "Kas";
                    } else {
                      return "InDir";
                    }
                  })()
                }}
              </td>
              <td class="text-center">
                {{
                  item.range && item.range.code.substring(0, 1).toUpperCase() === "E"
                    ? "W"
                    : "B"
                }}
              </td>
            </tr>
          </template>
        </template>

        <!-- <template v-slot:item.id="{ item, index }">
          {{ from + index }}
        </template>
        <template v-slot:item.department_name="{ item }" style>
          {{ item.department ? item.department["name_" + $i18n.locale] : "" }}
        </template>   
        <template v-slot:item.position_code="{ item }">
          {{ item.position ? item.position.code : "" }}
        </template>
        <template v-slot:item.position_name="{ item }">
          <v-row>
            <v-col
              :title="item.position ? item.position['name_' + $i18n.locale] : ''"
            >
              {{ item.position ? item.position["name_" + $i18n.locale] : "" }}
              <v-btn
                style="float: right"
                v-if="
                  (item.department &&
                    item.department.documents &&
                    item.department.documents.legth) ||
                  item.document_staffs.length
                "
                color="#7B68EE"
                small
                icon
                @click="relationdocument(item)"
              >
                <v-icon>
                  {{ "mdi-book-open-page-variant-outline" }}
                </v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </template>    -->
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
    filter: {},
    loading: false,
    downloadExcel: false,
    application_excel: [],
    coefficients: [],
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
          value: "id",
          align: "center",
          width: 30,
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Branch"),
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("FC"),
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("department.department_name"),
          value: "department_name",
          class: "blue-grey lighten-5 lign-center",
          width: 300,
        },
        {
          text: this.$t("department.department_code"),
          value: "department_code",
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("employee.position"),
          value: "position_name",
          class: "blue-grey lighten-5 lign-center",
          width: 300,
        },
        // text: this.$t("code") + ' ' + this.$t("staff.position_id") + 'и',
        {
          text: this.$t("position_code"),
          value: "position_code",
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("Diapazon"),
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("Xarajat type"),
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("Persona Type"),
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("Smena"),
          value: "staffShift",
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("JINSI"),
          value: "gender",
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("F.I.SH"),
          value: "position_name",
          class: "blue-grey lighten-5 lign-center",
          width: 150,
        },

        {
          text: this.$t("Tab.№"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Staj"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("employee.born_date"),
          class: "blue-grey lighten-5 lign-center",
          width: 90,
        },
        {
          text: this.$t("qabul sana"),
          class: "blue-grey lighten-5 lign-center",
          width: 90,
        },
        {
          text: this.$t("Kat."),
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },

        {
          text: this.$t("Buyruq №"),
          class: "blue-grey lighten-5 lign-center",
          width: 100,
        },
        {
          text: this.$t("Buyruq sana"),
          class: "blue-grey lighten-5 lign-center",
          width: 90,
        },
        {
          text: this.$t("BP"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("TS"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("XS"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("AS"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("BPV"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("TSV"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Ustama"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Dir/InDir"),
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("B/W"),
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
        .post(this.$store.state.backend_url + "api/employe-staff/full", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          language: this.$i18n.locale,
        })
        // this.staffData = response.data.data.filter((v) => v.employee_staff.length > 0);
        .then((response) => {
          this.coefficients = response.data.coefficient;
          this.staffData = response.data.employee.data.map((v) => {
            v.perconFactCount = v.employee_staff
              ? v.employee_staff.filter((v) => v.is_main_staff == 1).length
              : 0;
            v.perconCount = v.employee_staff.length;
            let empStaf =
              v.employee_staff.length > v.rate_count
                ? v.employee_staff
                : [...v.employee_staff];
            let countt =
              v.rate_count - v.perconFactCount > 0 ? v.rate_count - v.perconFactCount : 0;
            if (
              countt > 0 &&
              !this.filter.gender &&
              !this.filter.tabel &&
              !this.filter.staj &&
              !this.filter.kat_number &&
              !this.filter.buyruq_number &&
              !this.filter.buyruq_date &&
              !this.filter.koef_code
            ) {
              empStaf = Array.from({ length: countt }, () => ({
                firstname_ru: "Вакант",
                firstname_uz_cyril: "Вакант",
                firstname_uz_latin: "Vakant",
                lastname_ru: "Вакант",
                lastname_uz_cyril: "Вакант",
                lastname_uz_latin: "Vakant",
                tabel: "Vakant",
              }));
            }

            return { ...v, employee_staff: empStaf };
          });
          this.from = response.data.employee.from;
          this.server_items_length = response.data.employee.total;
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
