<template>
<div class="fullHeight">
    <v-card class="heightFull" style="border-radius: 10px; border: 1px solid #dce5ef;" elevation="0">
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("directory.index") }}</span>
        <div
            class="headerSearch d-flex align-center"
          >
          <v-autocomplete
          class="txt_search1"
          style="max-width: 400px;"
            v-model="filter"
            :label="$t('directory.type')"
            :items="directory_types"
            @change="getList"
            item-value="id"
            item-text="text"
            hide-details
            dense
            solo
          ></v-autocomplete>
        <v-text-field
          v-model="search"
          prepend-inner-icon="mdi-magnify"
          class="txt_search1"
          style="border-radius: 0px;"
          :placeholder="$t('search')"
          @keyup.native.enter="getList"
          dense
          solo
          single-line
          hide-details
        ></v-text-field>      
          <v-menu
            transition="slide-y-transition"
            left
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn class="txt_searchBtn  ml-2" outlined v-bind="attrs" v-on="on">
                <v-icon size="18" color="white">mdi-format-list-bulleted</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;" @click="newItem">   
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                    </v-list-item-title
                  ></v-list-item>
                <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;" @click="0">                  
                  <v-list-item-title>
                    <v-icon color='#107C41' size="18">mdi-microsoft-excel</v-icon>
                    Скачать таблицу Excel
                    </v-list-item-title
                  ></v-list-item>
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
              style="
                width: 100%;
                height:100%;
                border-radius: 10px;
              "
              fixed-header
              :loading-text="$t('loadingText')"
              :no-data-text="$t('noDataText')"
              :height="screenHeight"
              :loading="loading"
              :headers="headers"
              item-key="id"
              :items="items"
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
                prevIcon: 'mdi-arrow-left',
                nextIcon: 'mdi-arrow-right',
              }"
              @update:page="updatePage"
              @update:items-per-page="updatePerPage"
            >
            <template v-slot:item="{ item, index }">
              <tr>
                <td  style="max-width: 50px">{{ index + from }}</td>
                <td style="max-width: 50px">{{ item.code }}</td>
                <td style="max-width: 300px">{{ item.name_uz_latin }}</td>
                <td style="max-width: 300px">{{ item.name_uz_cyril }}</td>
                <td style="max-width: 300px">{{ item.name_ru }}</td>
                <td style="max-width: 50px">
                  <v-btn
                  v-if="$store.getters.checkRole('directory')"
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
                  v-if="$store.getters.checkRole('directory')"
                  class="pl-0 pr-2"
                  color="error"
                  style="min-width: 25px;"
                  small
                  text
                  @click="deleteItem(item)"
                >
                  <v-icon size="20">mdi-trash-can-outline</v-icon>
                </v-btn>
                </td>
              </tr>
            
            </template>
              <!-- <template v-slot:item.id="{ item }">{{
                items
                  .map(function (x) {
                    return x.id;
                  })
                  .indexOf(item.id) + 1
              }}</template> -->

              <!-- <template v-slot:item.directory_type_id="{ item }">
                <span style="white-space: normal; max-width: 100px">{{
                  item.directory_type
                    ? item.directory_type["name_" + $i18n.locale]
                    : ""
                }}</span>
              </template> -->
              <!-- <template v-slot:item.actions="{ item }">
                <v-btn
                  v-if="$store.getters.checkRole('directory')"
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
                  v-if="$store.getters.checkRole('directory')"
                  class="pl-0 pr-2"
                  color="error"
                  style="min-width: 25px;"
                  small
                  text
                  @click="deleteItem(item)"
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
        <span class="dialog-head_title">{{ addType ? $t('directory.type') : $t('directory.index') }}</span>
        <v-divider class="mt-1 mb-4" style="border-color: #dce5ef"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm" v-if="!addType" class="ma-0">
            <v-row class="ma-0 pa-0 dialog-form">
              <v-col cols="12" md="12" class="mb-2 pa-0">
                <v-autocomplete
                  clearable
                  v-model="form.directory_type_id"
                  :items="directory_types"
                  :label="$t('directoryType')"
                  hide-details="auto"
                  item-value="id"
                  dense
                  outlined
                >
                <template v-slot:append-outer>
                  <v-btn @click="addType = !addType" outlined color="success" class="mt-n2"><v-icon>mdi-plus</v-icon></v-btn>
                </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12" md="12"  class="mb-2 pa-0">
                <v-text-field
                  v-model="form.code"
                  :label="$t('code')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12"  class="mb-2 pa-0">
                <v-text-field
                  v-model="form.name_uz_latin"
                  :label="$t('name_uz_latin')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12"  class="mb-2 pa-0">
                <v-text-field
                  v-model="form.name_uz_cyril"
                  :label="$t('name_uz_cyril')"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12"  class="mb-2 pa-0">
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
          <v-row v-else class="ma-0 pa-0 dialog-form">
            <v-col cols="12" md="12"  class="mb-2 pa-0">
                <v-text-field
                  v-model="typeForm.name_uz_latin"
                  :label="$t('directory.name_uz_latin')"
                  hide-details="auto"
                  dense
                  outlined
                >
                  <template v-slot:append-outer>
                    <v-btn @click="addType = !addType" outlined color="error" class="mt-n2"><v-icon>mdi-close</v-icon></v-btn>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="12"  class="mb-2 pa-0">
                <v-text-field
                  v-model="typeForm.name_uz_cyril"
                  :label="$t('directory.name_uz_cyril')"
                  hide-details="auto"
                  dense
                  outlined
                >
                </v-text-field>
              </v-col>
              <v-col cols="12" md="12"  class="mb-2 pa-0">
                <v-text-field
                  v-model="typeForm.name_ru"
                  :label="$t('directory.name_ru')"
                  hide-details="auto"
                  dense
                  outlined
                >
                </v-text-field>
              </v-col>
          </v-row>
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      addType: false,
      typeForm: {
        name_uz_latin: "",
        name_uz_cyril: "",
        name_ru: "",
      },
      loading: false,
      search: "",
      page: 1,
      from: 0,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      dialog: false,
      editMode: null,
      items: [],
      companies: [],
      directory_types: [],
      form: {},
      filter: 1,
      dialogHeaderText: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", class: "blue-grey lighten-5", width: 50 },
        { text: this.$t("code"), value: "code", class: "blue-grey lighten-5", width: 50, sortable: true },
        { text: this.$t("staff.name_uz_latin"), class: "blue-grey lighten-5", value: "name_uz_latin",  },
        { text: this.$t("staff.name_uz_cyril"),class: "blue-grey lighten-5",  value: "name_uz_cyril",  },
        { text: this.$t("staff.name_ru"), class: "blue-grey lighten-5", value: "name_ru",  },
        {
          text: this.$t("actions"),
          value: "actions",class: "blue-grey lighten-5", 
          width: 70,
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkRole("directory") ||
          this.$store.getters.checkRole("directory")
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

    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/directories", {
          pagination: this.dataTableOptions,
          search: this.search,
          filter: this.filter,
        })
        .then((response) => {
          this.items = response.data.directories.data;
          this.from = response.data.directories.from;
          this.server_items_length = response.data.directories.total;
          this.directory_types = response.data.directory_types.map((v) => {
            return { text: v["name_" + this.$i18n.locale], id: v.id };
          });
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("directory.add");
      this.form = {
        id: Date.now(),
        name_uz_latin: "",
        name_uz_cyril: "",
        name_ru: "",
        code: "",
        directory_type_id: this.filter,
      };
      this.dialog = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },

    editItem(item) {
      this.dialogHeaderText = this.$t("directory.edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.addType) this.saveType();
      else {
        if (this.$refs.dialogForm.validate())
          axios
            .post(
              this.$store.state.backend_url + "api/directories/update",
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
      }
    },
    saveType() {
      axios
        .post(
          this.$store.state.backend_url + "api/directories/add-type",
          this.typeForm
        )
        .then((res) => {
          this.directory_types.push({
            text: res.data["name_" + this.$i18n.locale],
            id: res.data.id,
          });
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
          this.addType = false;
          this.typeForm = {
            name_uz_latin: '',
            name_uz_cyril:'',
            name_ru: ''
          };
        })
        .catch((err) => {
          console.log(err);
          this.addType = false;
          this.typeForm = {
            name_uz_latin: '',
            name_uz_cyril:'',
            name_ru: ''
          };
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
                "api/directories/delete/" +
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
  background: #FF9F0E;
  border: 0.20px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px!important;
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
