<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("inventory.quarter") }}</span>
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
                  @click="newItem"
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
                
                <td class="py-0 my-0 dense">
                  <v-autocomplete
                    class="py-0"
                    clearable
                    v-model="filterForm.status"
                    :items="[0,1]"
                    hide-details
                    @change="getList()"
                  >               
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0 dense"></td>
              </tr>
            </template>
            <template v-slot:item.year="{ item }">
              <span style="white-space: normal; max-width: 100px">
                {{ item && item.year ? item.year : "" }}
              </span>
            </template>
            <template v-slot:item.quarter="{ item }">
              <span style="white-space: normal; max-width: 100px">
                {{ item && item.quarter ? item.quarter : "" }}
              </span>
            </template>
            <template v-slot:item.status="{ item }">
              <span style="white-space: normal; max-width: 100px">
                {{ item && item.status ? item.status : 0 }}
              </span>
            </template>
            <template v-slot:[`item.actions`]="{ item }">
              <v-btn 
                class="px-1"
                color="blue"
                style="min-width: 25px"
                small
                text
                @click="editItem(item)"
              >
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn>
              <v-btn 
                class="px-1"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)"
              >
                <v-icon size="18">mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("inventory.quarter") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("inventory.quarter") }}</label>
                <v-text-field
                  v-model="form.quarter"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("inventory.year") }}</label>
                <v-text-field
                  v-model="form.year"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3">
                <v-checkbox
                  v-model="form.status"
                  :label="$t('inventory.status')"
                ></v-checkbox>
              </v-col>
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
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

        // { text: this.$t("wh_name"), value: "wh_name" },

        // { text: this.$t("wh_number"), value: "wh_number" },

        { text: this.$t("inventory.year"), value: "year" },
        { text: this.$t("inventory.quarter"), value: "quarter" },
        { text: this.$t("inventory.status"), value: "status" },

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
          .post(
            this.$store.state.backend_url + "api/pg_inventory/quarter/update",
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
              this.$store.state.backend_url +
                "api/pg_inventory/quarter/delete/" +
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
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/pg_inventory/quarterindex", {
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
        year: "",
        quarter: "",
        status: "",
      };
      this.dialog = true;
      this.editMode = false;
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