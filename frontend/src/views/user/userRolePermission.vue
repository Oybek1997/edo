<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("user.user_role_permission") }}</span>
        <!-- <v-menu
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
                  @click="tableToExcel('table', 'Lorem Table')"
                  >
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    Скачать таблицу Excel
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu>  -->
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
          ref="table"
          id="table"
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
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            item-key="id"
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
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
          >
            <template v-slot:[`body.prepend`]>
              <tr class="py-0 my-0 prepend_height">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.username"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.employee"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.position"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.department_code"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.department_name"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.role"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.permission"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <!-- <td class="py-0 my-0 dense"></td> -->
              </tr>
            </template>

            <template v-slot:[`item.id`]="{ item }">{{
              items
                .map(function (x) {
                  return x.id;
                })
                .indexOf(item.id) + 1
            }}</template>
            <template v-slot:[`item.employee_id`]="{ item }">
              <td style="max-width: 100px;">
              {{
              item.employee
                ? $i18n.locale == "uz_latin"
                  ? item.employee["lastname_uz_latin"] +
                    " " +
                    item.employee["firstname_uz_latin"]
                  : item.employee["lastname_uz_cyril"] +
                    " " +
                    item.employee["firstname_uz_cyril"]
                : ""
              }}
              </td>
            </template>
            <template v-slot:[`item.username`]="{ item }">{{
              item.username.toUpperCase()
            }}</template>
            <template v-slot:[`item.position`]="{ item }">
              <td style="max-width: 150px;">
              {{
                item.employee &&
                item.employee.is_active &&
                item.employee.employee_staff &&
                item.employee.employee_staff.length &&
                item.employee.employee_staff[0].staff.position
                  ? item.employee.employee_staff[0].staff.position[
                      "name_" + $i18n.locale
                    ]
                  : ""
              }}
              </td>
            </template>
            <template v-slot:[`item.department`]="{ item }">
              <td style="max-width: 150px;">
              {{
              item.employee && item.employee.employee_staff.length > 0
                ? item.employee.employee_staff[0].staff.department[
                    "name_" + $i18n.locale
                  ]
                : ""
              }}
              </td>
            </template>
            <template v-slot:[`item.department_code`]="{ item }">
              <td style="max-width: 100px;">
              {{
              item.employee &&
              item.employee.employee_staff.length > 0 &&
              item.employee.employee_staff[0].staff.department.department_code
              }}
              </td>
            </template>
            <template v-slot:[`item.roles`]="{ item }">
              <td style="max-width: 400px;">
                <span v-for="(item, idxRole) in item.roles" :key="idxRole">
                  {{ item.name + ", " }}
                </span>
              </td>
            </template>
            <template v-slot:[`item.permissions`]="{ item }">
              <td style="max-width: 400px;">
              <span v-for="(item, idxPermission) in item.permissions" :key="idxPermission">{{
                item.name + ", "
              }}</span>
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

  </div>
</template>
<script>
const axios = require("axios").default;
import TableToExcel from "@linways/table-to-excel";
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data: () => ({
    passwordForm: {
      new_password: "",
      confirm_password: "",
    },
    assignedRoleSearch: "",
    notAssignedRoleSearch: "",
    assignedPermissionSearch: "",
    notAssignedPermissionSearch: "",
    loading: false,
    serachTermin: null,
    dialog: false,
    BPermission: false,
    editMode: null,
    filterDialog: false,
    items: [],
    employees: [],
    roles: [],
    permissions: [],
    form: { roles: [], permissions: [] },
    employee: {},
    search: "",
    fullscreen: false,
    dataTableOptions: { page: 1, itemsPerPage: 20 },
    page: 1,
    from: 0,
    server_items_length: -1,
    dialogHeaderText: "",
    filterForm: {
      username: "",
      employee: "",
      position: "",
      department_name: "",
      role: "",
      permission: "",
    },
    today: moment().format("YYYY-MM-DD"),
    user_excel: [],
    downloadExcel: false,
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    
    
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: "10px" },

        { text: this.$t("user.username"), value: "username" },

        { text: this.$t("user.employee"), value: "employee_id" },

        { text: this.$t("user.position"), value: "position" },

        { text: this.$t("user.department_code"), value: "department_code" },

        { text: this.$t("user.department_id"), value: "department" },

        { text: this.$t("user.roles"), value: "roles" },
        { text: this.$t("user.permission"), value: "permissions" },

      ];
    },
    searchData() {
      return this.$store.getters.checkPermission;
    },
    filteredSearchData() {
      let searchData = this.searchData;
      if (this.serachTermin)
        searchData = searchData.filter(
          (b) =>
            b.tabel.toLowerCase().indexOf(this.serachTermin.toLowerCase()) >=
              0 ||
            b.description
              .toLowerCase()
              .indexOf(this.serachTermin.toLowerCase()) >= 0
        );

      if (this.level.length)
        searchData = searchData.filter(
          (b) =>
            this.level.filter((val) => b.level.indexOf(val) !== -1).length > 0
        );

      return searchData;
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
        .post(this.$store.state.backend_url + "api/user-role-permission", {
          filter: this.filterForm,
          search: this.search,
          pagination: this.dataTableOptions,
        })
        .then((response) => {
          this.items = response.data.users.data;
          this.roles = response.data.roles;
          this.permissions = response.data.permissions;
          // .map((v) => {
          //   return { text: v.name, value: v.id };
          // });
          if (!this.items.length) {
            this.noDataText = this.$t("noDataText");
          }
          this.server_items_length = response.data.users.total;
          this.from = response.data.users.from;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    tableToExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    }
    
  },
  mounted() {
    this.getList();
    // this.getEmployeeList();
    // Swal.fire({
    //   position: "top-end",
    //   icon: "success",
    //   title: "Your work has been saved",
    //   showConfirmButton: false,
    //   timer: 1500
    // });
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
