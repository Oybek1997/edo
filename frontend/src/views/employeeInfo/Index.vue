<template>
  <div>
    <v-card class="ma-1 pa-1" :disabled="loading">
      <v-card-title class="pa-1">
        <span>{{ $t("employee.info") }}</span>
        <v-spacer></v-spacer>
        <!-- <v-text-field v-model="search" append-icon="mdi-magnify" class="mr-2" style="width:100px !important;" id="employeeSearch" label="Search" @keyup.native.enter="getList" outlined dense single-line hide-details></v-text-field> -->
        <v-btn
          class="mr-5"
          color
          outlined
          x-small
          fab
          @click="
            filterDialog = true;
            search = '';
          "
        >
          <v-icon>mdi-magnify</v-icon>
        </v-btn>
      </v-card-title>
      <!-- Main employee table -->
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
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        disable-sort
        :footer-props="{
          itemsPerPageOptions: [50, 100, 200],
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
        @dblclick:row="rowClick"
      >
        <template v-slot:body.prepend>
          <tr class="py-0 my-0 prepend_height">
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filterForm.department_code"
                type="number"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.tabel"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filterForm.info"
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
                v-model="filterForm.position"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
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

        <template v-slot:item.department_code="{ item }">
          <v-row
            v-for="(itm, idx) in item.employee_staff"
            :key="idx"
            style="max-width: 100px"
          >
            <v-col class="col-12 text-truncate py-0">
              {{
                itm.staff && itm.staff.department
                  ? itm.staff.department.department_code
                  : ""
              }}
            </v-col>
          </v-row>
        </template>
        <template v-slot:header.tabel="{ header }">
          <span style="white-space: normal; width: 50px">
            {{ header.text }}
          </span>
        </template>

        <template v-slot:item.info="{ item }">
          <span v-if="$i18n.locale == 'uz_latin'">
            {{ item.firstname_uz_latin }} {{ item.lastname_uz_latin }}
            {{ item.middlename_uz_latin }}
          </span>
          <span v-else>
            {{ item.firstname_uz_cyril }} {{ item.lastname_uz_cyril }}
            {{ item.middlename_uz_cyril }}
          </span>
        </template>

        <template v-slot:header.department_code="{ header }">
          <div
            style="white-space: normal; max-width: 70px"
            class="text-truncate"
          >
            {{ header.text }}
          </div>
        </template>

        <template v-slot:item.department_id="{ item }">
          <v-row
            v-for="(itm, idx) in item.employee_staff"
            :key="idx"
            style="max-width: 350px"
          >
            <v-col class="col-12 text-truncate py-0">
              {{
                itm.staff && itm.staff.department
                  ? itm.staff.department["name_" + $i18n.locale]
                  : ""
              }}
            </v-col>
          </v-row>
        </template>
        <template v-slot:item.phone_number="{ item }">
          <v-row
            v-for="(itm, idx) in item.employee_phones"
            :key="idx"
            style="max-width: 250px"
          >
            <v-col class="col-12 text-truncate py-0">
              {{
                itm.phone_number ? itm.phone_type + ": " + itm.phone_number : ""
              }}
            </v-col>
          </v-row>
        </template>

        <template v-slot:item.employee_staff="{ item }">
          <span
            style="white-space: normal; min-width: 50px"
            class="d-block ma-0 pa-0"
            v-for="(itm, idx) in item.employee_staff"
            :key="idx"
            >{{ itm.first_work_date }}</span
          >
        </template>

        <template v-slot:item.staffs="{ item }">
          <v-row
            v-for="(itm, idx) in item.employee_staff"
            :key="idx"
            style="max-width: 350px"
          >
            <v-col
              class="col-12 text-truncate py-0"
              v-if="itm.staff && itm.staff.position"
              >{{ itm.staff.position["name_" + $i18n.locale] }}</v-col
            >
          </v-row>
        </template>

        <template v-slot:header.employee_staff="{ header }">
          <span style="white-space: normal">{{ header.text }}</span>
        </template>

        <template v-slot:header.born_date="{ header }">
          <span style="white-space: normal">{{ header.text }}</span>
        </template>
      </v-data-table>
    </v-card>

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
          <v-form>
            <v-row class="ma-0 pa-0">
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("employee.firstname") }}</label>
                <v-text-field
                  v-model="filterForm.firstname"
                  class="ma-0 pa-0"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("employee.lastname") }}</label>
                <v-text-field
                  v-model="filterForm.lastname"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("employee.middlename") }}</label>
                <v-text-field
                  v-model="filterForm.middlename"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("employee.tabel") }}</label>
                <v-text-field
                  v-model="filterForm.tabel"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("department.department_code") }}</label>
                <v-text-field
                  v-model="filterForm.department_code"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("gender.gender") }}</label>
                <v-autocomplete
                  clearable
                  v-model="filterForm.gender"
                  :items="[
                    { text: $t('Male'), value: 'M' },
                    { text: $t('Female'), value: 'F' },
                  ]"
                  hide-details="auto"
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
    dateRules: [
      (v) =>
        !v ||
        /^\d{4}[\-\-](0?[1-9]|1[012])[\-\-](0?[1-9]|[12][0-9]|3[01])$/.test(
          v
        ) ||
        "Date must be valid(YYYY-DD-MM)",
    ],
    expanded: [],
    search: "",
    page: 1,
    from: 0,
    server_items_length: -1,
    today: moment().format("YYYY-MM-DD"),
    dataTableOptions: {
      page: 1,
      itemsPerPage: 50,
    },
    dataTableValue: [],
    loading: false,
    createdAtMenu1: false,
    createdAtMenu11: false,
    createdAtMenu12: false,
    createdAtMenu2: false,
    createdAtMenu3: false,
    createdAtMenu4: false,
    dialog: false,
    filterDialog: false,
    fullscreen: false,
    editMode: null,
    items: [],
    user: {},
    form: {},
    menu: "",
    menu1: "",
    leavingReasons: [],
    disabled: false,
    selectStatus: [
      {
        text: "Not active",
        value: 0,
      },
      {
        text: "Active",
        value: 1,
      },
    ],
    formData: null,
    filterForm: {
      id: Date.now(),
      tabel: "",
      firstname: "",
      lastname: "",
      middlename: "",
      gender: "",
      department_code: "",
      info: "",
    },
    genders: [
      {
        name: "Erkak",
        value: "M",
      },
      {
        name: "Ayol",
        value: "F",
      },
    ],
    employee: {},
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    headers() {
      return [
        {
          text: "#",
          value: "id",
          align: "center",
          width: 30,
        },
        {
          text: this.$t("department.department_code"),
          value: "department_code",
          width: 80,
        },
        {
          text: this.$t("employee.tabel"),
          value: "tabel",
          align: "center",
          width: 50,
        },
        {
          text: this.$t("employee.info"),
          value: "info",
          width: 250,
        },
        {
          text: this.$t("employee.department_id"),
          value: "department_id",
          width: 250,
        },
        {
          text: this.$t("employee.position"),
          value: "staffs",
          width: 250,
        },
        {
          text: this.$t("profile.email"),
          value: "user.email",
          width: 200,
        },
        {
          text: this.$t("employee.employee_phones"),
          value: "phone_number",
          width: 250,
        },

        // {
        //   text: this.$t("employee.nationality_id"),
        //   value: "nationality.name_" + this.$i18n.locale,
        //   width: 30
        // },
        // { text: this.$t("employee.INN"), value: "INN", width: 30 },
        // { text: this.$t("employee.INPS"), value: "INPS", width: 30 },
      ];
    },
  },
  methods: {
    rowClick(item, row) {
      row.expand(!row.isExpanded);
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
      this.employee = item;
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getRef() {
      let locale = this.$i18n.locale;
      axios
        .get(this.$store.state.backend_url + "api/employees/get-ref/" + locale)
        .then((response) => {
          this.employees = response.data.employees;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/employeesView", {
          pagination: this.dataTableOptions,
          filter: this.filterForm,
          search: this.search,
          locale: this.$i18n.locale,
        })
        .then((response) => {
          this.items = response.data.employees.data;
          console.log("eeee", this.items);
          this.items = this.items.reduce((acc, current) => {
            const x = acc.find((item) => item.id === current.id);
            if (!x) {
              return acc.concat([current]);
            } else {
              return acc;
            }
          }, []);
          this.from = response.data.employees.from;
          this.server_items_length = response.data.employees.total;
          // this.server_items_length = response.data.count;
          this.loading = false;
          this.getRef();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
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
