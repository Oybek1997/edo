<template style="margin-top: 0px;">
  <div class="fullHeight" style="margin-top: -47px;">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef; margin-top: -47px;"
      elevation="0"
    >    
      <v-card-title class="px-4 py-3">
        <span class="headerTitle">{{ $t('SOZLAMARLAR') }}</span>
      </v-card-title>
      <v-card 
        class="mx-5 mt-2 mb-5 pa-5"
        style="border-radius: 10px; border: 1px solid #dce5ef; margin-top: -47px;"
        elevation="0"
      >
        <v-row class="mx-0">
          <!-- <v-col class="ma-0 pa-0" md="1">
            <span style="color:#000; font-size:14px; vertical-align: -webkit-baseline-middle;" class="font-weight-bold">
                Til sozlamalari:
            </span>            
          </v-col>
          <v-col class="ma-0 py-0 pa-0" md="11"> 
            <v-menu offset-y>
              <template v-slot:activator="{ on }">
                <v-btn text outlined small v-on="on">
                    <v-icon left>mdi-flag</v-icon>                
                    {{ languages[$i18n.locale] }}
                </v-btn>
              </template>
              <v-list>
                <v-list-item
                    v-for="(item, index) in locales"
                    :key="index"
                    @click="setLocale(item.value)"
                >
                    <v-list-item-title>{{ item.text }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-col> -->
          <v-col class="ma-0 pa-0" md="1" style="display: flex; align-items: center;">
            <span style="color:#000; font-size:14px; vertical-align: -webkit-baseline-middle;" class="font-weight-bold">
                Til sozlamalari:
            </span> 
          </v-col>
          <v-col class="ma-0 py-0 pa-0" md="10" style="display: flex; align-items: center;">              
            <v-radio-group
              class="ma-0 pa-0 language_radio setting_title"
              v-model="row"
              row
              hide-details
            >
              <div v-for="(item) in locales" class="mr-5" style="display: flex; align-items: center;">
                <v-radio
                  :value="item.value"
                  @change="setLocale(item.value)"
                >
                <template v-slot:label>
                  <img :src="item.img" style="width: 20px; height:20px" alt="Uzbekistan">
                  <span style="margin-right: 8px;margin-left: 5px;">{{ item.text }}</span>
                </template>

                </v-radio>
              </div>
            </v-radio-group>
          </v-col>
        </v-row>
      </v-card>
      <v-card 
        class="ma-5 pa-5"
        style="border-radius: 10px; border: 1px solid #dce5ef;"
        elevation="0"
      >
        <v-row class="mx-0">
          <v-col class="ma-0 pa-0" md="1">
            <span style="color:#000; font-size:14px; vertical-align: -webkit-baseline-middle;" class="font-weight-bold">
                E-imzo:
            </span>            
          </v-col>
          <v-col class="ma-0 py-0 pa-0" md="2">
            <span class="ma-0 pa-0" fab dark x-small>
              <!-- v-if="employee && user && employee.id == user.employee_id" -->
              <img
                src="img/imzo.png"
                height="25"
                style="cursor: pointer; border: 2px solid #faa"
                @click="eImzoDialog = true"
              />
              
            </span>
          </v-col>
          <v-col class="ma-0 pa-0" md="6">
            <span style="color:#000; font-size:14px; vertical-align: -webkit-baseline-middle;" class="font-weight-bold">
              {{ eimzoName[0] }} ({{ eimzoName[1] }})
            </span>            
          </v-col>
        </v-row>
      </v-card>
      <v-card 
        class="ma-5 pa-0"
        style="border-radius: 0px; border: 0px solid #dce5ef;"
        elevation="0"
      >
        <v-list class="py-0 px-3 list-group" style="background-color: #EDF2F7; border-radius: 0px;">
          <v-list-item style="min-height: 38px;">
            <v-list-item-content class="py-0 list-text">
              <v-list-item-title>
                <span class="setting_title">Общие</span>
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
        <v-row class="mx-0">
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Общие</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Счета</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Оповещения</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Импорт</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Экспорт</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Интеграции</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Вебхуки</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
        </v-row>
      </v-card>
      <v-card 
        class="ma-5 pa-0"
        style="border-radius: 0px; border: 0px solid #dce5ef;"
        elevation="0"
      >
        <v-list class="py-0 px-3 list-group" style="background-color: #EDF2F7; border-radius: 0px;">
          <v-list-item style="min-height: 38px;">
            <v-list-item-content class="py-0 list-text">
              <v-list-item-title>
                <span class="setting_title">Основные</span>
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
        <v-row class="mx-0">
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Главная</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Календари</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Локации</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Подразделения</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Департаменты</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Должности</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Политики праздников</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Виды занятости</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
          <v-col class="ma-0 pa-0" md="4">
            <v-list class="py-0 px-3 list-group" style="">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon size="22" color="#00B950">mdi-tune</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span class="setting_title">Пол</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>        
          </v-col>
        </v-row>
      </v-card>
    </v-card>
    <v-dialog
      v-model="eImzoDialog"
      scrollable
      persistent
      @keydown.esc="eImzoDialog = false"
      :overlay="false"
      max-width="650px"
      transition="dialog-transition"
    >
      <v-card style="height: 100%; border-radius:10px; background-color: #fffffa;">
        <v-card-title class="headline grey lighten-2 py-3" primary-title>
          {{ $t("profile.choose_key") }}
          <v-spacer></v-spacer>
          <v-btn color="red" dark x-small fab class @click="eImzoDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-row class="ma-0">
          <v-col cols="12" md="12" class="pa-4">
            <v-card-text class="pa-0">
              <label v-show="false" id="message"></label>
              <v-form name="testform">
                <v-row>
                  <v-col cols="12" class="my-0 py-0">
                    {{ $t("profile.select_key") }}
                    <br />
                    <select
                      name="key"
                      @change="cbChanged(this)"
                      style="border: 1px solid black"
                      class="pa-2 v-input__control"
                    ></select>
                    <br />
                    <label v-show="false" id="keyId"></label>
                  </v-col>
                  <v-col cols="12" class="mt-0 mb-6 py-0">
                    <v-btn block color="#203d5b" dark @click="push()">
                      {{ $t("profile.push") }}
                      <v-progress-circular v-if="loading" indeterminate :width="3" :size="18"></v-progress-circular>
                    </v-btn>
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
          </v-col>
        </v-row>
        <v-card-actions>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
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
      selected: null,
      loading: false,
      index: 0,
      staticInfo:[            
      ],
      row: null,
      eImzoDialog: false,
      eimzo_username: "",
      eimzo_name: "",
      eimzo_password: "",
      eimzo_inn: "",
      eimzo_given_date: "",
      eimzo_expere_date: "",
      dialog: false,
      editMode: null,
      currentDate: new Date().toISOString().substr(0, 7),
      newMonth: new Date().getMonth() + 1,
      menu: false,
      items: ['Ozbekcha', 'Русский', 'English'],
      itemsDate: ['(GMT-11:00) Pacific/Midway', '(GMT+03:00) Europe/Moscow', 'Africa/Lagos'],
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      eimzoName: '',
      server_items_length: -1,
      languages: {},
      locales: [],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
      { text: "№", value: "id", align: "center", width: 30 },

      ];
    }
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    setLocale: function (arg) {
      this.$i18n.locale = arg;
      
      this.$store.dispatch("setLocale", arg);
      location.reload();
    },
    updatePerPage($event) {
      this.getList();
    },
    weekend(date){
      return ["0","6"].includes(moment(date).format('d'));
    },
    getList() {
      return 'Salom'
    },
    getUser(id) {
      axios
        .get(
          this.$store.state.backend_url + "api/employees/show-employee/" + id
        )
        .then(res => {
          this.employee = res.data;
          this.nationality = res.data.nationality;
          this.company = res.data.company;
          this.employee_addresses = res.data.employee_addresses;
          this.employee_official_document = res.data.employee_official_document;
          this.employee_phones = res.data.employee_phones;
          this.staff = res.data.staff[0];
          this.tariff_scale = res.data.employee_staff[0].tariff_scale;
          this.roles = res.data.user && res.data.user.roles ? res.data.user.roles : [];
          this.employee_relatives = res.data.employee_relative;
          this.employee_work_histories = res.data.employee_work_histories;
          this.employee_education_histories = res.data.employee_education_histories;
          this.employee_id = res.data.user ? res.data.user.employee_id : 0;
          this.employee_tabel = res.data.tabel;
          this.myTable = res.data.tabel;
          this.staffHistory(this.employee_id);
          console.log(this.staff);
        })

        .catch(e => {
          console.error(e);
        });
    },
    getAvatar(id) {
      axios
        .get(this.$store.state.backend_url + "api/employees/get-avatar/" + id)
        .then(response => {
          this.employee.base64 = response.data;
          this.base64 = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getUserDocument() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/user-documents/" +
            this.$route.params.id
        )
        .then(res => {
          this.allDocuments = res.data;
        })
        .catch(e => {
          console.error(e);
        });
    },
    getUserEimzo() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/user-eimzo-info/"
        )
        .then(res => {
          this.eimzoName = res.data;
        })
        .catch(e => {
          console.error(e);
        });
    },
    setDefaultImage(event) {
      event.target.src = require("../../assets/User-Default.jpg");
    },
    cbChanged(c) {
      if (document.getElementById("keyId"))
        document.getElementById("keyId").innerHTML = "";
      this.getUserAuth();
    },
    getVacations() {
      let user = this.$store.getters.getUser();
      let tabel;
      if (this.selected_employee) {
        tabel = this.selected_employee;
      } else {
        tabel = user.employee.tabel;
      }
      this.loading = true;
      this.test = {
        tabel: [tabel],
        month1: "2022",
        month2: "2023"
      };
      axios
        .post(
          this.$store.state.backend_url +
            "api/document-templates/used-vacation-days",
          { filter: this.test }
        )
        .then(response => {
          if (response != 2) {
            this.vacations = response.data;
            this.loading = false;
          } else {
            this.vacations = null;
          }
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getUserAuth() {
      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      if (id && id.hasAttribute("vo")) {
        var vo = JSON.parse(id.getAttribute("vo"));
        this.eimzo_username = vo.name;
        this.eimzo_name = vo.CN;
        this.eimzo_password = vo.serialNumber;
        this.eimzo_inn = vo.TIN;
        this.eimzo_given_date = vo.validFrom;
        this.eimzo_expere_date = vo.validTo;
      } else {
        this.eimzo_username = "";
        this.eimzo_name = "";
        this.eimzo_password = "";
        this.eimzo_inn = "";
        this.eimzo_given_date = "";
        this.eimzo_expere_date = "";
      }
    },
    push() {
      axios
        .post(this.$store.state.backend_url + "api/users/eimzo-push", {
          eimzo_username: this.eimzo_username,
          eimzo_name: this.eimzo_name,
          eimzo_password: this.eimzo_password,
          eimzo_inn: this.eimzo_inn,
          eimzo_given_date: this.eimzo_given_date,
          eimzo_expere_date: this.eimzo_expere_date
        })
        .then(res => {
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Your E-IMZO has been saved",
            showConfirmButton: false,
            timer: 1500
          });
        })
        .catch(err => {});
      this.eImzoDialog = false;
    },
    AppLoad() {
      // EIMZOClient.API_KEYS = [
      //   "edo.uzautomotors.com",
      //   "79DC56F42765A0017C31309DB9600EA924684ED023A8079460454768331626AB94CFFF8FC2D4007976D4A6C56F11D56DFA962276DC54AE8C0F39E8A8EBDFA10B"
      // ];
      EIMZOClient.API_KEYS = [
        this.$store.state.EIMZO_DOMAIN,
        this.$store.state.EIMZO_API_KEY
      ];
      this.uiLoading();
      let EIMZO_MAJOR = this.EIMZO_MAJOR;
      let EIMZO_MINOR = this.EIMZO_MINOR;
      let uiLoadKeys = this.uiLoadKeys;
      EIMZOClient.checkVersion(
        function(major, minor) {
          var newVersion = EIMZO_MAJOR * 100 + EIMZO_MINOR;
          var installedVersion = parseInt(major) * 100 + parseInt(minor);
          if (installedVersion < newVersion) {
            this.uiUpdateApp();
          } else {
            EIMZOClient.installApiKeys(
              function() {
                uiLoadKeys();
              },
              function(e, r) {
                if (r) {
                  this.uiShowMessage(r);
                } else {
                  this.wsError(e);
                }
              }
            );
          }
        },
        function(e, r) {
          if (r) {
            this.uiShowMessage(r);
          } else {
            this.uiNotLoaded(e);
          }
        }
      );
    },
    uiLoading() {
      var l = document.getElementById("message");
      if (l) {
        l.innerHTML = "Загрузка ...";
        l.style.color = "red";
      }
    },
    uiClearCombo() {
      var combo = document.testform.key;
      combo.length = 0;
    },
    uiCreateItem(itmkey, vo) {
      var now = new Date();
      vo.expired = dates.compare(now, vo.validTo) > 0;
      var itm = document.createElement("option");
      itm.value = itmkey;
      itm.text = vo.CN;
      if (!vo.expired) {
      } else {
        itm.style.color = "gray";
        itm.text = itm.text + " (срок истек)";
      }
      itm.setAttribute("vo", JSON.stringify(vo));
      itm.setAttribute("id", itmkey);
      return itm;
    },
    uiUpdateApp() {
      var l = document.getElementById("message");
      if (l) l.innerHTML = this.errorUpdateApp;
    },
    uiLoadKeys() {
      this.uiClearCombo();
      let uiCreateItem = this.uiCreateItem;
      let uiShowMessage = this.uiShowMessage;
      let eimzo_password = this.employee.eimzo_password;
      let getUserAuth = this.getUserAuth;
      EIMZOClient.listAllUserKeys(
        function(o, i) {
          var itemId = "itm-" + o.serialNumber + "-" + i;
          return itemId;
        },
        function(itemId, v) {
          return uiCreateItem(itemId, v);
        },
        function(items, firstId) {
          var combo = document.testform.key;
          var option = document.createElement("option");
          option.text = "select";
          combo.add(option);
          // combo.append(<option value="">Select</option>);
          for (var itm in items) {
            var vo = items[itm].getAttribute("vo");
            if (!JSON.parse(vo).expired) {
              combo.append(items[itm]);
            }
            if (vo.includes(eimzo_password)) {
              items[itm].setAttribute("selected", "true");
              getUserAuth();
            }
          }
          // if (firstId) {
          //   var id = document.getElementById(firstId);
          //   id.setAttribute("selected", "true");
          // }
        },
        function(e, r) {
          // uiShowMessage(this.errorCAPIWS);
        }
      );
    },
    uiShowMessage(message) {
      alert(message);
    },
    wsError(e) {
      if (e) {
        this.uiShowMessage(this.errorCAPIWS + " : " + e);
      } else {
        this.uiShowMessage(this.errorBrowserWS);
      }
    },
    uiNotLoaded(e) {
      var l = document.getElementById("message");
      if (l) {
        l.innerHTML = "";
        if (e) {
          this.wsError(e);
        } else {
          this.uiShowMessage(this.errorBrowserWS);
        }
      }
    }
},
  watch: {
    eImzoDialog(value) {
      if (value) this.AppLoad();
    }
  },
  mounted() {
    this.getUserEimzo();
    this.user = JSON.parse(window.localStorage.getItem("user"));
    if (this.$route.params.id) {
      this.getUser(this.$route.params.id);
      this.getAvatar(this.$route.params.id);
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-pdf/" +
            this.$route.params.id +
            "/" +
            this.$i18n.locale
        )
        .then(res => {
          this.pdfBase64 = res.data;
        })
        .catch(e => {
          console.error(e);
        });
    }
    this.row = this.$i18n.locale;
    this.getUserDocument();
    this.getVacations();
    this.getList();
    this.languages =
    this.$store.state.COMPANY_ID == 1
        ? {
            uz_latin: `O'zbekcha`,
            uz_cyril: `English`,
            ru: `Русский`,
        }
        : {
            uz_latin: `O'zbekcha`,
            uz_cyril: `Ўзбекча`,
            ru: `Русский`,
        };
    this.locales =
    this.$store.state.COMPANY_ID == 1
        ? [
            { value: `uz_latin`, text: `O'zbekcha`, img: "img/svg/uzbekistan.png"},
            { value: `uz_cyril`, text: `English`, img: "img/svg/USA.png"},
            { value: `ru`, text: `Русский`, img: "img/svg/Russia.png" },
        ]
        : [
            { value: `uz_latin`, text: `O'zbekcha` },
            { value: `uz_cyril`, text: `Ўзбекча` },
            { value: `ru`, text: `Русский` },
        ];
  }
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
.setting_title {
  color: #000;
  font-size: 14px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>