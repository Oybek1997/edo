<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Vakansiyalar oynasi") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="filter.search"
            append-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            readonly
            dense
            hide-details
            solo
            single-line
          ></v-text-field>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="getList"
          >
            <v-icon color="#00B950" left>mdi-magnify</v-icon>Қидириш
          </v-btn>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="
              getDetailExcel();
              inventory_excel = [];
            "
          >
            <v-icon color="#107C41" left>mdi-microsoft-excel</v-icon>Юклаб олиш
          </v-btn>
        </div>
      </v-card-title>
      <!-- :items="staffData.filter((v) => v.employee_staff.length > 0)" -->
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="staffData"
        class="mainTable"
        style="width: 100%; height: 100%; border-radius: 10px"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
        :disable-pagination="true"
        disable-sort
        :footer-props="{
          itemsPerPageOptions: [25, 50, 100, 200],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-left-box',
          lastIcon: 'mdi-arrow-right-box',
          prevIcon: 'mdi-arrow-left-drop-circle-outline',
          nextIcon: 'mdi-arrow-right-drop-circle-outline',
        }"
      >
        <template v-slot:[`body.prepend`]>
          <tr>
            <td colspan="2"></td>
            <td>
              <v-text-field
                v-model="filter.branchName"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.function_code"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <!-- <v-text-field
                v-model="filter.function_name"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field> -->
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
                v-model="filter.department_name"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.position_id"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.position_name"
                hide-details
                dense
                @keyup.enter="getList"
              ></v-text-field>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </template>
        <template v-slot:item.number="{ item, index }" style>
          {{ from + index }}
        </template>
        <template v-slot:item.sendJobs="{ item, index }" style>
          <v-tooltip
            bottom
            v-if="item.rate_count_bp - item.employeeCount - item.rate_count_sv > 0"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                v-if="item.rate_count_critical > 0"
                dark
                v-bind="attrs"
                v-on="on"
                @click="sendJobs(item)"
                icon
              >
                <v-icon color="#00B950" left>mdi-cube-send</v-icon>
              </v-btn>
            </template>
            <span>{{ $t("UzAutoJobs га юбориш") }}</span>
          </v-tooltip>
          <v-tooltip
            bottom
            v-if="item.rate_count_bp - item.employeeCount - item.rate_count_sv > 0"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                v-if="item.rate_count_critical > 0"
                dark
                v-bind="attrs"
                v-on="on"
                @click="rejectDialog(item)"
                icon
              >
                <v-icon color="red" left>mdi-bookmark-remove</v-icon>
              </v-btn>
            </template>
            <span>{{ $t("Критик холатни бекор қилиш") }}</span>
          </v-tooltip>
        </template>
        <template v-slot:item.fun_dep="{ item }" style>
          {{
            item.department
              ? item.department.functional_department
                ? item.department.functional_department["name_" + $i18n.locale]
                : ""
              : ""
          }}
        </template>
        <template v-slot:item.fun_dep_code="{ item }" style>
          {{
            item.department
              ? item.department.functional_department
                ? item.department.functional_department.functional_department_code
                : ""
              : ""
          }}
        </template>
        <template v-slot:item.dep_name="{ item }" style>
          {{ item.department ? item.department["name_" + $i18n.locale] : "" }}
        </template>
        <template v-slot:item.dep_code="{ item }" style>
          {{ item.department ? item.department.department_code : "" }}
        </template>
        <template v-slot:item.position_name="{ item }" style>
          {{ item.position ? item.position["name_" + $i18n.locale] : "" }}
        </template>
        <template v-slot:item.position_code="{ item }" style>
          {{ item.position ? item.position.code : "" }}
        </template>
        <template v-slot:item.b_rate_count_vacant="{ item }" style>
          {{ item.rate_count_bp - item.employeeCount }}
        </template>
        <template v-slot:item.comment="{ item }" style>
          {{ item.uzauto_jobs_status
  ? commentStatus.find((v) => v.id == item.uzauto_jobs_status.status).value+' '+item.uzauto_jobs_status.created_at 
  : 'Янги' }}

        </template>

        <template v-slot:item.minReq="{ item }" style>
          <template v-if="item.staf_min_req && item.staf_min_req.length">
            <tr v-for="(itema, index) in item.staf_min_req" :key="index">
              <td class="text-center px-2">
                {{ index + 1 }}
              </td>
              <td class="text-left px-2">
                {{ itema.name }}
              </td>
              <td rowspan="3" v-if="index === 0">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-if="item.rate_count_critical > 0"
                      text
                      small
                      class="ma-0 pa-0"
                      min-width="0"
                      v-bind="attrs"
                      v-on="on"
                      @click="changeRec(item)"
                    >
                      <v-icon color="#6ac82d">mdi-plus</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ $t("Янги минимал талабларни қўшиш") }}</span>
                </v-tooltip>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td class="text-left" rowspan="4">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-if="item.rate_count_critical > 0"
                      text
                      small
                      class="ma-0 pa-0"
                      min-width="0"
                      v-bind="attrs"
                      v-on="on"
                      @click="changeRec(item)"
                    >
                      <v-icon color="#6ac82d">mdi-plus</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ $t("Янги минимал талабларни қўшиш") }}</span>
                </v-tooltip>
              </td>
            </tr>
          </template>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="dialogJobs" max-width="800px" persistent>
      <v-card class="ma-0 pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("UzUzAuto JOBs га юбориш") }}</span>
        <v-divider class="mt-1 mb-3" style="border-color: #dce5ef"></v-divider>
        <v-card-text class="pl-0">
          <v-card-text class="pt-0">
            <span class="hTitle">{{ $t("Функционал бўлим:") }}</span>
            <v-col cols="12" class="pa-0">
              <v-input
                :messages="
                  'Department code: ' +
                  (sendItem.department ? sendItem.department.department_code : '')
                "
                prepend-icon="mdi-source-commit"
              >
                {{
                  sendItem.department ? sendItem.department["name_" + $i18n.locale] : ""
                }}
              </v-input>
            </v-col>
            <v-col cols="12" class="pa-0">
              <v-input
                :messages="
                  'Position code: ' + (sendItem.position ? sendItem.position.code : '')
                "
                prepend-icon="mdi-source-commit-end"
              >
                {{ sendItem.position ? sendItem.position["name_" + $i18n.locale] : "" }}
              </v-input>
            </v-col>
            <span class="hTitle">{{ $t("Лавозимга қўйилган талаблар:") }}</span>
            <template v-if="sendItem.staf_min_req && sendItem.staf_min_req.length">
              <tr v-for="(itema, index) in sendItem.staf_min_req" :key="index">
                <td class="text-center px-2">
                  {{ index + 1 }}
                </td>
                <td class="text-left px-2">
                  {{ itema.name }}
                </td>
              </tr>
            </template>
            <template v-else>
              <tr>
                <td>none</td>
              </tr>
            </template>
          </v-card-text>
          <v-divider></v-divider>
          <v-card class="mt-1 text-end" outlined elevation="0" style="border: none">
            <v-btn
              v-if="sendItem.rate_count_critical > 0"
              @click="setUzAutoJobs"
              color="#3FCB5D"
              right
              small
              dark
              elevation="0"
              style="
                margin: 0px 20px 0px 0px;
                text-transform: none;
                border-radius: 5px;
                padding: 5px 20px;
              "
            >
              {{ $t("Юбориш") }}
            </v-btn>
            <v-btn
              color="#EB4034"
              right
              small
              dark
              elevation="0"
              @click="dialogJobs = false"
              @keydown.esc="dialogJobs = false"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            >
              {{ $t("Отменить") }}
            </v-btn>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogRec" max-width="800px" persistent>
      <v-card class="ma-0 pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("Минимал талаблар") }}</span>
        <v-divider class="mt-1 mb-3" style="border-color: #dce5ef"></v-divider>
        <v-card-text class="pl-0">
          <v-card-text class="pt-0">
            <v-row>
              <v-col cols="5" sm="6" class="pa-1">
                <v-simple-table dense fixed-header height="300px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="font-weight-black">#</th>
                        <th class="text-left font-weight-black" colspan="1">
                          {{ $t("Бириктирилган талаблар") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th colspan="3" class="pa-0 ma-0">
                          <v-text-field
                            prepend-inner-icon="mdi-magnify"
                            v-model="assignedMinRecueSearch"
                            outlined
                            dense
                            hide-details
                            clearable
                            placeholder="Search"
                            class="ma-0 pa-0 txt_search1"
                          ></v-text-field>
                        </th>
                      </tr>
                      <tr v-for="(item, index) in roleMinRecueList" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td :title="item.name">
                          <p class="text-truncate ma-0" style="max-width: 280px">
                            {{ item.name }}
                          </p>
                        </td>
                        <td class="pa-0">
                          <v-icon color="error" class="my-1" @click="removeMinRecue(item)"
                            >mdi-minus-circle-outline
                          </v-icon>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-col>
              <v-col cols="12" sm="6" class="pa-1">
                <v-simple-table dense fixed-header height="300px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="font-weight-black">#</th>
                        <th class="text-left font-weight-black" colspan="1">
                          {{ $t("Бириктирилмаган талаблар") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th colspan="3" class="pa-0">
                          <v-text-field
                            v-model="notAssignedMinRecueSearch"
                            prepend-inner-icon="mdi-magnify"
                            outlined
                            dense
                            hide-details
                            clearable
                            placeholder="Search"
                            class="ma-0 pa-0 txt_search1"
                          ></v-text-field>
                        </th>
                      </tr>
                      <tr v-for="(item, index) in MinRecueList" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td :title="item.name">
                          <p class="text-truncate ma-0" style="max-width: 280px">
                            {{ item.name }}
                          </p>
                        </td>
                        <td class="pa-0">
                          <v-icon color="primary" class="my-1" @click="addMinRecue(item)"
                            >mdi-plus-circle-outline
                          </v-icon>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider></v-divider>
          <v-card class="mt-1 text-end" outlined elevation="0" style="border: none">
            <v-btn
              @click="saveStafMinRec"
              color="#3FCB5D"
              right
              small
              dark
              elevation="0"
              style="
                margin: 0px 20px 0px 0px;
                text-transform: none;
                border-radius: 5px;
                padding: 5px 20px;
              "
            >
              {{ $t("save") }}
            </v-btn>
            <v-btn
              color="#EB4034"
              right
              small
              dark
              elevation="0"
              @click="dialogRec = false"
              @keydown.esc="dialogRec = false"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            >
              {{ $t("Отменить") }}
            </v-btn>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogReject" max-width="400px" persistent>
      <v-card class="ma-0 pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("Критик вакантни бекор қилиш") }}</span>
        <v-divider class="mt-1 mb-3" style="border-color: #dce5ef"></v-divider>
        <v-card-text class="pl-0">
          <v-card-text class="pt-0">
            <v-text-field
              prepend-inner-icon="mdi-information-variant"
              v-model="rejectComment.comment"
              dense
              hide-details
              clearable
              placeholder="Изох киритинг"
              :rules="[v => v && v.length >= 20 || 'Минимум 20 та белги']"
            ></v-text-field>
          </v-card-text>
          <v-divider></v-divider>
          <v-card class="mt-1 text-end" outlined elevation="0" style="border: none">
            <v-btn
            v-if="rejectComment && rejectComment.comment && rejectComment.comment.length >= 20"
              color="#FB8C00"
              @click="StafKritikReject"
              right
              small
              dark
              elevation="0"
              style="
                margin: 0px 20px 0px 0px;
                text-transform: none;
                border-radius: 5px;
                padding: 5px 20px;
              "
            >
              {{ $t("Reject") }}
            </v-btn>
            <v-btn
              color="#EB4034"
              right
              small
              dark
              elevation="0"
              @click="dialogReject = false"
              @keydown.esc="dialogReject = false"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            >
              {{ $t("Отменить") }}
            </v-btn>
          </v-card>
        </v-card-text>
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
    <v-dialog
      v-model="downloadExcel"
      hide-overlay
      persistent
      width="300"
      @keydown.esc="downloadExcel = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <v-card-text>
          <span class="dialog-head_title">Excel га юклаб олиш</span>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-btn
            color="success"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
          >
            <download-excel :data="application_excel" :name="'RP_' + today + '.xls'">
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn
            color="red"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            ><span style="color: red">{{ $t("close") }}</span>
            <v-icon color="red" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
export default {
  data: () => ({
    dateRules: [
      (v) =>
        !v ||
        /^\d{4}[\-\-](0?[1-9]|1[012])[\-\-](0?[1-9]|[12][0-9]|3[01])$/.test(v) ||
        "Date must be valid(YYYY-DD-MM)",
    ],
    assignedMinRecueSearch: "",
    notAssignedMinRecueSearch: "",
    items: [],
    staffData: [],
    filter: {},
    dialogReject: false,
    dialogRec: false,
    dialogJobs: false,
    loading: false,
    itemStafId: null,
    downloadExcel: false,
    application_excel: [],
    sendItem: [],
    commentStatus: [
      { id: 201, value: "Юборилди:" },
      { id: 202, value: "Эълон қилинди:" },
      { id: 200, value: "Якунланди:" },
    ],
    rejectComment: { comment: null, id: null },
    sendJobsItem: {
      company_id: null,
      depatament_name: null,
      depatament_code: null,
      position_name: null,
      position_code: null,
      send_count: null,
      staf_min_req: null,
    },
    MinRecues: [],
    MinRecueForm: { MinRecues: [] },
    today: moment().format("YYYY-MM-DD"),
    server_items_length: null,
    dataTableOptions: { page: 1, itemsPerPage: 25 },
    server_items_length: -1,
    page: 1,
    from: 0,
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 205;
    },
    headers() {
      return [
        {
          text: "#",
          value: "number",
          align: "center",
          width: 30,
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("SEND"),
          value: "sendJobs",
          class: "blue-grey lighten-5 lign-center",
          width: 60,
        },
        {
          text: this.$t("Branch"),
          value: "department.branch.name",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("FD Code"),
          value: "fun_dep_code",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Function Department"),
          value: "fun_dep",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("DepCode"),
          value: "dep_code",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Department"),
          value: "dep_name",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Pos Code"),
          value: "position_code",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Position"),
          value: "position_name",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Kritik"),
          value: "rate_count_critical",
          class: "blue-grey lighten-5 center",
        },
        {
          text: this.$t("Yuborilgan"),
          value: "rate_count_sv",
          class: "blue-grey lighten-5 center",
        },
        {
          text: this.$t("Талаб"),
          value: "minReq",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Izox"),
          value: "comment",
          class: "blue-grey lighten-5 center",
        },
      ];
      // .filter(
      //   (v) =>
      //     v.value != "actions" ||
      //     this.$store.getters.permissions("staff-update") ||
      //     this.$store.getters.permissions("staff-delete")
      // );
    },
    MinRecueList() {
      return this.MinRecues.filter((v) => {
        return !this.MinRecueForm.find((p) => p.id == v.id);
      }).filter((v) =>
        this.notAssignedMinRecueSearch
          ? v.name.toUpperCase().search(this.notAssignedMinRecueSearch.toUpperCase()) >= 0
          : true
      );
    },
    roleMinRecueList() {
      return this.MinRecueForm.filter((v) =>
        this.assignedMinRecueSearch
          ? v.name.toString().search(this.assignedMinRecueSearch.toUpperCase()) >= 0
          : true
      );
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    changeRec(item) {
      this.itemStafId = item.id;
      this.MinRecueForm = item.staf_min_req;
      this.dialogRec = true;
    },
    rejectDialog(item) {
      this.rejectComment.id = item.id;
      this.dialogReject = true;
    },
    sendJobs(item) {
      this.sendItem = item;
      this.filter.sendCount = 0;
      this.dialogJobs = true;
    },
    addMinRecue(item) {
      this.MinRecueForm.push(item);
    },
    removeMinRecue(item) {
      this.MinRecueForm = this.MinRecueForm.filter((v) => v.id != item.min_req_id);
    },
    manageMinRecues(item) {
      this.MinRecueForm = item;
    },
    StafKritikReject() {
      axios
        .post(this.$store.state.backend_url + "api/employe-staff/reject-critical", {
          reject: this.rejectComment,
        })
        .then(() => {
          this.getList();
          this.dialogReject = false;
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
        .catch((error) => {
          console.error(error);
          Swal.fire({
            position: "center",
            icon: "error",
            width: "250px",
            title: this.$t("swal_error_text"),
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
          });
        });
    },
    saveStafMinRec() {
      axios
        .post(this.$store.state.backend_url + "api/employe-staff/update-min-req", {
          minRec: this.MinRecueForm,
          stafId: this.itemStafId,
        })
        .then(() => {
          this.getList();
          this.dialogRec = false;
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
        .catch((error) => {
          console.error(error);
          Swal.fire({
            position: "center",
            icon: "error",
            width: "250px",
            title: this.$t("swal_error_text"),
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
          });
        });
    },
    getList() {
      this.filter.excells = 0;
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/employe-staff/vacancies", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          language: this.$i18n.locale,
        })
        .then((response) => {
          this.staffData = response.data.employeeStaff.data;
          this.from = response.data.employeeStaff.from;
          this.server_items_length = response.data.employeeStaff.total;
          this.MinRecues = response.data.minRec;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },

    setUzAutoJobs() {
      this.sendJobsItem.company_id = 20;
      this.sendJobsItem.staff_id = this.sendItem.id;
      this.sendJobsItem.depatament_name = this.sendItem.department[
        "name_" + this.$i18n.locale
      ];
      this.sendJobsItem.depatament_code = this.sendItem.department.department_code;
      this.sendJobsItem.position_name = this.sendItem.position[
        "name_" + this.$i18n.locale
      ];
      this.sendJobsItem.position_code = this.sendItem.position.code;
      this.sendJobsItem.send_count = this.sendItem.rate_count_critical;
      let minRecNames = this.sendItem.staf_min_req.map((item) => item.name);
      this.sendJobsItem.staf_min_req = minRecNames.join("\n");
      this.loading = true;
      console.log('this.sendJobsItem=',this.sendJobsItem);
      axios
        .post(this.$store.state.backend_url + "api/candidate/setCandidate", {
          data: this.sendJobsItem,
        })
        .then((response) => {
          this.loading = false;
          this.dialogJobs = false;
          this.getList();
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
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },

    // getDetailExcel() {
    //   let new_array = [];
    //   this.application_excel=[];
    //   this.filter.excells = 1;
    //   this.loading = true;
    //   this.dataTableOptions.itemsPerPage = 500;
    //   axios
    //     .post(this.$store.state.backend_url + "api/employe-staff/full", {
    //       filter: this.filter,
    //       pagination: this.dataTableOptions,
    //       language: this.$i18n.locale,
    //     })
    //     // this.staffData = response.data.data.filter((v) => v.employee_staff.length > 0);
    //     .then((response) => {
    //       response.data.map((v, index) => {
    //         new_array.push({
    //           "№": index + this.dataTableOptions.page,
    //           // ID: v.ID,
    //           Branch: v.Branch,
    //           FunctionalName: v.FunctionalName,
    //           FunctionalCode: v.FunctionalCode,
    //           DepartmentName: v.DepartmentName,
    //           DepartmentCode: v.DepartmentCode,
    //           PositionName: v.PositionName,
    //           PositionCode: v.PositionCode,
    //           Status: v.Status,
    //           RangeName: v.RangeName,
    //           RangeCode: v.RangeCode,
    //           personalType: v.personalType,
    //           expenceType: v.expenceType,
    //           firstname: v.firstname,
    //           lastname: v.lastname,
    //           middlename: v.middlename,
    //           bornDate: v.bornDate,
    //           Category: v.Category,
    //           tabel: v.tabel,
    //           Shift: v.Shift,
    //           experience: v.experience,
    //           firstWorkDate: v.firstWorkDate,
    //           enterOrderNumber: v.enterOrderNumber,
    //           enterOrderDate: v.enterOrderDate,
    //           BP: v.BP,
    //           TS: v.TS,
    //           XS: v.XS,
    //           AS: v.AS,
    //           BPV: v.BPV,
    //           TSV: v.TSV,
    //           Coeff: v.Coeff,
    //           WB: v.WB,
    //           DirInDir: v.DirInDir,
    //           Coeff: v.Coeff,
    //           WB: v.WB,
    //           DirInDir: v.DirInDir,
    //         });
    //       });
    //       this.application_excel = this.application_excel.concat(new_array);
    //       if (response.data.length == 1000) {
    //         this.getDetailExcel(++page);
    //       } else {
    //         this.loading = false;
    //         this.downloadExcel = true;
    //       }
    //       this.filter.excells = 0;
    //       this.dataTableOptions.itemsPerPage = 50;
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //       this.loading = false;
    //     });
    // },
  },
  mounted() {
    this.getList();
  },
};
</script>

<style scoped>
.staff-s table > thead > tr > th {
  white-space: normal;
  display: block;
  display: -webkit-box;
  max-width: 100%;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.fullHeight {
  height: calc(100% - 100px);
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
.hTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 0.4;
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

.btn_class {
  padding: 20px;
  margin: 0px 0px -19px 20px;
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
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
  color: #212529;
  font-size: 14px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_search2 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
  color: #212529;
  width: 50px;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
</style>
