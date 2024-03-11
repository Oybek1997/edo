<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Касаллик таътилини рўйхатдан ўтказиш") }}</span>
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
                  v-if="$store.getters.checkPermission('registration-period-create')"
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
                    @click="tableToExcel('table', 'Lorem Table')"
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
            ref="table"
            id="table"
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
            item-key="id"
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
            @update:sort-desc="updatePage"
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
          >
            <template v-slot:body.prepend="{ item }">
              <tr class="prepend_height">
                <td></td>
                <td>
                <v-autocomplete
                    clearable
                    v-model="filter.hospital_diagnosis_id"
                    :items="
                      hospitalDiagnoses.map(v => ({
                        text: v.text,
                        value: v.value,
                      }))
                    "
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                <v-autocomplete
                    clearable
                    v-model="filter.diagnosis_code_id"
                    :items="
                      diagnosisCodes.map(v => ({
                        text: v.name,
                        value: v.id,
                      }))
                    "
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                <v-autocomplete
                    clearable
                    v-model="filter.district_id"
                    :items="
                      districts.map(v => ({
                        text: v.name_uz_latin,
                        value: v.id,
                      }))
                    "
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                <v-text-field v-model="filter.address" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.tabel" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field
                    v-model="filter.lastname_uz_cyril"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                <v-text-field
                    v-model="filter.department_code"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                <v-text-field
                    v-model="filter.employee_department"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.illness_list_serieses" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.illness_list_numbers" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.begin_date" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.end_date" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.created_at" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.description" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.username" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td></td>
              </tr>
            </template>
            <template v-slot:item.id="{ item }">
              {{
              items
              .map(function (x) {
              return x.id;
              })
              .indexOf(item.id) + from
              }}
            </template>
            <template v-slot:item.hospital_diagnosis.name="{ item }">
              <td style="width: 200px;">
                {{ item.hospital_diagnosis.name }}
              </td>
            </template>
            <template v-slot:item.employee_department="{ item }">
              <td style="width: 200px;">
                {{ item.employee_department }}
              </td>
            </template>
            <template v-slot:item.description="{ item }">
              <td style="width: 150px;">
                {{ item.description }}
              </td>
            </template>
            <template v-slot:item.address="{ item }">
              <td style="width: 100px;">
                {{ item.address }}
              </td>
            </template>
            <template v-slot:item.created_by.username="{ item }">
              <td style="width: 50px;">
                {{ item.created_by.username }}
              </td>
            </template>
            <template v-slot:item.employee="{ item }">
              <td style="width: 100px;">
              <span v-if="$i18n.locale == 'uz_latin'">
                {{ item.employee ? item.employee.lastname_uz_cyril : ""}}
                {{ item.employee ? item.employee.firstname_uz_cyril : "" }}
              </span>
              <span v-else>
                {{ item.employee ? item.employee.firstname_uz_cyril : "" }}
                {{ item.employee ? item.employee.lastname_uz_cyril : "" }}
              </span>
              </td>
            </template>
            <template v-slot:item.district="{ item }">
              <span>
                {{ item.district.region ? item.district.region.name_uz_cyril : ""}}
                {{ item.district ? item.district.name_uz_cyril : "" }}
              </span>
            </template>
            <template v-slot:item.created_at="{ item }">{{ moment(item.created_at).format('YYYY.MM.DD [&nbsp;] HH:mm') }}</template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                v-if="$store.getters.checkPermission('registration-period-update')"
                class="pl-0 pr-2"
                color="blue"
                style="min-width: 25px"
                small
                text
                @click="editItem(item)"
              >
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn>
              <v-btn  v-if="$store.getters.checkPermission('registration-period-delete')" 
                class="pl-0 pr-2"
                color="error"
                style="min-width: 25px"
                small
                text
              @click="deleteItem(item)">
                <v-icon size="18">mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog v-model="dialog" @keydown.esc="dialog = false" persistent max-width="600">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Ходим') }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.employee_id"
                  :items="employees"
                  @change="changeEmployee"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Бўлим коди') }}</label>
                <v-text-field
                  outlined
                  class="pa-0"
                  disabled
                  v-model="form.department_code"
                  hide-details
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Бўлими') }}</label>
                <v-text-field
                  outlined
                  class="pa-0"
                  disabled
                  v-model="form.employee_department"
                  hide-details
                  dense
                ></v-text-field>
              </v-col>              
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Касаллик варақаси серияси') }}</label>
                <v-text-field
                  v-model="form.illness_list_serieses"
                  :minlength="4"
                  :maxlength="4"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Касаллик варақаси рақами') }}</label>
                <v-text-field
                  type="number"
                  v-model="form.illness_list_numbers"
                  :minlength="7"
                  :maxlength="7"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('МКБХ') }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.hospital_diagnosis_id"
                  :items="hospitalDiagnoses"
                  :item-text="'text'"
                  item-value="value"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.text"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.text"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Туман') }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.district_id"
                  :items="districts"
                  :item-text="'name_uz_cyril'"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name_uz_cyril"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name_uz_cyril"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Шифохона номи') }}</label>
                <v-text-field
                  v-model="form.address"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <v-menu                  
                  v-model="createdAtMenu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <label class="labelTitle" for>{{ $t('Бошланиш санаси') }}</label>
                    <v-text-field
                      v-model="form.begin_date"
                      :rules="[(v) => !!v || $t('input.required')]"                      
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.begin_date" @input="createdAtMenu = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <v-menu
                  v-model="createdAtMenu1"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <label class="labelTitle" for>{{ $t('Тугаш санаси') }}</label>
                    <v-text-field
                      v-model="form.end_date"
                      :rules="[(v) => !!v || $t('input.required')]"                      
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.end_date" @input="createdAtMenu1 = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Изоҳ') }}</label>
                <v-text-field
                  v-model="form.description"
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
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const moment = require("moment");
import TableToExcel from "@linways/table-to-excel";
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      moment: moment,
      momentDate: new Date(),
      loading: false,
      dialog: false,
      editMode: null,
      items: [],
      employees: [],
      hospitalDiagnoses: [],
      diagnosisCodes: [],
      districts: [],
      form: {},
      filter: {
        hospital_diagnosis_id: "",
        diagnosis_code_id: "",
        district_id: "",
        illness_list_serieses: "",
        illness_list_numbers: "",
        address: "",
        begin_date: "",
        end_date: "",
        description: "",
        created_at: "",
        username: "",
        department_code: "",
        employee_department: "",
        lastname_uz_cyril: "",
        tabel: "",
      },
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      createdAtMenu: false,
      createdAtMenu1: false,
    };
  },  
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("МКБХ"), value: "hospital_diagnosis.name", width: 200 },
        { text: this.$t("Код диагнос"), value: "hospital_diagnosis.diagnosis_code.name", width: 50 },
        { text: this.$t("Туман"), value: "district" },
        
        { text: this.$t("Шифохона номи"), value: "address", width: 100 },
        { text: this.$t("Табел"), value: "employee.tabel" },
        {
          text: this.$t("Ходим"),
          value: "employee", width: 100
        },
        { text: this.$t("Бўлим коди"), value: "department_code" },
        { text: this.$t("Бўлими"), value: "employee_department", width: 200 },
        { text: this.$t("Серияси"), value: "illness_list_serieses" },
        { text: this.$t("Рақами"), value: "illness_list_numbers" },
        { text: this.$t("Бошланиш санаси"), value: "begin_date" },
        { text: this.$t("Тугаш санаси"), value: "end_date" },
        { text: this.$t("Рўйхатга олиш санаси"), value: "created_at" },
        { text: this.$t("Изоҳ"), value: "description", width: 150 },
        { text: this.$t("Фойдаланувчи"), value: "created_by.username", width: 50 },
        {
          text: this.$t("Амал"),
          value: "actions",
          width: 80,
          align: "center"
        }
      ].filter(
        v =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("registration-period-update") ||
          this.$store.getters.checkPermission("registration-period-delete")
      );
    }
  },
  methods: {
    changeEmployee($event) {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-employee-with-staff/" +
            $event
        )
        .then(response => {
          this.form.employee_department =
            response.data.staff[0].department.name_uz_cyril;
          this.form.department_code =
            response.data.staff[0].department.department_code;
          this.form.tabel =
            response.data.tabel;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getRef() {
      let locale = this.$i18n.locale;
      locale = locale == "uz_latin" ? "uz_cyril" : "uz_cyril";
      axios
        .get(
          this.$store.state.backend_url +
            "api/staff-criticals/get-ref/" +
            locale
        )
        .then(response => {
          this.employees = response.data.map(v => ({
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
                : "")
          }));
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getRefHospitalDiagnosis() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/registration-period-illness/get-ref-hospital-diagnoses"
        )
        .then(response => {
          this.hospitalDiagnoses = response.data.map(v => ({
            value: v.id,
            text:
              v.diagnosis_code.name +
              " " +
              v.name
          }));
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getRefDiagnosisCode() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/diagnosis-code/get-ref"
        )
        .then(response => {
          this.diagnosisCodes = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getRefDistrict() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/districts/get-ref"
        )
        .then(response => {
          this.districts = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/medpunkt/registration-period-illness", {
          pagination: this.dataTableOptions,
          filter: this.filter,
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
      if (this.$store.getters.checkPermission("registration-period-create")) {
        this.dialogHeaderText = this.$t("Касаллик таътилини рўйхатдан ўтказиш");
        this.form = {
          id: Date.now()
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("registration-period-update")) {
        this.dialogHeaderText = this.$t("Таҳрирлаш");
        this.formIndex = this.items.indexOf(item);
        this.form = Object.assign({}, item);
        this.dialog = true;
        this.editMode = true;
        if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      }
    },
    save() {
      if(this.form.begin_date <= this.form.end_date){
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url +
              "api/medpunkt/registration-period-illness/update",
            Object.assign(this.form, {})
          )
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

            if(res.data == "xato"){
              Toast.fire({
              icon: "error",
              title: this.$t("Маълумот киритишда хатолик бор")
            });
            }
            else{

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation")
            });
            }
          })
          .catch(err => {
            console.log(err);

           
          });

      }
      else{
        alert('Бошланиш ёки Тугаш вақти хато киритилди');
      }
      
    },
    deleteItem(item) {
      if (this.$store.getters.checkPermission("registration-period-delete")) {
        const index = this.items.indexOf(item);
        Swal.fire({
          title: this.$t("Ушбу амални бажаришга аминмисиз?"),
          text: this.$t("Ушбу амални кейин орқага қайтариб бўлмайди"),
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: this.$t("Ха, ўчирилсин!"),
          cancelButtonText: this.$t("Бекор қилиш")
        }).then(result => {
          if (result.value) {
            axios
              .delete(
                this.$store.state.backend_url +
                  "api/medpunkt/registration-period-illness/delete/" +
                  item.id
              )
              .then(res => {
                this.getList(this.page, this.itemsPerPage);
                this.dialog = false;
                Swal.fire("Deleted!", this.$t("Маълумот ўчирилди"), "success");
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
      }
    },
    tableToExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    }
  },
  mounted() {
    this.getList();
    this.getRef();
    this.getRefDistrict();
    this.getRefDiagnosisCode();
    this.getRefHospitalDiagnosis();
  },
  created() {}
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