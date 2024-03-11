<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("message.requisites") }}</span>
        <v-spacer></v-spacer>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            class="txt_search1"
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            style="width: 100px !important"
            :placeholder="$t('searchInText')"
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
                      getRequisitesExcel(1);
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
        class="btn_class" 
          v-if="$store.getters.checkPermission('requisite-create')"
          color="#6ac82d"
          dark
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
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            item-key="id"
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
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
          >
            <template v-slot:item="{ item, index }">
              <tr>
                <td>{{ index + from }}</td>
                <td style="max-width: 300px">
                  {{ item["name_" + $i18n.locale] }}
                </td>
                <td style="max-width: 300px">
                  {{ item["address_" + $i18n.locale] }}
                </td>
                <td style="max-width: 100px">{{ item.inn }}</td>
                <td style="max-width: 150px">{{ item.account }}</td>
                <td style="max-width: 100px">{{ item.swift }}</td>
                <td style="max-width: 50px">{{ item.oknh }}</td>
                <td style="max-width: 50px">{{ item.mfo }}</td>
                <td style="max-width: 50px">
                  <v-btn
                    v-if="$store.getters.checkPermission('requisite-update') && item.created_by == user.id"
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
                    v-if="$store.getters.checkPermission('requisite-delete') && item.created_by == user.id"
                    class="pl-0 pr-2"
                    color="error"
                    style="min-width: 25px"
                    small
                    text
                    @click="deleteItem(item.id)"
                  >
                    <v-icon size="20">mdi-trash-can-outline</v-icon>
                  </v-btn>
                </td>
              </tr>
            </template>
            <!-- <template v-slot:item.id="{ item }">{{
              items.indexOf(item) + from
            }}</template>
            <template v-slot:item.name="{ item }">
              <span>
                {{ item["name_" + $i18n.locale] }}
              </span>
            </template>
            <template v-slot:item.address="{ item }">
              <span>
                {{ item["address_" + $i18n.locale] }}
              </span>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                v-if="$store.getters.checkPermission('requisite-update')"
                class="px-0"                
                color="#3FCB5D"
                style="min-width: 25px;"
                small
                text
                @click="editItem(item)"
              >
                <v-icon size="20">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                v-if="$store.getters.checkPermission('requisite-delete')"
                class="pl-0 pr-2"
                color="error"
                style="min-width: 25px;"
                small
                text
                @click="deleteItem(item.id)"
              >
                <v-icon size="20">mdi-trash-can-outline</v-icon>
              </v-btn>
            </template> -->
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
            <v-row class="dialog-form">
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  :label="$t('company_requisites.name_uz_latin')"
                  v-model="form.name_uz_latin"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.name_uz_cyril"
                  :label="$t('company_requisites.name_uz_cyril')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.name_ru"
                  :label="$t('company_requisites.name_ru')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.address_uz_latin"
                  :label="$t('company_requisites.address_name_uz_latin')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.address_uz_cyril"
                  :label="$t('company_requisites.address_name_uz_cyril')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.address_ru"
                  :label="$t('company_requisites.address_name_ru')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.inn"
                  :label="$t('company_requisites.inn')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.account"
                  :label="$t('company_requisites.account')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.swift"
                  :label="$t('company_requisites.swift')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.oknh"
                  :label="$t('company_requisites.oknh')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-0 pa-2">
                <v-text-field
                  v-model="form.mfo"
                  :label="$t('company_requisites.mfo')"
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
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="280"  @keydown.esc="downloadExcel = false">
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
              :name="'Rekvizitlar_ruyxati_' + today + '.xls'"
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
            {{ $t("Отменить") }} <v-icon right>mdi-close-box-outline</v-icon>
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
      loading: false,
      search: "",
      from: 1,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      dialog: false,
      editMode: null,
      items: [],
      form: {},
      table_menu: null,
      dialogHeaderText: "",
      punkt_excel: [],
      downloadExcel: false,
      today: moment().format("YYYY-MM-DD"),
    };
  },
  computed: {
    user() {
      return this.$store.getters.getUser();
    },
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
          text: this.$t("company_requisites.name"),
          class: "blue-grey lighten-5",
          value: "name",
        },
        {
          text: this.$t("company_requisites.address"),
          class: "blue-grey lighten-5",
          value: "address",
        },
        {
          text: this.$t("company_requisites.inn"),
          class: "blue-grey lighten-5",
          value: "inn",
        },
        {
          text: this.$t("company_requisites.account"),
          class: "blue-grey lighten-5",
          value: "account",
        },
        {
          text: this.$t("company_requisites.swift"),
          class: "blue-grey lighten-5",
          value: "swift",
        },
        {
          text: this.$t("company_requisites.oknh"),
          class: "blue-grey lighten-5",
          value: "oknh",
        },
        {
          text: this.$t("company_requisites.mfo"),
          class: "blue-grey lighten-5",
          value: "mfo",
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
        .post(this.$store.state.backend_url + "api/company-requisites", {
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          this.items = response.data.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      if (this.$store.getters.checkPermission("requisite-create")) {
        this.dialogHeaderText = this.$t("company_requisites.add_requisites");
        this.form = {
          id: Date.now(),
          name_uz_latin: "",
          name_uz_cyril: "",
          name_ru: "",
          address_uz_latin: "",
          address_uz_cyril: "",
          address_ru: "",
          inn: "",
          account: "",
          swift: "",
          oknh: "",
          mfo: "",
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("requisite-update")) {
        this.dialogHeaderText = this.$t("company_requisites.edit");
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
            this.$store.state.backend_url + "api/company-requisites/update",
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
    },
    deleteItem(id) {
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
                "api/company-requisites/delete/" +
                id
            )
            .then((res) => {
              this.items = this.items.filter((v) => v.id != id);
              this.getList(this.page, this.itemsPerPage);
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
              });
              console.log(err);
            });
        }
      });
    },
    getRequisitesExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/requisites-report", {
          locale: this.$i18n.locale,
          page: page,
          perPage: 1000,
        })
        .then((response) => {
          new_array = response.data;
          this.punkt_excel = this.punkt_excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getRequisitesExcel(++page);
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
