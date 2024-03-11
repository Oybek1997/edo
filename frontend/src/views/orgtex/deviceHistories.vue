<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("device.history") }}</span>
        <v-spacer></v-spacer>
        <v-btn
          class="mx-3"
          color="indigo"
          x-small
          dark
          fab
          @click="tableToExcel('table', 'Lorem Table')"
        >
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn>
        <v-btn
          v-if="$store.getters.checkPermission('orgtex-admin')"
          color="#6ac82d"
          x-small
          dark
          fab
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>
      <v-data-table
        ref="table"
        id="table"
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        class="mainTable ma-1"
        style="border: 1px solid #aaa;"
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
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
        @update:sort-desc="updatePage"
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
      >
        <template v-slot:body.prepend="{ item }">
          <tr class="prepend_height">
            <td></td>
            <td>
              <v-autocomplete
                clearable
                v-model="filter.device_type_id"
                :items="
                  deviceTypes.map(v => ({
                    text: v.type,
                    value: v.id,
                  }))
                "
                hide-details
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-text-field v-model="filter.model" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.inventory_number"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.serial_number"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-autocomplete
                clearable
                v-model="filter.status"
                :items="
                  statuses.map(v => ({
                    text: v.text,
                    value: v.value,
                  }))
                "
                hide-details
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-text-field v-model="filter.tabel" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.lastname_uz_latin"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
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
                v-model="filter.employee_department"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.employee_position"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.act_number" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.first_use_date"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.created_at" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.username" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.address" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.description" hide-details dense @keyup.enter="getList"></v-text-field>
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
        <template v-slot:item.employee="{ item }">
          <span v-if="$i18n.locale == 'uz_latin'">
            {{ item.employee ? item.employee.lastname_uz_latin : ""}}
            {{ item.employee ? item.employee.firstname_uz_latin : "" }}
          </span>
          <span v-else>
            {{ item.employee ? item.employee.firstname_uz_cyril : "" }}
            {{ item.employee ? item.employee.lastname_uz_cyril : "" }}
          </span>
        </template>
        <template v-slot:item.created_at="{ item }">{{ item.created_at.slice(0, 10) }}</template>
        <template v-slot:item.status="{ item }">
          <template v-if="item.status == 1">Принял</template>
          <template v-else-if="item.status == 2">Отдал</template>
          <template v-else-if="item.status == 3">Ремонт</template>
          <template v-else>Уничтожен</template>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            v-if="$store.getters.checkPermission('orgtex-admin')"
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="dialog" @keydown.esc="dialog = false" persistent max-width="600">
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
            <v-row class="ma-0 pa-0">
              <v-col cols="6">
                <v-autocomplete
                  :label="$t('device.inventory_number')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.device_id"
                  :items="devices"
                  :item-text="'inventory_number'"
                  item-value="id"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details
                  dense
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.inventory_number"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.inventory_number"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-autocomplete
                  :label="$t('Status')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.status"
                  :items="statuses"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details
                  dense
                  @change="changeStatus"
                ></v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-autocomplete
                  :label="$t('user.employee')"
                  outlined
                  class="pa-0"
                  clearable
                  :disabled="form.status != 1"
                  v-model="form.employee_id"
                  :items="employees"
                  @change="changeEmployee"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  :label="$t('user.department_code')"
                  outlined
                  class="pa-0"
                  disabled
                  v-model="form.department_code"
                  hide-details
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  :label="$t('user.department_id')"
                  outlined
                  class="pa-0"
                  disabled
                  v-model="form.employee_department"
                  hide-details
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  :label="$t('user.position')"
                  outlined
                  disabled
                  class="pa-0"
                  v-model="form.employee_position"
                  hide-details
                  dense
                ></v-text-field>
              </v-col>
              <!-- <v-col cols="6">
                <v-menu
                  v-model="createdAtMenu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      :label="$t('device.use_date')"
                      v-model="form.first_use_date"
                      :rules="[v => !!v || $t('input.required')]"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="form.first_use_date"
                    @input="createdAtMenu = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>-->
              <v-col cols="6" v-if="form.status == 1">
                <v-text-field
                  :label="$t('device.act_number')"
                  v-model="form.act_number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" v-if="form.status == 1">
                <v-text-field
                  :label="$t('device.address')"
                  v-model="form.address"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  :label="$t('description')"
                  v-model="form.description"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
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
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import TableToExcel from "@linways/table-to-excel";
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
      dialog: false,
      editMode: null,
      items: [],
      employees: [],
      devices: [],
      deviceTypes: [],
      form: {},
      filter: {
        device_id: "",
        device_type_id: "",
        model: "",
        serial_number: "",
        inventory_number: "",
        status: "",
        act_number: "",
        first_use_date: "",
        description: "",
        created_at: "",
        username: "",
        department_code: "",
        employee_department: "",
        employee_position: "",
        lastname_uz_latin: "",
        tabel: "",
        address: ""
      },
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      createdAtMenu: false,
      statuses: [
        {
          value: 1,
          text: "Принял"
        },
        {
          value: 2,
          text: "Отдал"
        },
        {
          value: 3,
          text: "Ремонт"
        },
        {
          value: 4,
          text: "Уничтожен"
        }
      ]
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("device.type"), value: "device.device_type.type" },
        { text: this.$t("device.model"), value: "device.model" },
        {
          text: this.$t("device.inventory_number"),
          value: "device.inventory_number"
        },
        {
          text: this.$t("device.serial_number"),
          value: "device.serial_number"
        },
        { text: this.$t("Status"), value: "status" },
        { text: this.$t("Tabel"), value: "employee.tabel" },
        {
          text: this.$t("user.employee"),
          value: "employee"
        },
        { text: this.$t("user.department_code"), value: "department_code" },
        { text: this.$t("user.department_id"), value: "employee_department" },
        { text: this.$t("user.position"), value: "employee_position" },
        { text: this.$t("device.act_number"), value: "act_number" },
        { text: this.$t("device.use_date"), value: "device.first_use_date" },
        { text: this.$t("device.reg_date"), value: "created_at" },
        { text: this.$t("device.user"), value: "created_by.username" },
        { text: this.$t("device.address"), value: "address" },
        { text: this.$t("description"), value: "description" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 80,
          align: "center"
        }
      ].filter(
        v =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("orgtex-admin") ||
          this.$store.getters.checkPermission("orgtex-admin")
      );
    }
  },
  methods: {
    changeStatus($event) {
      if ($event != 1) {
        this.form.employee_id = JSON.parse(
          window.localStorage.getItem("user")
        ).employee_id;
        console.log(
          JSON.parse(window.localStorage.getItem("user")).employee_id
        );
        this.changeEmployee(
          JSON.parse(window.localStorage.getItem("user")).employee_id
        );
      } else {
        this.form.employee_id = null;
        this.form.department_code = null;
        this.form.employee_position = null;
        this.form.employee_department = null;
      }
    },
    changeEmployee($event) {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-employee-with-staff/" +
            $event
        )
        .then(response => {
          this.form.employee_department =
            response.data.staff[0].department.name_uz_latin;
          this.form.department_code =
            response.data.staff[0].department.department_code;
          this.form.employee_position =
            response.data.staff[0].position.name_uz_latin;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
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
        .then(response => {
          this.employees = response.data.map(v => ({
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
                : "")
          }));
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getRefDevice() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/orgtex/device-histories/get-ref-device/" +
            window.localStorage.getItem("device_branch_id")
        )
        .then(response => {
          this.devices = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getRefDeviceType() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/orgtex/device-type/get-ref/" +
            window.localStorage.getItem("device_branch_id")
        )
        .then(response => {
          this.deviceTypes = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },

    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/orgtex/device-histories", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          device_branch_id: window.localStorage.getItem("device_branch_id")
        })
        .then(response => {
          this.items = response.data.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      if (this.$store.getters.checkPermission("orgtex-admin")) {
        this.dialogHeaderText = this.$t("device.add_history");
        this.form = {
          id: Date.now()
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("orgtex-admin")) {
        this.dialogHeaderText = this.$t("edit");
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
            this.$store.state.backend_url +
              "api/orgtex/device-histories/update",
            Object.assign(this.form, {
              device_branch_id: window.localStorage.getItem("device_branch_id")
            })
          )
          .then(res => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: toast => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
              }
            });

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation")
            });
          })
          .catch(err => {
            console.log(err);
          });
    },
    deleteItem(item) {
      if (this.$store.getters.checkPermission("orgtex-admin")) {
        const index = this.items.indexOf(item);
        Swal.fire({
          title: this.$t("swal_title"),
          text: this.$t("swal_text"),
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: this.$t("swal_delete")
        }).then(result => {
          if (result.value) {
            axios
              .delete(
                this.$store.state.backend_url +
                  "api/orgtex/device-histories/delete/" +
                  item.id
              )
              .then(res => {
                this.getList(this.page, this.itemsPerPage);
                this.dialog = false;
                Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
              })
              .catch(err => {
                Swal.fire({
                  icon: "error",
                  title: this.$t("swal_error_title"),
                  text: this.$t("swal_error_text")
                  //footer: "<a href>Why do I have this issue?</a>"
                });
                console.log(err);
              });
          }
        });
      }
    },
    tableToExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    }
  },
  mounted() {
    this.getList();
    this.getRef();
    this.getRefDevice();
    this.getRefDeviceType();
  },
  created() {}
};
</script>
