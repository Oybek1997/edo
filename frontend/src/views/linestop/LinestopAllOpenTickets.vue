<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("linestop.all_open_tickets") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            class="txt_search1"
            style="border-radius: 0px"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            dense
            solo
            single-line
            hide-details
          ></v-text-field>
          <v-menu
            transition="slide-y-transition"
            left
            v-model="filter_menu"
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn class="txt_searchBtn" outlined v-bind="attrs" v-on="on">
                <v-icon color="green" outlined left>mdi-filter-outline</v-icon>
                {{ $t("linestop.filter") }}
              </v-btn>
            </template>
            <v-card>
              <v-list>
                <p class="headerTitleS pa-2 ma-0">
                  <strong>{{ $t("linestop.select_line") }}</strong>
                </p>
                <v-list-item
                  v-for="(item, index) in lines"
                  :key="`line-${item.id}-${index}`"
                  style="margin: -22px 0px 0px 0px"
                >
                  <v-list-item-action>
                    <v-checkbox
                      :input-value="selectedLines.includes(item.id)"
                      @change="handleCheckboxChange(item.id)"
                    ></v-checkbox>
                  </v-list-item-action>
                  <v-list-item-title
                    class="rowTestClass"
                    style="margin-left: -30px; cursor: pointer"
                    >{{ item.comment }}</v-list-item-title
                  >
                </v-list-item>
                <p class="headerTitleS pa-2 ma-0">
                  <strong>{{ $t("linestop.select_status") }}</strong>
                </p>
                <v-list-item
                  v-for="(status, index) in statuses"
                  :key="`status-${status.id}-${index}`"
                  style="margin: -22px 0px 0px 0px"
                >
                  <v-list-item-action>
                    <v-checkbox
                      :input-value="SelectedStatus === status.id"
                      @change="handleStatusChange(status.id)"
                    ></v-checkbox>
                  </v-list-item-action>
                  <v-list-item-title
                    class="rowTestClass"
                    style="margin-left: -30px; cursor: pointer"
                    @click="handleStatusItemClick(status.id)"
                  >
                    {{ status.name }}
                  </v-list-item-title>
                </v-list-item>
                <p class="headerTitleS pa-2 ma-0">
                  <strong>{{ $t("linestop.select_creater") }}</strong>
                </p>
                <v-list-item
                  v-for="(creater, index) in creaters"
                  :key="`creater-${creater.id}-${index}`"
                  style="margin: -22px 0px 0px 0px"
                >
                  <v-list-item-action>
                    <v-checkbox
                      :input-value="SelectedCreater === creater.id"
                      @change="handleCreaterChange(creater.id)"
                    ></v-checkbox>
                  </v-list-item-action>
                  <v-list-item-title
                    class="rowTestClass"
                    style="margin-left: -30px; cursor: pointer"
                    @click="() => handleCreaterChange(creater.id)"
                  >
                    {{ creater.name }}
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-card>
          </v-menu>
          <v-menu
            transition="slide-y-transition"
            left
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn class="txt_searchBtn" outlined v-bind="attrs" v-on="on">
                <v-icon size="18" color="black"
                  >mdi-format-list-bulleted</v-icon
                >
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title @click="getListExcel()">
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    {{ $t("linestop.download_excel") }}
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu>
          <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn class="txt_searchBtn" outlined v-bind="attrs" v-on="on">
                {{ $t("linestop.columns") }}
                <v-icon color="green" right>mdi-checkbox-marked-outline</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-list
                v-for="(item, i) in headers.filter((v) => v.tabList)"
                :key="i"
              >
                <v-list-item :style="i > 0 ? 'margin:-35px 0px 0px 0px' : ''">
                  <v-list-item-action>
                    <v-checkbox
                      style="color: #2c8dff"
                      :style="
                        item.visible
                          ? 'margin-left: -30px;cursor: pointer;'
                          : 'color:grey;margin-left: -30px;cursor: pointer;'
                      "
                      v-model="item.visible"
                      @click="showHeaderss()"
                    ></v-checkbox>
                  </v-list-item-action>
                  <!-- ;' -->
                  <v-list-item-title
                    class="rowTestClass"
                    @click="showHeaderss2(item)"
                    :style="
                      item.visible
                        ? 'margin-left: -30px;cursor: pointer'
                        : 'color:grey;margin-left: -30px;cursor: pointer;'
                    "
                    >{{ item.text }}</v-list-item-title
                  >
                </v-list-item>
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
            :height="screenHeight"
            :loading="loading"
            :headers="showHeaders"
            :items="items"
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, 200],
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
            @update:sort-by="updatePage"
            @dblclick:row="rowClick"
            single-expand
          >
            <template v-slot:[`item.id`]="{ item }">
              <router-link
                :to="`/linestopsidebar/linestop-ticket/${item.plcdata_id}`"
              >
                <p
                  class="ma-0"
                  style="
                    font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial,
                      sans-serif;
                  "
                >
                  T-00{{ item.plcdata_id }}
                </p>
              </router-link>
            </template>

            <template v-slot:[`item.plcdata.stopdt`]="{ item }">
              <p
                class="ma-0"
                style="
                  font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial,
                    sans-serif;
                "
              >
                {{
                  item.plcdata.stopdt ? item.plcdata.stopdt.slice(0, 19) : " "
                }}
              </p>
            </template>
            <template v-slot:[`item.plcdata.startdt`]="{ item }">
              <p
                class="ma-0"
                style="
                  font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial,
                    sans-serif;
                "
              >
                {{
                  item.plcdata.startdt
                    ? item.plcdata.startdt.slice(0, 19)
                    : "-/-"
                }}
              </p>
            </template>
            <template v-slot:[`item.duration`]="{ item }">
              <p
                class="ma-0"
                style="
                  font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial,
                    sans-serif;
                "
              >
              {{ item.duration ? item.duration.slice(0, 5) + $t('linestop.sec') : "-/-" }}
              </p>
            </template>
            <template v-slot:item.who="{ item }">
              <span v-if="item.status == 2 || item.status == 3">{{ getEmployeeFio(item) }}</span>
              <span v-else> ~ </span>
            </template>
            <template v-slot:[`item.status`]="{ item }">
              <p
                class="ma-0"
                style="
                  font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial,
                    sans-serif;
                "
              >
                <span v-if="item.status == 0">
                  <v-btn x-small color="primary" dark class="tdClass" right>
                    {{ $t("linestop.new") }}
                  </v-btn></span
                >
                <span v-else-if="item.status == 1" class="indigo">
                  <v-btn x-small color="deep-orange darken-1" dark class="tdClass" right>
                    {{ $t("linestop.pending") }}
                  </v-btn></span
                >
                <span v-else-if="item.status == 2" class="lime darken-3">
                  <v-btn x-small color="error" dark class="tdClass" right>
                    {{ $t("linestop.working") }}
                  </v-btn></span
                >
                <span v-else-if="item.status == 3"
                  ><v-btn
                    x-small
                    color="#00E676"
                    dark
                    class="tdClass"
                    elevation="0"
                    right
                  >
                  {{ $t("linestop.closed") }}
                  </v-btn></span
                >
              </p>
            </template>
            <template v-slot:[`item.plcdata.status`]="{ item }">
              <p
                v-if="item.plcdata.status == 1"
                style="
                  color: #f8a300;
                  font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial,
                    sans-serif;
                "
                class="text_nowrap ma-0 pa-2"
              >
              {{ $t("linestop.operator_creator") }}
              </p>
              <p
                v-else-if="item.plcdata.status == 0"
                style="
                  color: #10a08f;
                  font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial,
                    sans-serif;
                "
                class="text_nowrap ma-0 pa-2"
              >
              {{ $t("linestop.auto_creator") }}
              </p>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <!-- downloading excel dialog qismi boshlandi -->
    <v-dialog
      v-model="downloadExcel"
      hide-overlay
      persistent
      width="280"
      @keydown.esc="downloadExcel = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">Excelga yuklab olish</span>
        <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
        <v-card-text class="pa-0 ma-0">
          <v-btn
            color="#3FCB5D"
            right
            small
            dark
            elevation="0"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
          >
            <download-excel
              :data="punkt_excel"
              :name="'Все_открытые_тикеты_' + today + '.xls'"
            >
              {{ $t("download") }} <v-icon right>mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn
            class
            color="#EB4034"
            right
            small
            dark
            elevation="0"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
          >
            {{ $t("Отменить") }} <v-icon right>mdi-close-box-outline</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <!-- downloading excel dialog qismi tugadi -->
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
  data() {
    return {
      loading: false,
      search: "",
      dialog: false,
      items: [],
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 18 },
      page: 1,
      from: 0,
      server_items_length: -1,
      punkt_excel: [],
      downloadExcel: false,
      today: moment().format("YYYY-MM-DD"),
      table_menu: "",
      filter: "",
      filter_menu: false,
      selectedLines: [],
      SelectedStatus: null,
      SelectedCreater: "",
      lines: [],
      statuses: [
        { id: 0, name: "Новый" },
        { id: 1, name: "Ожидающий" },
      ],
      creaters: [
        { id: 0, name: "Автобот" },
        { id: 1, name: "Оператор" },
      ],
      headers: [
      {
          text: "",
          value: "data-table-expand",
          align: "center",
          class: "grey lighten-3",
          tabList: false,
          visible: true,
        },
        {
          text: this.$t("linestop.ticket_id"),
          value: "id",
          class: "grey lighten-3",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("linestop.line"),
          value: "plcdata.line.line",
          align: "center",
          class: "grey lighten-3",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("linestop.shop"),
          value: "plcdata.line.shop.name",
          class: "grey lighten-3",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("linestop.sector"),
          value: "plcdata.sector",
          align: "center",
          class: "grey lighten-3",
          sortable: false,
          tabList: false,
          visible: true,
        },
        {
          text: this.$t("linestop.stoptime"),
          value: "plcdata.stopdt",
          class: "grey lighten-3",
          align: "center",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("linestop.starttime"),
          value: "plcdata.startdt",
          class: "grey lighten-3",
          align: "center",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("linestop.duration"),
          value: "duration",
          align: "center",
          class: "grey lighten-3",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("linestop.receiver"),
          value: "who",
          align: "center",
          width: 100,
          class: "grey lighten-3",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("linestop.status"),
          value: "status",
          align: "center",
          width: 100,
          class: "grey lighten-3",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("linestop.created_by"),
          value: "plcdata.status",
          width: 100,
          class: "grey lighten-3",
          tabList: true,
          visible: true,
        },
      ],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 320;
    },
    showHeaders() {
      return this.headers.filter((s) => s.visible);
    },
  },
  methods: {
    getEmployeeFio(item) {
      if (
        item &&
        item.ticket_user &&
        Array.isArray(item.ticket_user) &&
        item.ticket_user.length > 0 &&
        item.ticket_user[0].employee
      ) {
        return item.ticket_user[0].employee.fio;
      }
      return "~";
    },
    handleCheckboxChange(lineId) {
      if (this.selectedLines.includes(lineId)) {
        this.selectedLines = [];
      } else {
        this.selectedLines = [lineId];
      }
      this.getList();
    },

    handleStatusChange(statusId) {
      if (this.SelectedStatus === statusId) {
        this.SelectedStatus = null;
      } else {
        this.SelectedStatus = statusId;
      }
      this.getList();
    },
    handleCreaterChange(createrId) {
      if (this.SelectedCreater === createrId) {
        this.SelectedCreater = null;
      } else {
        this.SelectedCreater = createrId;
      }
      this.getList();
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    toggleExpand($event) {},
    rowClick(item, row) {
      row.expand(!row.isExpanded);
    },
    created() {
      this.headers = Object.values(this.headersMap);
      this.selectedHeaders = this.headers;
    },
    showHeaderss() {
      return this.headers.filter((s) => s.visible);
    },
    showHeaderss2(item) {
      item.visible = !item.visible;
      this.showHeaderss();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/linestop/allopentickets", {
          pagination: this.dataTableOptions,
          line: this.selectedLines,
          status: this.SelectedStatus,
          search: this.search,
          creater: this.SelectedCreater,
        })
        .then((response) => {
          this.items = response.data.tickets.data;
          this.server_items_length = response.data.tickets.total;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getListExcel() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/linestop/allopenticketsexcel"
        )
        .then((response) => {
          this.punkt_excel = response.data;
          this.downloadExcel = true;
          this.loading = false;
        })
        .catch((error) => {
          console.error("Error fetching data:", error);
          this.downloadExcel = true;
          this.loading = false;
        });
    },
    getLines() {
      axios
        .get(this.$store.state.backend_url + "api/get/lines")
        .then((response) => {
          this.lines = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
  mounted() {
    this.getList();
    this.getLines();
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
.txt_searchTopRight {
  background: #ff9f0e;
  border: 0.2px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px !important;
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
.tdClass {
  display: block;
  color: #fff;
  width: 100%;
  height: 30px;
  border-radius: 15px;
  cursor: default;
  outline: none;
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
  border: 1px solid #e6e6e6;
  box-shadow: none;
  height: 34px !important;
  border-radius: 0px;
}

.headerTitleS {
  width: 100%;
  color: #000;
  font-size: 14px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.rowTestClass {
  color: #000;
  font-family: Inter;
  font-size: 12px;
  font-style: normal;
  font-weight: 300;
  line-height: normal;
}
</style>
