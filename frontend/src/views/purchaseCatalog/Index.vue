<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{
          $t("purchase_catalogs.catalogs")
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
            v-if="$store.getters.checkPermission('purchase-catalog-create')"
            v-btn
            color="#6ac82d"
            class="btn_class"
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
            class="doc-template_data-table purchase-catalog_data-table"
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
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, 200],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-minus',
              nextIcon: 'mdi-plus',
            }"
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
            @update:expanded="toggleExpand"
          >
            <template v-slot:body.prepend>
              <tr class="py-0 my-0 prepend_height">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.code"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.part_number"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.name_eng"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.name_ru"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.specification"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.manufacturer"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.description"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.material_content"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.using_location"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.using_purpose"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.measure"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
              </tr>
            </template>
            <template v-slot:item="{ item, index }">
              <tr>
                <td style="max-width: 30px">{{ index + from }}</td>
                <td style="max-width: 30px">
                  <v-btn
                    v-if="
                      $store.getters.checkRole(
                        'superadmin'
                      ) || $store.getters.getUser().id == item.created_by
                    "
                    class="px-0"
                    color="#3FCB5D"
                    style="min-width: 25px"
                    small
                    text
                    @click="editItem(item)"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <!-- <v-btn color="blue" small text @click="editItemFiles(item)">
                <v-icon>mdi-download</v-icon>
              </v-btn>-->
                  <!-- <v-btn
                v-if="$store.getters.checkPermission('purchase-catalog-delete')"
                color="red"
                small
                text
                @click="deleteItem(item)"
              >
                <v-icon>mdi-delete</v-icon>
              </v-btn>-->
                </td>
                <td style="max-width: 30px">{{ item.code }}</td>
                <td style="max-width: 50px">{{ item.part_number }}</td>
                <td style="max-width: 100px">
                  <span>{{ item.name_eng }}</span>
                </td>
                <td style="max-width: 100px">
                  <span>{{ item.name_ru }}</span>
                </td>
                <td style="max-width: 150px">
                  <span>{{ item.specification }}</span>
                </td>
                <td style="max-width: 100px">{{ item.manufacturer }}</td>
                <td style="max-width: 150px">
                  <span>{{ item.description }}</span>
                </td>
                <td style="max-width: 100px">{{ item.material_content }}</td>
                <td style="max-width: 100px">
                  <span>{{ item.using_location }}</span>
                </td>
                <td style="max-width: 100px">{{ item.using_purpose }}</td>
                <td style="max-width: 100px">{{ item.measure.short_name }}</td>
              </tr>
            </template>
            <!-- <template
          v-slot:item.id="{ item }"
        >{{items.map(function(x) {return x.id; }).indexOf(item.id) + 1}}</template>-->
            <!-- <template v-slot:item.requirement_type_id="{ item }">
          <span
            style="white-space:normal;max-width: 100px;"
          >{{ item.requirement_type['name_'+$i18n.locale] }}</span>
        </template>-->
            <!-- <template v-slot:item.actions="{ item }">
          <v-btn
            v-if="$store.getters.checkPermission('purchase-catalog-update')"
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="blue" small text @click="editItemFiles(item)">
            <v-icon>mdi-download</v-icon>
          </v-btn>
          <v-btn
            v-if="$store.getters.checkPermission('purchase-catalog-delete')"
            color="red"
            small
            text
            @click="deleteItem(item)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>-->
          </v-data-table></v-col
        ></v-row
      >
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
              <v-col cols="4" md="4" class="mb-2 pl-0">
                <v-text-field
                  v-model="form.code"
                  :maxlength="20"
                  :label="$t('purchase_catalogs.code')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4" md="4" class="mb-2 pl-2">
                <v-text-field
                  v-model="form.part_number"
                  :maxlength="10"
                  :label="$t('purchase_catalogs.part_number')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4" md="4" class="mb-2 pl-0">
                <v-autocomplete
                  clearable
                  v-model="form.unit_measure_id"
                  :maxlength="10"
                  :items="measure_name"
                  :label="$t('purchase_catalogs.measure')"
                  item-text="short_name"
                  item-value="id"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.name_eng"
                  :maxlength="255"
                  :label="$t('purchase_catalogs.name_eng')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.name_ru"
                  :maxlength="255"
                  :label="$t('purchase_catalogs.name_ru')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.specification"
                  :label="$t('purchase_catalogs.specification')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.material_content"
                  :label="$t('purchase_catalogs.material_content')"
                  :maxlength="100"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.manufacturer"
                  :maxlength="255"
                  :label="$t('purchase_catalogs.manufacturer')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.using_location"
                  :maxlength="255"
                  hide-details="auto"
                  :label="$t('purchase_catalogs.using_location')"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.using_purpose"
                  hide-details="auto"
                  :label="$t('purchase_catalogs.using_purpose')"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-text-field
                  v-model="form.description"
                  :label="$t('purchase_catalogs.description')"
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
    <v-dialog v-model="fileDialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">File upload</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="fileDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col
                cols="10"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("staff.uploadFiles") }}</label>
                <v-file-input
                  v-model="selectFiles"
                  multiple
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf"
                  small-chips
                  show-size
                  hide-details
                ></v-file-input>
              </v-col>
              <v-col cols="2" style="min-width: 100px" class="px-0 text-center">
                <v-btn
                  :disabled="selectFiles.length == 0"
                  class="mt-6"
                  color="success"
                  @click="addFiles"
                  >+</v-btn
                >
              </v-col>
            </v-row>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th width="20" class="text-left">#</th>
                    <th class="text-left">{{ $t("staff.file") }}</th>
                    <th width="20" class="text-left"></th>
                    <th width="20" class="text-left"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in getFormDataValues" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td style="max-width: 340px">{{ item.file_name }}</td>
                    <td>
                      <v-btn color="primary" text @click="viewPdfFile(item)">
                        <v-icon>mdi-download</v-icon>
                      </v-btn>
                    </td>
                    <td>
                      <v-icon color="error" @click="removeTmpFile(item.id)"
                        >mdi-minus-circle-outline</v-icon
                      >
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" width="800">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ fileForView.file_name }}</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="pdfViewDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text class>
          <pdf
            v-if="fileForView.id > 0"
            :src="
              $store.state.backend_url + 'staffs/get-file/' + fileForView.id
            "
          ></pdf>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="green"
            text
            :href="
              $store.state.backend_url + 'staffs/get-file/' + fileForView.id
            "
            >{{ $t("download") }}</v-btn
          >
          <v-btn
            color="primary"
            text
            @click="
              pdfViewDialog = false;
              fileForView.id = 0;
            "
            >{{ $t("close") }}</v-btn
          >
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
        <span class="dialog-head_title">{{
           $t("purchase_catalogs.catalogs")
        }}</span>
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
      table_menu: null,
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
      filterForm: {
        code: "",
        part_number: "",
        name_eng: "",
        name_ru: "",
        specification: "",
        manufacturer: "",
        description: "",
        material_content: "",
        using_location: "",
        using_purpose: "",
        measure_name: "",
      },
      dialogHeaderText: "",
      formData: null,
      punkt_excel: [],
      downloadExcel: false,
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
        // { text: this.$t("code"), value: "code", width: 30 },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 30,
          class: "blue-grey lighten-5",
          align: "center",
        },
        {
          text: this.$t("purchase_catalogs.code"),
          class: "blue-grey lighten-5",
          value: "code",
          width: 30,
        },
        {
          text: this.$t("purchase_catalogs.part_number"),
          value: "part_number",
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("purchase_catalogs.name_eng"),
          class: "blue-grey lighten-5",
          value: "name_eng",
        },
        {
          text: this.$t("purchase_catalogs.name_ru"),
          class: "blue-grey lighten-5",
          value: "name_ru",
        },
        {
          text: this.$t("purchase_catalogs.specification"),
          value: "specification",
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("purchase_catalogs.manufacturer"),
          value: "manufacturer",
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("purchase_catalogs.description"),
          value: "description",
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("purchase_catalogs.material_content"),
          value: "material_content",
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("purchase_catalogs.using_location"),
          value: "using_location",
          class: "blue-grey lighten-5",
          style: "white-space: normal;",
        },
        {
          text: this.$t("purchase_catalogs.using_purpose"),
          value: "using_purpose",
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("purchase_catalogs.measure"),
          value: "measure.short_name",
          class: "blue-grey lighten-5",
        },
      ];
    },
    getFormDataValues() {
      return this.staffTmp.files;
    },
  },
  methods: {
    toggleExpand($event) {},
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },

    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/purchase-catalogs", {
          pagination: this.dataTableOptions,
          filter: this.filterForm,
        })
        .then((response) => {
          this.items = response.data.data;
          this.from = response.data.from;
          this.server_items_length = response.data.total;
          this.measure_name = response.data.data.map((v) => {
            return { shortName: v.measure.short_name, id: v.measure.id };
          });
          this.loading = false;
          this.getMeasure();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getMeasure() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/unit-measures/list")
        .then((response) => {
          this.measure_name = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      if (this.$store.getters.checkPermission("purchase-catalog-create")) {
        this.dialogHeaderText = this.$t("purchase_catalogs.add_catalog");
        this.form = {
          id: Date.now(),
          code: "",
          // code: "",
          part_number: "",
          description: "",
          material_content: "",
          name_eng: "",
          name_ru: "",
          manufacturer: "",
          using_location: "",
          using_purpose: "",
          measure_name_id: "",
          unit_measure_id: "",
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (
        this.$store.getters.checkPermission("purchase-catalog-update") ||
        this.$store.getters.getUser().id == item.created_by
      ) {
        this.dialogHeaderText = this.$t("purchase_catalogs.edit_catalog");
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
            this.$store.state.backend_url + "api/purchase-catalogs/update",
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
      if (this.$store.getters.checkPermission("purchase-catalog-delete")) {
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
                  "api/purchase-catalogs/delete/" +
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
      }
    },
    viewPdfFile(item) {
      this.fileForView = item;
      this.pdfViewDialog = true;
      // $store.state.backend_url + 'staffs/get-file/'+item.id
    },
    editItemFiles(item) {
      // if (this.$store.getters.checkPermission("staff-update_requirements"))
      {
        this.formData = new FormData();
        this.staffTmp = item;
        this.fileDialog = true;
      }
    },
    addFiles() {
      this.formData = new FormData();
      this.selectFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
      });
      // this.formData.append("object_type_id", this.selectObjectType);
      axios
        .post(
          this.$store.state.backend_url +
            "api/staffs/update-files/" +
            this.staffTmp.id,
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((res) => {
          this.selectFiles = [];
          // this.selectObjectType = "";
          this.staffTmp.files = res.data.files;
        })
        .catch(function (e) {
          console.log("FAILURE!!");
        });
    },
    removeTmpFile(id) {
      axios
        .delete(this.$store.state.backend_url + "api/staffs/delete-file/" + id)
        .then((res) => {
          this.staffTmp.files = this.staffTmp.files.filter((v) => v.id != id);
        })
        .catch(function (e) {
          console.log("FAILURE!!");
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
.v-data-table {
  line-height: 15px !important;
  /* white-space: normal!important; */
}

.dataЕableRow {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: N; /* number of lines to show */
  line-height: X; /* fallback */
  max-height: X * N; /* fallback */
}
.dataЕableRow1 {
  /* line-height: 15px; */
  max-height: 30px;
  overflow: hidden;
  white-space: normal;
  text-overflow: ellipsis;
  max-width: 150px;
  margin: 0px 0px 0px 0px;
}
.dataЕableRow2 {
  max-width: 100%px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  white-space: normal;
}
.doc-template_data-table table > thead > tr > th,
.doc-template_data-table table > thead > tr > th span {
  white-space: normal !important;
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
