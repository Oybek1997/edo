<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("signerGroup.signer_group_id") }}</span>
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
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
            <v-icon color="#00B950" left>mdi-filter-outline</v-icon>Фильтр
          </v-btn>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
              Столбцы <v-icon color="#00B950" right>mdi-checkbox-marked-outline</v-icon>
          </v-btn>
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
                  v-if="$store.getters.checkPermission('signer_group-create')"
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
                    getUserExcel(1);
                    user_excel = [];
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
            :options.sync="dataTableOptions"
            @update:page="updatePage"
            :disable-pagination="true"
            disable-sort
            :footer-props="{
              itemsPerPageOptions: [50, 100, 200],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
            @update:expanded="toggleExpand"
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
            <template v-slot:item.staff_id="{ item }">
              {{
                item.staff.employees[0]['lastname_' + $i18n.locale] + " " + item.staff.employees[0]['firstname_' + $i18n.locale]+ " " + item.staff.position['name_' + $i18n.locale]
              }}
            </template>
            <template v-slot:item.document_template_id="{ item }">
              {{
                item.document_template ? item.document_template['name_' + $i18n.locale] : ''
              }}
            </template>
            <template v-slot:item.document_type_id="{ item }">
              {{
                item.document_type ? item.document_type['name_' + $i18n.locale] : ''
              }}
            </template>
            <template v-slot:item.actions="{ item }">
              <!-- <v-btn
                v-if="$store.getters.checkPermission('signer_group-update')"
                class="px-1"
                color="blue"
                style="min-width: 25px"
                small
                text
                @click="editItem(item)"
              >
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn> -->
              <v-btn
                v-if="$store.getters.checkPermission('signer_group-delete')"
                class="px-1"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)"
              >
                <v-icon size="18">mdi-trash-can-outline</v-icon>
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
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("name_uz_latin") }}</label>
                <v-text-field
                  v-model="form.name_uz_latin"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("name_uz_cyril") }}</label>
                <v-text-field
                  v-model="form.name_uz_cyril"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("name_ru") }}</label>
                <v-text-field
                  v-model="form.name_ru"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
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
            class=""
            color="red"
            right
            small
            dark
            @click="dialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="StaffDialog" persistent max-width="800px">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ SignerStaffDetaildialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="StaffSave" ref="dialogform">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.index") }}</label>
                <v-autocomplete
                  clearable
                  v-model="StaffForm.staff_id"
                  :items="staffList"
                  item-text="staffInfo"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                  full-width
                  class="my-1"
                >
                  <template v-slot:selection="{ item }">
                    <v-row class="mx-0 dialogForm" style="font-size: 12px">
                      <v-col cols="12" class="ma-0 pa-0">
                        <b>{{
                          item.department
                            ? item.department.code + " " + item.department.text
                            : ""
                        }}</b>
                      </v-col>
                      <v-col cols="12" class="ma-0 pa-0">
                        <b>{{ item.position ? item.position.text : "" }}</b>
                      </v-col>
                    </v-row>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-row
                      style="
                        border-bottom: 1px solid #ccc;
                        font-size: 14px;
                        max-width: 700px;
                      "
                      class="mx-0 dialogForm"
                    >
                      <v-col cols="12" class="ma-0 pa-0">
                        <b>{{
                          item.department
                            ? item.department.code + " " + item.department.text
                            : ""
                        }}</b>
                      </v-col>
                      <v-col cols="12" class="ma-0 pa-0">
                        <b>{{ item.position ? item.position.text : "" }}</b>
                      </v-col>
                    </v-row>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3" v-if="StaffForm.document_type_id==0">
                <label class="labelTitle" for>{{ $t("Hujjat shabloni") }}</label>
                <v-autocomplete
                  v-model="StaffForm.document_template_id"
                  clearable
                  :rules="[(v) => !!v || $t('input.required')]"
                  :items="document_templates"
                  item-text="text"
                  item-value="id"
                  hide-details="auto"
                  multiple
                  dense
                  chips
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3" v-if="StaffForm.document_template_id==0">
                <label class="labelTitle" for>{{ $t("Hujjat turi") }}</label>
                <v-autocomplete
                  v-model="StaffForm.document_type_id"
                  clearable
                  :rules="[(v) => !!v || $t('input.required')]"
                  :items="document_types"
                  item-text="name_uz_latin"
                  item-value="id"
                  hide-details="auto"
                  multiple
                  dense
                  chips
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveStaff"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="StaffDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
import Swal from "sweetalert2";

export default {
  data: () => ({
    dateRules: [
      (v) =>
        !v ||
        /^\d{4}[\-\-](0?[1-9]|1[012])[\-\-](0?[1-9]|[12][0-9]|3[01])$/.test(
          v
        ) ||
        "Date must be valid(YYYY-DD-MM)",
    ],
    expanded: [],
    loading: false,
    dataTableValue: [],
    search: "",
    dialog: false,
    signerStaffDetailDialog: false,
    editMode: null,
    staffList: [],
    items: [],
    details: [],
    form: {},
    dialogHeaderText: "",
    SignerStaffDetaildialogHeaderText: "",
    StaffForm: {},
    StaffDialog: false,
    actionTypes: [],
    document_templates: [],
    document_types: [],
    signerGroups: [],
    page: 1,
    from: 1,
    server_items_length: -1,
    dataTableOptions: { page: 1, itemsPerPage: 50 },
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "", value: "data-table-expand", width: 30 },
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("Shtat"),
          value: "staff_id",
        },
        {
          text: this.$t("Hujjat shabloni"),
          value: "document_template_id",
        },
        {
          text: this.$t("Hujjat turi"),
          value: "document_type_id",
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 30,
          align: "center",
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("signer_group-update") ||
          this.$store.getters.checkPermission("signer_group-delete")
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
    saveStaff() {
        axios
          .post(
            this.$store.state.backend_url + "api/notsigner/update",
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

    toggleExpand($event) {},
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/notsigner", {
          pagination: this.dataTableOptions,
          search: this.search,
          language: this.$i18n.locale,
        })
        .then((response) => {
          this.items = response.data.data;
          console.log(this.items);
          this.from = response.data.from;
          this.server_items_length = response.data.total;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    documentTemplates(){
      axios
        .post(
          this.$store.state.backend_url +
            "api/document-templates/get-list",{
              language: this.$i18n.locale
            }
        )
        .then((response) => {
          this.document_templates = response.data;

          this.loading = false;
        })
        .catch((error) => {
          console.error(error);
          this.loading = false;
        });
    },
    documentTypes(){
      axios
        .get(
          this.$store.state.backend_url +
            "api/document-types"
        )
        .then((response) => {
          this.document_types = response.data;

          this.loading = false;
        })
        .catch((error) => {
          console.error(error);
          this.loading = false;
        });
    },
    getRef() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/signers-groups/get-ref/" +
            this.$i18n.locale
        )
        .then((response) => {
          this.staffList = response.data.staffs.map((v) => {
            v.staffInfo = "";
            if (v.department) {
              v.staffInfo += v.department.code;
              v.staffInfo += " ";
              v.staffInfo += v.department.text;
            }
            if (v.range) v.staffInfo += v.range.code;
            v.staffInfo += " ";
            if (v.position) v.staffInfo += v.position.text;
            return v;
          });

          this.loading = false;
        })
        .catch((error) => {
          console.error(error);
          this.loading = false;
        });
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
                "api/signers_group_detail/delete/" +
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
                "api/notsigner/delete/" +
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
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/signers-groups/update",
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
              title: this.$t("create_update_operation"),
            });
          })
          .catch((err) => {
            console.log(err);
          });
    },
    updatePage($event) {
      this.getList();
    },
    newItem() {
      if (this.$store.getters.checkPermission("signer_group-create")) {
        this.dialogHeaderText = this.$t("signerGroup.new_signer_group");
        this.StaffForm = {
          id: Date.now(),
          staff_id: "",
          document_template_id: [],
          document_type_id: [],
        };
        this.StaffDialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("signer_group-update")) {
        this.dialogHeaderText = this.$t("signerGroup.edit_signer_group");
        this.form = Object.assign({}, item);
        this.dialog = true;
        this.editMode = true;
        if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      }
    },

    editStaff(item) {
      this.dialogHeaderText = this.$t("edit staff");
      this.StaffForm = Object.assign({}, item);
      this.StaffDialog = true;
      this.editMode = true;
      if (this.$refs.staffDialogform)
        this.$refs.staffDialogform.resetValidation();
    },
  },

  mounted() {
    this.getList();
    this.getRef();
    this.documentTemplates();
    this.documentTypes();
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
.dialogTitle {
  color: #000;
  font-size: 16px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.labelTitle {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
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
</style>