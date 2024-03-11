<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{
          $t("documentTemplates.index")
        }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            class="txt_search1"
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <!--  -->
          <v-menu
            transition="slide-y-transition"
            left
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
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
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="newItem()"
                >
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                  </v-list-item-title></v-list-item
                >
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title @click="getDocsheetsExcel(1); punkt_excel = [];">
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    Скачать таблицу Excel
                  </v-list-item-title>
                  </v-list-item>
              </v-list>
            </v-card>
          </v-menu>
          <!-- <v-btn class="txt_searchBtn ml-2" style="width: 25px; padding: 0 13px;" outlined>
            <v-icon size="18" color="white">mdi-format-list-bulleted</v-icon>
          </v-btn> -->
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
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="items"
            single-expand
            item-key="id"
            :server-items-length="server_items_length"
            item-text="template_name"
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, -1],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            disable-sort
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
          >
            <template v-slot:item.id="{ item }">
              <span
                style="white-space: normal; display: block; cursor: pointer"
                @click="$router.push('../document-templates/update/' + item.id)"
                >{{
                  items
                    .map(function (x) {
                      return x.id;
                    })
                    .indexOf(item.id) + from
                }}</span
              >
            </template>
            <template v-slot:item.template_name="{ item }">
              <span
                @click="$router.push('../document-templates/update/' + item.id)"
                style="white-space: normal; display: block; cursor: pointer"
                v-if="item"
                >{{ item["name_" + $i18n.locale] }}</span
              >
            </template>
            <template v-slot:item.department_id="{ item }">
              <span
                @click="$router.push('../document-templates/update/' + item.id)"
                style="white-space: normal; display: block; cursor: pointer"
                v-if="item.department"
                >{{ item.department["name_" + $i18n.locale] }}</span
              >
            </template>
            <template
              v-slot:item.document_type_id="{ item }"
              @click="$router.push('../document-templates/update/' + item.id)"
            >
              <span
                @click="$router.push('../document-templates/update/' + item.id)"
                style="white-space: normal; display: block; cursor: pointer"
                v-if="item.document_type"
                >{{ item.document_type["name_" + $i18n.locale] }}</span
              >
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                class="px-0"
                color="#3FCB5D"
                style="min-width: 25px"
                small
                text
                @click="$router.push('../document-templates/update/' + item.id)"
              >
                <v-icon size="20">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                class="pl-0 pr-2"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)"
              >
                <v-icon size="20">mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
    
    <v-card class="mt-1 pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("documentTemplates.index") }}:</span>
        <v-divider class="mt-1 mb-4" style="border-color: #dce5ef"></v-divider>
        <v-card-text>
          <v-form
            @keyup.native.enter="save"
            ref="dialogForm"
            class="ma-0"
          >
            <v-row class="ma-0 pa-0 dialog-form">
              <v-col cols="6" md="6" class="mb-2 pa-0">
                <v-autocomplete
                  :label="$t('department.index')"
                  clearable
                  v-model="form.department_id"
                  :items="department"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="mb-2 pa-0 pl-5">
                <v-autocomplete
                  clearable
                  :label="$t('documentTypes.index')"
                  v-model="form.document_type_id"
                  :items="documentType"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="mb-2 pa-0">
                <v-autocomplete
                  clearable
                  :label="$t('signerGroup.signer_group_id')"
                  v-model="form.signer_group_id"
                  :items="signerGroup"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="mb-2 pa-0 pl-5">
                <v-text-field
                :label="$t('name_uz_latin')"
                  v-model="form.name_uz_latin"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="mb-2 pa-0">
                <v-text-field
                :label="$t('name_uz_cyril')"
                  v-model="form.name_uz_cyril"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="mb-2 pa-0 pl-5">
                <v-text-field
                :label="$t('name_ru')"
                  v-model="form.name_ru"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="mb-2 pa-0">
                <v-text-field
                :label="$t('description_uz_latin')"
                  v-model="form.decription_uz_latin"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="mb-2 pa-0 pl-5">
                <v-text-field
                :label="$t('description_uz_cyril')"
                  v-model="form.decription_uz_cyril"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="mb-2 pa-0">
                <v-text-field
                :label="$t('description_ru')"
                  v-model="form.decription_ru"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="save"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            <!-- @click="addSigners" -->

            {{ $t("save") }}
          </v-btn>
          <v-btn
            class
            color="#EB4034"
            right
            small
            dark
            elevation="0"
            @click="dialog = false"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="StaffDialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ "" }}</span>
          <v-spacer></v-spacer>

          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="StaffDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="StaffSave" ref="staffDialogform">
            <v-row>
              <v-col cols="6">
                <label for>{{
                  $t("documentTemplates.attribute_name_uz_latin")
                }}</label>
                <v-text-field
                  v-model="StaffForm.attribute_name_uz_latin"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{
                  $t("documentTemplates.attribute_name_uz_cyril")
                }}</label>
                <v-text-field
                  v-model="StaffForm.attribute_name_uz_cyril"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{
                  $t("documentTemplates.attribute_name_ru")
                }}</label>
                <v-text-field
                  v-model="StaffForm.attribute_name_ru"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{
                  $t("documentTemplates.value_min_lenght")
                }}</label>
                <v-text-field
                  v-model="StaffForm.value_min_lenght"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{
                  $t("documentTemplates.value_max_lenght")
                }}</label>
                <v-text-field
                  v-model="StaffForm.value_max_lenght"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{ $t("documentTemplates.description") }}</label>
                <v-text-field
                  v-model="StaffForm.description"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("documentTemplates.dataType") }}</label>
                <v-autocomplete
                  clearable
                  v-model="StaffForm.data_type_id"
                  :items="dataTypes"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveStaff(item)">{{
            $t("save")
          }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
            @click="save"
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data() {
    return {
      page: 1,
      from: 1,
      StaffDialog: false,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      dataTableValue: [],
      loading: false,
      downloadExcel: false,
      punkt_excel: [],
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      regions: [],
      form: {},
      dialogHeaderText: "",
      createdAtMenu2: false,
      department: [],
      documentType: [],
      signerGroup: [],
      dataTypes: [],
      StaffForm: {},
      today: moment().format("YYYY-MM-DD"),
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        {
          text: "#",
          value: "id",
          align: "center",
          class: "blue-grey lighten-5",
          width: 30,
        },
        {
          text: this.$t("name_" + this.$i18n.locale),
          class: "blue-grey lighten-5",
          value: "template_name",
        },
        {
          text: this.$t("department_id"),
          class: "blue-grey lighten-5",
          value: "department_id",
        },
        {
          text: this.$t("documentTypes.index"),
          class: "blue-grey lighten-5",
          value: "document_type_id",
        },
        // { text: this.$t("name_uz_cyril"), value: "name_uz_cyril" },
        // { text: this.$t("name_ru"), value: "name_ru" },
        {
          text: this.$t("actions"),
          class: "blue-grey lighten-5",
          value: "actions",
          width: "70",
          align: "center",
        },
      ];
    },
  },
  methods: {
    saveStaff() {
      if (this.$refs.staffDialogform.validate())
        axios
          .post(
            this.$store.state.backend_url +
              "api/document-templates/updateDocumentDetailtAttribute",
            this.StaffForm
          )
          .then((res) => {
            this.getList();
            this.StaffDialog = false;
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
          .catch((err) => {
            console.log(err);
          });
    },
    editStaff(item) {
      this.dialogHeaderText = this.$t("edit staff");
      this.StaffForm = Object.assign({}, item);
      this.StaffDialog = true;
      this.editMode = true;
      if (this.$refs.staffDialogform)
        this.$refs.staffDialogform.resetValidation();
    },
    deleteStaff(item) {
      const index = this.items.indexOf(item);
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
          axios
            .delete(
              this.$store.state.backend_url +
                "api/document-templates/destroyDetailAttribute/" +
                item.id
            )
            .then((res) => {
              this.getList(this.page, this.itemsPerPage);
              this.StaffDialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
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
      });
    },
    newAttribute(item) {
      this.StaffForm = {
        id: Date.now(),
        document_template_id: item.id,
        attribute_name_uz_latin: "",
        attribute_name_uz_cyril: "",
        attribute_name_ru: "",
        value_min_lenght: "",
        value_max_lenght: "",
        description: "",
        data_type_id: "",
      };
      this.StaffDialog = true;
      this.editMode = false;
      if (this.$refs.staffDialogform) this.$refs.staffDialogform.reset();
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/document-templates", {
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          this.items = response.data.data;
          this.from = response.data.from;
          this.server_items_length = response.data.total;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getRef() {
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-ref",
          {
            pagination: this.dataTableOptions,
            language: this.$i18n.locale,
          }
        )
        .then((response) => {
          this.department = response.data.department;
          this.signerGroup = response.data.signerGroups.map((v) => {
            return { text: v.text, value: v.id };
          });
          this.documentType = response.data.documentType;
          this.dataTypes = response.data.dataTypes;
          this.loading = false;
        })
        .catch((error) => {
          console.error(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("document-templates.newDistrict");
      this.form = {
        id: Date.now(),
        department_id: "",
        document_type_id: "",
        signer_group_id: "",
        name_uz_latin: "",
        name_uz_cyril: "",
        name_ru: "",
        decription_uz_latin: "",
        decription_uz_cyril: "",
        decription_ru: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("document-templates.newDistrict");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/document-templates/update",
            this.form
          )
          .then((res) => {
            this.getList();
            this.dialog = false;
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
              title: $t("create_update_operation"),
            });
          })
          .catch((err) => {
            console.log(err);
          });
    }, //document-types
    deleteItem(item) {
      const index = this.items.indexOf(item);
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
          axios
            .delete(
              this.$store.state.backend_url +
                "api/document-templates/delete/" +
                item.id
            )
            .then((res) => {
              this.getList(this.page, this.itemsPerPage);
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
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
      });
    },
    getDocsheetsExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/document-sheets-report", {
          locale: this.$i18n.locale,
          page: page,
          perPage: 1000,
        })
        .then((response) => {
          new_array = response.data;
          this.punkt_excel = this.punkt_excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getDocsheetsExcel(++page);
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
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > tbody > tr > td {
  color: #676768;
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
