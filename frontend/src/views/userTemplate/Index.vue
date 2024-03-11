<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("unblocked_users") }}</span>
        <v-spacer></v-spacer>

        <!-- <v-btn class="mr-2" color outlined x-small fab @click="filterDialog = true; search = '';">
          <v-icon>mdi-magnify</v-icon>
        </v-btn>-->

        <v-btn
          v-if="$store.getters.checkPermission('unblock_users')"
          color="#6ac82d"
          dark
          fab
          x-small
          @click="newItem()"
        >
          <v-icon>mdi-plus</v-icon>
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
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
        single-expand
        :expanded="[]"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        item-key="id"
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
        <!--<template v-slot:[`body.prepend`]>
          <tr class="py-0 my-0">
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
            <td class="py-0 my-0 dense"></td>
          </tr>
        </template>-->

        <template v-slot:[`item.id`]="{ item }">{{
          items
            .map(function (x) {
              return x.id;
            })
            .indexOf(item.id) + 1
        }}</template>
        <template v-slot:[`item.employee_id`]="{ item }">{{
          item.user.employee
            ? $i18n.locale == "uz_latin"
              ? item.user.employee["lastname_uz_latin"] +
                " " +
                item.user.employee["firstname_uz_latin"]
              : item.user.employee["lastname_uz_cyril"] +
                " " +
                item.user.employee["firstname_uz_cyril"]
            : ""
        }}</template>
        <template v-slot:[`item.username`]="{ item }">{{
          item.username
        }}</template>
        <template v-slot:[`item.position`]="{ item }">
          {{
            item.user.employee &&
            item.user.employee.employee_staff &&
            item.user.employee.employee_staff.length &&
            item.user.employee.employee_staff[0].staff.position
              ? item.user.employee.employee_staff[0].staff.position[
                  "name_" + $i18n.locale
                ]
              : ""
          }}
        </template>
        <template v-slot:[`item.department`]="{ item }">{{
          item.user.employee && item.user.employee.employee_staff.length > 0
            ? item.user.employee.employee_staff[0].staff.department[
                "name_" + $i18n.locale
              ]
            : ""
        }}</template>
        <template v-slot:[`item.department_code`]="{ item }">{{
          item.user.employee &&
          item.user.employee.employee_staff.length > 0 &&
          item.user.employee.employee_staff[0].staff.department.department_code
        }}</template>
        <template v-slot:[`item.eimzo_username`]="{ item }"
          ><span v-if="item.eimzo_username"
            ><v-icon style="color: green">mdi-check-all</v-icon></span
          >
          <span v-if="!item.eimzo_username"
            ><v-icon style="color: red">mdi-minus</v-icon></span
          ></template
        >
        <template v-slot:[`item.actions`]="{ item }">
          <v-btn
            v-if="$store.getters.checkPermission('unblock_users')"
            class="px-1"
            color="red"
            small
            text
            @click="deleteItem(item)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("user.employee_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.user_id"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search"
                  :items="users"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="fullname"
                  item-value="id"
                >
                </v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
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

    <!-- begin Users filtr -->
    <v-dialog
      v-model="filterDialog"
      persistent
      max-width="800px"
      :fullscreen="fullscreen"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("filter") }}</span>
          <v-spacer></v-spacer>
          <v-btn
            class="mr-2"
            color
            outlined
            x-small
            fab
            @click="fullscreen = !fullscreen"
          >
            <v-icon>mdi-window-maximize</v-icon>
          </v-btn>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="filterDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save">
            <v-row class="ma-0 pa-0">
              <v-col cols="6" sm="6" class="ma-0 pa-1">
                <label for>{{ $t("user.username") }}</label>
                <v-text-field
                  v-model="filterForm.username"
                  class="ma-0 pa-0"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" sm="6" class="ma-0 pa-1">
                <label for>{{ $t("user.employee_id") }}</label>
                <v-text-field
                  v-model="filterForm.employee_id"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("user.position") }}</label>
                <v-text-field
                  v-model="filterForm.position"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <!-- <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t('user.department_id') }}</label>
                <v-text-field v-model="filterForm.department_id" hide-details="auto" dense outlined></v-text-field>
              </v-col>-->

              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("user.department_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="filterForm.department_id"
                  :items="employee"
                  item-text="name"
                  item-value="id"
                  multiplehide-details="auto"
                  multiple
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="getList()">
            <v-icon>mdi-magnify</v-icon>
            {{ $t("filter") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- end Users filtr -->
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data: () => ({
    loading: false,
    serachTermin: null,
    employeeViewDialog: false,
    dialog: false,
    BPermission: false,
    editMode: null,
    filterDialog: false,
    items: [],
    employees: [],
    users: [],
    form: {},
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

        { text: this.$t("user.username"), value: "user.username" },

        { text: this.$t("user.employee"), value: "employee_id" },

        { text: this.$t("user.position"), value: "position" },

        { text: this.$t("user.department_code"), value: "department_code" },

        { text: this.$t("user.department_id"), value: "department" },
        {
          text: this.$t("actions"),
          value: "actions",
          // width: 180,
          align: "center",
        },
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
    toggleExpand($event) {},

    getEmployeeList() {
      axios
        .post(this.$store.state.backend_url + "api/user-search", {
          search: this.search,
        })
        .then((res) => {
          this.users = res.data.data.map((v) => {
            v.fullname =
              this.$i18n.locale != "uz_latin"
                ? v.employee["lastname_uz_latin"] +
                  " " +
                  v.employee["firstname_uz_latin"] +
                  " " +
                  v.employee["middlename_uz_latin"]
                : v.employee.firstname_uz_cyril +
                  " " +
                  v.employee.lastname_uz_cyril +
                  " " +
                  v.employee.middlename_uz_cyril +
                  " " +
                  v.username;
            return v;
          });
          // this.employees = this.employees.map(v => {
          //   v.full_name =
          // this.$i18n.locale != "ru"
          //   ? v["firstname_" + this.$i18n.locale] +
          //     " " +
          //     v["lastname_" + this.$i18n.locale] +
          //     " " +
          //     v["middlename_" + this.$i18n.locale]
          //   : v.firstname_uz_cyril +
          //     " " +
          //     v.lastname_uz_cyril +
          //     " " +
          //     v.middlename_uz_cyril;
          // v.search =
          //   v.firstname_uz_cyril +
          //   " " +
          //   v.lastname_uz_cyril +
          //   " " +
          //   v.middlename_uz_cyril +
          //   " " +
          //   v.firstname_uz_latin +
          //   " " +
          //   v.lastname_uz_latin +
          //   " " +
          //   v.middlename_uz_latin +
          //   " " +
          //   v.tabel;
          //   return v;
          // });
        })
        .catch((err) => {
          console.error(err);
        });
    },

    getRef() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-ref/" +
            this.$i18n.locale
        )
        .then((response) => {
          this.employees = response.data.employees;
          this.companies = response.data.companies;
          this.staff = response.data.staff;
          this.tariffScales = response.data.tariff_scales;
          this.countries = response.data.countries;
          this.regions = response.data.regions;
          this.districts = response.data.districts;
          this.nationalities = response.data.nationalities;
          this.addressTypes = response.data.address_types.map((v) => ({
            value: v.id,
            text: v["name_" + this.$i18n.locale],
          }));
          this.coefficients = response.data.coefficients.map((v) => ({
            value: v.id,
            text: v.code + " " + v.description,
          }));
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/unblock-users", {
          filter: this.filterForm,
          search: this.search,
          pagination: this.dataTableOptions,
        })
        .then((response) => {
          this.items = response.data.users.data;
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
    newItem() {
      this.dialogHeaderText = this.$t("add");
      this.form = {
        id: Date.now(),
        user_id: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("user.updateUser");
      this.dialog = true;
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      this.employees = [];
      if (this.form.user.employee_id) {
        if (!this.employees.some((v) => v.id == this.form.user.employee_id)) {
          this.employees = [this.form.user.employee].map((v) => {
            v.full_name =
              this.$i18n.locale != "ru"
                ? v["firstname_" + this.$i18n.locale] +
                  " " +
                  v["lastname_" + this.$i18n.locale] +
                  " " +
                  v["middlename_" + this.$i18n.locale]
                : v.firstname_uz_cyril +
                  " " +
                  v.lastname_uz_cyril +
                  " " +
                  v.middlename_uz_cyril;
            v.search =
              v.firstname_uz_cyril +
              " " +
              v.lastname_uz_cyril +
              " " +
              v.middlename_uz_cyril +
              " " +
              v.firstname_uz_latin +
              " " +
              v.lastname_uz_latin +
              " " +
              v.middlename_uz_latin +
              " " +
              v.tabel;
            return v;
          });
        }
      }
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/unblock-users/update",
            this.form
          )
          .then((res) => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
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
    deleteItem(item) {
      const index = this.items.indexOf(item);
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
              this.$store.state.backend_url +
                "api/unblock-users/delete/" +
                item.id
            )
            .then((res) => {
              this.getList(this.page, this.itemsPerPage);
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
    viewEmployee(employee) {
      this.employee = employee;
      this.employeeViewDialog = true;
    },
  },
  mounted() {
    this.getList();
  },
};
</script>

<style scoped>
.dense {
  padding: 0px;
  height: 10px !important;
}

.dense .v-text-field__details {
  display: none !important;
}

.dense .v-text-field {
  padding: 0px;
  margin: 0px;
}

.dense div div div {
  margin-bottom: 0px !important;
}
</style>
