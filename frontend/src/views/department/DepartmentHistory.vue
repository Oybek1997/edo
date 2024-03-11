<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("department.departments") }}</span>
        <v-spacer></v-spacer>
        <v-form ref="myForm">
          <v-menu
            ref="menu"
            v-model="menu"
            :close-on-content-click="false"
            :return-value.sync="date"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="auto"
            nudge-left="170"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-responsive max-width="120">
                <v-text-field
                  v-model="date"
                  outlined
                  dense
                  readonly
                  v-bind="attrs"
                  v-on="on"
                  hide-details
                ></v-text-field>
              </v-responsive>
            </template>
            <v-date-picker
              v-model="date"
              type="month"
              no-title
              scrollable
              @change="
                $refs.menu.save(date);
                getList();
              "
            >
            </v-date-picker>
          </v-menu>
        </v-form>
        <v-btn
          v-if="$store.getters.checkPermission('department-create')"
          color="#6ac82d"
          dark
          fab
          x-small
          @click="newItem"
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
                v-model="filter.functional_department_code"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.functional_department_name"
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
            <td>
              <v-text-field
                v-model="filter.department_manager"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <!-- <td></td> -->
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
          <span style="white-space: normal; max-width: 100px">
            {{ item.department_name }}
          </span>
        </template>
        <template v-slot:item.position_id="{ item }">
          <!-- <span style="white-space: normal; max-width: 100px">{{
            item.staff.map(v => ({
              text: v.position["name_" + $i18n.locale],
              value: v.position_id
            }))
          }}</span>-->
          <span v-for="staff in item.staff" :key="staff.index"
            >{{
              staff.position ? staff.position["name_" + $i18n.locale] : ""
            }}
            ,</span
          >
        </template>
        <template v-slot:item.manager="{ item }">
          <span
            >{{
              item.manager_staff && item.manager_staff.employee_staff.length>0 ? item.manager_staff.employee_staff[0].employee["firstname_"+ $i18n.locale] + " " + 
              item.manager_staff.employee_staff[0].employee["lastname_"+ $i18n.locale] + " " + 
              item.manager_staff.employee_staff[0].employee["middlename_"+ $i18n.locale] : ""
            }}
            </span
          >
        </template>

        <template v-slot:item.parent_id="{ item }">
          <span
            style="white-space: normal; max-width: 100px"
            v-if="item.parent"
            >{{ item.parent["name_" + $i18n.locale] }}</span
          >
        </template>
        <template v-slot:item.functional_parent_id="{ item }">
          <span
            style="white-space: normal; max-width: 100px"
            v-if="item.functional_parent"
            >{{ item.functional_parent["name_" + $i18n.locale] }}</span
          >
        </template>
        <template v-slot:item.funcdepcode="{ item }">
          {{ item.functional_department ? item.functional_department.functional_department_code : '' }}         
        </template>
        <template v-slot:item.functional_department="{ item }">
          {{ item.functional_department ? item.functional_department["name_" + $i18n.locale] : '' }}        
        </template>
        <template v-slot:item.department_type_id="{ item }">
          <span
            style="white-space: normal; max-width: 100px"
            v-if="item.department_type"
            >{{ item.department_type["name_" + $i18n.locale] }}</span
          >
        </template>
        <!-- <template v-slot:item.actions="{ item }">
          <v-btn
            v-if="$store.getters.checkPermission('department-update')"
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            v-if="$store.getters.checkPermission('department-update')"
            color="blue"
            small
            text
            @click="addfunctionalDepartment(item)"
          >
            <v-icon>mdi-pencil-box-multiple</v-icon>
          </v-btn>
          <v-btn
            v-if="false && $store.getters.checkPermission('personal_type-delete') && item.staff.length == ''"
            color="red"
            small
            text
            @click="deleteItem(item)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template> -->

        <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="headers.length" class="pa-3">
            <!-- Employee view -->
            <table class="infoTable ma-0 pa-0">
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
          </td>
          <!-- Employee Coefficients view -->
        </template>
      </v-data-table>
    </v-card>
    <v-dialog
      v-model="functionaldialog"
      @keydown.esc="functionaldialog = false"
      persistent
      max-width="800px"
    >
      <v-card>
        <v-card-title>
          <span class="headline">{{ $t("department.functionalindex") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="functionaldialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("department.manager_staff") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.functional_department_id"
                  :items="functionaldepartment"
                  :rules="[(v) => !!v || $t('input.required')]"
                  item-value="id"
                  item-text="search"
                  hide-details="auto"
                  dense
                  outlined 
                  :search-input.sync="search"

                >                
                  <template slot="item" slot-scope="{ item }">
                    {{ item ? item.functional_department_code + " " + item["name_" + $i18n.locale] : "" }}
                  </template>
                  <template slot="selection" slot-scope="{ item }">
                    {{ item ? item.functional_department_code + " " + item["name_" + $i18n.locale] : "" }}
                  </template>
                </v-autocomplete>            
              </v-col>
   
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card>
        <v-card-title>
          <span class="headline">{{ $t("department.dialog") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="6">
                <label for>{{ $t("department.parent_id") }}</label>
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
              <v-col cols="6">
                <label for>{{ $t("department.functional_parent_id") }}</label>
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
              <v-col cols="6">
                <label for>{{ $t("department.department_type_id") }}</label>
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
              <v-col cols="6">
                <label for>{{ $t("name_uz_latin") }}</label>
                <v-text-field
                  v-model="form.name_uz_latin"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("name_uz_cyril") }}</label>
                <v-text-field
                  v-model="form.name_uz_cyril"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("name_ru") }}</label>
                <v-text-field
                  v-model="form.name_ru"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("department.department_code") }}</label>
                <v-text-field
                  v-model="form.department_code"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("department.functional_department_code") }}</label>
                <v-text-field
                  v-model="form.functional_department_code"                  
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("department.branch") }}</label>
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
              <v-col cols="6">
                <label for>{{ $t("department.manager_staff") }}</label>
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
                <v-row>
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
              <v-col cols="6">
                <v-checkbox v-model="form.is_active" :label="$t('employee.active')"></v-checkbox>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
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
    functionaldialog: false,
    editMode: null,
    items: [],
    functionaldepartment: [],
    deps: [],
    branches: [],
    company: [],
    departmentTypes: [],
    users: [],
    form: {},
    filterDialog: false,
    fullscreen: false,
    date: new Date().toISOString().substr(0, 7),
    menu: false,
    modal: false,
    menu2: false,
    filter: {
      parent_department_code: "",
      department_type_id: "",
      department_code: "",
      functional_department_code: "",
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
          // console.log(employee_staff);
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
          text: this.$t("department.functional_department_code"),
          value: "funcdepcode",
          width: 30,
        },
        {
          text: this.$t("department.functionalindex"),
          value: "functional_department",
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
          text: this.$t("department.manager"),
          value: "manager",
          class: "my-5",
        },
        // {
        //   text: this.$t("department.functional_parent_id"),
        //   value: "functional_parent_id",
        //   width: 200,
        // },
        {
          text: this.$t("department.parent_id"),
          value: "parent_id",
          width: 200,
        },
        {
          text: this.$t("department.department_type_id"),
          value: "department_type_id",
          width: 200,
        },
        {
          text: this.$t("department.branch"),
          value: "branch.name",
          width: 200,
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
        .post(this.$store.state.backend_url + "api/department-history/getlist", {
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
    getfunctionaldepartment() {
      axios
        .get(this.$store.state.backend_url + "api/functionaldepartment")
        .then((response) => {
          this.functionaldepartment = response.data
          this.functionaldepartment.map((v)=>{
            v.search = v.functional_department_code + " " + v["name_" + this.$i18n.locale];
          })
          // console.log(this.functionaldepartment);
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
          functional_department_code: "",
          name_uz_latin: "",
          name_uz_cyril: "",
          name_ru: "",
          is_active: true,
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
    addfunctionalDepartment(item) {
      if (this.$store.getters.checkPermission("department-update")) {
        this.dialogHeaderText = this.$t("department.departments");
        this.form = Object.assign({}, item);
        this.functionaldialog = true;
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
            if(res.data.status === 215){
              Swal.fire("Connected Staff Exist to this Department!", "Fail");
            }else{

            
            this.getList();
            this.dialog = false;
            this.functionaldialog = false;
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
            // console.log(res);
            
            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation"),
            });
          }
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
                if (res.data.status === 215) {
                 Swal.fire("Connected Staff Exist to this Department!", "Fail");
                 } else {
                  this.getList();
                  this.dialog = false;
                  Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
               }  
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
    this.getfunctionaldepartment();
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
