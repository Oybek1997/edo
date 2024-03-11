<template>
    <div class="fullHeight">
    <v-card class="heightFull" style="border-radius: 10px; border: 1px solid #dce5ef;" elevation="0">
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t('message.report') }}</span>
        <div
            class="headerSearch d-flex align-center"
          >
          <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            class="txt_search1"
            style=""
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
                  <v-list-item-title @click="uploadExcel('table', 'Lorem Table')">
                    <v-icon color='#107C41' size="18">mdi-microsoft-excel</v-icon>
                    Скачать таблицу Excel
                    </v-list-item-title
                  ></v-list-item>
              </v-list>
            </v-card>
          </v-menu>
      
        </div>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
            <v-data-table
              dense
              class="doc-template_data-table"
              style="
                  width: 100%;
                  height: 100%;
                  border-radius: 10px;
                "
              fixed-header
              :loading-text="$t('loadingText')"
              :no-data-text="$t('noDataText')"
              :height="screenHeight"
              :loading="loading"
              :headers="headers"
              :item="items"
              :search="search"
              item-key="id"
              multi-sort
              :footer-props="{
                itemsPerPageOptions: [ 50, 100, 200, -1 ],
                itemsPerPageAllText:$t('itemsPerPageAllText'),
                itemsPerPageText: $t('itemsPerPageText'),
                showFirstLastPage: true,
                firstIcon: 'mdi-arrow-collapse-left',
                lastIcon: 'mdi-arrow-collapse-right',
                prevIcon: 'mdi-arrow-left',
                nextIcon: 'mdi-arrow-right'
              }"
              :expanded.sync="expanded"
              single-expand
              show-expand
              @update:expanded="toggleExpand"
              @click:row="rowClick"
            >
              <template
                v-slot:item.id="{ item }"
              >{{items.map(function(x) {return x.id; }).indexOf(item.id) + 1}}</template>
              <template v-slot:item.create_document="{ item }">
                <v-menu
                  v-if="item.create_documents.length"
                  bottom
                  origin="center center"
                  transition="scale-transition"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color block text small v-bind="attrs" v-on="on">{{ item.create_document }}</v-btn>
                  </template>

                  <v-list class="pa-1">
                    <p v-for="(item, i) in item.create_documents" :key="i" class="ma-1 text-center">
                      <v-btn
                        color="primary"
                        block
                        outlined
                        text
                        small
                        target="_blank"
                        :to="'show/'+item.document_id"
                      >{{ item.document_number }}</v-btn>
                    </p>
                  </v-list>
                </v-menu>
              </template>
              <template v-slot:item.expired="{ item }">
                <v-menu
                  v-if="item.expired_documents.length"
                  bottom
                  origin="center center"
                  transition="scale-transition"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color="error" block text small v-bind="attrs" v-on="on">{{ item.expired }}</v-btn>
                  </template>

                  <v-list class="pa-1">
                    <p v-for="(item, i) in item.expired_documents" :key="i" class="ma-1 text-center">
                      <v-btn
                        color="error"
                        block
                        outlined
                        text
                        small
                        target="_blank"
                        :to="'show/'+item.document_id"
                      >{{ item.document_number }}</v-btn>
                    </p>
                  </v-list>
                </v-menu>
              </template>
              <template v-slot:item.prosesing="{ item }">
                <v-menu
                  v-if="item.prosesing_documents.length"
                  bottom
                  origin="center center"
                  transition="scale-transition"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color block text small v-bind="attrs" v-on="on">{{ item.prosesing }}</v-btn>
                  </template>

                  <v-list class="pa-1">
                    <p v-for="(item, i) in item.prosesing_documents" :key="i" class="ma-1 text-center">
                      <v-btn
                        color="primary"
                        block
                        outlined
                        text
                        small
                        target="_blank"
                        :to="'show/'+item.document_id"
                      >{{ item.document_number }}</v-btn>
                    </p>
                  </v-list>
                </v-menu>
              </template>
              <template v-slot:item.waiting="{ item }">
                <v-menu
                  v-if="item.waiting_documents.length"
                  bottom
                  origin="center center"
                  transition="scale-transition"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color block text small v-bind="attrs" v-on="on">{{ item.waiting }}</v-btn>
                  </template>

                  <v-list class="pa-1">
                    <p v-for="(item, i) in item.waiting_documents" :key="i" class="ma-1 text-center">
                      <v-btn
                        color="primary"
                        block
                        outlined
                        text
                        small
                        target="_blank"
                        :to="'show/'+item.document_id"
                      >{{ item.document_number }}</v-btn>
                    </p>
                  </v-list>
                </v-menu>
              </template>
              <template v-slot:item.draft="{ item }">
                <v-btn v-if="item.draft_documents.length" color block text small>{{ item.draft }}</v-btn>
              </template>
              <template v-slot:item.completed_on_time="{ item }">
                <v-btn
                  v-if="item.completed_on_time_documents.length"
                  color
                  block
                  text
                  small
                >{{ item.completed_on_time }}</v-btn>
              </template>
              <template v-slot:item.failed_in_time="{ item }">
                <v-btn
                  v-if="item.failed_in_time_documents.length"
                  color
                  block
                  text
                  small
                >{{ item.failed_in_time }}</v-btn>
              </template>
              <template v-slot:expanded-item="{ headers, item }">
                <td :colspan="headers.length">
                  <v-row class="justify-center">
                    <v-col cols="12" md="10" lg="9" xl="7" class="py-1 ma-2">
                      <v-card class="pa-3">
                        <strong>{{ $t("department.index") }}:</strong>
                        {{ item.department }}
                        <br />
                        <strong>{{$t("position.index")}}:</strong>
                        {{ item.position }}
                      </v-card>
                    </v-col>
                  </v-row>
                </td>
              </template>
            </v-data-table>
        </v-col>
      </v-row>
    </v-card>

    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t('loadingText') }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
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
      search: "",
      items: [],
      table_menu: null,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      expanded: []
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
    headers() {
      return [
        { text: "", value: "data-table-expand" },
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("employee.tabel"),
          value: "tabel",
          align: "center",
          width: 30
        },
        { text: this.$t("employee.info"), value: "employee_name" },
        { text: this.$t("report.create_document"), value: "create_document" },
        { text: this.$t("report.expired"), value: "expired" },
        { text: this.$t("report.prosesing"), value: "prosesing" },
        { text: this.$t("report.waiting"), value: "waiting" },
        { text: this.$t("document.drafts"), value: "draft" },
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
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/documents/report", {
          pagination: this.dataTableOptions,
          search: this.search
        })
        .then(response => {
          this.items = response.data;
          this.items.map(v => {
            v.employee_name =
            v.employee["firstname_" + this.language] +
            " " +
            v.employee["lastname_" + this.language] +
            " " +
            v.employee["middlename_" + this.language];
            v.tabel = v.employee.tabel;
            v.department = v.staff.department["name_" + this.$i18n.locale];
            v.position = v.staff.position["name_" + this.$i18n.locale];
          });
          console.log('this.items=',this.items);
          this.items = this.items.slice().sort(function(a, b) {
            return b.expired - a.expired;
          });
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
  height: 93vh;
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
.headerSearch .v-text-field--outlined fieldset {
  border: none;
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
.headerSearch .v-text-field--enclosed.v-input--dense:not(.v-text-field--solo).v-text-field--outlined .v-input__append-inner{
  margin-top: 4px;
  font-size: 18px;
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
.filterBtn {
  color: #000;
  font-size: 12px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  border: 1px solid #e6e6e6;
  /* border-right: 0px; */
  /* border-left: 0px; */
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
.dropdown-list .v-list-item .v-list-item__title {
  color: #000;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>
