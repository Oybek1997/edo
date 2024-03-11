<template>
  <div>
    <v-btn
      v-show="isHelp"
      style="margin-right: 20px"
      color="warning"
      x-small
      dark
      fab
      @click="showHelp"
    >
      <v-icon>mdi-help</v-icon>
    </v-btn>

    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600"
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
          <v-form ref="dialogForm">
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("helps.title") }}</label>
                <v-text-field
                  v-model="form.title"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("helps.name") }}</label>
                <v-textarea
                  v-model="form.name"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  auto-grow
                  outlined
                  rows="3"
                  row-height="25"
                ></v-textarea>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("helps.src") }}</label>
                <v-text-field
                  v-model="form.src"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                  readonly
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
                      let allowedExtensions =
                        /(\.docx)$/i ||
                        /(\.xlsx)$/i ||
                        /(\.doc)$/i ||
                        /(\.png)$/i ||
                        /(\.jpg)$/i ||
                        /(\.pdf)$/i;
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
                  :label="$t('helps.status')"
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
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="viewDialog"
      scrollable
      @keydown.esc="viewDialog = false"
      persistent
      max-width="80%"
    >
      <template v-for="(item, index) in items">
        <v-card v-if="item.id == viewId" class="pa-2" :key="index">
          <v-card-title>
            <div class="d-inline pa-2 blue accent-4 white--text" style="width:80%">
              {{ item.title ? item.title : $t("timeline.no_title") }}
            </div>
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
            <v-spacer></v-spacer>
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
                >{{ file.file_name }}</v-btn
              >
            </v-row>
            <!-- <v-chip class="ma-2" color="white">
              <i>{{ item }}</i>
            </v-chip>-->
            <v-spacer></v-spacer>
          </v-card-actions>
        </v-card>
      </template>
    </v-dialog>
    <v-dialog
      v-model="viewAdmin"
      scrollable
      @keydown.esc="viewAdmin = false"
      persistent
      max-width="80%"
    >
      <template>
        <v-card class="ma-1 pa-1">
          <v-card-title class="pa-1">
            <span>{{ $t("helps.index") }}</span>
            <v-spacer></v-spacer>
            <v-btn
              color="#6ac82d"
              style="margin-right: 20px"
              x-small
              dark
              fab
              @click="newItem"
            >
              <v-icon>mdi-plus</v-icon>
            </v-btn>
            <v-btn color="red" outlined x-small fab @click="viewAdmin = false">
              <v-icon>mdi-close</v-icon>
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
            style="border: 1px solid #aaa"
            item-key="id"
            :hide-default-footer="true"
          >
            <!-- <template v-slot:body.prepend="{ item }">
              <tr>
                <td></td>
                <td>
                  <v-text-field
                    v-model="filter.title"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-text-field
                    v-model="filter.name"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-text-field
                    v-model="filter.src"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-autocomplete
                    clearable
                    v-model="filter.is_active"
                    :items="
                      isActives.map((v) => ({
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
            </template> -->
            <template v-slot:item.id="{ item }">{{
              items
                .map(function (x) {
                  return x.id;
                })
                .indexOf(item.id) + 1
            }}</template>

            <template v-slot:item.is_active="{ item }">
              <template v-if="item.is_active == 1">{{
                $t("timeline.active")
              }}</template>
              <template v-else-if="item.is_active == 0">{{
                $t("timeline.inactive")
              }}</template>
            </template>
            <template v-slot:item.title="{ item }">
              <td style="max-width: 200px">{{ item.title.substr(0, 30) }}</td>
            </template>
            <template v-slot:item.name="{ item }">
              <td style="max-width: 300px">{{ item.name.substr(0, 100) }}</td>
            </template>
            <template v-slot:item.src="{ item }">
              <td style="max-width: 150px">{{ item.src }}</td>
            </template>
            <!-- <template v-slot:item.file_type="{ item }">
          <span
            v-for="(file, indexFile) in item.files"
            :key="indexFile"
          >{{ file ? file.file_name : "" }}</span>
            </template>-->
            <template v-slot:item.actions="{ item }">
              <v-btn
                color="green"
                small
                text
                @click="viewItem(item.id, item.name)"
              >
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
      </template>
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
      loading: false,
      filter: {
        title: "",
        name: "",
        src: "",
        is_active: "",
      },
      currentUrl: window.location.href,
      dialog: false,
      viewDialog: false,
      viewAdmin: false,
      viewId: "",
      editMode: null,
      items: [],
      form: {},
      files: [],
      isHelp: false,
      help: [],
      imageUrl: "",
      fileUrl: this.$store.state.backend_url + "staffs/file-download/",
      isActives: [
        {
          value: 0,
          text: "Not Active",
        },
        {
          value: 1,
          text: "Active",
        },
      ],
      dialogHeaderText: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },

        { text: this.$t("helps.title"), value: "title" },
        { text: this.$t("helps.name"), value: "name" },
        { text: this.$t("helps.src"), value: "src" },
        { text: this.$t("helps.status"), value: "is_active" },

        // { text: this.$t("blankTemplate.fileType"), value: "file_type" },
        // { text: this.$t("blankTemplate.status"), value: "is_active" },

        {
          text: this.$t("actions"),
          value: "actions",
          width: 180,
          align: "center",
        },
      ];
    },
  },
  methods: {
    checkHelp() {
      // sahifa ochilganida ushbu sahifa uchun aktiv holatdagi help mavjud ekanligi yoki yo'qligini teklshirib Button holatini o
      // o'zgartiriuvchi funksiya
      let item = this.items.find((v) => v.is_active == 1);
      if (item || this.$store.getters.checkPermission("help-admin")) {
        this.isHelp = true;
      } else {
        this.isHelp = false;
      }
    },
    showHelp() {
      // filter yordamida sahifaga tegishli bo'lgan helpni bazadan olish agar u mavjud bo'lsa adminga
      // tablitsada ko'rinishida ro'yxat chiqarish, userga esa helpni korish oynasini ochish
      // agar mavjud bo'lmasa Admin uchun yangi help qo'shish oynasini ochishda ishlatiladigan funskiya
      if (this.items.length != 0) {
        let item = this.items.find((v) => v.is_active == 1);
        if (item && !this.$store.getters.checkPermission("help-admin")) {
          this.viewItem(item.id, item.name);
        } else if (this.$store.getters.checkPermission("help-admin")) {
          this.viewAdmin = true;
        }
      } else if (
        this.items.length == 0 &&
        this.$store.getters.checkPermission("help-admin")
      ) {
        this.newItem();
      }
    },

    getList() {
      // ushbu sahifa uchun biriktirilgan barcha helplarni backend'dan qidirib keladigan funksiya
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/helpComponent", {
          filter: this.currentUrl,
        })
        .then((response) => {
          this.items = response.data;
          this.loading = false;
          this.checkHelp();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      // sahifaga yangi help qo'shish funksiyasi
      this.dialogHeaderText = this.$t("add");
      this.dialog = true;
      this.editMode = false;
      // if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      this.form = {
        id: Date.now(),
        title: "",
        name: "",
        src: this.currentUrl,
      };
    },
    editItem(item) {
      // mavjud help'ni tahrirlashga yordam beruvchi funksiya
      this.dialogHeaderText = this.$t("edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      // yangi qo'shilgan yoki tahrirlangan help'larnibazaga yozish uchun ishlatiladigan funksiya
      let formData = new FormData();

      this.files.forEach((v, i) => {
        formData.append("files[]", v);
      });
      this.files = [];
      Object.keys(this.form).forEach((v) => {
        formData.append(v, this.form[v]);
      });
      if (this.$refs.dialogForm.validate())
        axios
          .post(this.$store.state.backend_url + "api/helps/update", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
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
    deleteItem(item) {
      // tanlangan helni o'chirishda ishlatiladigan funksiya
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
              this.$store.state.backend_url + "api/helps/delete/" + item.id
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
              });
              console.log(err);
            });
        }
      });
    },
    viewItem(id, name) {
      // userlarga ushbu sahifa uchun aktiv holatdagi help'ni dialog oynasida ko'rsatish uchunn  mo'ljallnagan funksiya
      this.viewDialog = true;
      this.viewId = id;
      let img =
        "<img style='width:100%' src = " +
        this.$store.state.backend_url +
        "staffs/file-download/";

      let result = name.match(/@(.*?)@/g);
      if (result) {
        result = result.map((v) => v.replaceAll("@", ""));
      } else {
        result = name;
      }
      let help = this.items.find((v) => v.id == id);

      let files = help.files.filter((v) => result.includes(v.file_name));

      files.forEach((v) => {
        name = name.replace("@" + v.file_name + "@", img + v.id + "'>");
      });

      this.imageUrl = name;
    },
  },
  mounted() {
    this.getList();
  },
  created() {},
};
</script>
<style scoped>
.v-card__text{
  font-size: 1.275rem;
  font-weight: 400;
  line-height: 2.075rem;
  letter-spacing: 0.0071428571em;
  color: rgba(0, 0, 0, 1) !important;
}
</style>
