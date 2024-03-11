<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("partners.index") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
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
                  @click="newItem"
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
                  <v-list-item-title
                    @click="
                      getDocsheetsExcel(1);
                      punkt_excel = [];
                    "
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

          <!-- <v-btn
          v-if="$store.getters.checkPermission('partners-create')"
          color="#6ac82d" class="btn_class"  dark
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
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
            :search="search"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100],
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
            <template v-slot:item="{ item, index }">
              <tr>
                <td style="text-align: center">{{ index + from }}</td>
                <td>{{ item.name }}</td>
                <td style="max-width: 200px">{{ item.adress }}</td>
                <td style="max-width: 200px">{{ item.bank_name }}</td>
                <td style="max-width: 300px">{{ item.bank_adress }}</td>
                <td style="max-width: 300px">{{ item.account }}</td>
                <td style="max-width: 300px">{{ item.swift_code }}</td>
                <td style="max-width: 300px">{{ item.inn }}</td>
                <td style="max-width: 50px">
                  <v-btn
                    v-if="
                      $store.getters.checkPermission('partners-update') ||
                      item.created_by == user.id ||
                      item.updated_by == user.id
                    "
                    class="px-0"
                    color="#3FCB5D"
                    style="min-width: 25px"
                    small
                    text
                    @click="editItem(item)"
                  >
                    <v-icon size="20">mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="$store.getters.checkPermission('partners-delete')"
                    class="pl-0 pr-2"
                    color="error"
                    style="min-width: 25px"
                    small
                    text
                    @click="deleteItem(item)"
                  >
                    <v-icon size="20">mdi-trash-can-outline</v-icon>
                  </v-btn>
                </td>
              </tr>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600px"
    >
      <v-card class="mt-1 pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ dialogHeaderText }}:</span>
        <v-divider class="mt-1 mb-4" style="border-color: #dce5ef"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm" class="ma-0">
            <v-row class="ma-0 pa-0 dialog-form">
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.name"
                  :label="$t('partners.name')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.adress"
                  :label="$t('partners.adress')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.bank_name"
                  :label="$t('partners.bank_name')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <!-- <v-col cols="12">
                <label for>bank_name</label>
                <v-autocomplete
                  clearable
                  v-model="form.bank_name"
                  :items="measure_name"
                  item-text="shortName"
                  item-value="id"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>-->
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.bank_adress"
                  :label="$t('partners.bank_adress')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.account"
                  :label="$t('partners.account')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.swift_code"
                  :label="$t('partners.swift_code')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.inn"
                  :label="$t('partners.inn')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.mfo"
                  :label="$t('partners.mfo')"
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
    <!-- downloading excel dialog qismi boshlandi -->
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="400">
      <v-card class="pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("partners.index") }}</span>
        <v-divider class="mt-1 mb-4" style="border-color: #dce5ef"></v-divider>
        <v-card-text class="py-1 px-3">
          <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="punkt_excel"
              :name="'Imzo_qoyuvchilar_guruhi' + today + '.xls'"
            >
              <v-btn
                color="#3FCB5D"
                right
                small
                dark
                @click="save"
                elevation="0"
                style="text-transform: none; border-radius: 5px"
              >
                {{ $t("download") }}
              </v-btn>
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
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <!-- downloading excel dialog qismi tugadi -->
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data() {
    return {
      selectObjectType: "",
      objectTypesList: [],
      fileDialog: false,
      pdfViewDialog: false,
      fileForView: { id: 0 },
      selectFiles: [],
      loading: false,
      search: "",
      page: 1,
      from: 0,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      dialog: false,
      editMode: null,
      items: [],
      companies: [],
      measure_name: [],
      form: {},
      dialogHeaderText: "",
      formData: null,
      punkt_excel: [],
      downloadExcel: false,
      today: moment().format("YYYY-MM-DD"),
      table_menu: null,
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
          class: "blue-grey lighten-5",
          align: "center",
          width: 50,
        },
        {
          text: this.$t("partners.name"),
          class: "blue-grey lighten-5",
          value: "name",
        },
        {
          text: this.$t("partners.adress"),
          class: "blue-grey lighten-5",
          value: "adress",
        },
        {
          text: this.$t("partners.bank_name"),
          class: "blue-grey lighten-5",
          value: "bank_name",
        },
        {
          text: this.$t("partners.bank_adress"),
          class: "blue-grey lighten-5",
          value: "bank_adress",
        },
        {
          text: this.$t("partners.account"),
          class: "blue-grey lighten-5",
          value: "account",
        },
        {
          text: this.$t("partners.swift_code"),
          class: "blue-grey lighten-5",
          value: "swift_code",
        },
        {
          text: this.$t("partners.inn"),
          class: "blue-grey lighten-5",
          value: "inn",
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          class: "blue-grey lighten-5",
          align: "center",
        },
      ];
    },
    getFormDataValues() {
      return this.staffTmp.files;
    },
    user() {
      return this.$store.getters.getUser();
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
      axios
        .post(this.$store.state.backend_url + "api/partners", {
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          this.items = response.data.data;

          this.from = response.data.from;
          this.server_items_length = response.data.total;
          // this.measure_name = response.data.data.map(v => {
          //   return { shortName: v.measure.short_name, id: v.measure.id };
          // });
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      if (this.$store.getters.checkPermission("partners-create")) {
        this.dialogHeaderText = this.$t("partners.add_partners");
        this.form = {
          id: Date.now(),
          name: "",
          adress: "",
          bank_name: "",
          bank_adress: "",
          account: "",
          swift_code: "",
          inn: "",
          mfo: "",
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (
        this.$store.getters.checkPermission("partners-update") ||
        item.created_by == this.user.id ||
        item.updated_by == this.user.id
      ) {
        this.dialogHeaderText = this.$t("partners.edit");
        this.formIndex = this.items.indexOf(item);
        this.form = Object.assign({}, item);
        this.dialog = true;
        this.editMode = true;
        if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      }
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/partners/update",
            this.form
          )
          .then((res) => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              requirement: "top-end",
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
    }, //document-types
    deleteItem(item) {
      if (this.$store.getters.checkPermission("partners-delete")) {
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
                this.$store.state.backend_url + "api/partners/delete/" + item.id
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
      }
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
