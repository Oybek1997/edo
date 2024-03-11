<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("employee.index") }}</span>
        <v-spacer></v-spacer>

        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          class="mr-2"
          style="width: 100px !important"
          :placeholder="$t('search')"
          @keyup.native.enter="getList"
          outlined
          dense
          single-line
          hide-details
        ></v-text-field>

        <!-- <v-btn
          v-if="$store.getters.checkPermission('tariff_scale-create')"
          color="#6ac82d"
          x-small
          dark
          fab
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn> -->
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
        :search="search"
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
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
        <template v-slot:item.id="{ item }">{{
          items
            .map(function (x) {
              return x.id;
            })
            .indexOf(item.id) + from
        }}</template>
        <template v-slot:header.tabel="{ header }">
          <span style="white-space: normal; width: 50px">
            {{ header.text }}
          </span>
        </template>
        <template v-slot:item.tabel="{ item }">
          <router-link
            :to="'/personcontrol/profile/' + item.id"
            style="text-decoration: none"
            >{{ item.tabel }}</router-link
          >
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
        <template v-slot:item.info="{ item }">
          {{
            $i18n.locale == "uz_latin"
              ? item.lastname_uz_latin +
                " " +
                item.firstname_uz_latin +
                " " +
                item.middlename_uz_latin
              : item.lastname_uz_cyril +
                " " +
                item.firstname_uz_cyril +
                " " +
                item.middlename_uz_cyril
          }}
        </template>
        <template v-slot:item.category="{ item }">
          <span
            style="white-space: normal; min-width: 50px"
            class="d-block ma-0 pa-0"
            >{{ item.tariff_scale ? item.tariff_scale.category : "" }}</span
          >
        </template>
        <template v-slot:item.position="{ item }">
          {{
            item.main_staff
              ? item.main_staff[0].position["name_" + $i18n.locale]
              : ""
          }}
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
        <!-- <template v-slot:item.actions="{ item }">
           <v-btn
            v-if="$store.getters.checkPermission('tariff_scale-update')"
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            v-if="$store.getters.checkPermission('tariff_scale-delete')"
            color="red"
            small
            text
            @click="deleteItem(item)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template> -->
      </v-data-table>
    </v-card>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
import Template from "../document/Template.vue";
export default {
  components: { Template },
  data() {
    return {
      loading: false,
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      dialogHeaderText: "",
      page: 1,
      from: 0,
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
          text: this.$t("employee.tabel"),
          value: "tabel",
          align: "center",
          width: 50,
        },
        { text: this.$t("employee.info"), value: "info" },
        {
          text: this.$t("employee.category"),
          value: "category",
          width: 80,
        },
        { text: this.$t("employee.position"), value: "staffs" },
        {
          text: this.$t("department.department_code"),
          value: "department_code",
          width: 80,
        },
        {
          text: this.$t("employee.department_id"),
          value: "department_id",
        },
        {
          text: this.$t("employee.first_work_date"),
          value: "employee_staff",
          align: "center",
          width: 100,
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
    getList() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document/resolution-employees",
          {
            pagination: this.dataTableOptions,
            search: this.search,
          }
        )
        .then((response) => {
          this.items = response.data.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
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
  created() {},
};
</script>
