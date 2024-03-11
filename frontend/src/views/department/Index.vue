<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("department.departments") }}</span>
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
                  v-if="$store.getters.checkPermission('department-create')"
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
            :loading="loading"
            :headers="headers"
            :items="items"
            single-expand
            :expanded="expanded"
            item-key="id"
            show-expand
            @dblclick:row="rowClick"
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
            <template v-slot:body.prepend="{ item }">
              <tr class="prepend_height">
                <td></td>
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
                    v-model="filter.department_name"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td></td>
                <td></td>
                <td>
                  <v-text-field
                    v-model="filter.parent_department_code"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-autocomplete
                    clearable
                    v-model="filter.department_type_id"
                    :items="
                      departmentTypes.map((v) => ({
                        text: v['name_' + $i18n.locale],
                        value: v.id,
                      }))
                    "
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                  <v-autocomplete
                    clearable
                    v-model="filter.branch_id"
                    :items="
                      branches.map((v) => ({
                        text: v.name,
                        value: v.id,
                      }))
                    "
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td></td>
              </tr>
            </template>
            <template v-slot:item.id="{ item }">
              {{
                items
                  .map(function (x) {
                    return x.id;
                  })
                  .indexOf(item.id) + from
              }}
            </template>
            <template v-slot:item.name="{ item }">
              <td style="max-width: 150px">
                <span style="white-space: normal; max-width: 100px">
                  {{ item["name_" + $i18n.locale] }}
                </span>
              </td>
            </template>
            <template v-slot:item.position_id="{ item }">
              <!-- <span style="white-space: normal; max-width: 100px">{{
                item.staff.map(v => ({
                  text: v.position["name_" + $i18n.locale],
                  value: v.position_id
                }))
              }}</span>-->
              <td style="max-width: 500px">
                <span v-for="staff in item.staff" :key="staff.index"
                  >{{
                    staff.position ? staff.position["name_" + $i18n.locale] : ""
                  }}
                  ,</span>
              </td>
            </template>

            <template v-slot:item.parent_id="{ item }">
              <td style="max-width: 100px">
                <span
                  v-if="item.parent"
                  >{{ item.parent["name_" + $i18n.locale] }}</span>
              </td>
            </template>
            <template v-slot:item.functional_parent_id="{ item }">
              <td style="max-width: 100px">
              <span
                v-if="item.functional_parent"
                >{{ item.functional_parent["name_" + $i18n.locale] }}</span
              >
              </td>
            </template>
            <template v-slot:item.department_type_id="{ item }">
              <td style="max-width: 100px">
              <span
                v-if="item.department_type"
                >{{ item.department_type["name_" + $i18n.locale] }}</span
              >
              </td>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                v-if="$store.getters.checkPermission('department-update')"
                class="pl-0 pr-2"
                color="blue"
                style="min-width: 25px"
                small
                text
                @click="editItem(item)"
              >
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                v-if="$store.getters.checkPermission('personal_type-delete')"
                class="pl-0 pr-2"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)"
              >
                <v-icon size="18">mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>

            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-3">
                <v-card class="my-2">
                  <div>
                    <v-system-bar window color="#eee">
                      <b>{{ $t("") }}</b>
                      <v-spacer></v-spacer>
                      <!-- <v-icon size="18" color="success" medium @click="newReserveEmployeeItem(item)"
                        >mdi-plus</v-icon
                      > -->
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-1">
                  <table class="doc-template_data-table ma-0 pa-0"
                      style="width: 100%">
                  <tr>
                    <th>{{ $t("position.index") }}</th>
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("staff.rate_count") }}
                    </th>
                    <th>{{ $t("ranges.index") }}</th>
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("message.personalType") }}
                    </th>
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("message.expenceType") }}
                    </th>
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("staff.order_date") }}
                    </th>
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("staff.order_number") }}
                    </th>
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("staff.begin_date") }}
                    </th>
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("staff.end_date") }}
                    </th>
                  </tr>
                  <tr v-for="(itm, idx) in item.staff" :key="idx">
                    <td style="max-width: 200px; white-space: normal">
                      {{ itm.position ? itm.position.name_ru : "" }}
                    </td>
                    <td style="max-width: 100px; white-space: normal">
                      {{ itm.rate_count }}
                    </td>
                    <td>{{ itm.range ? itm.range.code : "" }}</td>
                    <td>
                      {{
                        itm.personal_type
                          ? itm.personal_type["name_" + $i18n.locale]
                          : ""
                      }}
                    </td>
                    <td>
                      {{
                        itm.expence_type
                          ? itm.expence_type["name_" + $i18n.locale]
                          : ""
                      }}
                    </td>
                    <td>{{ itm.order_date }}</td>
                    <td>{{ itm.order_number }}</td>
                    <td>{{ itm.begin_date }}</td>
                    <td>{{ itm.end_date }}</td>
                  </tr>
                </table>
                </v-container>
                </v-card>
              </td>
              <!-- Employee Coefficients view -->
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0">
          <span class="dialogTitle">{{ $t("department.dialog") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("department.parent_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.parent_id"
                  :items="departmentList"
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("department.functional_parent_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.functional_parent_id"
                  :items="departmentList"
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("department.department_type_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.department_type_id"
                  :items="
                    departmentTypes.map((v) => ({
                      text: v ? v.name_ru : '',
                      value: v.id,
                    }))
                  "
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("name_uz_latin") }}</label>
                <v-text-field
                  v-model="form.name_uz_latin"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("name_uz_cyril") }}</label>
                <v-text-field
                  v-model="form.name_uz_cyril"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("name_ru") }}</label>
                <v-text-field
                  v-model="form.name_ru"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("department.department_code") }}</label>
                <v-text-field
                  v-model="form.department_code"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("department.branch") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.branch_id"
                  :items="branches"
                  :item-text="'name'"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("department.manager_staff") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.manager_staff_id"
                  :items="form.staff"
                  :item-text="'position.name_' + $i18n.locale"
                  item-value="id"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
                <v-row class="mx-0">
                  <v-col
                    cols="12"
                    class="my-0 py-0"
                    v-for="(item, index) in departmentManagers"
                    :key="index"
                  >
                    {{ index + 1 }}.
                    {{ item && item.employee ? item.employee["tabel"] : "" }}-
                    {{
                      item.employee
                        ? $i18n.locale == "uz_latin"
                          ? item.employee["firstname_uz_latin"]
                          : item.employee["firstname_uz_cyril"]
                        : ""
                    }}
                    {{
                      item.employee
                        ? $i18n.locale == "uz_latin"
                          ? item.employee["lastname_uz_latin"]
                          : item.employee["lastname_uz_cyril"]
                        : ""
                    }}
                    {{
                      item.employee
                        ? $i18n.locale == "uz_latin"
                          ? item.employee["middlename_uz_latin"]
                          : item.employee["middlename_uz_cyril"]
                        : ""
                    }}
                  </v-col>
                </v-row>
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
  data: () => ({
    expanded: [],
    page: 1,
    from: 0,
    server_items_length: -1,
    dataTableOptions: { page: 1, itemsPerPage: 20 },
    dataTableValue: [],
    loading: false,
    search: "",
    dialog: false,
    editMode: null,
    items: [],
    deps: [],
    branches: [],
    company: [],
    departmentTypes: [],
    users: [],
    form: {},
    filterDialog: false,
    fullscreen: false,
    filter: {
      parent_department_code: "",
      department_type_id: "",
      department_code: "",
      department_name: "",
      branch_id: "",
    },
    dialogHeaderText: "",
    staff: [],
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    departmentManagers() {
      if (this.form.manager_staff_id) {
        if (
          Object.keys(this.form).length !== 0 &&
          this.form.staff.find((v) => v.id == this.form.manager_staff_id)
        ) {
          let employee_staff = this.form.staff.find(
            (v) => v.id == this.form.manager_staff_id
          ).employee_staff;
          console.log(employee_staff);
          return employee_staff.filter(
            (es) => es.is_active == 1 && es.employee
          );
        }
      } else [];
    },
    departmentList() {
      return this.deps.map((value) => {
        let v = value;
        v.name_uz_latin = v.department_code
          ? v.department_code + " " + v.name_uz_latin
          : "";

        v.name_uz_cyril = v.department_code
          ? v.department_code + " " + v.name_uz_cyril
          : "";

        v.name_ru =
          v.department_code && v.name_ru
            ? v.department_code + " " + v.name_ru
            : "";
        return v;
      });
    },
    headers() {
      return [
        { text: "", value: "data-table-expand", width: 30 },
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("department.department_code"),
          value: "department_code",
          width: 30,
        },
        {
          text: this.$t("department.name"),
          value: "name",
          class: "my-5",
        },
        {
          text: this.$t("department.manager_staff"),
          value: "position_id",
          class: "my-5",
        },
        {
          text: this.$t("department.functional_parent_id"),
          value: "functional_parent_id",
          width: 100,
        },
        {
          text: this.$t("department.parent_id"),
          value: "parent_id",
          width: 100,
        },
        {
          text: this.$t("department.department_type_id"),
          value: "department_type_id",
          width: 100,
        },
        {
          text: this.$t("department.branch"),
          value: "branch.name",
          width: 100,
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("department-update") ||
          this.$store.getters.checkPermission("department-delete")
      );
    },
  },
  methods: {
    rowClick(item, row) {
      row.expand(!row.isExpanded);
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
      this.employee = item;
      this.employeeStaff = item.employee_staff;
      this.employeeCoefficients = item.employee_coefficients;
      this.employeeAddresses = item.employee_addresses;
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getList() {
      this.loading = true;
      this.filterDialog = false;
      axios
        .post(this.$store.state.backend_url + "api/departmentsView", {
          pagination: this.dataTableOptions,
          filter: this.filter,
        }) //deplists
        .then((response) => {
          this.items = response.data.departments.data;
          this.server_items_length = response.data.departments.total;
          this.from = response.data.departments.from;

          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getRef() {
      axios
        .post(this.$store.state.backend_url + "api/departmentsGetRef")
        .then((response) => {
          this.departmentTypes = response.data.departmentType;
          this.staff = response.data.staff;
          this.deps = response.data.deplists;
          this.branches = response.data.branches;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    newItem() {
      if (this.$store.getters.checkPermission("department-create")) {
        this.dialogHeaderText = this.$t("department.create");
        this.form = {
          id: Date.now(),
          company_id: "",
          parent_id: "",
          functional_parent_id: "",
          department_type_id: "",
          manager_staff_id: "",
          department_code: "",
          name_uz_latin: "",
          name_uz_cyril: "",
          name_ru: "",
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("department-update")) {
        this.dialogHeaderText = this.$t("department.departments");
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
            this.$store.state.backend_url + "api/departments/update",
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
            console.log(res);
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
      if (this.$store.getters.checkPermission("department-delete")) {
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
                  "api/departments/delete/" +
                  item.id
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
      }
    },
  },
  mounted() {
    this.getList();
    this.getRef();
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
