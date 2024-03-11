<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("carPurchase.index") }}</span>
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
          outlined
          x-small
          fab
          @click="
            getExcel(1);
            excel = [];
          "
          class="mr-2"
        >
          <!-- <span style="color: #4caf50">MC EXCEL</span> -->
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn>
        <v-btn color="#6ac82d" x-small dark fab @click="newItem()">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>
      <div class="d-flex justify-center py-4">
        <v-card class="mr-4">
          <v-list-item>
            <v-img
              class
              style="width: 110px; height: 85px"
              src="img/damas.png"
            ></v-img>
            <v-card-title>{{
              car_count.length > 0 ? car_count[0].model : ""
            }}</v-card-title>
          </v-list-item>
        </v-card>
        <v-card class="mr-4">
          <v-list-item>
            <v-img
              style="width: 110px; height: 86px"
              src="img/nexia.png"
            ></v-img>
            <v-card-title>{{
              car_count.length > 0 ? car_count[1].model : ""
            }}</v-card-title>
          </v-list-item>
        </v-card>
        <v-card class>
          <v-list-item>
            <v-img
              style="width: 110px; height: 85px"
              src="img/trailblazer.png"
            ></v-img>
            <v-card-title>{{
              car_count.length > 0 ? car_count[2].model : ""
            }}</v-card-title>
          </v-list-item>
        </v-card>
      </div>
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
          itemsPerPageOptions: [20, 50, 100],
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
        <template v-slot:item.id="{ item }">
          {{
            items
              .map(function (x) {
                return x.id;
              })
              .indexOf(item.id) + from
          }}
        </template>
        <template v-slot:item.department_info="{ item }">
          <span v-if="$i18n.locale == 'uz_latin'">
            {{ item.employee.staff[0].department.department_code }} -
            {{ item.employee.staff[0].department.name_uz_latin }}
          </span>
          <span v-else-if="$i18n.locale == 'uz_cyril'">
            {{ item.employee.staff[0].department.department_code }} -
            {{ item.employee.staff[0].department.name_uz_cyril }}
          </span>
          <span v-else>
            {{ item.employee.staff[0].department.department_code }} -
            {{ item.employee.staff[0].department.name_ru }}
          </span>
        </template>
        <template v-slot:item.position_info="{ item }">
          <span v-if="$i18n.locale == 'uz_latin'">{{
            item.employee.staff[0].position.name_uz_latin
          }}</span>
          <span v-else-if="$i18n.locale == 'uz_cyril'">{{
            item.employee.staff[0].position.name_uz_cyril
          }}</span>
          <span v-else>{{ item.employee.staff[0].position.name_ru }}</span>
        </template>
        <template v-slot:item.created_at="{ item }">{{
          item.created_at.slice(0, 10)
        }}</template>
        <template v-slot:item.employee.info="{ item }">
          <span v-if="$i18n.locale == 'uz_latin'">
            {{ item.employee.firstname_uz_latin }}
            {{ item.employee.lastname_uz_latin }}
            {{ item.employee.middlename_uz_latin }}
          </span>
          <span v-else>
            {{ item.employee.firstname_uz_cyril }}
            {{ item.employee.lastname_uz_cyril }}
            {{ item.employee.middlename_uz_cyril }}
          </span>
        </template>
        <template v-slot:item.accountant_employee.accInfo="{ item }">
          <span v-if="$i18n.locale == 'uz_latin'">
            {{
              item.accountant_employee
                ? item.accountant_employee.firstname_uz_latin
                : ""
            }}
            {{
              item.accountant_employee
                ? item.accountant_employee.lastname_uz_latin
                : ""
            }}
            {{
              item.accountant_employee
                ? item.accountant_employee.middlename_uz_latin
                : ""
            }}
          </span>
          <span v-else>
            {{
              item.accountant_employee
                ? item.accountant_employee.firstname_uz_cyril
                : ""
            }}
            {{
              item.accountant_employee
                ? item.accountant_employee.lastname_uz_cyril
                : ""
            }}
            {{
              item.accountant_employee
                ? item.accountant_employee.middlename_uz_cyril
                : ""
            }}
          </span>
        </template>
        <template v-slot:item.created_by="{ item }">
          <span v-if="$i18n.locale == 'uz_latin'">
            {{ item.created_by.employee.lastname_uz_latin }}
            {{ item.created_by.employee.firstname_uz_latin.substr(0, 1) }}.
            {{ item.created_by.employee.middlename_uz_latin.substr(0, 1) }}.
          </span>
          <span v-else>
            {{ item.created_by.employee.lastname_uz_cyril }}
            {{ item.created_by.employee.firstname_uz_cyril.substr(0, 1) }}.
            {{ item.created_by.employee.middlename_uz_cyril.substr(0, 1) }}.
          </span>
        </template>
        <template v-slot:item.file_name="{ item }">
          <v-btn color="green" small text @click="viewFile(item.file.id)">
            <v-icon>mdi-eye</v-icon>
          </v-btn>
          {{ item.file ? item.file.file_name : "" }}
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            v-if="
              item.accountant_status == 0 &&
              ($store.getters.checkPermission('car-purchase-update') ||
                $store.getters.getUser().id == item.created_by.id)
            "
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            v-if="
              item.accountant_status == 0 &&
              $store.getters.getUser().id == item.created_by.id
            "
            color="red"
            small
            text
            @click="deleteItem(item)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
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
            <div
              style="color: red"
              v-if="
                !(
                  form.created_by &&
                  (form.accountant_status != 0 ||
                    $store.getters.getUser().id !=
                      (form.created_by ? form.created_by.id : 0))
                )
              "
            >
              <v-card-title>
                {{ "Ариза юборишда қуйидаги талабларга амал қилинг:" }}
              </v-card-title>

              <span>
                {{
                  "1. Ходим ва танланган модел ҳақидаги маълумотлар қўлёзма аризадаги маълумотлар билан мос келиши шарт."
                }}
              </span>

              <p>
                {{
                  "2. Қўлёзма аризани тизимга *.PDF файл кўринишида бириктириш шарт."
                }}
              </p>
              <span>
                {{
                  "Ушбу қоидаларга амал қилинмаган ҳолларда АРИЗА БЕКОР ҚИЛИНАДИ"
                }}
              </span>
            </div>
            <v-row>
              <v-col cols="6">
                <label for>{{ $t("carPurchase.employee") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.employee_id"
                  :items="employees"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  :disabled="
                    form.created_by &&
                    (form.accountant_status != 0 ||
                      $store.getters.getUser().id !=
                        (form.created_by ? form.created_by.id : 0))
                  "
                ></v-autocomplete>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("carPurchase.carModel") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.car_model_id"
                  :items="car_models"
                  item-value="id"
                  item-text="carInfo"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  :disabled="
                    form.created_by &&
                    (form.accountant_status != 0 ||
                      $store.getters.getUser().id !=
                        (form.created_by ? form.created_by.id : 0))
                  "
                ></v-autocomplete>
              </v-col>
              <v-col
                cols="12"
                v-if="
                  $store.getters.checkPermission('car-purchase-update') &&
                  !!form.created_by
                "
              >
                <label for>{{ $t("carPurchase.comment") }}</label>
                <v-text-field
                  v-model="form.accountant_comment"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <!-- <v-col cols="6">
                <label for>{{ $t("carPurchases.regStatus") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.accountant_status"
                  :items="regStatuses"
                  item-text="value"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>-->
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
                v-if="!form.created_by"
              >
                <label for>{{ $t("carPurchase.file") }}</label>
                <v-file-input
                  v-model="file"
                  :rules="[
                    (v) => {
                      let allowedExtensions = /(\.pdf)$/i;
                      let error = false;
                      (v) => {
                        if (!allowedExtensions.exec(file.name)) {
                          error = true;
                        }
                      };
                      return !error || $t('requiredpdf');
                    },
                    (v) => !!v || $t('input.required'),
                  ]"
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf, application/pdf"
                  small-chips
                  show-size
                  hide-details="auto"
                ></v-file-input>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            v-if="
              form.accountant_status == 0 &&
              (!form.created_by ||
                $store.getters.getUser().id == form.created_by.id)
            "
            color="primary"
            @click="save(0)"
            >{{ $t("save") }}</v-btn
          >
          <v-btn
            v-if="
              $store.getters.checkPermission('car-purchase-update') &&
              !!form.created_by
            "
            color="success"
            @click="save(1)"
            >{{ $t("confirm") }}</v-btn
          >
          <v-btn
            v-if="
              $store.getters.checkPermission('car-purchase-update') &&
              !!form.created_by
            "
            class="ml-2"
            color="error"
            @click="save(2)"
            >{{ $t("cancel") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" fullscreen>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false">
            {{ $t("close") }}
          </v-btn>
        </v-card-title>
        <v-card-text class="d-flex justify-center">
          <iframe
            width="100%"
            :height="830"
            :src="$store.state.backend_url + 'staffs/get-file/' + fileForView"
          ></iframe>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            @click="pdfViewDialog = false"
            class="mr-4"
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
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="py-1 px-3">
          <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="excel"
              :name="'arizalar_ruyxati_' + today + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
export default {
  data() {
    return {
      fileForView: null,
      pdfViewDialog: false,
      downloadExcel: false,
      loading: false,
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      file: null,
      info: "",
      accInfo: "",
      employees: [],
      car_models: [],
      excel: [],
      car_count: [],
      formData: [],
      form: {},
      today: moment().format("YYYY-MM-DD"),
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      statuses: [
        {
          id: 0,
          name: "new",
        },
        {
          id: 1,
          name: "ok",
        },
        {
          id: 2,
          name: "cancel",
        },
      ],
      regStatuses: [
        {
          id: 0,
          value: "new",
        },
        {
          id: 1,
          value: "ready",
        },
      ],
      employees: [],
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
          text: this.$t("carPurchase.createdAt"),
          value: "created_at",
        },
        {
          text: this.$t("carPurchase.employee"),
          value: "employee.info",
        },
        {
          text: this.$t("carPurchase.department"),
          value: "department_info",
        },
        {
          text: this.$t("carPurchase.position"),
          value: "position_info",
        },
        {
          text: this.$t("carPurchase.carModel"),
          value: "car_model.name",
        },
        {
          text: this.$t("carPurchase.file"),
          value: "file_name",
        },
        {
          text: this.$t("carPurchase.comment"),
          value: "accountant_comment",
        },
        {
          text: this.$t("carPurchase.status"),
          value: "accountant_status",
        },
        {
          text: this.$t("carPurchase.accEmployee"),
          value: "accountant_employee.accInfo",
        },
        {
          text: this.$t("carPurchase.regStatus"),
          value: "registry_status",
        },
        {
          text: this.$t("carPurchase.createdBy"),
          value: "created_by",
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 150,
          align: "left",
        },
      ];
    },
  },
  methods: {
    viewFile(file_id) {
      this.fileForView = file_id;
      this.pdfViewDialog = true;
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
        .post(this.$store.state.backend_url + "api/car-purchases", {
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
      let locale = this.$i18n.locale;
      locale = locale == "uz_latin" ? "uz_latin" : "uz_cyril";
      axios
        .get(
          this.$store.state.backend_url + "api/car-purchases/get-ref/" + locale
        )
        .then((response) => {
          this.employees = response.data.employees.map((v) => ({
            value: v.id,
            text:
              v.tabel +
              " " +
              v["lastname_" + locale] +
              " " +
              (v["firstname_" + locale]
                ? v["firstname_" + locale].substr(0, 1) + ". "
                : "") +
              " " +
              (v["middlename_" + locale]
                ? v["middlename_" + locale].substr(0, 1) + ". "
                : ""),
          }));
          this.car_models = response.data.car_models.map((v) => {
            v.carInfo = v.name + " " + v.options;
            return v;
          });
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getCarCount() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/car-purchases/count")
        .then((response) => {
          this.car_count = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/car-purchases/get-excel", {
          locale: this.$i18n.locale,
          page: page,
          perPage: 1000,
        })
        .then((response) => {
          new_array = response.data;
          this.excel = this.excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getExcel(++page);
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
    newItem() {
      this.dialogHeaderText = this.$t("carPurchase.create");
      this.form = {
        id: Date.now(),
        employee_id: "",
        car_model_id: "",
        accountant_comment: "",
        accountant_status: "",
        account_employee_id: "",
        registry_status: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("carPurchase.edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save(action) {
      if (this.$refs.dialogForm.validate()) {
        this.formData = new FormData();
        this.loading = true;
        this.formData.append("file", this.file);
        this.formData.append("employee_id", this.form.employee_id);
        this.formData.append("id", this.form.id);
        this.formData.append("car_model_id", this.form.car_model_id);
        this.formData.append(
          "accountant_comment",
          this.form.accountant_comment
        );
        this.formData.append("accountant_status", action);
        axios
          .post(
            this.$store.state.backend_url + "api/car-purchases/update",
            this.formData,
            {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            }
          )
          .then((res) => {
            this.loading = false;
            this.dialog = false;
            if (res.data == 0) {
              Swal.fire({
                icon: "error",
                title: "Хатолик!",
                text: "Ушбу ходим учун ариза киритилган.",
              });
            } else {
              this.getList();
            }
            console.log(res.data == 0);
          })
          .catch((err) => {
            console.log(err);
            this.loading = false;
          });
      }
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
                "api/car-purchases/delete/" +
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
              });
              console.log(err);
            });
        }
      });
    },
  },
  mounted() {
    this.getList();
    this.getRef();
    this.getCarCount();
  },
};
</script>
