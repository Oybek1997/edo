<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("KPI sozlamalari") }}</span>
        <v-btn small class="ml-5" @click="kpidateshow = !kpidateshow">
          {{ 'KPI sanalari' }}
        </v-btn>
        <v-spacer></v-spacer>


        <v-btn
          v-if="$store.getters.checkPermission('kpi-settings')"
          color="#6ac82d"
          small
          dark
          @click="newItem"
        >
        {{ 'kpi biriktirish' }}
          <!-- <v-icon>mdi-plus</v-icon> -->
        </v-btn>
      </v-card-title>
      <v-data-table
        v-if="kpidateshow || true"
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :loading="loading"
        :headers="headersdate"
        :items="itemdates"
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
        single-expand
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
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
            item.id
          }}
        </template>
        <template v-slot:item.from_kpi_facts="{ item }">          
          <v-text-field
            v-if="textarea && item == itemdate"
            v-model.lazy="item.from_kpi_facts" 
            dense
            single-line
            hide-details
          ></v-text-field>
          <span v-else>
            {{
            item.from_kpi_facts
            }}
          </span>
        </template>
        <template v-slot:item.to_kpi_facts="{ item }">
          <v-text-field
            v-if="textarea && item == itemdate"
            v-model.lazy="item.to_kpi_facts" 
            dense
            single-line
            hide-details
          ></v-text-field>
          <span v-else>
            {{
              item.to_kpi_facts
            }}
          </span>
        </template>
        <template v-slot:item.from_comissions="{ item }">
          <v-text-field
            v-if="textarea && item == itemdate"
            v-model.lazy="item.from_comissions" 
            dense
            single-line
            hide-details
          ></v-text-field>
          <span v-else>
            {{
              item.from_comissions
            }}
          </span>
        </template>
        <template v-slot:item.to_comissions="{ item }">
          <v-text-field
            v-if="textarea && item == itemdate"
            v-model.lazy="item.to_comissions" 
            dense
            single-line
            hide-details
          ></v-text-field>
          <span v-else>
            {{
              item.to_comissions
            }}
          </span>
        </template>
        <template v-slot:item.quarter="{ item }">
          {{
            item.quarter + ' chorak'
          }}
        </template>
        <template v-slot:item.type="{ item }">
          {{
            item.type == 1 ? 'Kpi fact kiritish uchun' : 'Komissiya tasdiqlashi uchun'
          }}
        </template>
        <template v-slot:item.actions="{ item }">        
          <v-btn
            v-if="$store.getters.checkPermission('kpi-settings') && okdate && item == itemdate"
            color="success"
            small
            text
            @click="okeyDate(item)"
          >
          <v-icon>mdi-check-underline</v-icon>
          </v-btn>
          <v-btn
            v-if="$store.getters.checkPermission('kpi-settings')"
            color="primary"
            small
            text
            @click="updateDate(item)"
          >
          <v-icon>mdi-pencil</v-icon>
          </v-btn>
        </template>
      </v-data-table>


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
        single-expand
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
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
        <template v-slot:body.prepend>
          <tr class="py-0 my-0">
            <!-- <td class="py-0 my-0 dense"></td> -->
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense">            
              <v-autocomplete
                class="py-0"
                v-model.lazy="filter.employees"
                :items="resalution_employee"
                hide-details
                item-value="id"
                @change="getList()"
              >
                <template v-slot:selection="{ item }">{{ item.text }}</template>
                <template v-slot:item="{ item }">
                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
            </td>
            <td class="py-0 my-0 dense">
                <v-select
                  v-model.lazy="filter.quarter"
                  :items = quarter
                  hide-details
                  item-value="id"
                  item-text="text"
                  @change="getList()"
                  clearable
                ></v-select>
            </td>
            <td class="py-0 my-0 dense">
              <v-select
                v-model.lazy="filter.years"
                :items = years
                hide-details
                @change="getList()"
                clearable
              ></v-select>
            </td>
            <td class="py-0 my-0 dense">
              <v-autocomplete
                class="py-0"
                v-model.lazy="filter.department"
                :items="departments"
                hide-details
                item-value="id"
                @change="getList()"
              >
                <template v-slot:selection="{ item }">{{ item.text }}</template>
                <template v-slot:item="{ item }">
                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
            </td>
            <td class="py-0 my-0 dense">
              <v-row>
                <v-col cols="6"> <!-- v-switch uchun -->
                  <v-switch
                    class="pb-2"
                    v-model.lazy="all_facts_status"
                    @click="saveAllStatus(all_facts_status,'for_facts')"
                    color="primary"
                    hide-details
                  ></v-switch>
                </v-col>
                <v-col cols="6"> <!-- v-checkbox uchun -->
                  <v-checkbox 
                    v-model.lazy="filter.select"
                    @change="getList()"
                  ></v-checkbox>
                </v-col>
              </v-row>
            </td>
            <!-- <td class="py-0 my-0 dense">
              <v-switch
                class="pb-2"
                v-model.lazy="all_facts_status"
                @click="saveAllStatus(all_facts_status,'for_facts')"
                color="primary"
                hide-details
              ></v-switch>
              <v-checkbox 
                v-model.lazy="filter.select"
                @change="getList()"
                >
              </v-checkbox>
            </td> -->
            <td class="py-0 my-0 dense">
              <v-switch
                class="pb-2"
                v-model.lazy="all_comission_status"
                @click="saveAllStatus(all_comission_status,'for_comission')"
                color="primary"
                hide-details
              ></v-switch>
            </td>
            <td class="py-0 my-0 dense"></td>
            <!-- <td class="py-0 my-0 dense"><v-btn class="center" small>{{ 'create' }}</v-btn></td> -->
        </tr>
        </template>
        <template v-slot:item.id="{ item }">
          {{
            item.id
          }}
        </template>
        <template v-slot:item.fio="{ item }">
          {{
            item.user.employee['firstname_' + $i18n.locale] + ' '  +        
            item.user.employee['lastname_' + $i18n.locale] + ' '  +        
            item.user.employee['middlename_' + $i18n.locale]           
          }}
        </template>
        <template v-slot:item.quarter="{ item }">
          {{
            (item.kpiobjekt && item.kpiobjekt.quarter) ? item.kpiobjekt.quarter : ''
          }}
        </template>
        <template v-slot:item.years="{ item }">
          {{
            (item.kpiobjekt && item.kpiobjekt.years) ? item.kpiobjekt.years : ''
          }}
        </template>
        <template v-slot:item.dep="{ item }">
          {{
            item.kpiobjekt && item.kpiobjekt.dep ?
            item.kpiobjekt.dep['department_code'] + ' '+
            item.kpiobjekt.dep['name_'+ $i18n.locale] + ' ' +
            item.kpiobjekt.dep['id']            
            : ''
          }}
        </template>
        <template v-slot:item.for_facts="{ item }">
          <v-switch
            v-model.lazy="item.for_facts"
            @click="saveStatus(item, item.for_facts, 'for_facts')"
            color="primary"
            hide-details
          ></v-switch>
        </template>
        <template v-slot:item.for_comission="{ item }">
          <v-switch
            v-model.lazy="item.for_comission"
            @click="saveStatus(item, item.for_comission, 'for_comission')"
            color="primary"
            hide-details
          ></v-switch>
        </template>
        <!-- <template v-slot:item.department="{ item }">
          {{
            item.kpiobjekt && item.kpiobjekt.department ?
            item.kpiobjekt.department['department_code'] + ' '+
            item.kpiobjekt.department['name_'+ $i18n.locale] + ' ' +
            item.kpiobjekt.department['id']            
            : ''
          }}
        </template> -->
        <template v-slot:item.actions="{ item }">        
          <v-btn
            v-if="$store.getters.checkPermission('kpi-settings')"
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
          <v-form @keyup.native.enter="save">
            <v-row>
              <v-col cols="12">           
                <v-autocomplete
                  label="Yil"
                  v-model="form.year"
                  :items="years"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                  item-value="id"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12">             
                <v-autocomplete
                  label="Chorak"
                  v-model="form.quarter"
                  :rules="[(v) => !!v || $t('input_required')]"
                  :items="quarter"                  
                  @change="depEmp()"
                  hide-details
                  dense
                  outlined
                  item-value="value"
                  item-text="text"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" v-if="department_employee">            
                <v-autocomplete
                  label="Bo`limlar (Imzolangan / Hammasi)!"
                  v-model="form.department_id"
                  :items="departments"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                  item-text="text"
                  item-value="id"
                  class="mx-1"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" v-if="department_employee">            
                <v-autocomplete
                  label="Xodimlar"
                  v-model="form.resolution_comission_id"
                  :items="resalution_employee"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                  item-text="text"
                  item-value="id"
                  class="mx-1"
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
    <v-dialog v-model="dialogerror" width="300" hide-overlay>
      <v-card color="red" dark>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span color="red" class="headline">{{ 'error' }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialogerror = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text color="white">
          {{ $t("Bu hodimga kpi biriktirilgan") }}
          <v-progress-linear
            indeterminate
            color="error"
            class="mb-0"
          ></v-progress-linear>
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
      loading: false,
      dialogerror: false,
      search: "",
      dialog: false,
      kpidateshow: false,
      okdate: false,
      itemdate: null,
      textarea: false,
      department_employee: false,
      editMode: null,
      items: [],
      filter: {},
      all_facts_status: false,
      all_comission_status: false,
      itemdates: [],
      form: {},
      dialogHeaderText: "",
      page: 1,
      from: 0,          
      departments: [],        
      resalution_employee: [],
      quarter: [
        { text: this.$t("1-chorak"), value: 1 },
        { text: this.$t("2-chorak"), value: 2 },
        { text: this.$t("3-chorak"), value: 3 },
        { text: this.$t("4-chorak"), value: 4 },
      ],
      years: [
        moment().subtract(1, 'years').format("YYYY"),
        moment().format("YYYY"),
      ],
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      emailRules: [
        (v) =>
          !v ||
          /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
          "E-mail must be valid",
      ],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    headersdate() {
      return [
        { text: "#", value: "id", width: 30 },
        { text: "chorak", value: "quarter" },
        // { text: "type", value: "type" },

        { text: "Fakt uchun sana(boshi)", value: "from_kpi_facts" },
        { text: "Fakt uchun sana(oxiri)", value: "to_kpi_facts" },

        { text: "Tasdiqlash uchun(boshi)", value: "from_comissions" },
        { text: "Tasdiqlash uchun(oxiri)", value: "to_comissions" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 120,
          align: "center",
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("organizations-update") ||
          this.$store.getters.checkPermission("organizations-delete")
      );
    },
    headers() {
      return [
        { text: "#", value: "id", width: 30 },
        { text: "fio", value: "fio" },
        { text: "chorak", value: "quarter" , width: 80},
        { text: "yil", value: "years" , width: 60},
        { text: "bo'lim", value: "dep" },
        // { text: "department", value: "department" },
        { text: "fakt uchun", value: "for_facts", },
        { text: "kamissiya uchun", value: "for_comission" },
        // { text: this.$t("name"), value: "name_" + this.$i18n.locale },
        // { text: this.$t("user.email"), value: "email" },
        {
          // text: this.$t("actions"),
          text: "amal",
          value: "actions",
          width: 120,
          align: "center",
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("organizations-update") ||
          this.$store.getters.checkPermission("organizations-delete")
      );
    },
  },
  methods: {
    allStatus(items){
      this.all_facts_status = (items.find((v)=>v.for_facts == 1)) ? true : false;
      this.all_comission_status = (items.find((v)=>v.for_comission == 1)) ? true : false;      
    },
    saveAllStatus(status, i){
      this.items.forEach(element => {
        element[i] = status;
      });
      axios
        .post(
          this.$store.state.backend_url + "api/kpiallstatus/update",{
            items:this.items,
            status:status,
            i:i
          }
        ).then((res) => {
          this.getList();
        })
      // console.log(this.items);
    },
    saveStatus(item, status, i){
      axios
        .post(
          this.$store.state.backend_url + "api/kpistatus/update",{
            id:item.id,
            status:status,
            i:i
          }
        ).then((res) => {
          this.getList();
        })
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    updateDate(i){
      this.okdate = true
      this.textarea = true
      this.itemdate = i;
    },
    
    okeyDate(i){
      axios
        .post(
          this.$store.state.backend_url + "api/kpisettingdate/update",
          i
        )
        .then((res) => {        
            this.kpiSettingDate();
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
            this.textarea = false;
            this.okdate = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    
    resolutionEmployee() {
      axios
        .post(this.$store.state.backend_url + "api/kpi/resolution-employee", {
          language: this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale,
        })
        .then((res) => {
          this.resalution_employee = res.data.map((v) => {
            v.text = v.tabel + " " + v.firstname + " " + v.lastname;
            return v;
          });
        })
        .catch((error) => {
          console.log(error);
        });
    },
    depEmp(){
      this.department_employee = true,
      this.getDepartments()
    },
    getDepartments(e) {
      axios
        .post(this.$store.state.backend_url + "api/documents/kpi-departments", {
          locale: this.$i18n.locale,
          years: this.form.year,
          quarter:  this.form.quarter,
          type: e,
        })
        .then((res) => {
          if(e == 0){
            // this.departments = res.data;
            // console.log(this.departments);
            this.departments = res.data.map((v) => {
              v.text =
                v.department_code +
                " " +
                v.name_uz_latin +
                " " +
                (parseInt(v.ok ? v.ok : 0) + parseInt(v.cancel ? v.cancel : 0)) +
                "/" +
                (parseInt(v.ok ? v.ok : 0) +
                  parseInt(v.cancel ? v.cancel : 0) +
                  parseInt(v.notok ? v.notok : 0));
              return v;
            });
          } else {
            this.departments = res.data.departments.map((v) => {
              v.text =
                v.department_code +
                " " +
                v.name_uz_latin +
                " " +
                (parseInt(v.ok ? v.ok : 0) + parseInt(v.cancel ? v.cancel : 0)) +
                "/" +
                (parseInt(v.ok ? v.ok : 0) +
                  parseInt(v.cancel ? v.cancel : 0) +
                  parseInt(v.notok ? v.notok : 0));
              return v;
            });
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getList() {
      // console.log(this.filter);
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/kpi/kpiobjektuser", {
          filter: this.filter,
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          // console.log(response);
          let res = response.data;
          this.items = res;
          this.from = res.from;
          this.server_items_length = res.total;    
          this.allStatus( this.items);      
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    kpiSettingDate() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/kpi/kpi-setting-date", {
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          this.itemdates = response.data;
          this.from = response.data.from;
          this.server_items_length = response.data.total;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialog = true;
      if (this.$store.getters.checkPermission("kpi-settings")) {
        this.dialogHeaderText = this.$t("KPI uchun ruhsat");
        this.form = {
          id: Date.now(),
          year: [],
          quarter: [],
          resolution_comission_id: [],
          department_id: null
        };

        this.editMode = false;
      }
    },
    save() {
      axios
        .post(
          this.$store.state.backend_url + "api/kpiobjektuser/update",
          this.form
        )
        .then((res) => {
          if(res.data.status == 400){
            this.dialogerror = true;
          } else {
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
          }
        })
        .catch((err) => {
          console.log(err);
        });
    }, //document-types
    deleteItem(item) {
      if (this.$store.getters.checkPermission("kpi-settings")) {
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
                  "api/kpiobjektuser/delete/" +
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
      }
    },
  },
  mounted() {    
    this.getList();
    this.resolutionEmployee()
    this.kpiSettingDate();
    this.getDepartments(0);
  },
  created() {},
};
</script>
