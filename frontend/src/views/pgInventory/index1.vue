<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("user.index") }}</span>
        <div class="headerSearch d-flex align-center">
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
                  @click="newItem(BPermission)"
                >
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                  </v-list-item-title></v-list-item
                >
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title
                  @click="
                    getUserExcel(1);
                    user_excel = [];
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
            :loading="loading"
            :headers="headers"
            :items="items"
            single-expand
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
            
            <template v-slot:item.name="{ item }">
              <span style="white-space: wh_name; max-width: 100px">
                {{ item.wh_name}}
              </span>
            </template>
            <template v-slot:item.wh_number="{ item }">
              <span style="white-space: normal; max-width: 100px">
                {{ item.wh_number }}
              </span>
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
const moment = require("moment");
export default {
  data: () => ({
    passwordForm: {
      new_password: "",
      confirm_password: "",
    },
    passwordDialog: false,
    assignedRoleSearch: "",
    notAssignedRoleSearch: "",
    assignedPermissionSearch: "",
    notAssignedPermissionSearch: "",
    loading: false,
    serachTermin: null,
    employeeViewDialog: false,
    dialog: false,
    dialogPermission: false,
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

        { text: this.$t("wh_name"), value: "wh_name" },
        { text: this.$t("wh_number"), value: "wh_number" },
        { text: this.$t("quarter_id"), value: "quarter_id" },

        {
          text: this.$t("actions"),
          value: "actions",
          // width: 180,
          align: "center",
        },
      ];
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

    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/pg_inventory/wh", {        
          filter: this.filterForm,
          search: this.search,
          pagination: this.dataTableOptions,
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
    newItem() {
      this.dialogHeaderText = this.$t("user.newUser");
      this.form = {
        id: Date.now(),
        name: "",
        email: "",
        phone: "",
        employee_id: "",
        position: "",
        username: "",
        password: "",
        permissions: [],
        roles: [],
      };
      this.dialog = true;
      this.BPermission = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editPassword() {
      this.passwordForm = {
        new_password: "",
        confirm_password: "",
      };
      this.passwordDialog = true;
    },
    editItem(item, BPermission) {
      this.dialogHeaderText = this.$t("user.updateUser");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      // this.form.roleIds = item.roles.map((v) => v.id);
      if (BPermission != true) this.dialog = true;
      else this.dialogPermission = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      this.employees = [];
      if (this.form.employee_id) {
        if (!this.employees.some((v) => v.id == this.form.employee_id)) {
          this.employees = [this.form.employee].map((v) => {
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
          .post(this.$store.state.backend_url + "api/users/update", this.form)
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