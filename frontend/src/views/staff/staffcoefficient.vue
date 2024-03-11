<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("inventory.product") }}</span>
        <v-spacer></v-spacer>       

        <v-btn color="#6ac82d" dark fab x-small @click="newItem()">
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
            
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.id"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filterForm.name"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filterForm.part_number"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filterForm.unit_measure"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filterForm.year"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filterForm.quarter"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense"></td>
            <!-- <td class="py-0 my-0 dense">
              <v-autocomplete
                class="py-0"
                clearable
                v-model="filterForm.type"
                :items="userTypes"
                hide-details
                item-value="id"
                @change="getList()"
              >
                <template v-slot:selection="{ item }">{{ item.text }}</template>
                <template v-slot:item="{ item }">
                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
            </td> -->
          </tr>
        </template>

        <template v-slot:item.part_number="{ item }">
          <span style="white-space: normal; max-width: 100px">
            {{ item.part_number }}
          </span>
        </template>
        <template v-slot:item.name="{ item }">
          <span style="white-space: normal; max-width: 100px">
            {{ item.name }}
          </span>
        </template>
        <template v-slot:item.unit_measure="{ item }">
          <span style="white-space: normal; max-width: 100px">
            {{ item.unit_measure }}
          </span>
        </template>
        <template v-slot:item.year="{ item }">
          <span style="white-space: normal; max-width: 100px">
            {{ item.quarter && item.quarter.year ? item.quarter.year : "" }}
          </span>
        </template>
        <template v-slot:item.quarter="{ item }">
          <span style="white-space: normal; max-width: 100px">
            {{
              item.quarter && item.quarter.quarter ? item.quarter.quarter : ""
            }}
          </span>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <v-btn
            class="px-1"
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn class="px-1" color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("inventory.product") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <!-- <v-col cols="6">
                <label for>{{ $t("regions.country_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.wh_name"
                  :items="country"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col> -->

              <v-col cols="12" sm="6">
                <label for>{{ $t("inventory.productName") }}</label>
                <v-text-field
                  v-model="form.name"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" sm="6">
                <label for>{{ $t("inventory.partNumber") }}</label>
                <v-text-field
                  v-model="form.part_number"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <label for>{{ $t("inventory.unitMeasure") }}</label>
                <v-text-field
                  v-model="form.unit_measure"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{ $t("inventory.quarter") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.quarter_id"
                  :items="quarteritems"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  item-value = "id"
                  dense
                  outlined
                >

                
                  <template slot="item" slot-scope="{ item }">
                    {{ item ? item.year + " yil " + item.quarter + " chorak" : "" }}
                  </template>
                  <template slot="selection" slot-scope="{ item }">
                    {{ item ? item.year + " yil " + item.quarter + " chorak" : "" }}
                  </template>
                </v-autocomplete>
              </v-col>

              <!-- <v-col cols="12" sm="6">
                <label for>{{ $t("name_ru") }}</label>
                <v-text-field
                  v-model="form.name_ru"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col> -->
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="py-1 px-3">
          <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="user_excel"
              :name="'hodimlar_ruyxati_' + today + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
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
    quarteritems: [],
    employees: [],
    roles: [],
    permissions: [],
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
      // username: "",
      // employee: "",
      // position: "",
      // department_name: "",
      // role: "",
    },
    userTypes: [
      {
        id: "D",
        text: "Diller",
      },
      {
        id: "K",
        text: "Korxona",
      },
    ],
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

        { text: this.$t("inventory.productName"), value: "name" },
        { text: this.$t("inventory.partNumber"), value: "part_number" },
        { text: this.$t("inventory.unitMeasure"), value: "unit_measure" },


        { text: this.$t("inventory.year"), value: "year" },
        { text: this.$t("inventory.quarter"), value: "quarter" },

        // { text: this.$t("user.department_code"), value: "department_code" },

        // { text: this.$t("user.department_id"), value: "department" },
        // { text: this.$t("ERI"), value: "eimzo_username", width: 50 },

        // { text: this.$t("user.roles"), value: "roles" },
        // { text: this.$t("user.type"), value: "type" },
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
    addRole(item) {
      this.form.roles.push(item);
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("create_update_operation");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(this.$store.state.backend_url + "api/pg_inventory/product/update", this.form)
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
    }, //document-types
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
              this.$store.state.backend_url + "api/pg_inventory/product/delete/" + item.id
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
    quarter() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/pg_inventory/quarter", {
          filter: this.filterForm,
          search: this.search,
          pagination: this.dataTableOptions,
        })
        .then((response) => {
          this.quarteritems = response.data;
          // this.quarteritems = response.data.map((v) => {
          //   return v.year + " " + v.quarter + " " + "chorak";
          // });
          
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/staffs/staffcoefficient", {
          filter: this.filterForm,
          search: this.search,
          pagination: this.dataTableOptions,
        })
        .then((response) => {
          this.items = response.data.data;
          // console.log(response);
          this.server_items_length = response.data.total;
          this.from = response.data.from;
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
        part_number: "",
        unit_measure: "",
        quarter_id: "",
      };
      this.dialog = true;
      this.editMode = false;
    },
  },
  mounted() {
    this.getList();
    this.quarter();
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
