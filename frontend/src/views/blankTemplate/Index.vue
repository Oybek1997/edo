<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("blankTemplate.index") }}</span>
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
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="newBlank()"
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
                @click="$router.push('/blank-templates/edit/' + item.id)"
                >{{
                  items
                    .map(function (x) {
                      return x.id;
                    })
                    .indexOf(item.id) + 1
                }}</span
              >
            </template>
            <template v-slot:item.blank_name="{ item }">
              <span
                @click="$router.push('/blank-templates/edit/' + item.id)"
                style="white-space: normal; display: block; cursor: pointer"
                v-if="item"
                >{{ item.blank_name }}</span
              >
            </template>
            <template v-slot:item.description="{ item }">
              <span
                @click="$router.push('/blank-templates/edit/' + item.id)"
                style="white-space: normal; display: block; cursor: pointer"
                v-if="item.description"
                >{{ item.description }}</span
              >
            </template>
            <template v-slot:item.file_type="{ item }">
              <span v-if="item.file_type === 0"
                ><v-icon size="18" color="blue">mdi-file-word-outline</v-icon>Word</span
              >
              <span v-else
                ><v-icon size="18" color="green">mdi-file-excel-outline</v-icon>Excel</span
              >
            </template>
            <template v-slot:item.is_active="{ item }">
              <span v-if="item.is_active === 1"
                ><v-icon size="18" color="green" large>mdi-toggle-switch</v-icon></span
              >
              <span v-else
                ><v-icon size="18" color="gray" large
                  >mdi-toggle-switch-off-outline</v-icon
                ></span
              >
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn 
                class="px-1"
                color="blue"
                style="min-width: 25px"
                small
                text
                @click="editItem(item)">
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                class="px-1"
                color="blue"
                style="min-width: 25px"
                small
                text
                @click="downloadTemplate(item.file_name)"
              >
                <v-icon size="18">mdi-download</v-icon>
              </v-btn>
              <v-btn 
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
      v-model="blankDialog"
      @keyup.native.enter="save"
      @keydown.esc="blankDialog = false"
      persistent
      max-width="600px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("blankTemplate.index") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="pa-0 mb-3">
                <label for>{{ $t("name") }}</label>
                <v-text-field
                  v-model="form.blank_name"
                  hide-details="auto"
                  dense
                  outlined
                >
                </v-text-field>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3">
                <label for>{{ $t("carPurchase.comment") }}</label>
                <v-textarea
                  v-model="form.description"
                  hide-details="auto"
                  dense
                  rows="2"
                  outlined
                >
                </v-textarea>
              </v-col>
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 pa-0 mb-3"
              >
                <label for>{{ $t("blankTemplate.file") }}</label>
                <v-file-input
                  v-model="file"
                  :rules="[
                    (v) => {
                      let allowedExtensions = /(\.docx)$/i || /(\.xlsx)$/i;
                      let error = false;
                      (v) => {
                        if (!allowedExtensions.exec(file.name)) {
                          error = true;
                        }
                      };
                      return !error || $t('requiredformat');
                    },
                    (v) => v.size < 5242880 || $t('requiredsize5'),
                    (v) => !!v || $t('input.required'),
                  ]"
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-document"
                  accept=".docx, .xlsx, application/docx, application/xlsx"
                  small-chips
                  show-size
                  hide-details="auto"
                ></v-file-input>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3">
                <label for>{{ $t("blankTemplate.status") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.is_active"
                  :items="[
                    { text: 'Aktiv', value: 1 },
                    { text: 'Noaktiv', value: 0 },
                  ]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6"> </v-col>
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
            @click="blankDialog = false"
            elevation="0"
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";

export default {
  data() {
    return {
      page: 1,
      from: 0,
      StaffDialog: false,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      dataTableValue: [],
      loading: false,
      items: [],
      formData: [],
      form: {},
      file: {},
      blankDialog: false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("blankTemplate.blank_name"), value: "blank_name" },
        {
          text: this.$t("documentTemplates.description"),
          value: "description",
        },
        { text: this.$t("blankTemplate.fileType"), value: "file_type" },
        { text: this.$t("blankTemplate.status"), value: "is_active" },

        {
          text: this.$t("actions"),
          value: "actions",
          width: 180,
          align: "center",
          padding: 0,
        },
      ];
    },
  },
  methods: {
    newBlank() {
      this.form = {
        id: Date.now(),
        blank_name: "",
        description: "",
        is_active: "",
      };
      this.blankDialog = true;
      if (this.$refs.blankDialog) this.$refs.blankDialog.reset();
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    downloadTemplate(file_name) {
      axios
        .get(this.$store.state.backend_url + "api/get-url/" + file_name)
        .then((res) => {
          let url = this.$store.state.backend_url + res.data;
          window.open(url);
          axios.post(
            this.$store.state.backend_url + "api/blank-templates/delete-file",
            {
              file: res.data,
            }
          );
        });
    },
    editItem(item) {
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.blankDialog = true;
      this.blankDialog.this.editMode = true;
    },
    deleteItem() {},
    save() {
      if (this.$refs.dialogForm.validate()) {
        this.loading = true;
        this.formData = new FormData();
        this.formData.append("id", this.form.id);
        this.formData.append("file", this.file);
        this.formData.append("file_type", this.form.file_type);
        this.formData.append("blank_name", this.form.blank_name);
        this.formData.append("description", this.form.description);
        this.formData.append("is_active", this.form.is_active);
        axios
          .post(
            this.$store.state.backend_url + "api/blank-templates/update",
            this.formData
          )
          .then(() => {
            this.getList();
            this.blankDialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
            });
            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation"),
            });
            this.loading = false;
          })
          .catch(function (error) {
            console.log(error);
            this.loading = false;
          });
      }
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
                "api/blank-templates/delete/" +
                item.id
            )
            .then((res) => {
              this.getList();
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
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/blank-templates", {
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          this.items = response.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
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