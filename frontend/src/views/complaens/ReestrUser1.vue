<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("user.index") }}</span>
        <v-spacer></v-spacer>

        <!-- <v-btn class="mr-2" color outlined x-small fab @click="filterDialog = true; search = '';">
          <v-icon>mdi-magnify</v-icon>
        </v-btn>-->

        <v-btn
          outlined
          x-small
          fab
          @click="
            getUserExcel(1);
            user_excel = [];
          "
          class="mr-2"
        >
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn>

        <v-btn color="#6ac82d" dark fab x-small @click="newItem(BPermission)">
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
        <template v-slot:[`body.prepend`]>
          <tr class="py-0 my-0">
            <td class="py-0 my-0 dense"></td>
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
                v-model="filterForm.eimzo_username"
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
            <td class="py-0 my-0 dense"></td>
          </tr>
        </template>

        <template v-slot:[`item.id`]="{ item }">{{
          items
            .map(function (x) {
              return x.id;
            })
            .indexOf(item.id) + 1
        }}</template>
        <template v-slot:[`item.employee_id`]="{ item }">{{
          item.employee
            ? $i18n.locale == "uz_latin"
              ? item.employee["lastname_uz_latin"] +
                " " +
                item.employee["firstname_uz_latin"]
              : item.employee["lastname_uz_cyril"] +
                " " +
                item.employee["firstname_uz_cyril"]
            : ""
        }}</template>
        <template v-slot:[`item.username`]="{ item }">{{
          item.username.toUpperCase()
        }}</template>
        <template v-slot:[`item.position`]="{ item }">
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
        </template>
        <template v-slot:[`item.department`]="{ item }">{{
          item.employee && item.employee.employee_staff.length > 0
            ? item.employee.employee_staff[0].staff.department[
                "name_" + $i18n.locale
              ]
            : ""
        }}</template>
        <template v-slot:[`item.department_code`]="{ item }">{{
          item.employee &&
          item.employee.employee_staff.length > 0 &&
          item.employee.employee_staff[0].staff.department.department_code
        }}</template>       
      </v-data-table>
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
const moment = require("moment");
export default {
  data: () => ({
    loading: false,
    items: [],
    employees: [],
    form: { },
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
    // user_excel: [],
    // downloadExcel: false,
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
        // { text: this.$t("ERI"), value: "eimzo_username", width: 50 },

        // { text: this.$t("user.roles"), value: "roles" },
        // {
        //   text: this.$t("actions"),
        //   value: "actions",
        //   // width: 180,
        //   align: "center",
        // },
      ];
    },
    searchData() {
      return this.$store.getters.checkPermission;
    },
    // filteredSearchData() {
    //   let searchData = this.searchData;
    //   if (this.serachTermin)
    //     searchData = searchData.filter(
    //       (b) =>
    //         b.tabel.toLowerCase().indexOf(this.serachTermin.toLowerCase()) >=
    //           0 ||
    //         b.description
    //           .toLowerCase()
    //           .indexOf(this.serachTermin.toLowerCase()) >= 0
    //     );

    //   if (this.level.length)
    //     searchData = searchData.filter(
    //       (b) =>
    //         this.level.filter((val) => b.level.indexOf(val) !== -1).length > 0
    //     );

    //   return searchData;
    // },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    toggleExpand($event) {},
    // addRole(item) {
    //   this.form.roles.push(item);
    // },
    // removeRole(item) {
    //   this.form.roles = this.form.roles.filter((v) => v.id != item.id);
    // },
    // addPermission(item) {
    //   console.log(item);
    //   this.form.permissions.push(item);
    // },
    // removePermission(item) {
    //   this.form.permissions = this.form.permissions.filter(
    //     (v) => v.id != item.id
    //   );
    // },
    // getEmployeeList() {
    //   axios
    //     .post(this.$store.state.backend_url + "api/employee-search", {
    //       search: this.search,
    //     })
    //     .then((res) => {
    //       this.employees = res.data.data;
    //       this.employees = this.employees.map((v) => {
    //         v.full_name =
    //           this.$i18n.locale != "ru"
    //             ? v["firstname_" + this.$i18n.locale] +
    //               " " +
    //               v["lastname_" + this.$i18n.locale] +
    //               " " +
    //               v["middlename_" + this.$i18n.locale]
    //             : v.firstname_uz_cyril +
    //               " " +
    //               v.lastname_uz_cyril +
    //               " " +
    //               v.middlename_uz_cyril;
    //         v.search =
    //           v.firstname_uz_cyril +
    //           " " +
    //           v.lastname_uz_cyril +
    //           " " +
    //           v.middlename_uz_cyril +
    //           " " +
    //           v.firstname_uz_latin +
    //           " " +
    //           v.lastname_uz_latin +
    //           " " +
    //           v.middlename_uz_latin +
    //           " " +
    //           v.tabel;
    //         return v;
    //       });
    //     })
    //     .catch((err) => {
    //       console.error(err);
    //     });
    // },
    // getUserExcel(page) {
    //   let new_array = [];
    //   this.loading = true;
    //   axios
    //     .post(this.$store.state.backend_url + "api/users/get-excel", {
    //       locale: this.$i18n.locale,
    //       page: page,
    //       perPage: 1000,
    //     })
    //     .then((response) => {
    //       new_array = response.data;
    //       this.user_excel = this.user_excel.concat(new_array);
    //       if (response.data.length == 1000) {
    //         this.getUserExcel(++page);
    //       } else {
    //         this.loading = false;
    //         this.downloadExcel = true;
    //       }
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //       this.loading = false;
    //     });
    // },

    // getRef() {
    //   axios
    //     .get(
    //       this.$store.state.backend_url +
    //         "api/employees/get-ref/" +
    //         this.$i18n.locale
    //     )
    //     .then((response) => {
    //       this.employees = response.data.employees;
    //       this.companies = response.data.companies;
    //       this.staff = response.data.staff;
    //       this.tariffScales = response.data.tariff_scales;
    //       this.countries = response.data.countries;
    //       this.regions = response.data.regions;
    //       this.districts = response.data.districts;
    //       this.nationalities = response.data.nationalities;
    //       this.addressTypes = response.data.address_types.map((v) => ({
    //         value: v.id,
    //         text: v["name_" + this.$i18n.locale],
    //       }));
    //       this.coefficients = response.data.coefficients.map((v) => ({
    //         value: v.id,
    //         text: v.code + " " + v.description,
    //       }));
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //     });
    // },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/complaens/users", {
          filter: this.filterForm,
          search: this.search,
          language: this.$i18n.locale,
          pagination: this.dataTableOptions,
        })
        .then((response) => {
          console.log(response);
          this.items = response.data.data;
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
    // getList() {
    //   this.loading = true;
    //   axios
    //     .post(this.$store.state.backend_url + "api/users-filter", {
    //       filter: this.filterForm,
    //       search: this.search,
    //       pagination: this.dataTableOptions,
    //     })
    //     .then((response) => {
    //       this.items = response.data.users.data;
    //       this.roles = response.data.roles;
    //       this.permissions = response.data.permissions;
    //       // .map((v) => {
    //       //   return { text: v.name, value: v.id };
    //       // });
    //       if (!this.items.length) {
    //         this.noDataText = this.$t("noDataText");
    //       }
    //       this.server_items_length = response.data.users.total;
    //       this.from = response.data.users.from;
    //       this.loading = false;
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //       this.loading = false;
    //     });
    // },
    // newItem() {
    //   this.dialogHeaderText = this.$t("user.newUser");
    //   this.form = {
    //     id: Date.now(),
    //     name: "",
    //     email: "",
    //     phone: "",
    //     employee_id: "",
    //     position: "",
    //     username: "",
    //     password: "",
    //     permissions: [],
    //     roles: [],
    //   };
    //   this.dialog = true;
    //   this.BPermission = true;
    //   this.editMode = false;
    //   if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    // },
    // editPassword() {
    //   this.passwordForm = {
    //     new_password: "",
    //     confirm_password: "",
    //   };
    //   this.passwordDialog = true;
    // },
    // editItem(item, BPermission) {
    //   this.dialogHeaderText = this.$t("user.updateUser");
    //   this.formIndex = this.items.indexOf(item);
    //   this.form = Object.assign({}, item);
    //   // this.form.roleIds = item.roles.map((v) => v.id);
    //   if (BPermission != true) this.dialog = true;
    //   else this.dialogPermission = true;
    //   this.editMode = true;
    //   if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    //   this.employees = [];
    //   if (this.form.employee_id) {
    //     if (!this.employees.some((v) => v.id == this.form.employee_id)) {
    //       this.employees = [this.form.employee].map((v) => {
    //         v.full_name =
    //           this.$i18n.locale != "ru"
    //             ? v["firstname_" + this.$i18n.locale] +
    //               " " +
    //               v["lastname_" + this.$i18n.locale] +
    //               " " +
    //               v["middlename_" + this.$i18n.locale]
    //             : v.firstname_uz_cyril +
    //               " " +
    //               v.lastname_uz_cyril +
    //               " " +
    //               v.middlename_uz_cyril;
    //         v.search =
    //           v.firstname_uz_cyril +
    //           " " +
    //           v.lastname_uz_cyril +
    //           " " +
    //           v.middlename_uz_cyril +
    //           " " +
    //           v.firstname_uz_latin +
    //           " " +
    //           v.lastname_uz_latin +
    //           " " +
    //           v.middlename_uz_latin +
    //           " " +
    //           v.tabel;
    //         return v;
    //       });
    //     }
    //   }
    // },
    // save() {
    //   if (this.$refs.dialogForm.validate())
    //     axios
    //       .post(this.$store.state.backend_url + "api/users/update", this.form)
    //       .then((res) => {
    //         this.getList();
    //         this.dialog = false;
    //         const Toast = Swal.mixin({
    //           toast: true,
    //           position: "top-end",
    //           showConfirmButton: false,
    //           timer: 3000,
    //           timerProgressBar: true,
    //           onOpen: (toast) => {
    //             toast.addEventListener("mouseenter", Swal.stopTimer);
    //             toast.addEventListener("mouseleave", Swal.resumeTimer);
    //           },
    //         });

    //         Toast.fire({
    //           icon: "success",
    //           title: this.$t("create_update_operation"),
    //         });
    //       })
    //       .catch((err) => {
    //         console.log(err);
    //       });
    // },
    // deleteItem(item) {
    //   const index = this.items.indexOf(item);
    //   Swal.fire({
    //     title: this.$t("swal_title"),
    //     text: this.$t("swal_text"),
    //     icon: "warning",
    //     showCancelButton: true,
    //     confirmButtonColor: "#3085d6",
    //     cancelButtonColor: "#d33",
    //     confirmButtonText: this.$t("swal_delete"),
    //   }).then((result) => {
    //     if (result.value) {
    //       axios
    //         .delete(
    //           this.$store.state.backend_url + "api/users/delete/" + item.id
    //         )
    //         .then((res) => {
    //           this.getList(this.page, this.itemsPerPage);
    //           this.dialog = false;
    //           Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
    //         })
    //         .catch((err) => {
    //           Swal.fire({
    //             icon: "error",
    //             title: this.$t("swal_error_title"),
    //             text: this.$t("swal_error_text"),
    //             //footer: "<a href>Why do I have this issue?</a>"
    //           });
    //           console.log(err);
    //         });
    //     }
    //   });
    // },
    // viewEmployee(employee) {
    //   this.employee = employee;
    //   this.employeeViewDialog = true;
    // },
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
