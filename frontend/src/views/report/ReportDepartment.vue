<template>
  <div class="fullHeight">
      <v-card class="heightFull" style="border-radius: 10px; border: 1px solid #dce5ef;" elevation="0">
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("department.index") }}</span>
        <div
            class="headerSearch d-flex align-center"
          >
        <v-text-field
          prepend-inner-icon="mdi-magnify"
          class="txt_search1"
          style="width: 100px !important"
          :placeholder="$t('search')"
          @keyup.enter="getList"
          dense
          hide-details
          solo
        ></v-text-field>
        <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
            v-if="$store.getters.checkPermission('organizations-create')"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn class="txt_searchBtn  ml-2" outlined v-bind="attrs" v-on="on">
                <v-icon size="18" color="white">mdi-format-list-bulleted</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;">   
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                    </v-list-item-title
                  ></v-list-item>
                <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;" @click="0">                  
                  <v-list-item-title>
                    <v-icon color='#107C41' size="18">mdi-microsoft-excel</v-icon>
                    Скачать таблицу Excel
                    </v-list-item-title
                  ></v-list-item>
              </v-list>
            </v-card>
          </v-menu>
      
        </div>
      </v-card-title>
      <v-simple-table
        dense
        fixed-header
        class="doc-template_data-table"
        style="width: 100%; height: 100%; border-radius: 10px;"
        :height="screenHeight"
      >
        <template v-slot:default>
          <thead>
            <tr style="background-color: #F6F9FB!important;">
              <th class="text-center" style="max-width: 50px; background-color: #F6F9FB!important;">#</th>
              <th class="" style="background-color: #F6F9FB!important;">
                {{ $t("department.index") }}
              </th>
              <th class="text-center" style="background-color: #F6F9FB!important;">{{ $t("reportDepartment.all") }}</th>
              <th class="text-center" style="background-color: #F6F9FB!important;">
                {{ $t("reportDepartment.published") }}
              </th>
              <th class="text-center" style="background-color: #F6F9FB!important;">
                {{ $t("reportDepartment.processing") }}
              </th>
              <th class="text-center" style="background-color: #F6F9FB!important;">{{ $t("reportDepartment.signed") }}</th>
              <th class="text-center" style="background-color: #F6F9FB!important;">{{ $t("reportDepartment.ready") }}</th>
              <th class="text-center" style="background-color: #F6F9FB!important;">
                {{ $t("reportDepartment.completed") }}
              </th>
              <th class="text-center" style="background-color: #F6F9FB!important;">
                {{ $t("reportDepartment.cancelled") }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td class="text-center">
                {{
                  items
                    .map(function(x) {
                      return x.id;
                    })
                    .indexOf(item.id) + 1
                }}
              </td>
              <!-- <td>
                <router-link :to="'/reports/department/' + item.id">{{
                  item["name_" + $i18n.locale]
                }}</router-link>
              </td> -->
              <td>{{ item["name_" + $i18n.locale] }}</td>
              <td class="text-center">{{ item.create_documents }}</td>
              <td class="text-center">{{ item.published }}</td>
              <td class="text-center">{{ item.processing }}</td>
              <td class="text-center">{{ item.signed }}</td>
              <td class="text-center">{{ item.ready }}</td>
              <td class="text-center">{{ item.completed }}</td>
              <td class="text-center">{{ item.cancelled }}</td>
            </tr>
            <tr v-if="department">
              <td class="text-center">{{ items.length + 1 }}</td>
              <td>{{ department["name_" + $i18n.locale] }}</td>
              <td class="text-center">{{ department.create_documents }}</td>
              <td class="text-center">{{ department.published }}</td>
              <td class="text-center">{{ department.processing }}</td>
              <td class="text-center">{{ department.signed }}</td>
              <td class="text-center">{{ department.ready }}</td>
              <td class="text-center">{{ department.completed }}</td>
              <td class="text-center">{{ department.cancelled }}</td>
            </tr>
            <tr v-if="all">
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
            </tr>
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
      table_menu: null,
      department: 0,
      all: {}
    };
  },
  watch: {
    $route(to, from) {
      this.getList();
    }
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
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("department.index"), value: "department" },
        { text: this.$t("report.create_document"), value: "create_document" },
        { text: this.$t("report.expired"), value: "expired" },
        { text: this.$t("report.prosesing"), value: "prosesing" },
        { text: this.$t("report.waiting"), value: "waiting" },
        {
          text: this.$t("report.completed_on_time"),
          value: "completed_on_time"
        },
        { text: this.$t("report.failed_in_time"), value: "failed_in_time" }
      ];
    }
  },
  methods: {
    toggleExpand($event) {},
    rowClick(item, row) {
      row.expand(!row.isExpanded);
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/reports/department", {
          parent_id: this.$route.params.parent_id
            ? this.$route.params.parent_id
            : 0
        })
        .then(response => {
          this.items = response.data.departments;
          this.department = response.data.department;
          this.all = response.data.all;
          // this.items = this.items.sort(function (a, b) {
          //   return b.expired - a.expired;
          // });
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    }
  },
  mounted() {
    this.getList();
  },
  created() {}
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
  background: #FF9F0E;
  border: 0.20px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px!important;
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
</style>
