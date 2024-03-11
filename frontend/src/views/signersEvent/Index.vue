<template>
  <div class="templateEllipsis">
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("purchase_catalogs.catalogs") }}</span>
        <v-spacer></v-spacer>

        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          class="mr-2"
          style="width: 100px !important"
          :placeholder="$t('search')"
          @keyup.native.enter="getList"
          outlined
          dense
          single-line
          hide-details
        ></v-text-field>

        <v-btn
          v-if="$store.getters.checkPermission('purchase-catalog-create')"
          color="#6ac82d"
          x-small
          dark
          fab
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
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
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100, -1],
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
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.part_number"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.name_eng"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.name_ru"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.specification"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.manufacturer"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.description"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.material_content"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.using_location"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.using_purpose"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="width: 50px">
              <v-text-field
                v-model="filterForm.measure"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
              ></v-text-field>
            </td>
          </tr>
        </template>
        <template v-slot:item="{ item, index }">
          <tr>
            <td>{{ index + 1 }}</td>
            <td>
              <v-btn
                v-if="$store.getters.checkPermission('purchase-catalog-update')"
                color="blue"
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
            <td>{{ item.code }}</td>
            <td>{{ item.part_number }}</td>
            <td style="max-width: 200px">{{ item.name_eng }}</td>
            <td style="max-width: 200px">{{ item.name_ru }}</td>
            <td style="max-width: 300px">{{ item.specification }}</td>
            <td style="max-width: 300px">{{ item.manufacturer }}</td>
            <td style="max-width: 300px">{{ item.description }}</td>
            <td style="max-width: 200px">{{ item.material_content }}</td>
            <td style="max-width: 100px">{{ item.using_location }}</td>
            <td style="max-width: 300px">{{ item.using_purpose }}</td>
            <td style="max-width: 300px; text-align: center">
              {{ item.measure.short_name }}
            </td>
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
      </v-data-table>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="4">
                <label for>{{ $t("purchase_catalogs.code") }}</label>
                <v-text-field
                  v-model="form.code"
                  :maxlength="20"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <label for>{{ $t("purchase_catalogs.part_number") }}</label>
                <v-text-field
                  v-model="form.part_number"
                  :maxlength="10"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <label for>{{ $t("purchase_catalogs.measure") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.unit_measure_id"
                  :maxlength="10"
                  :items="measure_name"
                  item-text="short_name"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="12">
                <label for>{{ $t("purchase_catalogs.name_eng") }}</label>
                <v-text-field
                  v-model="form.name_eng"
                  :maxlength="255"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("purchase_catalogs.name_ru") }}</label>
                <v-text-field
                  v-model="form.name_ru"
                  :maxlength="255"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("purchase_catalogs.specification") }}</label>
                <v-text-field
                  v-model="form.specification"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>
                  {{ $t("purchase_catalogs.material_content") }}
                </label>
                <v-text-field
                  v-model="form.material_content"
                  :maxlength="100"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("purchase_catalogs.manufacturer") }}</label>
                <v-text-field
                  v-model="form.manufacturer"
                  :maxlength="255"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("purchase_catalogs.using_location") }}</label>
                <v-text-field
                  v-model="form.using_location"
                  :maxlength="255"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("purchase_catalogs.using_purpose") }}</label>
                <v-text-field
                  v-model="form.using_purpose"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("purchase_catalogs.description") }}</label>
                <v-text-field
                  v-model="form.description"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
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
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        // { text: this.$t("code"), value: "code", width: 30 },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
        },
        { text: this.$t("purchase_catalogs.code"), value: "code", width: 30 },
        {
          text: this.$t("purchase_catalogs.part_number"),
          value: "part_number",
          width: 30,
        },
        { text: this.$t("purchase_catalogs.name_eng"), value: "name_eng" },
        { text: this.$t("purchase_catalogs.name_ru"), value: "name_ru" },
        {
          text: this.$t("purchase_catalogs.specification"),
          value: "specification",
        },
        {
          text: this.$t("purchase_catalogs.manufacturer"),
          value: "manufacturer",
        },
        {
          text: this.$t("purchase_catalogs.description"),
          value: "description",
        },
        {
          text: this.$t("purchase_catalogs.material_content"),
          value: "material_content",
        },
        {
          text: this.$t("purchase_catalogs.using_location"),
          value: "using_location",
          style: "white-space: normal;",
        },
        {
          text: this.$t("purchase_catalogs.using_purpose"),
          value: "using_purpose",
        },
        {
          text: this.$t("purchase_catalogs.measure"),
          value: "measure.short_name",
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
          search: this.search,
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
      if (this.$store.getters.checkPermission("purchase-catalog-update")) {
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
  },
  mounted() {
    this.getList();
  },
};
</script>
<style scoped>
.templateEllipsis table > thead > tr > th {
  white-space: normal;
  display: block;
  display: -webkit-box;
  max-width: 50px;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
