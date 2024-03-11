<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("department.index") }}</span>
        <v-spacer></v-spacer>
        <div class="px-1" style="width: 350px">
          <v-menu
            ref="rangeMenu"
            :close-on-content-click="false"
            :return-value.sync="filter.document_range"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="filter.document_range"
                v-bind="attrs"
                @keyup.native.enter="getFilter()"
                v-on="on"
                hide-details
                clearable
                outlined
                dense
              ></v-text-field>
            </template>
            <v-date-picker v-model="date" range no-title scrollable>
              <v-spacer></v-spacer>
              <v-btn
                text
                color="primary"
                @click="$refs.rangeMenu.isActive = false"
                >Cancel</v-btn
              >
              <v-btn
                text
                color="primary"
                @click="
                  $refs.rangeMenu.save(date);
                  filter.document_range = date;
                  getFilter();
                "
                >OK</v-btn
              >
            </v-date-picker>
          </v-menu>
        </div>
      </v-card-title>
      <v-simple-table
        dense
        fixed-header
        style="border: 1px solid #aaa"
        :height="screenHeight"
        class="ma-1"
      >
        <template v-slot:default>
          <thead>
            <tr>
              <th rowspan="3" class="text-center" style="max-width: 50px">#</th>
              <th rowspan="3" class="text-center">
                {{ $t("department.index") }}
              </th>
              <th rowspan="3" class="text-center">
                {{ $t("document.inbox") }}
              </th>
              <th colspan="10" class="text-center">
                {{ $t("reportDepartment.of_them") }}:
              </th>
              <!-- <th class="text-center">{{ $t("document.inbox") }}</th> -->
            </tr>
            <tr>
              <th rowspan="2" class="text-center">
                {{ $t("reportDepartment.done") }}
              </th>
              <th colspan="2" class="text-center">
                {{ $t("reportDepartment.of_them_completed") }}
              </th>
              <th colspan="2" class="text-center">
                {{ $t("reportDepartment.result") }}
              </th>
              <th rowspan="2" class="text-center">
                {{ $t("reportDepartment.on_performance") }}
              </th>
              <th colspan="3" class="text-center">
                {{ $t("reportDepartment.for_execution") }}
              </th>
              <th rowspan="2" class="text-center">
                {{ $t("reportDepartment.overdue") }}
              </th>
            </tr>
            <tr>
              <th class="text-center">{{ $t("reportDepartment.on_time") }}</th>
              <th class="text-center">
                {{ $t("reportDepartment.out_of_date") }}
              </th>
              <th class="text-center">
                {{ $t("document.ok") }}
              </th>
              <th class="text-center">
                {{ $t("document.cancel") }}
              </th>
              <th class="text-center">
                {{ $t("reportDepartment.up_to_1_day") }}
              </th>
              <th class="text-center">
                {{ $t("reportDepartment.up_to_2_3_days") }}
              </th>
              <th class="text-center">
                {{ $t("reportDepartment.more_than_3_days") }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td class="text-center">
                {{
                  items
                    .map(function (x) {
                      return x.id;
                    })
                    .indexOf(item.id) + 1
                }}
              </td>
              <td>
                <router-link :to="'/reports/department-okd/' + item.id">{{
                  item["name_" + $i18n.locale]
                }}</router-link>
              </td>
              <td class="text-center">{{ item.inbox }}</td>
              <td class="text-center">{{ item.done }}</td>
              <td class="text-center">{{ item.on_time }}</td>
              <td class="text-center">{{ item.out_of_date }}</td>
              <td class="text-center">{{ item.ok }}</td>
              <td class="text-center">{{ item.cancel }}</td>
              <td class="text-center">{{ item.on_performance }}</td>
              <td class="text-center">{{ item.up_to_1_day }}</td>
              <td class="text-center">{{ item.up_to_2_3_days }}</td>
              <td class="text-center">{{ item.more_than_3_days }}</td>
              <td class="text-center">{{ item.overdue }}</td>
            </tr>
            <tr v-for="item in employees" :key="item.id">
              <td class="text-center">
                {{
                  employees
                    .map(function (x) {
                      return x.id;
                    })
                    .indexOf(item.id) +
                  1 +
                  items.length
                }}
              </td>
              <td>
                {{
                  item["firstname_" + language] +
                  " " +
                  item["lastname_" + language]
                }}
              </td>
              <td class="text-center">{{ item.inbox }}</td>
              <td class="text-center">{{ item.done }}</td>
              <td class="text-center">{{ item.on_time }}</td>
              <td class="text-center">{{ item.out_of_date }}</td>
              <td class="text-center">{{ item.ok }}</td>
              <td class="text-center">{{ item.cancel }}</td>
              <td class="text-center">{{ item.on_performance }}</td>
              <td class="text-center">{{ item.up_to_1_day }}</td>
              <td class="text-center">{{ item.up_to_2_3_days }}</td>
              <td class="text-center">{{ item.more_than_3_days }}</td>
              <td class="text-center">{{ item.overdue }}</td>
            </tr>
            <!-- <tr v-if="department">
              <td class="text-center">{{ items.length + 1 }}</td>
              <td>{{ department["name_" + $i18n.locale] }}</td>
              <td class="text-center">{{ department.inbox }}</td>
              <td class="text-center">{{ department.done }}</td>
              <td class="text-center">{{ department.on_time }}</td>
              <td class="text-center">{{ department.out_of_date }}</td>
              <td class="text-center">{{ department.ok }}</td>
              <td class="text-center">{{ department.cancel }}</td>
              <td class="text-center">{{ department.on_performance }}</td>
            </tr> -->
            <!-- <tr v-if="all">
              <td class="text-center" colspan="2">
                {{ all["name_" + $i18n.locale] }}
              </td>
              <td class="text-center">{{ all.create_documents }}</td>
              <td class="text-center">{{ all.published }}</td>
              <td class="text-center">{{ all.processing }}</td>
              <td class="text-center">{{ all.signed }}</td>
              <td class="text-center">{{ all.ready }}</td>
              <td class="text-center">{{ all.completed }}</td>
              <td class="text-center">{{ all.cancelled }}</td>
            </tr> -->
          </tbody>
        </template>
      </v-simple-table>
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
export default {
  data() {
    return {
      loading: false,
      items: [],
      department: 0,
      employees: [],
      all: {},
      filter: {},
      today: new Date(),
      date: null,
    };
  },
  watch: {
    $route(to, from) {
      this.getList();
    },
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 130;
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
    headers() {
      return [
        // { text: "", value: "data-table-expand" },
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("department.index"), value: "department" },
        { text: this.$t("report.create_document"), value: "create_document" },
        { text: this.$t("report.expired"), value: "expired" },
        { text: this.$t("report.prosesing"), value: "prosesing" },
        { text: this.$t("report.waiting"), value: "waiting" },
        // { text: this.$t("document.drafts"), value: "draft" },
        {
          text: this.$t("report.completed_on_time"),
          value: "completed_on_time",
        },
        { text: this.$t("report.failed_in_time"), value: "failed_in_time" },
      ];
    },
  },
  methods: {
    toggleExpand($event) {},
    rowClick(item, row) {
      row.expand(!row.isExpanded);
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
    },
    getFilter() {
      console.log(this.filter);
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/reports/department-okd", {
          parent_id: this.$route.params.parent_id
            ? this.$route.params.parent_id
            : 0,
        })
        .then((response) => {
          this.items = response.data.departments;
          this.department = response.data.department;
          this.employees = response.data.employees;
          this.all = response.data.all;
          // this.items = this.items.sort(function (a, b) {
          //   return b.expired - a.expired;
          // });
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
    this.filter.document_range = [this.today.getFullYear()+'-'+(this.today.getMonth()+1)+'-'+this.today.getDate()]
  },
  created() {},
};
</script>

<style scoped>
table tbody tr td {
  white-space: normal !important;
}
</style>
