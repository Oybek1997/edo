<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ headerText }}</span>
        <v-spacer></v-spacer>
        <v-btn
          outlined
          x-small
          fab
          @click="
            getDocumentExcel(1);
            document_excel = [];
          "
          class="mr-2"
        >
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn>
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
      >
        <template v-slot:item.id="{ item }">
          {{
            items
              .map(function (x) {
                return x.id;
              })
              .indexOf(item.id) + from
          }}
        </template>
        <template v-slot:item.document_date="{ item }">
          {{ item.document_date.substr(0, 10) }}
        </template>
        <template v-slot:item.status="{ item }">
          {{ document_status[item.status]["name_" + $i18n.locale] }}
        </template>
        <template v-slot:item.all="{ item }">
          <template v-for="(document_signer, index) in item.document_signers">
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.rozilik="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 1) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.tasdiq="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 2) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.yuboruvchi="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 3) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.ijro_uchun="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 4) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.malumot_uchun="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 5) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.hujjat_yaratuvchisi="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 6) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.empty="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 7) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.komissiya_azolari="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 8) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.komissiya_raisi="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 9) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.komissiya_kotibi="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 10) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.kuzatuvchi="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 11) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.kuzatuvchi_1="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 12) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.tahirlovchi="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 13) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.tarqatuvchi="{ item }">
          <template
            v-for="(document_signer, index) in item.document_signers.filter(
              (v) => {
                if (v.action_type_id == 14) return v;
              }
            )"
          >
            <div :key="index">
              <span v-if="document_signer.fio">
                {{ document_signer.fio }}
              </span>
              <span v-else>
                {{
                  document_signer.employee_staffs.employee[
                    "firstname_" + $i18n.locale
                  ].substr(0, 1) +
                  "." +
                  document_signer.employee_staffs.employee[
                    "middlename_" + $i18n.locale
                  ].substr(0, 1) +
                  ". " +
                  document_signer.employee_staffs.employee[
                    "lastname_" + $i18n.locale
                  ]
                }}
              </span>
            </div>
          </template>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            v-if="$store.getters.checkPermission(item.permission) || true"
            color="success"
            small
            icon
            :to="'/document/' + item.pdf_file_name"
          >
            <v-icon>mdi-eye</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="py-1 px-3">
          <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="document_excel"
              :name="headerText + '_' + today + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
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
  data() {
    return {
      loading: false,
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      form: {},
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      page: 1,
      from: 0,
      server_items_length: -1,
      headers: [],
      headerText: "",
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
      document_excel: [],
      downloadExcel: false,
      today: moment().format("YYYY-MM-DD"),
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
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
      this.headers = [];
      axios
        .post(
          this.$store.state.backend_url +
            "api/unv-report/" +
            this.$route.params.report_template_id,
          {
            pagination: this.dataTableOptions,
            search: this.search,
            language: this.$i18n.locale,
          }
        )
        .then((response) => {
          this.items = response.data.report.data;
          this.server_items_length = response.data.report.total;
          this.from = response.data.report.from;
          this.headers.push({
            text: "#",
            value: "id",
            align: "center",
            width: 40,
            sortable: false,
          });
          let reportTemplate = response.data.report_template;
          this.headerText = reportTemplate["name_" + this.$i18n.locale];
          reportTemplate.report_columns.forEach((v) => {
            this.headers.push({
              text: v["name_" + this.$i18n.locale],
              value: v.table_list_column_name
                ? v.table_list_column_name
                : v.report_column_name,
              sortable: false,
            });

            if (v.report_column_table == 3) {
              this.items.map((va) => {
                if (v.table_list_column_name) {
                  va[v.table_list_column_name] =
                    va.document_detail_contents.find((val) => {
                      if (
                        val.d_d_attribute_id == v.report_column_name &&
                        val.attribute_name == v.table_list_column_name
                      )
                        return val;
                    }).value;
                } else {
                  va[v.report_column_name] = va.document_detail_contents.find(
                    (val) => {
                      if (val.d_d_attribute_id == v.report_column_name)
                        return val;
                    }
                  ).value;
                }
              });
            }
          });
          this.headers.push({
            text: this.$t("actions"),
            value: "actions",
            width: 50,
            align: "center",
            sortable: false,
          });
          this.loading = false;
          console.log(this.headers);
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDocumentExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url +
            "api/unv-report/" +
            this.$route.params.report_template_id,
          {
            locale: this.$i18n.locale,
            pagination: {
              page: page,
              itemsPerPage: 1000,
            },
          }
        )
        .then((response) => {
          let reportTemplate = response.data.report_template;
          response.data.report.data.map((v, index) => {
            let obj = {
              "#": index + page * 1000 - 999,
            };
            reportTemplate.report_columns.map((rt) => {
              if (rt.report_column_table == 3) {
                if (rt.table_list_column_name) {
                  v[rt.table_list_column_name] =
                    v.document_detail_contents.find((val) => {
                      if (
                        val.d_d_attribute_id == rt.report_column_name &&
                        val.attribute_name == rt.table_list_column_name
                      )
                        return val;
                    })
                      ? v.document_detail_contents.find((val) => {
                          if (
                            val.d_d_attribute_id == rt.report_column_name &&
                            val.attribute_name == rt.table_list_column_name
                          )
                            return val;
                        }).value
                      : "";
                } else {
                  v[rt.report_column_name] = v.document_detail_contents.find(
                    (val) => {
                      if (val.d_d_attribute_id == rt.report_column_name)
                        return val;
                    }
                  )
                    ? v.document_detail_contents.find((val) => {
                        if (val.d_d_attribute_id == rt.report_column_name)
                          return val;
                      }).value
                    : "";
                }
              }
              obj[rt["name_" + this.$i18n.locale]] =
                rt.report_column_name == "status"
                  ? this.document_status.find((ds) => {
                      if (ds.id == v.status) return ds;
                    })["name_" + this.$i18n.locale]
                  : v[
                      rt.table_list_column_name
                        ? rt.table_list_column_name
                        : rt.report_column_name
                    ];
            });
            new_array.push(obj);
          });

          this.document_excel = this.document_excel.concat(new_array);
          if (response.data.report.data.length == 1000) {
            this.getDocumentExcel(++page);
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
  },
  mounted() {
    this.getList();
  },
  created() {},
};
</script>