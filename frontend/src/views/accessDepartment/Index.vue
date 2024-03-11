<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("accessDepartment.index") }}</span>
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
                  v-if="$store.getters.checkPermission('access-department-create')"
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
                      getDocsheetsExcel(1);
                      punkt_excel = [];
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
            :search="search"
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            disable-sort
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
            <!-- <template v-slot:body.prepend>
              <tr class="py-0 my-0">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.tabel"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.info"
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
                    v-model="filterForm.accessType"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.actions"
                    type="text"
                    disabled
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
              </tr>
            </template> -->

            <template v-slot:item.id="{ item }">
              {{
                items
                  .map(function (x) {
                    return x.id;
                  })
                  .indexOf(item.id) + from
              }}
            </template>
            <template v-slot:item.department.department_code="{ item }">
              <span>{{
                item.department ? item.department.department_code : ""
              }}</span>
            </template>
            <template v-slot:item.department.department_name="{ item }">
              <span>{{
                item.department ? item.department["name_" + $i18n.locale] : ""
              }}</span>
            </template>

            <template v-slot:item.employee.info="{ item }">
              <span>
                {{ item.employee["lastname_" + $i18n.locale] }}
                {{ item.employee["firstname_" + $i18n.locale] }}
                {{ item.employee["middlename_" + $i18n.locale] }}
              </span>
            </template>
            <template v-slot:item.access_type.name="{ item }">
              <span>
                {{ item.access_type["name_" + $i18n.locale] }}
              </span>
            </template>
            <template v-slot:item.actions="{ item }" >
              <div style="text-align: center;">
              <!-- <v-btn
                v-if="$store.getters.checkPermission('access-department-update')"
                color="blue"
                small
                text
                @click="editItem(item)"
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn> -->
              <v-btn
                v-if="$store.getters.checkPermission('access-department-delete')"
                class="pl-0 pr-2"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)"
              >
                <v-icon size="18">mdi-trash-can-outline</v-icon>
              </v-btn>
              </div>
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
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" sm="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("accessDepartment.employee") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.employee_id"
                  :items="employees"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" sm="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("accessDepartment.department") }}</label>
                <v-autocomplete
                chips
                  clearable
                  v-model="form.department_id"
                  :items="departments"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  multiple
                  class="mt-1"
                ></v-autocomplete>
              </v-col>
              <!-- <v-col cols="12" sm="6">
                <v-select
                chips
                label="Select"
                v-model="form.department_id"
                :items="departments"
                multiple
              ></v-select>
              </v-col> -->
              <!-- <v-col cols="12" sm="6">
                <label for>{{ $t("accessDepartment.accessType") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.access_type_id"
                  :items="accessTypes"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col> -->
            </v-row>
          </v-form>
          <!-- <small color="red">{{ $t("input_required") }}</small> -->
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="save"
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
export default {
  data() {
    return {
      loading: false,
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      employees: [],
      departments: [],
      accessTypes: [],
      form: {},
      filterForm: {
        tabel: "",
        firstname: "",
        lastname: "",
        middlename: "",
        department_code: "",
        department_name: "",
        info: "",
        accessType: "",
      },
      dialogHeaderText: "",
      page: 1,
      from: 1,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("accessDepartment.tabel"),
          value: "employee.tabel",
          width: 30,
        },
        {
          text: this.$t("accessDepartment.employee"),
          value: "employee.info",
          width: 300,
        },
        {
          text: this.$t("department.department_code"),
          value: "department.department_code",
          width: 80,
        },
        {
          text: this.$t("accessDepartment.department"),
          value: "department.department_name",
          width: 300,
        },
        {
          text: this.$t("accessDepartment.access"),
          value: "access_type.name",
          width: 80,
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 30,
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("access-department-delete")
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
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/access-departments", {
          pagination: this.dataTableOptions,
          search: this.search,
          filter: this.filterForm,
          locale: this.$i18n.locale,
        })
        .then((response) => {
          this.items = response.data[0].data;
          // this.employees = response.data.employee;
          // this.departments = response.data.department;
          // this.accessTypes = response.data.access_type;
          this.server_items_length = response.data[0].total;
          this.from = response.data[0].from;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getRef() {
      let locale = this.$i18n.locale;
      locale = locale == "uz_latin" ? "uz_latin" : "uz_cyril";
      axios
        .get(
          this.$store.state.backend_url +
            "api/access-departments/get-ref/" +
            locale
        )
        .then((response) => {
          this.employees = response.data.employees.map((v) => ({
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
          }));
          this.departments = response.data.departments.map((v) => ({
            value: v.id,
            text: v.department_code + " " + v["name_" + locale],
          }));
          this.accessTypes = response.data.access_types.map((v) => ({
            value: v.id,
            text: v["name_" + locale],
          }));
        })
        .catch((error) => {
          console.log(error);
        });
    },
    newItem() {
      if (this.$store.getters.checkPermission("access-department-create")) {
        this.dialogHeaderText = this.$t("accessDepartment.create");
        this.form = {
          id: Date.now(),
          employee_id: "",
          department_id: "",
          access_type_id: 1,
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("access-department-update")) {
        this.dialogHeaderText = this.$t("access-department.edit");
        this.formIndex = this.items.indexOf(item);
        this.form = Object.assign({}, item);
        this.dialog = true;
        this.editMode = true;
        if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      }
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/access-departments/update",
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
      if (this.$store.getters.checkPermission("access-department-delete")) {
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
                  "api/access-departments/delete/" +
                  item.id
              )
              .then((res) => {
                this.getList(this.page, this.itemsPerPage);
                this.dialog = false;
                // Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
              })
              .catch((err) => {
                Swal.fire({
                  icon: "error",
                  title: this.$t("swal_error_title"),
                  text: this.$t("swal_error_text"),
                });
                console.log(err);
              });
          }
        });
      }
    },
  },
  mounted() {
    this.getList();
    this.getRef();
  },
  created() {},
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
</style>
