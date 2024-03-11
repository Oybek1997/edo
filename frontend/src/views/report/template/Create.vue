<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("reports.index") }}</span>
        <v-spacer></v-spacer>

        <v-btn color="#6ac82d" small dark @click="save()">
          {{ $t("save") }}
        </v-btn>
      </v-card-title>
      <v-divider></v-divider>
      <v-form ref="dialogForm">
        <v-card-text class="pa-1">
          <v-row>
            <v-col cols="6" md="3" class="py-1 pr-1">
              <label for>{{ $t("name_uz_latin") }}</label>
              <v-text-field
                v-model="form.name_uz_latin"
                :rules="[(v) => !!v || $t('input_required')]"
                hide-details
                dense
                outlined
              ></v-text-field>
            </v-col>

            <v-col cols="6" md="3" class="pa-1">
              <label for>{{ $t("name_uz_cyril") }}</label>
              <v-text-field
                v-model="form.name_uz_cyril"
                :rules="[(v) => !!v || $t('input_required')]"
                hide-details
                dense
                outlined
              ></v-text-field>
            </v-col>

            <v-col cols="6" md="3" class="pa-1">
              <label for>{{ $t("name_ru") }}</label>
              <v-text-field
                v-model="form.name_ru"
                :rules="[(v) => !!v || $t('input_required')]"
                hide-details
                dense
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="3" class="py-1 pl-1">
              <label for>{{ $t("permission.name") }}</label>
              <v-text-field
                v-model="form.permission"
                :rules="
                  !errPermission
                    ? [(v) => !!v || $t('input_required')]
                    : [$t('input.required')]
                "
                hide-details
                dense
                outlined
                id="permission_input"
                @change="getTek(form.permission)"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-title class="pa-0 grey lighten-3">
          <v-spacer></v-spacer>
          {{ $t("reports.conditions") }}
          <v-spacer></v-spacer>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-1">
          <v-row>
            <v-col class="py-1">
              <v-autocomplete
                :label="$t('documentTypes.index')"
                clearable
                v-model="form.document_type_id"
                :items="documentTypes"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
                @change="getDocumentTemplate(form.document_type_id)"
              ></v-autocomplete>
            </v-col>
            <v-col
              class="py-1"
              v-if="form.document_type_id && form.document_type_id != 'all'"
            >
              <v-autocomplete
                :label="$t('documentTypes.index')"
                clearable
                v-model="form.document_template_id"
                :items="documentTemplates"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
                @change="getDocumentDetailAttribute(form.document_template_id)"
              ></v-autocomplete>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-title class="py-0 grey lighten-3">
          <v-spacer></v-spacer>
          {{ $t("reports.columns") }}
          <v-spacer></v-spacer>
          <v-btn color="success" small icon @click="addReportColumn">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-1">
          <v-simple-table>
            <thead>
              <tr>
                <th class="text-center pa-1" style="width: 30px">#</th>
                <th class="text-center pa-1">{{ $t("reports.column_table") }}</th>
                <th class="text-center pa-1">{{ $t("reports.column_name") }}</th>
                <th class="text-center pa-1">{{ $t("reports.additions") }}</th>
                <th class="text-center pa-1">{{ $t("name_uz_latin") }}</th>
                <th class="text-center pa-1">{{ $t("name_uz_cyril") }}</th>
                <th class="text-center pa-1">{{ $t("name_ru") }}</th>
                <th class="text-center pa-1" style="width: 50px">
                  {{ $t("actions") }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in form.report_columns" :key="index">
                <td class="text-center">{{ item.tr }}</td>
                <td class="pa-1 pb-0">
                  <v-autocomplete
                    clearable
                    v-model="item.report_column_table"
                    :items="
                      report_column_tables.filter((v) => {
                        if (
                          v.value == 1 ||
                          (v.value == 2 &&
                            documentTemplates.length &&
                            documentTemplates.find((va) => {
                              if (va.id == form.document_template_id) {
                                return va;
                              }
                            }) &&
                            documentTemplates.find((va) => {
                              if (va.id == form.document_template_id) {
                                return va;
                              }
                            }).has_employee == 1) ||
                          (v.value == 3 &&
                            documentTemplates.length &&
                            documentTemplates.find((va) => {
                              if (va.id == form.document_template_id) {
                                return va;
                              }
                            }) &&
                            documentTemplates.find((va) => {
                              if (va.id == form.document_template_id) {
                                return va;
                              }
                            }).document_detail_templates[0]
                              .document_detail_attributes.length) || v.value == 4
                        ) {
                          return v;
                        }
                      })
                    "
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    small
                  ></v-autocomplete>
                </td>
                <td class="pa-1 pb-0">
                  <v-autocomplete
                    v-if="item.report_column_table"
                    clearable
                    v-model="item.report_column_name"
                    :items="
                      item.report_column_table == 1
                        ? document_colums
                        : item.report_column_table == 2
                        ? document_detail_employee_columns
                        : item.report_column_table == 3
                        ? documentDetailAttributes
                        : item.report_column_table == 4
                        ? actionTypes
                        : []
                    "
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    small
                  ></v-autocomplete>
                  <!-- @change="getDocumentDetailAttribute(item.report_column_name)" -->
                </td>
                <td class="pa-1 pb-0">
                  <v-autocomplete
                    v-if="
                      (item.report_column_table == 3 &&
                        documentDetailAttributes.find((v) => {
                          if (v.id == item.report_column_name) return v;
                        }) &&
                        documentDetailAttributes.find((v) => {
                          if (v.id == item.report_column_name) return v;
                        }).table_list_id &&
                        documentDetailAttributes.find((v) => {
                          if (v.id == item.report_column_name) return v;
                        }).table_list.table_view) ||
                      (item.report_column_table == 2 &&
                        item.report_column_name == 'employee')
                    "
                    clearable
                    v-model="item.table_list_column_name"
                    :items="
                      documentDetailAttributes.find((v) => {
                        if (v.id == item.report_column_name) return v;
                      })
                        ? documentDetailAttributes.find((v) => {
                            if (v.id == item.report_column_name) return v;
                          }).table_list
                          ? documentDetailAttributes
                              .find((v) => {
                                if (v.id == item.report_column_name) return v;
                              })
                              .table_list.column_name.split(', ')
                          : []
                        : item.report_column_name == 'employee'
                        ? employee_columns
                        : []
                    "
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    small
                  ></v-autocomplete>
                  <!-- @change="getDocumentDetailAttribute(item.report_column_name)" -->
                </td>
                <td class="text-center pa-2 pb-0">
                  <v-text-field
                    v-model="item.name_uz_latin"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details
                    dense
                  ></v-text-field>
                </td>
                <td class="text-center pa-2 pb-0">
                  <v-text-field
                    v-model="item.name_uz_cyril"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details
                    dense
                  ></v-text-field>
                </td>
                <td class="text-center pa-2 pb-0">
                  <v-text-field
                    v-model="item.name_ru"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details
                    dense
                  ></v-text-field>
                </td>
                <td class="text-center">
                  <v-btn
                    color="error"
                    small
                    icon
                    @click="deleteReportColumn(item.id)"
                  >
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </v-simple-table>
        </v-card-text>
      </v-form>
      <v-card-text class="py-2"></v-card-text>
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
      form: {
        id: Date.now(),
        permission: "",
        report_columns: [
          {
            id: Date.now(),
            tr: 1,
            report_column_table: null,
            table_list_column_name: null,
            report_column_name: null,
          },
        ],
      },
      report_column_tables: [
        {
          text: this.$t("document.index"),
          value: 1,
        },
        {
          text: this.$t("reports.document_detail_employee"),
          value: 2,
        },
        {
          text: this.$t("reports.document_detail_content"),
          value: 3,
        },
        {
          text: this.$t("reports.document_signers"),
          value: 4,
        },
      ],
      document_colums: [
        {
          text: this.$t("document.document_date"),
          value: "document_date",
        },
        {
          text: this.$t("document.document_number"),
          value: "document_number",
        },
        {
          text: this.$t("document.title"),
          value: "title",
        },
        {
          text: this.$t("document.locale"),
          value: "locale",
        },
        {
          text: this.$t("document.status"),
          value: "status",
        },
        {
          text: this.$t("document.from_department"),
          value: "from_department",
        },
        {
          text: this.$t("document.from_manager"),
          value: "from_manager",
        },
        {
          text: this.$t("document.from_position"),
          value: "from_position",
        },
        {
          text: this.$t("document.to_department"),
          value: "to_department",
        },
        {
          text: this.$t("document.to_position"),
          value: "to_position",
        },
        {
          text: this.$t("document.document_type"),
          value: "document_type",
        },
        {
          text: this.$t("document.document_template"),
          value: "document_template",
        },
        {
          text: this.$t("document.version"),
          value: "version",
        },
      ],
      document_detail_employee_columns: [
        { text: this.$t("employee.index"), value: "employee" },
        { text: this.$t("employee.fullname"), value: "employee_fio" },
        { text: this.$t("employee.position"), value: "employee_position" },
      ],
      employee_columns: [
        { text: this.$t("employee.tabel"), value: "tabel" },
        { text: this.$t("employee.firstname"), value: "firstname_locale" },
        { text: this.$t("employee.lastname"), value: "lastname_locale" },
        { text: this.$t("employee.middlename"), value: "middlename_locale" },
      ],
      documentTypes: [],
      actionTypes: [],
      documentTemplates: [],
      documentDetailAttributes: [],
      errPermission: false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
    addReportColumn() {
      this.form.report_columns.push({
        id: Date.now(),
        tr: this.form.report_columns.length + 1,
        report_column_table: null,
        table_list_column_name: null,
        report_column_name: null,
      });
    },
    deleteReportColumn(id) {
      this.form.report_columns = this.form.report_columns.filter((v) => {
        if (v.id != id) return v;
      });
      let i = 1;
      this.form.report_columns.map((v) => {
        v.tr = i;
        i++;
      });
    },
    getList(id) {
      axios
        .get(this.$store.state.backend_url + "api/report-template/edit/" + id)
        .then((res) => {
          this.form = res.data;
          if (this.form.document_type_id) {
            this.getDocumentTemplate(this.form.document_type_id);
          }
          this.form.document_type_id =
            this.form.document_type_id == 0
              ? "all"
              : this.form.document_type_id;
          this.form.document_template_id =
            this.form.document_template_id == 0
              ? "all"
              : this.form.document_template_id;
          // console.log(res);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    getDocumentType() {
      axios
        .post(this.$store.state.backend_url + "api/document-types")
        .then((res) => {
          this.documentTypes = res.data;
          this.documentTypes.map((v) => {
            v.text = v["name_" + this.$i18n.locale];
            v.value = v.id;
            // console.log();
          });
          this.documentTypes.unshift({
            text: this.$t("all"),
            value: "all",
            id: 0,
          });
          // console.log(this.documentTypes);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    getActionType() {
      axios
        .get(this.$store.state.backend_url + "api/action-type")
        .then((res) => {
          this.actionTypes = res.data;
          this.actionTypes.map((v) => {
            v.text = v["name_" + this.$i18n.locale];
            v.value = v.id == 12 ? v.name_uz_latin+'_1' : v.name_uz_latin;
            v.value = v.value.toLowerCase();
            v.value = v.value.replaceAll(" ", "_");
            v.value = v.value.replaceAll("`", "");
            v.value = v.value.replaceAll("'", "");
          });

          this.actionTypes.unshift({
            text: this.$t("all"),
            value: "all",
            id: 0,
          });
          console.log(this.actionTypes);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    getDocumentTemplate(id) {
      if (id == "all") {
        return [];
      }
      axios
        .post(this.$store.state.backend_url + "api/document-templates/report/" + id)
        .then((res) => {
          this.documentTemplates = res.data;
          this.documentTemplates.map((v) => {
            v.text = v["name_" + this.$i18n.locale];
            v.value = v.id;
            // console.log();
          });
          this.documentTemplates.unshift({
            text: this.$t("all"),
            value: "all",
            id: 0,
          });
          if (this.form.document_template_id) {
            this.getDocumentDetailAttribute(this.form.document_template_id);
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
    getDocumentDetailAttribute(item) {
      if (item == "all") {
        return [];
      }
      let documentTemplate = this.documentTemplates.find((v) => {
        if (v.id == item) return v;
      });
      this.documentDetailAttributes =
        documentTemplate.document_detail_templates[0].document_detail_attributes;

      this.documentDetailAttributes.map((v) => {
        v.text = v["attribute_name_" + this.$i18n.locale];
        v.value = v.id.toString();
      });
      // console.log(documentTemplate);
    },
    getTableList() {},
    getTek(text) {
      axios
        .post(this.$store.state.backend_url + "api/check-permission", {
          permission: text,
        })
        .then((res) => {
          if (res.data == 1) {
            this.errPermission = true;
            Swal.fire({
              icon: "error",
              text: this.$t("this permission is available"),
            }).then((res) => {
              document.getElementById("permission_input").focus();
            });
          } else {
            this.errPermission = false;
          }
          // console.log(res);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    save() {
      if (this.$refs.dialogForm.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/report-template/update",
            this.form
          )
          .then((res) => {
            this.loading = false;
            this.$router.push("/reports/template");
            // console.log(res);
          })
          .catch((err) => {
            this.loading = false;
            console.log(err);
          });
        // console.log(this.form);
      }
    },
  },
  mounted() {
    if (this.$route.params.report_template_id) {
      this.getList(this.$route.params.report_template_id);
    }
    // console.log(this.$route.params.report_template_id);
    this.getDocumentType();
    this.getActionType();
  },
  created() {},
};
</script>
<style scoped>
table thead tr th {
  height: 0px !important;
}
table tbody tr td {
  height: 0px !important;
}
</style>