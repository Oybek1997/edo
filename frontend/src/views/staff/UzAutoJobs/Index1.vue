<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{
          $t("Saralanmagan nomzodlar bilan ishlash")
        }}</span>
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
            disabled
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
            disabled
          >
            <v-icon color="#107C41" left>mdi-microsoft-excel</v-icon>Юклаб олиш
          </v-btn>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="createDoc()"
          >
            <v-icon color="#00B950" left>mdi-file-document</v-icon>Хужжатлаштириш*
          </v-btn>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="createDoc()"
          >
            <v-icon color="#00B950" left>mdi-file-document</v-icon>Хужжатлаштириш*
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
        :items="items"
        class="mainTable"
        style="width: 100%; height: 100%; border-radius: 10px"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
        :disable-pagination="true"
        disable-sort
        :footer-props="{
          itemsPerPageOptions: [50, 100, 200],
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
            <td>
              <v-autocomplete
                v-model="filter.active"
                :items="[
                  { text: 'Active', value: 1 },
                  { text: 'NonActive', value: 2 },
                ]"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.tanlov_id"
                :items="Object.values(choiceItem)"
                clearable
                multiple
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
              <v-autocomplete
                v-model="filter.status"
                :items="itemMessages"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status1"
                :items="itemMessages.filter((v)=>v.id!=9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status2"
                :items="itemMessages.filter((v)=>v.id!=9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status3"
                :items="itemMessages.filter((v)=>v.id!=9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status4"
                :items="itemMessages.filter((v)=>v.id!=9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status5"
                :items="itemMessages.filter((v)=>v.id!=9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-text-field
                v-model="filter.attachedDoc1"
                @keyup.native.enter="getList"
                clearable
                hide-details="auto"
                dense
              ></v-text-field>
            </td>
            <td>
              <v-text-field
                v-model="filter.attachedDoc1"
                @keyup.native.enter="getList"
                clearable
                hide-details="auto"
                dense
              ></v-text-field>
            </td>
          </tr>
        </template>

        <template v-slot:item.number="{ item, index }" style>
          {{ from + index }}
        </template>
        <template v-slot:item.choice_id="{ item, index }" style>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <span v-bind="attrs" v-on="on">{{ item.choice.tanlov_id }}</span>
            </template>
            <span>{{
              item.choice
                ? "[" +
                  item.choice.staff.department.branch.name +
                  "] " +
                  item.choice.staff.department.name_uz_latin +
                  ", " +
                  item.choice.staff.position.name_uz_latin
                : ""
            }}</span>
          </v-tooltip>
        </template>
        <template v-slot:item.vacancie_name="{ item, index }" style>
          {{
            item.vacancies.lastname_uz_latin +
            " " +
            item.vacancies.firstname_uz_latin +
            " " +
            item.vacancies.middlename_uz_latin
          }}
        </template>
        <template v-slot:item.knowledge_direction="{ item, index }" style>
          {{
            item.vacancies.knowledge_specialty +
            "(" +
            item.vacancies.knowledge_direction +
            ")" +
            item.vacancies.knowledge_name
          }}
        </template>
        <template v-slot:item.language_skills="{ item, index }" style>
          {{
            item.vacancies.language_skills_first + item.vacancies.language_skills_second
              ? item.vacancies.language_skills_first +
                ", " +
                item.vacancies.language_skills_second
              : ""
          }}
        </template>
        <template v-slot:item.knowledgeNum="{ item, index }" style>
          {{ item.vacancies.knowledge_serial + " " + item.vacancies.knowledge_number }}
        </template>
        <template v-slot:item.attached_doc1="{ item, index }" style>
          {{
            item.choice
              ? item.choice.tanlov
                ? item.choice.tanlov.protocol_number
                : ""
              : ""
          }}
        </template>
        <template v-slot:item.attached_doc3="{ item, index }" style>
          {{
            item.choice ? (item.choice.tanlov ? item.choice.tanlov.order_number : "") : ""
          }}
        </template>
        <template v-slot:item.vacancies_status="{ item, index }" style>
          <span v-if="item.status == null" style="color: blue">
            {{ item.status == null ? "Yangi" : item.status }}
          </span>
          <v-icon color="primary" v-else-if="item.status == true">mdi-check</v-icon>
          <v-icon color="error" v-else-if="item.status == false">mdi-close</v-icon>
        </template>
        <template v-slot:item.vacancies_status1="{ item, index }" style>
          <template v-if="item.one_sorting_status === null">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 1)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 1)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.one_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span
            v-else-if="item.one_sorting_status !== 1 && item.one_sorting_status !== null"
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.one_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>
        <template v-slot:item.vacancies_status2="{ item, index }" style>
          <template
            v-if="
              item.two_sorting_status == null &&
              item.one_sorting_status != null &&
              item.one_sorting_status === 1
            "
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 2)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 2)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.two_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span
            v-else-if="item.two_sorting_status !== 1 && item.two_sorting_status !== null"
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.two_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>
        <template v-slot:item.vacancies_status3="{ item, index }" style>
          <template
            v-if="
              item.three_sorting_status == null &&
              item.two_sorting_status != null &&
              item.two_sorting_status === 1
            "
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 3)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 3)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.three_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span
            v-else-if="
              item.three_sorting_status !== 1 && item.three_sorting_status !== null
            "
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.three_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>

        <template v-slot:item.vacancies_status4="{ item, index }" style>
          <template
            v-if="
              item.four_sorting_status == null &&
              item.three_sorting_status != null &&
              item.three_sorting_status === 1
            "
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 4)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 4)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.four_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span
            v-else-if="
              item.four_sorting_status !== 1 && item.four_sorting_status !== null
            "
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.four_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>
        <template v-slot:item.vacancies_status5="{ item, index }" style>
          <template
            v-if="
              item.five_sorting_status == null &&
              item.four_sorting_status != null &&
              item.four_sorting_status === 1
            "
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 5)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 5)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.five_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span
            v-else-if="
              item.five_sorting_status !== 1 && item.five_sorting_status !== null
            "
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.five_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>
      </v-data-table>
    </v-card>
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
    <v-dialog
      v-model="rejectDialog"
      hide-overlay
      persistent
      width="300"
      @keydown.esc="rejectDialog = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <v-card-text>
          <span class="dialog-head_title">Bekor bo'lish sababi</span>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-autocomplete
            :items="itemMessages.filter((v) => v.id > 1&&v.id!=9999)"
            v-model="rejectID.type"
            item-text="name"
            item-value="id"
            clearable
            solo
            dense
            hide-details="auto"
            @change="getList"
          ></v-autocomplete>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-btn
            color="success"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            v-if="rejectID.type !== null"
            @click="rejectCanditate(), (rejectDialog = false)"
          >
            <span style="color: success">{{ $t("OK") }}</span>
            <v-icon color="success" height="20">mdi-check</v-icon>
          </v-btn>
          <v-btn
            @click="rejectDialog = false"
            color="red"
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
    items: [],
    staffData: [],
    itemMessages: [
      { id: 9999, name: "Янги", status: 1, type: 0 },
      { id: 0, name: "Muvaffaqiyatsiz", status: 1, type: 2 },
    ],
    filter: {},
    loading: false,
    downloadExcel: false,
    rejectDialog: false,
    rejectID: {
      itemId: null,
      sort: null,
      type: null,
    },
    application_excel: [],
    choiceItem: [],
    today: moment().format("YYYY-MM-DD"),
    server_items_length: null,
    dataTableOptions: { page: 1, itemsPerPage: 50 },
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
          text: this.$t("Tanlov Index"),
          value: "choice_id",
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("ID №"),
          value: "vacancies.uzJobPersonID",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("F.I.Sh."),
          value: "vacancie_name",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Ma`lumoti"),
          value: "vacancies.knowledge_type",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Yo`nalishi"),
          value: "vacancies.knowledge_direction",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Ish tajribasi"),
          value: "vacancies.experience",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Xorijiy til"),
          value: "language_skills",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Sertifikat/ Diplom"),
          value: "knowledgeNum",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Passport Seria"),
          value: "vacancies.passport_serial",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Passport Number"),
          value: "vacancies.passport_number",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Passport Number"),
          value: "vacancies.passport_number",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Viloyati"),
          value: "vacancies.passport_region",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Tuman/ Shaxat"),
          value: "vacancies.passport_town",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Tug`ilgan sana"),
          value: "vacancies.born_date",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Electron pochtasi"),
          value: "vacancies.email",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Telefon raqami"),
          value: "vacancies.tel_first",
          class: "blue-grey lighten-5 lign-center",
        },
        {
          text: this.$t("Tanlov xolati"),
          value: "vacancies_status",
          class: "blue-grey lighten-5 lign-center",
          width: 50,
        },
        {
          text: this.$t("Tanlov 1"),
          value: "vacancies_status1",
          class: "blue-grey lighten-5 lign-center",
          width: 120,
        },
        {
          text: this.$t("Tanlov 2"),
          value: "vacancies_status2",
          class: "blue-grey lighten-5 lign-center",
          width: 120,
        },
        {
          text: this.$t("Tanlov 3"),
          value: "vacancies_status3",
          class: "blue-grey lighten-5 lign-center",
          width: 120,
        },
        {
          text: this.$t("Tanlov 4"),
          value: "vacancies_status4",
          class: "blue-grey lighten-5 lign-center",
          width: 120,
        },
        {
          text: this.$t("Tanlov 5"),
          value: "vacancies_status5",
          class: "blue-grey lighten-5 lign-center",
          width: 120,
        },
        {
          text: this.$t("Bayonnoma #"),
          value: "attached_doc1",
          class: "blue-grey lighten-5 lign-center",
          width: 90,
        },
        {
          text: this.$t("Buyruq #"),
          value: "attached_doc2",
          class: "blue-grey lighten-5 lign-center",
          width: 90,
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("staff-update") ||
          this.$store.getters.checkPermission("staff-delete")
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
    handleApproveClick(itemId, sort) {
      this.rejectID.itemId = itemId;
      this.rejectID.sort = sort;
      this.rejectID.type = 1;
      this.rejectCanditate();
    },
    createDoc() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidates/document/create")
        .then((res) => {
          console.log("res", res);
          this.$router.push("/document/" + res.data.pdf_file_name);
        });
      this.loading = false;
    },
    rejectCanditate() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidate/reject", {
          item: this.rejectID.itemId,
          sort: this.rejectID.sort,
          type: this.rejectID.type,
        })
        .then((res) => {
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
        });
      this.loading = false;
      this.rejectID.type = null;
    },
    getList() {
      this.filter.excells = 0;
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidates-index", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          language: this.$i18n.locale,
        })
        .then((response) => {
          this.items = response.data.candidate.data;
          this.from = response.data.candidate.from;
          this.server_items_length = response.data.candidate.total;
          this.choiceItem = response.data.vacancy;
          this.choiceItem = response.data.vacancy;
          // this.itemMessages = response.data.messages;
          response.data.messages.forEach((element) => {
            let i = 0;
            this.itemMessages.push({
              id: element.id,
              name: element.name,
              style: element.style,
              type: element.type,
            });
          });
          console.log(this.itemMessages);
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDetailExcel() {
      let new_array = [];
      this.application_excel = [];
      this.filter.excells = 1;
      this.loading = true;
      this.dataTableOptions.itemsPerPage = 500;
      axios
        .post(this.$store.state.backend_url + "api/employe-staff/full", {
          filter: this.filter,
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
        })
        // this.staffData = response.data.data.filter((v) => v.employee_staff.length > 0);
        .then((response) => {
          response.data.map((v, index) => {
            new_array.push({
              "№": index + this.dataTableOptions.page,
              // ID: v.ID,
              Branch: v.Branch,
              FunctionalName: v.FunctionalName,
              FunctionalCode: v.FunctionalCode,
              DepartmentName: v.DepartmentName,
              DepartmentCode: v.DepartmentCode,
              PositionName: v.PositionName,
              PositionCode: v.PositionCode,
              Status: v.Status,
              RangeName: v.RangeName,
              RangeCode: v.RangeCode,
              personalType: v.personalType,
              expenceType: v.expenceType,
              firstname: v.firstname,
              lastname: v.lastname,
              middlename: v.middlename,
              bornDate: v.bornDate,
              Category: v.Category,
              tabel: v.tabel,
              Shift: v.Shift,
              experience: v.experience,
              firstWorkDate: v.firstWorkDate,
              enterOrderNumber: v.enterOrderNumber,
              enterOrderDate: v.enterOrderDate,
              BP: v.BP,
              TS: v.TS,
              XS: v.XS,
              AS: v.AS,
              BPV: v.BPV,
              TSV: v.TSV,
              Coeff: v.Coeff,
              WB: v.WB,
              DirInDir: v.DirInDir,
              Coeff: v.Coeff,
              WB: v.WB,
              DirInDir: v.DirInDir,
            });
          });
          this.application_excel = this.application_excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getDetailExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
          this.filter.excells = 0;
          this.dataTableOptions.itemsPerPage = 50;
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
