<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("device.status") }}</span>
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
                v-model="filter.serial_number"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
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
            <!-- <td>
              <v-text-field v-model="filter.tabel" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>-->
            <!-- <td>
              <v-text-field v-model="filter.username" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>-->
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
            <!-- <td>
              <v-text-field
                v-model="filter.employee_position"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>-->
            <td></td>
            <td>
              <v-text-field v-model="filter.act_number" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.username" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.created_at" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.first_use_date"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
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
        <template v-slot:item.created_at="{ item }">
          {{
          item.created_at.slice(0, 10)
          }}
        </template>
        <!-- <template v-slot:item.employee="{ item }">
          {{
          item.last_history.employee ? item.last_history.employee.firstname_uz_latin : ''
          }}
        </template>-->
        <template v-slot:item.employee="{ item }">
          <span v-if="$i18n.locale == 'uz_latin'">
            {{ item.last_history.employee.lastname_uz_latin }}
            {{ item.last_history.employee.firstname_uz_latin }}
          </span>
          <span v-else>
            {{ item.last_history.employee.firstname_uz_cyril }}
            {{ item.last_history.employee.lastname_uz_cyril }}
          </span>
        </template>
        <template v-slot:item.department_code="{ item }">
          {{
          item.last_history.department_code
          }}
        </template>
        <template v-slot:item.department_name="{ item }">
          {{
          item.last_history.employee_department
          }}
        </template>
        <template v-slot:item.position_name="{ item }">
          {{
          item.last_history.employee_position
          }}
        </template>
        <template v-slot:item.act_number="{ item }">
          {{
          item.last_history.act_number
          }}
        </template>
        <!-- <template v-slot:item.actions="{ item }">
          <v-btn color="blue" small text @click="editItem(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>-->
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
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("device.type") }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.device_type_id"
                  :items="deviceTypes"
                  :item-text="'type'"
                  item-value="id"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details
                  dense
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.type"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.type"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("device.model") }}</label>
                <v-text-field
                  v-model="form.model"
                  :rules="[v => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("device.serial_number") }}</label>
                <v-text-field
                  v-model="form.serial_number"
                  :rules="[v => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("device.inventory_number") }}</label>
                <v-text-field
                  v-model="form.inventory_number"
                  :rules="[v => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("device.use_date") }}</label>
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
                      v-model="form.first_use_date"
                      :rules="[v => !!v || $t('input.required')]"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.first_use_date" @input="createdAtMenu = false"></v-date-picker>
                </v-menu>
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
      deviceTypes: [],
      form: {},
      filter: {
        device_type_id: "",
        model: "",
        serial_number: "",
        inventory_number: "",
        username: "",
        created_at: "",
        first_use_date: "",
        status: "",
        lastname_uz_latin: ""
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
          text: "Принимать"
        },
        {
          value: 2,
          text: "Отдавать"
        },
        {
          value: 3,
          text: "Ремонт"
        },
        {
          value: 4,
          text: "Уничтожения"
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

        { text: this.$t("device.type"), value: "device_type.type" },
        { text: this.$t("device.model"), value: "model" },
        { text: this.$t("device.serial_number"), value: "serial_number" },
        { text: this.$t("device.inventory_number"), value: "inventory_number" },
        { text: this.$t("Status"), value: "status" },
        { text: this.$t("user.employee"), value: "employee" },
        { text: this.$t("user.department_code"), value: "department_code" },
        { text: this.$t("user.department_id"), value: "department_name" },
        { text: this.$t("user.position"), value: "position_name" },
        { text: this.$t("device.act_number"), value: "act_number" },
        { text: this.$t("device.user"), value: "created_by.username" },
        {
          text: this.$t("device.reg_date"),
          value: "created_at"
        },
        { text: this.$t("device.use_date"), value: "first_use_date" }

        // {
        //   text: this.$t("actions"),
        //   value: "actions",
        //   width: 130,
        //   align: "center"
        // }
      ].filter(
        v =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("object_type-update") ||
          this.$store.getters.checkPermission("object_type-delete")
      );
    }
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getRef() {
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
        .post(this.$store.state.backend_url + "api/orgtex/status", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          device_branch_id: window.localStorage.getItem("device_branch_id")
        })
        .then(response => {
          this.items = response.data.data.map(v => {
            v.status =
              v.last_history &&
              this.statuses.find(f => f.value == v.last_history.status)
                ? this.statuses.find(f => f.value == v.last_history.status).text
                : "Новый";
            return v;
          });
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
      if (this.$store.getters.checkPermission("object_type-create")) {
        this.dialogHeaderText = this.$t("device.add");
        this.form = {
          id: Date.now(),
          type: ""
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("object_type-update")) {
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
            this.$store.state.backend_url + "api/orgtex/devices/update",
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
    }, //document-types
    deleteItem(item) {
      if (this.$store.getters.checkPermission("object_type-delete")) {
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
                  "api/orgtex/devices/delete/" +
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
    // this.deviceTypeList();
  },
  created() {}
};
</script>