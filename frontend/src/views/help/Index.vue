<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t('Help') }}</span>
        <v-spacer></v-spacer>
        <v-btn style="margin-right: 20px;" color="warning" x-small dark fab @click="checkHelp">
          <v-icon>mdi-help</v-icon>
        </v-btn>
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
        class="mainTable ma-1"
        style="border: 1px solid #aaa;"
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100],
          itemsPerPageAllText:$t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right'
        }"
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
      >
        <template v-slot:body.prepend="{ item }">
          <tr class="prepend_height prepend_height">
            <td></td>
            <td>
              <v-text-field v-model="filter.title" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.name" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-text-field v-model="filter.src" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-autocomplete
                clearable
                v-model="filter.is_active"
                :items="
                  isActives.map(v => ({
                    text: v.text,
                    value: v.value,
                  }))
                "
                hide-details
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td></td>
          </tr>
        </template>
        <template
          v-slot:item.id="{ item }"
        >{{items.map(function(x) {return x.id; }).indexOf(item.id) + from}}</template>

        <template v-slot:item.is_active="{ item }">
          <template v-if="item.is_active == 1">Active</template>
          <template v-else-if="item.is_active == 0">Not Active</template>
        </template>
        <!-- <template v-slot:item.file_type="{ item }">
          <span
            v-for="(file, indexFile) in item.files"
            :key="indexFile"
          >{{ file ? file.file_name : "" }}</span>
        </template>-->
        <template v-slot:item.actions="{ item }">
          <v-btn color="green" small text @click="viewItem(item.id, item.name)">
            <v-icon>mdi-eye-outline</v-icon>
          </v-btn>
          <v-btn color="blue" small text @click="editItem(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="dialog" @keydown.esc="dialog = false" persistent max-width="600">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row>
              <v-col cols="12">
                <label for>{{ $t('Title') }}</label>
                <v-text-field
                  v-model="form.title"
                  :rules="[v => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t('Name') }}</label>
                <v-textarea
                  v-model="form.name"
                  :rules="[v => !!v || $t('input_required')]"
                  hide-details="auto"
                  auto-grow
                  outlined
                  rows="3"
                  row-height="25"
                ></v-textarea>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("SRC") }}</label>
                <v-text-field
                  v-model="form.src"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <!--              -->
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("blankTemplate.file") }}</label>
                <v-file-input
                  v-model="files"
                  multiple
                  :rules="[
                    (v) => {
                      let allowedExtensions = /(\.docx)$/i || /(\.xlsx)$/i || /(\.doc)$/i || /(\.png)$/i || /(\.jpg)$/i || /(\.pdf)$/i;
                      let error = false;
                      (v) => {
                        if (!allowedExtensions.exec(file.name)) {
                          error = true;
                        }
                      };
                      return !error || $t('requiredformat');
                    },
                    (v) => !!v || $t('input.required'),
                  ]"
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-document"
                  accept=".docx, .xlsx, .doc, .png, .jpg, .pdf, application/docx, application/xlsx, application/doc, application/png, application/jpg, application/pdf"
                  small-chips
                  show-size
                  hide-details="auto"
                ></v-file-input>
              </v-col>

              <!--              -->

              <v-col cols="6">
                <v-autocomplete
                  :label="$t('Is Active')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.is_active"
                  :items="isActives"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t('save') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="viewDialog"
      scrollable
      @keydown.esc="viewDialog = false"
      persistent
      max-width="800"
    >
      <template v-for="(item, index) in items">
        <v-card v-if="item.id == viewId" class="pa-2" :key="index">
          <v-card-title>
            <div
              class="d-inline pa-2 blue accent-4 white--text"
              style
            >{{ item.title ? item.title : $t("timeline.no_title") }}</div>
            <v-spacer></v-spacer>
            <v-chip
              v-if="true"
              class="ma-2"
              :color="item.is_active == 1 ? 'green' : 'red'"
              outlined
            >
              {{
              item.is_active == 1
              ? $t("timeline.active")
              : $t("timeline.inactive")
              }}
            </v-chip>
            <v-btn color="red" outlined x-small fab @click="viewDialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text v-html="imageUrl"></v-card-text>

          <v-divider></v-divider>
          <v-card-actions>
            <v-chip class="ma-2" outlined>
              <v-icon left>mdi-account-outline</v-icon>
              <i>
                <!-- {{
                item.created_by
                }}-->
                <span>
                  {{ item.created_by.employee["lastname_" + $i18n.locale] }}
                  {{ item.created_by.employee["firstname_" + $i18n.locale] }}
                </span>
              </i>
            </v-chip>
            <v-row justify="end">
              <v-btn
                small
                :href="fileUrl + file.id"
                class="ma-2"
                depressed
                v-for="file in item.files"
                dense
              >{{file.file_name}}</v-btn>
            </v-row>
            <!-- <v-chip class="ma-2" color="white">
              <i>{{ item }}</i>
            </v-chip>-->
            <v-spacer></v-spacer>
          </v-card-actions>
        </v-card>
      </template>
    </v-dialog>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t('loadingText') }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
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
      loading: false,
      filter: {
        title: "",
        name: "",
        src: "",
        is_active: ""
      },
      currentUrl: window.location.href,
      dialog: false,
      viewDialog: false,
      viewId: "",
      editMode: null,
      items: [],
      form: {},
      files: [],
      imageUrl: "",
      // fileUrl: "http://edo.loc/staffs/file-download/",
      fileUrl: "https://b-edo.uzautomotors.com/staffs/file-download/",
      isActives: [
        {
          value: 0,
          text: "Not Active"
        },
        {
          value: 1,
          text: "Active"
        }
      ],
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },

        { text: this.$t("Title"), value: "title" },
        { text: this.$t("Name"), value: "name" },
        { text: this.$t("SRC"), value: "src" },
        { text: this.$t("Status"), value: "is_active" },

        // { text: this.$t("blankTemplate.fileType"), value: "file_type" },
        // { text: this.$t("blankTemplate.status"), value: "is_active" },

        {
          text: this.$t("actions"),
          value: "actions",
          width: 180,
          align: "center"
        }
      ];
    }
  },
  methods: {
    checkHelp() {
      let help = this.items.find(v => v.src == this.currentUrl);
      if (help) {
        this.viewItem(help.id, help.name);
      } else {
        this.newItem();
      }
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
        .post(this.$store.state.backend_url + "api/helps", {
          pagination: this.dataTableOptions,
          filter: this.filter
        })
        .then(response => {
          this.items = response.data.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      // if (this.$store.getters.checkPermission("create_document_for_chief")){
      this.dialogHeaderText = this.$t("add");
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      this.form = {
        id: Date.now(),
        title: "",
        name: "",
        src: this.currentUrl
      };
      // }
      // else{
      //   alert("Bu sahifa uchun qo'llanma kiritilmagan!")
      // }
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      let formData = new FormData();

      this.files.forEach((v, i) => {
        formData.append("files[]", v);
      });
      this.files = [];
      Object.keys(this.form).forEach(v => {
        formData.append(v, this.form[v]);
      });
      if (this.$refs.dialogForm.validate())
        axios
          .post(this.$store.state.backend_url + "api/helps/update", formData, {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          })
          .then(res => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: toast => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
              }
            });

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation")
            });
          })
          .catch(err => {
            console.log(err);
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
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url + "api/helps/delete/" + item.id
            )
            .then(res => {
              this.getList(this.page, this.itemsPerPage);
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
              });
              console.log(err);
            });
        }
      });
    },
    viewItem(id, name) {
      this.viewDialog = true;
      this.viewId = id;
      // let img = "<img style='width:100%' src = 'http://edo.loc/staffs/file-download/";
      let img =
        "<img style='width:100%' src = 'https://b-edo.uzautomotors.com/staffs/file-download/";

      let result = name.match(/@(.*?)@/g);
      let result2;
      if (result) {
        result2 = result.map(v => v.replaceAll("@", ""));
      }
      let help = this.items.find(v => v.id == id);

      let files = help.files.filter(v => result2.includes(v.file_name));

      files.forEach(v => {
        name = name.replace("@" + v.file_name + "@", img + v.id + "'>");
      });

      this.imageUrl = name;
    }
  },
  mounted() {
    this.getList();
  },
  created() {}
};
</script>
<style scoped>
</style>
