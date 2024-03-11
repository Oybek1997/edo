<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{
          $t("signerGroup.signer_group_id")
        }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
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
                  v-if="$store.getters.checkPermission('signer_group-create')"
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="newItem"
                >
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>Добавить новую строку
                  </v-list-item-title>
                </v-list-item>
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
            show-expand
            :expanded="expanded"
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
            <template v-slot:item.name_uz_latin="{ item }">
              <td style="max-width: 300px">
                {{ item.name_uz_latin }}
              </td>
            </template>
            <template v-slot:item.name_uz_cyril="{ item }">
              <td style="max-width: 300px">
                {{ item.name_uz_cyril }}
              </td>
            </template>
            <template v-slot:item.name_ru="{ item }">
              <td style="max-width: 300px">
                {{ item.name_ru }}
              </td>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                v-if="$store.getters.checkPermission('signer_group-update')"
                color="#3FCB5D"
                class="px-0"
                style="min-width: 25px"
                small
                text
                @click="editItem(item)"
              >
                <v-icon size="20">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                v-if="$store.getters.checkPermission('signer_group-delete')"
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

            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-3">
                <v-card class="my-2">
                  <div>
                    <v-system-bar window color="#eee">
                      <b>{{ $t("signerGroup.staff_list") }}</b>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newStaff(item)"
                        >mdi-plus</v-icon
                      >
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-1">
                    <table
                      class="doc-template_data-table ma-0 pa-0"
                      style="width: 100%"
                    >
                      <tr>
                        <th style="text-align: left">#</th>
                        <th style="text-align: left">
                          {{ $t("staff.staff") }}
                        </th>
                        <th style="text-align: left">
                          {{ $t("department.name") }}
                        </th>
                        <th style="text-align: left">
                          {{ $t("actionTypes.index") }}
                        </th>
                        <th style="width: 150px">
                          {{ $t("actionTypes.sequence") }}
                        </th>
                        <th style="max-width: 100px !important">
                          {{ $t("documentTemplates.due_day_count") }}
                        </th>
                        <th style="max-width: 100px !important">
                          {{ $t("documentTemplates.sign_type") }}
                        </th>
                        <th style="max-width: 100px !important">
                          {{ $t("documentTemplates.registry_type") }}
                        </th>
                        <th style="width: 100px">{{ $t("actions") }}</th>
                      </tr>
                      <tr
                        v-for="(itm, idx) in item.signer_group_details
                          .slice()
                          .sort((a, b) => (a.sequence < b.sequence ? -1 : 1))"
                        :key="idx"
                      >
                        <td>{{ idx + 1 }}</td>
                        <td>
                          {{
                            itm.staff && itm.staff.position
                              ? itm.staff.position["name_" + $i18n.locale]
                              : ""
                          }}
                        </td>
                        <td>
                          {{
                            itm.staff &&
                            itm.staff.department &&
                            itm.staff.department["name_" + $i18n.locale]
                          }}
                        </td>
                        <td>{{ itm.action_type["name_" + $i18n.locale] }}</td>
                        <td style="text-align: center">{{ itm.sequence }}</td>
                        <td style="text-align: center">
                          {{ itm.due_day_count }}
                        </td>
                        <td style="text-align: center">
                          {{ itm.sign_type ? "e-imzo" : "AD" }}
                        </td>
                        <td style="text-align: center">
                          {{ itm.is_registry ? "Registrli" : "Registrsiz" }}
                        </td>
                        <td class style="max-width: 40px">
                          <v-btn
                            class="px-0"
                            color="#3FCB5D"
                            style="min-width: 25px"
                            x-small
                            text
                            @click="editStaff(itm)"
                          >
                            <v-icon size="18">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn
                            class="pl-0 pr-2"
                            color="error"
                            style="min-width: 25px"
                            small
                            text
                            @click="deleteStaff(itm)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>
              </td>
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
        <span class="dialog-head_title">{{ dialogHeaderText }}:</span>
        <v-divider class="mt-1 mb-4" style="border-color: #dce5ef"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm" class="ma-0">
            <v-row class="ma-0 pa-0 dialog-form">
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.name_uz_latin"
                  :label="$t('name_uz_latin')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.name_uz_cyril"
                  :label="$t('name_uz_cyril')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.name_ru"
                  :label="$t('name_ru')"
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

    <v-dialog v-model="StaffDialog" persistent max-width="700px">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("staff.staff") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="StaffSave" ref="staffDialogform">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="px-1 py-0 mb-3">
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
                    <v-row class="ma-0 pa-0" style="font-size: 12px">
                      <v-col cols="12" class="ma-0 pa-0">
                        <b>
                          {{
                            item.department
                              ? item.department.code +
                                " " +
                                item.department.text
                              : ""
                          }}
                        </b>
                      </v-col>
                      <v-col cols="12" class="ma-0 pa-0">
                        <b>{{ item.position ? item.position.text : "" }}</b>
                      </v-col>
                      <!-- <v-col cols="12" class="ma-0 pa-0">
                        {{$t('employee.range')}}:
                        {{item.range ? item.range.code : ''}} /
                        {{$t('staff.rate_count')}}:
                        {{item.rate_count}}
                      </v-col>-->
                    </v-row>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-row
                      style="
                        border-bottom: 1px solid #ccc;
                        font-size: 14px;
                        max-width: 700px;
                      "
                      class="ma-0 pa-0"
                    >
                      <v-col cols="12" class="ma-0 pa-0">
                        <b>
                          {{
                            item.department
                              ? item.department.code +
                                " " +
                                item.department.text
                              : ""
                          }}
                        </b>
                      </v-col>
                      <v-col cols="12" class="ma-0 pa-0">
                        <b>{{ item.position ? item.position.text : "" }}</b>
                      </v-col>
                      <!-- <v-col cols="12" class="ma-0 pa-0">
                        {{$t('employee.range')}}:
                        {{item.range ? item.range.code : ''}} /
                        {{$t('staff.rate_count')}}:
                        {{item.rate_count}}
                      </v-col>-->
                    </v-row>
                  </template>
                </v-autocomplete>
              </v-col>

              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("actionTypes.index") }}</label>
                <v-autocomplete
                  clearable
                  v-model="StaffForm.action_type_id"
                  :items="actionTypes"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("actionTypes.sequence") }}</label>
                <v-text-field
                  v-model="StaffForm.sequence"
                  :rules="[(v) => !!v || $t('input.required')]"
                  type="number"
                  min="1"
                  max="20"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("documentTemplates.due_day_count") }}</label>
                <v-text-field
                  v-model="StaffForm.due_day_count"
                  :rules="[(v) => !!v || $t('input.required')]"
                  type="number"
                  min="1"
                  max="20"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("documentTemplates.sign_type") }}</label>
                <v-select
                  v-model="StaffForm.sign_type"
                  clearable
                  :items="[
                    { value: 1, text: 'e-imzo' },
                    { value: 0, text: 'AD' },
                  ]"
                  dense
                  outlined
                ></v-select>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("documentTemplates.registry_type") }}</label>
                <v-select
                  v-model="StaffForm.is_registry"
                  clearable
                  :items="[
                    { value: 1, text: 'Reisterli' },
                    { value: 0, text: 'Reistersiz' },
                  ]"
                  dense
                  outlined
                ></v-select>
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
    <!-- downloading excel dialog qismi boshlandi -->
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="400">
      <v-card class="pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("signerGroup.signer_group_id") }}</span>
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
    table_menu: null,
    items: [],
    details: [],
    form: {},
    dialogHeaderText: "",
    SignerStaffDetaildialogHeaderText: "",
    StaffForm: {},
    StaffDialog: false,
    actionTypes: [],
    signerGroups: [],
    page: 1,
    from: 1,
    server_items_length: -1,
    dataTableOptions: { page: 1, itemsPerPage: 50 },
    punkt_excel: [],
    downloadExcel: false,
    today: moment().format("YYYY-MM-DD"),
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        {
          text: "",
          value: "data-table-expand",
          class: "blue-grey lighten-5",
          width: 30,
        },
        {
          text: "#",
          value: "id",
          align: "center",
          class: "blue-grey lighten-5",
          width: 30,
        },
        {
          text: this.$t("name_uz_latin"),
          value: "name_uz_latin",
          class: "blue-grey lighten-5",
          width: 300,
        },
        {
          text: this.$t("name_uz_cyril"),
          value: "name_uz_cyril",
          class: "blue-grey lighten-5",
          width: 300,
        },
        {
          text: this.$t("name_ru"),
          value: "name_ru",
          class: "blue-grey lighten-5",
          width: 300,
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
          class: "blue-grey lighten-5",
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
      if (this.$refs.staffDialogform.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/signerGroupDetail/update",
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
        .post(this.$store.state.backend_url + "api/signers-groups", {
          pagination: this.dataTableOptions,
          search: this.search,
          language: this.$i18n.locale,
        })
        .then((response) => {
          this.items = response.data.signer_groups.data;
          this.from = response.data.signer_groups.from;
          this.server_items_length = response.data.signer_groups.total;
          this.actionTypes = response.data.action_types.map((v) => {
            return { text: v.name, value: v.id };
          });
        })
        .catch((error) => {
          console.log(error);
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
                "api/signers-groups/delete/" +
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
    saveStaff() {
      if (this.$refs.staffDialogform.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/signerGroupDetail/update",
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
    newStaff(item) {
      this.StaffForm = {
        id: Date.now(),
        signer_group_id: item.id,
        staff_id: "",
        action_type_id: "",
        sequence: "",
        due_day_count: "",
        is_registry: "",
        sign_type: "",
      };
      this.StaffDialog = true;
      this.editMode = false;
      if (this.$refs.staffDialogform) this.$refs.staffDialogform.reset();
    },

    newItem() {
      if (this.$store.getters.checkPermission("signer_group-create")) {
        this.dialogHeaderText = this.$t("signerGroup.new_signer_group");
        this.form = {
          id: Date.now(),
          name_uz_latin: "",
          name_uz_cyril: "",
          name_ru: "",
        };
        this.dialog = true;
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
    this.getRef();
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
