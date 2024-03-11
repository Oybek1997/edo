<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("edi.materials_index") }}</span>
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

        <!-- v-if="$store.getters.checkPermission('warehouse-create')" -->
        <v-btn color="#6ac82d" x-small dark fab @click="newItem">
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
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :footer-props="{
          itemsPerPageOptions: [10, 20, 50, 100, -1],
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
      <template v-slot:item.id="{ item }">{{
          items.indexOf(item) + from
        }}</template>
         <template v-slot:item.created_by="{ item }">{{
          item.created_by && item.created_by.employee ? item.created_by.employee.fio : ''
        }}</template>
          <template v-slot:item.updated_by="{ item }">{{
          item.updated_by && item.updated_by.employee ? item.updated_by.employee.fio : ''
        }}</template>
        <template v-slot:item.status="{ item }">
        <v-switch v-model="item.status" hide-details class="mt-0" readonly></v-switch>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn color="blue" x-small text @click="editItem(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="red" x-small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="1000px"
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
            <table style="width: 100%" class="mt-2">
              <tr>
                <td class="pa-1">
                  <label for>{{ $t("edi.materials_material_number") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.material_number"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
                <td
                  rowspan="8"
                  style="vertical-align: top; width: 300px"
                  class="pa-2"
                >
                  <label for>{{ $t("edi.materials_picture") }}</label>
                  <v-file-input
                    v-model="image"
                    :rules="[
                      (v) => {
                        let allowedExtensions = /(\.jpg)$/i;
                        let error = false;
                        (v) => {
                          if (!allowedExtensions.exec(image.name)) {
                            error = true;
                          }
                        };
                        return !error || $t('requiredformat');
                      },
                    ]"
                    outlined
                    dense
                    prepend-icon
                    append-icon="mdi-image-outline"
                    accept=".jpg, .png, application/jpg, application/png"
                    small-chips
                    show-size
                    hide-details="auto"
                  ></v-file-input>
                  <div style="border: 1px solid #cde" class="mt-2">
                    <v-img
                      :lazy-src="
                        $store.state.backend_url +
                        'edi/materials/get-img/' +
                        form.id
                      "
                      width="300"
                      contain
                      :src="
                        $store.state.backend_url +
                        'edi/materials/get-img/' +
                        form.id
                      "
                    ></v-img>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="pa-1">
                  <label for>{{ $t("edi.materials_material_type_id") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.material_type_id"
                    :items="material_types"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <td class="pa-1">
                  <label for>{{ $t("edi.materials_material_group_id") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.material_group_id"
                    :items="material_groups"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <td class="pa-1">
                  <label for>{{ $t("edi.materials_unit_measure_id") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.unit_measure_id"
                    :items="unit_measures"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <td class="pa-1">
                  <label for>{{ $t("edi.materials_description") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.description"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="pa-1">
                  <label for>{{ $t("edi.materials_active_from") }}</label>
                </td>
                <td class="pa-1">
                  <v-menu
                    v-model="menu1"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.active_from"
                        readonly
                        outlined
                        dense
                        hide-details
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.active_from"
                      @input="menu1 = false"
                    ></v-date-picker>
                  </v-menu>
                </td>
              </tr>
              <tr>
                <td class="pa-1">
                  <label for>{{ $t("edi.materials_active_to") }}</label>
                </td>
                <td class="pa-1">
                  <v-menu
                    v-model="menu2"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.active_to"
                        readonly
                        outlined
                        dense
                        hide-details
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.active_to"
                      @input="menu2 = false"
                    ></v-date-picker>
                  </v-menu>
                </td>
              </tr>
              <tr>
                <td class="pa-1">
                  <label for>{{ $t("edi.materials_status") }}</label>
                </td>
                <td class="pa-1">
                  <v-switch
                    class="mt-0"
                    v-model="form.status"
                    :label="!!form.status ? 'Active' : 'Inactive'"
                    hide-details
                    dense
                  ></v-switch>
                </td>
              </tr>
            </table>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
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
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  components: {
    vueDropzone: vue2Dropzone,
  },
  data() {
    return {
      image: null,
      dropzoneOptions: {
        url: "https://httpbin.org/post",
        thumbnailWidth: 150,
        maxFilesize: 0.5,
        headers: { "My-Awesome-Header": "header value" },
      },
      menu1: false,
      menu2: false,
      loading: false,
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      material_groups: [],
      material_types: [],
      unit_measures: [],
      form: {
        active_from: null,
        active_to: null,
      },
      showDatePicker: false,
      showDatePicker2: false,
      dialogHeaderText: "",
      createdAtMenu2: false,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("edi.materials_material_number"),
          value: "material_number",
        },
        { text: this.$t("edi.materials_description"), value: "description" },
        { text: this.$t("edi.materials_active_from"), value: "active_from" },
        { text: this.$t("edi.materials_active_to"), value: "active_to" },
        { text: this.$t("edi.materials_status"), value: "status" },
        {
          text: this.$t("edi.materials_unit_measure_id"),
          value: "unit_measure.value",
        },
        {
          text: this.$t("edi.materials_material_type_id"),
          value: "material_type.name",
        },
        {
          text: this.$t("edi.materials_material_group_id"),
          value: "material_group.name",
        },
        {
          text: this.$t("edi.created_at"),
          value: "created_at",
        },
        {
          text: this.$t("edi.created_by"),
          value: "created_by",
        },
        {
          text: this.$t("edi.updated_at"),
          value: "updated_at",
        },
        {
          text: this.$t("edi.updated_by"),
          value: "updated_by",
        },
        { text: this.$t("edi.materials_actions"), value: "actions", width: 100 },
      ];
    },
  },
  methods: {
    savePhoto() {
      this.loading = true;
      this.formData = new FormData();
      this.formData.append("image", this.image);
      axios
        .post(
          this.$store.state.backend_url +
            "api/edi/materials/file-upload/" +
            this.form.id,
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((response) => {
          this.loading = false;
          this.dialog = false;
          this.getList();
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
        .catch(function (error) {
          this.loading = false;
        });
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
        .post(this.$store.state.backend_url + "api/edi/materials", {
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
    getRef() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/edi/materials/get-ref")
        .then((response) => {
          this.material_types = response.data.material_types;
          this.material_groups = response.data.material_groups;
          this.unit_measures = response.data.unit_measures;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("edi.materials_create");
      this.form = {
        id: Date.now(),
        material_number: null,
        description: null,
        picture: null,
        active_from: null,
        active_to: null,
        status: null,
        unit_measure_id: null,
        material_type_id: null,
        material_group_id: null,
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("edi.materials_edit");
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
            this.$store.state.backend_url + "api/edi/materials/update",
            this.form
          )
          .then((res) => {
            if (this.image) {
              this.savePhoto();
            } else {
              this.dialog = false;
              this.loading = false;
              this.getList();
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
            }
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
                "api/edi/materials/delete/" +
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
    toggleDatePicker(field) {
      if (field === "active_from") {
        this.showDatePicker = !this.showDatePicker;
      } else if (field === "active_to") {
        this.showDatePicker2 = !this.showDatePicker2;
      }
    },
  },
  mounted() {
    this.getList();
    this.getRef();
  },
};
</script>
