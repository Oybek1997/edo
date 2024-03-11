<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("message.lsp_index") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            class="txt_search1"
            style=""
            :placeholder="$t('search')"
            @keyup.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px">
            <v-icon color="#00B950" left>mdi-filter-outline</v-icon>Фильтр
          </v-btn>
          <!-- ////////////////////////// -->
          <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="filterBtn"
                outlined
                v-bind="attrs"
                v-on="on"
                style="background: #fff; height: 34px"
              >
                Столбцы
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
                    <!-- <v-switch 
                      v-model="item.visible"
                      color="primary"
                      @click="showHeaderss()"
                    ></v-switch> -->
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
          <!-- ////////////////////////// -->
          <v-menu
            transition="slide-y-transition"
            left
            v-model="table_menu1"
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
            v-if="$store.getters.checkPermission('organizations-create')"
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
                <!-- <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;" @click="newItem">   
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                    </v-list-item-title
                  ></v-list-item> -->
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title
                    @click="getDocsheetsExcel();"
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
        <!-- <v-row >
        <span class="mr-2 ml-4">
          <v-checkbox
            v-model="filter.document_statuses[0]"
            :label="
              counts.length > 1
                ? document_status[1]['name_' + $i18n.locale] +
                  ' ' +
                  '(' +
                  counts[0].count +
                  ')'
                : ''
            "
            @change="getList"
          ></v-checkbox>
        </span>
        <span class="mr-3"
          ><v-checkbox
            v-model="filter.document_statuses[1]"
            :label="
              counts.length > 2
                ? document_status[2]['name_' + $i18n.locale] +
                  ' ' +
                  '(' +
                  counts[1].count +
                  ')'
                : ''
            "
            @change="getList"
          ></v-checkbox>
        </span>
        <span class="mr-3"
          ><v-checkbox
            v-model="filter.document_statuses[2]"
            :label="
              counts.length > 3
                ? document_status[3]['name_' + $i18n.locale] +
                  ' ' +
                  '(' +
                  counts[2].count +
                  ')'
                : ''
            "
            @change="getList"
          ></v-checkbox>
        </span>
        <span class="mr-3">
          <v-checkbox
            v-model="filter.document_statuses[3]"
            :label="
              counts.length > 4
                ? document_status[4]['name_' + $i18n.locale] +
                  ' ' +
                  '(' +
                  counts[3].count +
                  ')'
                : ''
            "
            @change="getList"
          ></v-checkbox>
        </span>
        <span class="mr-3">
          <v-checkbox
            v-model="filter.document_statuses[4]"
            :label="
              counts.length > 5
                ? document_status[5]['name_' + $i18n.locale] +
                  ' ' +
                  '(' +
                  counts[4].count +
                  ')'
                : ''
            "
            @change="getList"
          ></v-checkbox>
        </span>
        <span class="mr-3">
          <v-checkbox
            v-model="filter.document_statuses[5]"
            :label="
              counts.length > 5
                ? document_status[6]['name_' + $i18n.locale] +
                  ' ' +
                  '(' +
                  counts[5].count +
                  ')'
                : ''
            "
            @change="getList"
          ></v-checkbox>
        </span>
      </v-row> -->
        <!-- <v-btn
          outlined
          x-small
          fab
          @click="
            getExcel(1);
            excel = [];
          "
          class="mr-2"
        >
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn> -->
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
            dense
            class="doc-template_data-table report-template_data-table"
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="noDataText"
            :height="screenHeight"
            :loading="loading"
            :headers="showHeaders"
            :items="items"
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            :footer-props="{
              itemsPerPageOptions: [50, 100, 200, -1],
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
            :expanded.sync="expanded"
            single-expand
            @update:expanded="toggleExpand"
            @dblclick:row="rowClick"
          >
            <!-- <template v-slot:body.prepend>
              <tr class="py-0 my-0">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.document_number"
                    @keyup.native.enter="getFilter()"
                    class="my-1"
                        type="text"
                        clearable
                        dense
                        outlined
                        hide-details
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-menu
                    ref="rangeMenu"
                    :close-on-content-click="false"
                    :return-value.sync="filter.document_range"
                    offset-y
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="filter.document_range"
                        @keyup.native.enter="getFilter()"
                        v-bind="attrs"
                        v-on="on"
                        class="my-1"
                        type="text"
                        clearable
                        dense
                        outlined
                        hide-details
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
                </td>
                <td class="py-0 my-0 dense" style="width: 120px"></td>
                <td class="py-0 my-0 dense" style="width: 120px">
                  <v-autocomplete
                  v-model="filter.document_template_id"
                  @change="getFilter()"
                  :items="document_templates"
                  multiple
                  class="my-1"
                        type="text"
                        clearable
                        dense
                        outlined
                        hide-details
                    item-value="id"
                  >
                    <template v-slot:selection="{ item }">
                      {{ item.text }}
                    </template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title v-text="item.text"></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0 dense" style="min-width: 140px"></td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense" style="min-width: 130px"></td>
                <td class="py-0 my-0 dense" style="min-width: 130px">
                  <v-text-field
                    v-model="filter.pending_action"
                    @keyup.native.enter="getFilter()"
                    class="my-1"
                        type="text"
                        clearable
                        dense
                        outlined
                        hide-details
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense"></td>
              </tr>
            </template> -->
            <template v-slot:item.document_number="{ item }">
              <td style="max-width: 100px">
                <span
                  :dark="item.reaction_status == 0 ? false : true"
                  :class="
                    item.reaction_status == 1
                      ? 'success'
                      : item.reaction_status == 2
                      ? 'error'
                      : item.reaction_status == 3
                      ? 'deep-purple'
                      : ''
                  "
                  :to="'/document/' + item.pdf_file_name"
                  >{{ item.document_number }}</span
                >
              </td>
            </template>
            <template v-slot:item.total_sum="{ item }">
              <td style="max-width: 100px">
                {{
                  item.document_details &&
                  item.document_details[0].document_detail_attribute_values[0]
                    ? item.document_details[0]
                        .document_detail_attribute_values[0].attribute_value
                    : ""
                }}
              </td>
            </template>
            <template v-slot:item.department_send="{ item }">
              <td style="max-width: 200px">
                {{
                  item.employee.employee_staff &&
                  item.employee.employee_staff[0].staff.department[
                    "name_" + $i18n.locale
                  ]
                }}
              </td>
            </template>
            <template v-slot:item.creator="{ item }">
              <td style="max-width: 100px">
                {{
                  $i18n.locale == "uz_latin"
                    ? item.employee.lastname_uz_latin +
                      " " +
                      item.employee.firstname_uz_latin.substr(0, 1) +
                      "." +
                      item.employee.middlename_uz_latin.substr(0, 1) +
                      "."
                    : item.employee.lastname_uz_cyril +
                      " " +
                      item.employee.firstname_uz_cyril.substr(0, 1) +
                      "." +
                      item.employee.middlename_uz_cyril.substr(0, 1) +
                      "."
                }}
              </td>
            </template>
            <template v-slot:item.department_receiver="{ item }">
              <td style="max-width: 100px">
                {{
                  item.department && item.department.manager_staff
                    ? item.department.manager_staff.employees.length
                      ? item.department.manager_staff.employees[0][
                          "lastname_" + language
                        ] +
                        " " +
                        item.department.manager_staff.employees[0][
                          "firstname_" + language
                        ].substr(0, 1) +
                        "." +
                        item.department.manager_staff.employees[0][
                          "middlename_" + language
                        ].substr(0, 1) +
                        "."
                      : item.department.manager_staff.position[
                          "name_" + $i18n.locale
                        ]
                    : item.department["name_" + $i18n.locale]
                }}
              </td>
            </template>
            <template v-slot:item.document_type="{ item }">
              <td style="max-width: 100px">
                {{ item.document_type["name_" + $i18n.locale] }}
              </td>
            </template>
            <template v-slot:item.document_template="{ item }">
              <td style="max-width: 200px">
                {{
                  item.document_template &&
                  item.document_template["name_" + $i18n.locale]
                }}
              </td>
            </template>
            <template v-slot:item.document_date="{ item }">
              <td style="max-width: 100px">
                {{
                  item.document_date.substr(0, 10) +
                  " " +
                  item.document_date.substr(11, 5)
                }}
              </td>
            </template>
            <template v-slot:item.status="{ item }" style="padding: 0px">
              <span
                v-if="item.status == 0"
                style="
                  display: block;
                  background: #757575;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 1"
                style="
                  display: block;
                  background: #00acc1;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 2"
                style="
                  display: block;
                  background: #039be5;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 3"
                style="
                  display: block;
                  background: teal;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 4"
                style="
                  display: block;
                  background: #d8cd1d;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 5"
                style="
                  display: block;
                  background: #00c853;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 6"
                style="
                  display: block;
                  background: #ef6c00;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
            </template>
            <template v-slot:item.pending_action="{ item }">
              <template v-for="document_signer in item.document_signers">
                <div
                  :key="document_signer.id"
                  class="ma-0"
                  v-if="
                    item.status != 6 &&
                    (document_signer.status == 0 || document_signer.status == 3)
                  "
                >
                  <div v-if="document_signer.signer_employee">
                    {{
                      document_signer.signer_employee &&
                      document_signer.signer_employee["lastname_" + language] +
                        " " +
                        document_signer.signer_employee[
                          "firstname_" + language
                        ].substr(0, 1) +
                        "." +
                        document_signer.signer_employee[
                          "middlename_" + language
                        ].substr(0, 1) +
                        "."
                    }}
                  </div>
                  <div v-else>
                    {{
                      document_signer.employee_staffs &&
                      document_signer.employee_staffs.employee[
                        "lastname_" + language
                      ] +
                        " " +
                        document_signer.employee_staffs.employee[
                          "firstname_" + language
                        ].substr(0, 1) +
                        "." +
                        document_signer.employee_staffs.employee[
                          "middlename_" + language
                        ].substr(0, 1) +
                        "."
                    }}
                  </div>
                </div>
              </template>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                v-if="item.created_employee_id == user.employee_id && false"
                color="primary"
                class="px-1"
                small
                icon
                @click="$router.push('/document/copy/' + item.id)"
              >
                <v-icon>mdi-content-copy</v-icon>
              </v-btn>
              <v-btn
                color="success"
                class="px-1"
                small
                icon
                @click="$router.push('/document/' + item.pdf_file_name)"
              >
                <v-icon>mdi-eye-outline</v-icon>
              </v-btn>
              <v-btn
                class="px-1"
                v-if="
                  $store.getters.checkPermission('document-update') &&
                  item.status < 1
                "
                color="blue"
                small
                icon
                @click="$router.push('/document/update/' + item.id)"
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                v-if="item.status == 0"
                color="red"
                class="px-1"
                small
                icon
                @click="deleteItem(item)"
              >
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </template>
            <!-- <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length">
                <v-row class="justify-center">
                  <v-col cols="12" md="10" lg="9" xl="7" class="py-1 ma-2">
                    <v-card class="pa-3">
                      <div
                        v-for="(
                          document_detail, detail_index
                        ) in item.document_details"
                        :key="detail_index"
                      >
                        <strong
                          style="float: left"
                          v-if="item.document_details.length > 1"
                          >{{ detail_index + 1 }}.</strong
                        >
                        <p
                          class="text-left font-weight-black my-1 pl-6"
                          v-html="document_detail.content"
                        ></p>
                        <v-simple-table dense>
                          <template v-slot:default>
                            <tbody>
                              <tr
                                v-for="item in document_detail.document_detail_attribute_values"
                                :key="item.index"
                              >
                                <td class="text-right px-2">
                                  <b>
                                    {{
                                      item.document_detail_attributes[
                                        "attribute_name_" + $i18n.locale
                                      ]
                                    }}:
                                  </b>
                                </td>
                                <td class="text-left px-2" width="50%">
                                  {{ item.attribute_value }}*
                                </td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </div>
                    </v-card>
                  </v-col>
                </v-row>
              </td>
            </template> -->
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <!-- downloading excel dialog qismi boshlandi -->
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="280" @keydown.esc="downloadExcel = false">
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
            style="text-transform: none; border-radius: 5px;"
            class="ma-3 pa-3"
          >
            <download-excel
              :data="punkt_excel"
              :name="'Hujjat_shablonlar_ruyxati_' + today + '.xls'"
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
            style="text-transform: none; border-radius: 5px;"
          >
            {{ $t("Отменить") }}<v-icon right>mdi-close-box-outline</v-icon>
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
    <!-- <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="py-1 px-3">
          <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel :data="excel" :name="'Hisobot_' + today + '.xls'">
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog> -->
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import Axios from "axios";
const Cookies = require("js-cookie");
export default {
  data() {
    return {
      date: null,
      loading: false,
      isLoading: false,
      downloadExcel: false,
      table_menu1: false,
      table_menu: false,
      excel: [],
      expanded: [],
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      punkt_excel: [],
      form: {},
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      page: 1,
      from: 0,
      server_items_length: -1,
      document_type: [],
      document_template: {
        document_detail_templates: [
          {
            document_detail_attributes: {},
          },
        ],
      },
      document_templates: [],
      filter: {
        document_statuses: [true, true, true, true, true, true],
        document_type_id: "",
        document_template_id: "",
        document_start_date: "",
        document_end_date: "",
        attributes: [],
        menu_item: "",
        document_number: "",
        reaction_status: [0, 1, 2, 3],
      },
      today: moment().format("YYYY-MM-DD"),
      showFilter: false,
      menu: [],
      tableLists: [],
      table_name: [],
      column_name: [],
      counts: [],
      is_locale: [],
      headers: [
        // { text: "", value: "data-table-expand" },
        {
          text: "ID",
          value: "id",
          align: "center",
          width: 30,
          visible: false,
          tabList: false,
        },
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          width: 150,
          sortable: false,
          visible: true,
          tabList: false,
        },
        {
          text: this.$t("document.document_date"),
          value: "document_date",
          width: 90,
          sortable: false,
          visible: false,
          tabList: true,
        },
        {
          text: this.$t("document.total_sum"),
          value: "total_sum",
          sortable: false,
          visible: true,
          tabList: true,
        },
        {
          text: this.$t("document.document_type"),
          value: "document_template",
          width: 220,
          sortable: false,
          visible: true,
          tabList: true,
        },
        {
          text: this.$t("document.creator"),
          value: "creator",
          sortable: false,
          visible: true,
          tabList: true,
        },
        {
          text: this.$t("document.department_send"),
          value: "department_send",
          width: 220,
          sortable: false,
          visible: true,
          tabList: true,
        },
        {
          text: this.$t("document.department_receiver"),
          value: "department_receiver",
          sortable: false,
        },
        {
          text: this.$t("document.pending_action"),
          value: "pending_action",
          sortable: false,
          visible: true,
          tabList: true,
        },
        {
          text: this.$t("document.status"),
          value: "status",
          align: "center",
          sortable: false,
          visible: true,
          tabList: false,
        },
        {
          text: this.$t(""),
          value: "actions",
          sortable: false,
          visible: true,
          tabList: false,
        },
      ],
      document_status: [
        {
          id: 0,
          name_uz_latin: "Yangi",
          name_uz_cyril: "Янги",
          name_ru: "Новый",
        },
        {
          id: 1,
          name_uz_latin: "E'lon qilindi",
          name_uz_cyril: "Эьлон қилинди",
          name_ru: "Опубликованный",
        },
        {
          id: 2,
          name_uz_latin: "Qayta ishlash",
          name_uz_cyril: "қайта ишлаш",
          name_ru: "Обработка",
        },
        {
          id: 3,
          name_uz_latin: "Imzolandi",
          name_uz_cyril: "Имзоланди",
          name_ru: "Подписано",
        },
        {
          id: 4,
          name_uz_latin: "Bajarildi",
          name_uz_cyril: "Бажарилди",
          name_ru: "Выполнено",
        },
        {
          id: 5,
          name_uz_latin: "Yakunlandi",
          name_uz_cyril: "Якунланди",
          name_ru: "Завершено",
        },
        {
          id: 6,
          name_uz_latin: "Bekor qilindi",
          name_uz_cyril: "Бекор қилинди",
          name_ru: "Отменен",
        },
      ],
      reaction_status: [
        {
          text: this.$t("document.new"),
          value: 0,
        },
        {
          text: this.$t("document.ok"),
          value: 1,
        },
        {
          text: this.$t("document.cancel"),
          value: 2,
        },
        {
          text: this.$t("document.process"),
          value: 3,
        },
      ],
      noDataText: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    showHeaders() {
      return this.headers.filter((s) => s.visible);
    },
    user() {
      let localStorage = window.localStorage;
      return JSON.parse(localStorage.getItem("user"));
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
  },
  methods: {
    format_date(value) {
      if (value) {
        return moment(String(value)).format("DD.MM.YYYY");
      }
    },
    showHeaderss() {
      return this.headers.filter((s) => s.visible);
    },
    showHeaderss2(item) {
      item.visible = !item.visible;
      this.showHeaderss();
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
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
    },
    getDocsheetsExcel(){
      this.downloadExcel = true;
    },
    getList() {
      this.loading = true;
      this.noDataText = this.$t("loadingText");
      axios
        .post(this.$store.state.backend_url + "api/reports/lsp", {
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
          filter: this.filter,
        })
        .then((response) => {
          this.items = response.data.documents.data;
          this.punkt_excel = response.data;
          this.table_list_value = response.data.table_list_value;
          this.items.map((document, index) => {
            document.reaction_status = 0;
            document.document_signers.map((document_signer) => {
              if (this.user.employee_id == document_signer.signer_employee_id) {
                document.reaction_status = document_signer.status;
              }
            });
            return document;
          });
          if (!this.items.length) {
            this.noDataText = this.$t("noDataText");
          }
          // console.log(this.items);
          this.server_items_length = response.data.documents.total;
          this.from = response.data.documents.from;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getStatusCount() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/documents/lsp-count")
        .then((response) => {
          this.counts = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/reports/lsp-excel", {
          locale: this.$i18n.locale,
          page: page,
          perPage: 1000,
          filter: this.filter,
        })
        .then((response) => {
          new_array = response.data;
          this.excel = this.excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDocumentTemplate() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-list",
          {
            language: this.$i18n.locale,
          }
        )
        .then((res) => {
          this.document_templates = res.data;
          this.document_templates = this.document_templates.filter(
            (v) => v.document_type_id == 7
          );
          this.loading = false;
        })
        .catch((err) => {
          console.error(err);
          this.loading = false;
        });
    },
    getDocumentType() {
      this.filter.attributes = [];
      axios
        .get(this.$store.state.backend_url + "api/document-types")
        .then((response) => {
          this.document_type = response.data;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getFilter() {
      this.showFilter = true;
      this.document_template = this.document_templates.find((v) => {
        return v.id == this.filter.document_template_id;
      });
      // console.log(this.filter);
      Cookies.set("filter", this.filter);
      this.getList();
    },
    editItem(item) {},
    deleteItem(item) {
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
          if (item.status == 0) {
            axios
              .delete(
                this.$store.state.backend_url +
                  "api/documents/delete/" +
                  item.id
              )
              .then((res) => {
                Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
                this.getList();
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
        }
      });
    },
  },
  watch: {
    $route(to, from) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.filter.document_type_id = this.$route.params.document_type;
      Cookies.set("filter", this.filter);
      this.getList();
    },
  },
  mounted() {
    // if (Cookies.get("filter")) {
    //   this.filter = JSON.parse(Cookies.get("filter"));
    // } else {
    //   this.filter.reaction_status = [0, 1, 2, 3];
    // }
    // console.log(Cookies.get("filter"));
    this.getDocumentTemplate();

    this.getList();
    this.getStatusCount();
  },
  created() {},
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
  background: #ff9f0e;
  border: 0.2px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px !important;
  height: 34px !important;
  border-radius: 1px;
  width: 25px;
  padding: 0 13px;
}
.txt_searchBtn2 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  height: 38px !important;
  border-radius: 0px;
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
.report-template_data-table table > tbody > tr > td {
  white-space: normal;
  max-width: 50px;
  height: 43px;
  margin: 0 auto;
  font-size: 12px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
