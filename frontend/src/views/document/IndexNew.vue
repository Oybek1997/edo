<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("document.index") }}</span>
        <v-spacer></v-spacer>
        <v-text-field
          v-model="filter.content"
          hide-details
          outlined
          dense
          :label="$t('searchInText')"
          class="mr-1"
          @keyup.native.enter="getFilter()"
        ></v-text-field>
        <v-autocomplete
          v-model="filter.staff_id"
          v-if="$route.params.menu_item == 'archive'"
          :items="staffs"
          :search-input.sync="search"
          :loading="isLoading"
          outlined
          dense
          :label="$t('position.index')"
          hide-details
          style="max-width: 520px; min-width: 466px"
          @change="getFilter()"
          @keyup="getStaffs()"
          item-value="id"
          item-text="search"
          clearable
        >
          <template v-slot:selection="{ item }" style="max-width: 150px">
            {{
              item.department_code +
              " " +
              item["department_name_" + $i18n.locale]
            }}
          </template>
          <template v-slot:item="{ item }">
            <v-list-item-content>
              <v-list-item-title>
                {{
                  item.department_code +
                  " " +
                  item["department_name_" + $i18n.locale]
                }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ item["position_name_" + $i18n.locale] }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </template>
        </v-autocomplete>
        <v-autocomplete
          v-model="filter.reaction_status"
          v-else
          :items="reaction_status"
          outlined
          dense
          :label="$t('document.reaction_status')"
          multiple
          hide-details
          style="max-width: 520px; min-width: 466px"
          @change="getFilter()"
        >
          <template v-slot:selection="{ item }">
            <v-chip
              :class="
                item.value == 1
                  ? 'success'
                  : item.value == 2
                  ? 'error'
                  : item.value == 3
                  ? 'deep-purple'
                  : item.value == 4
                  ? 'orange lighten-1'
                  : ''
              "
              small
              :dark="item.reaction_status == 0 ? false : true"
              class="ma-0 mr-1 px-1"
              >{{ item.text }}</v-chip
            >
          </template>
          <template v-slot:item="{ item }">
            <v-list-item-content>
              <v-list-item-title v-text="item.text"></v-list-item-title>
            </v-list-item-content>
          </template>
        </v-autocomplete>
      </v-card-title>
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="noDataText"
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
        :footer-props="{
          itemsPerPageOptions: $store.state.COMPANY_ID == 3 ? [20, 50,100, 200, -1] : [20,50],
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
        show-expand
        @update:expanded="toggleExpand"
        @dblclick:row="rowClick"
      >
        <template v-slot:body.prepend>
          <tr class="py-0 my-0 prepend_height">
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.document_number"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
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
                    v-bind="attrs"
                    @keyup.native.enter="getFilter()"
                    v-on="on"
                    hide-details
                    clearable
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

            <td class="py-0 my-0 dense">
              <v-autocomplete
                class="py-0"
                clearable
                v-model="filter.document_template_id"
                :items="document_templates"
                hide-details
                item-value="id"
                @change="getFilter()"
              >
                <template v-slot:selection="{ item }">{{ item.text }}</template>
                <template v-slot:item="{ item }">
                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.created_by"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" v-if="$route.params.document_type == 4">
              <v-text-field
                v-model="filter.summary"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" v-if="$route.params.document_type == 4">
              <v-text-field
                v-model="filter.registration"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" v-if="$route.params.document_type == 4">
              <v-text-field
                v-model="filter.korrespondent"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" v-if="$route.params.document_type == 4">
              <v-text-field
                v-model="filter.type"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.send_by"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.receive_by"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="min-width: 130px">
              <v-text-field
                v-model="filter.pending_action"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
          </tr>
        </template>
        <template v-slot:item.document_number="{ item }">
          <!-- <a :href="'#/documents/show/'+item.id">{{item.document_number}}</a> -->
          <!-- {{ signered[items.map(function(x) {return x.id; }).indexOf(item.id) + from - 1] }} -->
          <v-btn
            outlined
            small
            :dark="item.reaction_status == 0 ? false : true"
            rounded
            :class="
              item.action_type_id == 5
                ? 'info'
                : item.reaction_status == 1
                ? 'success'
                : item.reaction_status == 2
                ? 'error'
                : item.reaction_status == 3
                ? 'deep-purple'
                : item.reaction_status == 4
                ? 'orange lighten-1'
                : ''
            "
            :to="'/document/' + item.pdf_file_name"
            >{{ item.document_number_reg ? item.document_number_reg : item.document_number }}</v-btn
          >
        </template>
        <template v-slot:item.from_department="{ item }" >
          <span class="d-inline-block text-truncate" style="max-width: 150px;" :title="item.from_department">
          {{ item.from_department }}
          </span>
          <span class="d-inline-block text-truncate" style="max-width: 150px;" :title="item.from_manager">
          <b>{{ item.from_manager && item.from_manager.split(' ').map((v,k) => {
              if(k == 0)
              return v;
              else return v[0]+'.';
            }).join(' ') }}</b>
          </span>
        </template>
        <template v-slot:item.to_department="{ item }">
          <span class="d-inline-block text-truncate" style="max-width: 150px;" :title="item.to_department">
          {{ item.to_department }}
          </span>
          <span class="d-inline-block text-truncate" style="max-width: 150px;" :title="item.to_manager">
          <b>{{ item.to_manager && item.to_manager.split(' ').map((v,k) => {
              if(k == 0)
              return v;
              else return v[0]+'.';
            }).join(' ') }}</b>
          </span>
        </template>
        <template v-slot:item.files="{ item }">
          <p v-for="(f, fi) in item.files" :key="fi">
            <v-icon>mdi-file-outline</v-icon>
            <a :href="'https://b-edo.uzautomotors.com/staffs/file-download/'+f.id">{{f.file_name}}</a>
          </p>
        </template>
        <template v-slot:item.document_type="{ item }">
          {{ item.document_type["name_" + $i18n.locale] }}
        </template>
        <template v-slot:item.responsible="{ item }" >
          <td style="white-space:normal;">
            {{ item.responsible }}
          </td>
        </template>
        <template v-slot:item.document_template="{ item }">
          {{
            item.document_template &&
            item.document_template["name_" + $i18n.locale]
          }}
        </template>
        <template v-slot:item.document_date="{ item }">
          {{
            item.document_date_reg ? (
            item.document_date_reg.substr(0, 10) +
            " " +
            item.document_date_reg.substr(11, 5))
            : (item.document_date.substr(0, 10) +
            " " +
            item.document_date.substr(11, 5))
          }}
        </template>
        <template v-slot:item.status="{ item }" style="padding: 0px">
          123
        </template>
        <template v-slot:expanded-item="{ headers, item }">
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
                    >
                      <!-- {{  }} -->
                    </p>
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
                              {{ item.attribute_value }}
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
        </template>
      </v-data-table>
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
const moment = require("moment");
import Swal from "sweetalert2";
import Axios from "axios";
const Cookies = require("js-cookie");
// import moment from 'moment';
export default {
  data() {
    return {
      date: null,
      loading: false,
      isLoading: false,
      expanded: [],
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      form: {},
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
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
        title: "",
        document_type_id: 0,
        document_template_id: 0,
        document_start_date: "",
        document_end_date: "",
        attributes: [],
        menu_item: "",
        document_number: "",
        created_by: "",
        korrespondent: "",
        type: "",
        registration: "",
        send_by: "",
        receive_by: "",
        content: "",
        pending_action: "",
        reaction_status: [0, 1, 2, 3, 4],
        staff_id: null,
      },
      showFilter: false,
      menu: [],
      tableLists: [],
      table_name: [],
      column_name: [],
      is_locale: [],
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
        {
          id: 7,
          name_uz_latin: "Oldindan kelishuv",
          name_uz_cyril: "Олдидан килишув",
          name_ru: "Предсогласование",
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
        {
          text: this.$t("substantiate"),
          value: 4,
        },
      ],
      noDataText: "",
      staffs: [],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    user() {
      let localStorage = window.localStorage;
      return JSON.parse(localStorage.getItem('user'));
    },
    headers() {
      let headers = [
        { text: "", value: "data-table-expand" },
        // { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          align: "center",
          width: 150,
          sortable: false,
        },
        {
          text: this.$t("document.document_date"),
          value: "document_date",
          width: 150,
          sortable: false,
        },
        {
          text: this.$t("document.document_type"),
          value: "document_template",
          sortable: false,
        },
        {
          text: this.$t("document.responsible"),
          value: "responsible",
          width: 150,
          sortable: false,
        },
        {
          text: this.$t("document.creator"),
          value: "creator",
          sortable: false,
        },
        {
          text: this.$t("document.short_content"),
          value: "summary",
          sortable: false,
        },
        {
          text: this.$t("document.number"),
          value: "registration",
          sortable: false,
        },
        {
          text: this.$t("document.correspondent"),
          value: "korrespondent",
          sortable: false,
        },
        {
          text: this.$t("document.type"),
          value: "type",
          sortable: false,
        },
        {
          text: this.$t("document.signer"),
          value: "signer",
          sortable: false,
        },

        {
          text: this.$t("document.files"),
          value: "files",
          align: "center",
          sortable: false,
        },
      ];
      let localStorage = window.localStorage;
      let User = JSON.parse(localStorage.getItem('user'));

      let filtered_headers = headers.filter((header) => {
        if (this.$route.params.document_type != 4) {
          return (
            header.value != "summary" &&
            header.value != "registration" &&
            header.value != "korrespondent" &&
            header.value != "type"
          );
        } else return headers;
      });
      return filtered_headers;
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
    getList() {
      this.loading = true;
      this.noDataText = this.$t("loadingText");
      axios
        .post(this.$store.state.backend_url + "api/documents/filter", {
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
          filter: this.filter,
        })
        .then((response) => {
          let locale = this.$i18n.locale == "uz_latin" ? 'uz_latin' : 'uz_cyril';
          this.items = response.data.documents.data;
          this.table_list_value = response.data.table_list_value;
          this.items.map((document, index) => {
            document.reaction_status = 0;
            document.action_type_id = 0;
            document.document_signers.map((document_signer) => {
              if (this.user.employee_id == document_signer.signer_employee_id) {
                document.reaction_status = document_signer.status;
                document.action_type_id = document_signer.action_type_id;
              }
            });
            let creator = document.document_signers.find(v => v.action_type_id == 6);
            let creatorDepartment = document.document_signers.find(v => v.action_type_id == 6).staff.department;
            document.responsible = creatorDepartment['name_'+this.$i18n.locale];
            document.creator = creator.employee_staffs.employee['lastname_' + locale] + ' '+creator.employee_staffs.employee['firstname_' + locale].substring(0,1)+". "+creator.employee_staffs.employee['middlename_' + locale].substring(0,1)+".";
            let signer = document.document_signers.find(v => v.action_type_id == 2);
            if(signer){
              document.signer = signer.employee_staffs.employee['lastname_' + locale]+' '+signer.employee_staffs.employee['firstname_' + locale].substring(0,1)+". "+signer.employee_staffs.employee['middlename_' + locale].substring(0,1)+".";
            }
            else document.signer = '';
            return document;
          });
          if (!this.items.length) {
            this.noDataText = this.$t("noDataText");
          }
          this.server_items_length = response.data.documents.total;
          this.from = response.data.documents.from;
          this.loading = false;
          this.getStaffs();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });

      // axios
      //   .post(this.$store.state.backend_url + "api/mobile/document-list", {
      //     pagination: this.dataTableOptions,
      //     language: this.$i18n.locale,
      //     filter: this.filter,
      //   })
      //   .then((res) => {
      //     console.log(res);
      //   })
      //   .catch((err) => {
      //     console.log(err);
      //   });
    },
    getDocumentTemplate() {
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-list",
          {
            language: this.$i18n.locale,
          }
        )
        .then((res) => {
          this.document_templates = res.data;
        })
        .catch((err) => {
          console.error(err);
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
    getTableList(id) {
      this.isLoading = true;
      let columnName =
        this.is_locale[id] == 1
          ? this.column_name[id] + "_" + this.$i18n.locale
          : this.column_name[id];
      axios
        .post(this.$store.state.backend_url + "api/documents/table-list", {
          table_name: this.table_name[id],
          column_name: columnName,
          is_locale: this.is_locale[id],
          search: this.search,
        })
        .then((response) => {
          this.tableLists["table_" + id] = response.data.data;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error);
          this.isLoading = false;
        });
    },
    getStaffs() {
      this.staffs = [];
      if (this.$route.params.menu_item == "archive") {
        if (this.$route.params.document_type == "employee") {
          this.isLoading = true;
          axios
            .post(this.$store.state.backend_url + "api/get-staffs", {
              search: this.search,
              employee: true,
            })
            .then((res) => {
              this.staffs = res.data.data;
              this.staffs.map((v) => {
                v.search =
                  v.department_code +
                  " " +
                  v.department_name_ru +
                  " " +
                  v.department_name_uz_cyril +
                  " " +
                  v.department_name_uz_latin +
                  " " +
                  v.position_name_ru +
                  " " +
                  v.position_name_uz_cyril +
                  " " +
                  v.position_name_uz_latin;
              });
              this.isLoading = false;
            })
            .catch((err) => {
              console.log(err);
              this.isLoading = false;
            });
        } else {
          if (this.$store.getters.checkPermission("all-document-show")) {
            this.isLoading = true;
            axios
              .post(this.$store.state.backend_url + "api/get-staffs", {
                search: this.search,
                employee: false,
              })
              .then((res) => {
                this.staffs = res.data.data;
                this.staffs.map((v) => {
                  v.search =
                    v.department_code +
                    " " +
                    v.department_name_ru +
                    " " +
                    v.department_name_uz_cyril +
                    " " +
                    v.department_name_uz_latin +
                    " " +
                    v.position_name_ru +
                    " " +
                    v.position_name_uz_cyril +
                    " " +
                    v.position_name_uz_latin;
                });
                this.isLoading = false;
              })
              .catch((err) => {
                console.log(err);
                this.isLoading = false;
              });
          } else {
            user.employee.employee_staff.map((v) => {
              this.staffs.push(v.staff);
            });

            this.staffs.map((v) => {
              v.department_code = v.department.department_code;
              v["department_name_" + this.$i18n.locale] =
                v.department["name_" + this.$i18n.locale];
              v["position_name_" + this.$i18n.locale] =
                v.position["name_" + this.$i18n.locale];
              v.search =
                v.department_code +
                " " +
                v.department.name_ru +
                " " +
                v.department.name_uz_cyril +
                " " +
                v.department.name_uz_latin +
                " " +
                v.position.name_ru +
                " " +
                v.position.name_uz_cyril +
                " " +
                v.position.name_uz_latin;
            });
          }
        }
      }
      console.log(this.staffs);
    },
    editItem(item) {},
    documentCopy(id) {
      axios
        .post(this.$store.state.backend_url + "api/document/copy", {
          document_id: id,
          restore: false,
        })
        .then((res) => {
          this.$router.push("/document/update/" + res.data);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    restoreDocument(id) {
      axios
        .post(this.$store.state.backend_url + "api/document/copy", {
          document_id: id,
          restore: true,
        })
        .then((res) => {
          this.$router.push("/document/update/" + res.data);
        })
        .catch((err) => {
          console.log(err);
        });
    },
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
      this.filter.staff_id = null;
      Cookies.set("filter", this.filter);
      this.getList();
    },
  },
  mounted() {
    if (Cookies.get("filter")) {
      this.filter = JSON.parse(Cookies.get("filter"));
    } else {
      this.filter.reaction_status = [0, 1, 2, 3, 4];
    }
    this.filter.menu_item = 'all';
    this.filter.document_type_id = 27;
    // if (this.$route.params.menu_item) {
    //   this.filter.menu_item = this.$route.params.menu_item;
    //   this.filter.document_type_id = this.$route.params.document_type;
    //   Cookies.set("filter", this.filter);
    // }
    this.getDocumentTemplate();
    this.getList();
  },
};
</script>
<style scoped>
.v-item--active {
  background-color: #fff !important;
}
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
