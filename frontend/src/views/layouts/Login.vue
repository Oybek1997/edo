<template>
  <v-app>
    <div class="d-flex justify-center pt-5" v-if="false">
      <v-alert type="info" border="left" width="400">This is a test app.</v-alert>
    </div>
    <!-- Login card qismi boshlandi -->
    <v-row class="login-layout">
      <v-col cols="12" class="mt-6">
        <v-row justify="center" class="mt-6">
          <v-card cols="4" class="elevation-12 px-6 py-3 mt-8" width="460"  style="margin: 0 auto; border-radius: 15px">
            <v-card-title primary-title class="my-2" v-if="$store.state.COMPANY_ID == 1">
              <v-img src="img/gm/logo2.png" height="70" contain></v-img>
            </v-card-title>
            <v-card-title primary-title class="my-2" v-else>
              <v-img src="img/uzautodark.png" height="35" contain></v-img>
            </v-card-title>
            <v-card-text class="px-0">
              <label v-show="false" id="message"></label>
              <v-form @keyup.native.enter="e_imzo ? verify() : login()" name="testform">
                <v-row class="mx-0">
                  <v-col v-show="e_imzo" cols="12" class="my-2 py-0 px-0">
                    <br />
                    <v-autocomplete 
                    :label='$t("profile.select_key")' 
                    v-model="eimzoKey" 
                    :items="eimzoKeys" 
                    @change="changeEimzoKey" 
                    :error="selectError" 
                    :error-messages="selectErrorMessage" 
                    item-text="search" 
                    return-object 
                    item-value="serialNumber"
                    solo
                    class="labelInput"
                    >
                      <template v-slot:selection="{ item }">
                        <div style="font-size:14px;width:100%; margin:5px 0 0;">
                          <img src="img/pfx.ico" width="20" height="20" style="margin-bottom:-5px;"></img> № СЕРТИФИКАТА: {{ item.serialNumber
                          }}<br>
                          <b>ИНН:</b> {{ item.UID }}<br>
                          <b>ПИНФЛ:</b> {{ item.PINFL }}<br> <!-- ФИЗИЧЕСКОЕ ЛИЦО -->
                          <b>Ф.И.О.:</b> {{ item.CN }}<br>
                          <div :style="item.expired ? 'color:red;' : 'color:black;'">Срок действия сертификата ({{getDate(item.validFrom)}} - {{getDate(item.validTo)}})</div>
                          <div style="color:red;" v-if="!item.expired && moment(item.validTo, 'YYYYMMDD').lang('ru').endOf('day').fromNow().search('дней назад') != -1">Срок действия сертификата истекает {{ moment(item.validTo, "YYYYMMDD").lang("ru").endOf('day').fromNow() }}</div>
                          <div style="color:red;" v-else-if="item.expired">Срок действия сертификата истек {{ moment(item.validTo, "YYYYMMDD").lang("ru").endOf('day').fromNow() }}</div>
                        </div>
                      </template>
                      <template v-slot:item="{ item }">
                        <div style="width:100%;border-bottom:1px solid #EEE; margin-bottom:15px;">
                          <img src="img/pfx.ico" width="20" height="20" style="margin-bottom:-5px;"></img> № СЕРТИФИКАТА: {{ item.serialNumber }}<br>
                          <b>ИНН:</b> {{ item.UID }}<br>
                          <b>ПИНФЛ:</b> {{ item.PINFL }}<br> <!-- ФИЗИЧЕСКОЕ ЛИЦО -->
                          <b>Ф.И.О.:</b> {{ item.CN }}<br>
                          <div :style="item.expired ? 'color:red;' : 'color:black;'">Срок действия сертификата ({{getDate(item.validFrom)}} - {{getDate(item.validTo)}})</div>
                          <div style="color:red;" v-if="!item.expired && moment(item.validTo, 'YYYYMMDD').lang('ru').endOf('day').fromNow().search('дней назад') != -1">Срок действия сертификата истекает {{ moment(item.validTo, "YYYYMMDD").lang("ru").endOf('day').fromNow() }}</div>
                          <div style="color:red;" v-else-if="item.expired">Срок действия сертификата истек {{ moment(item.validTo, "YYYYMMDD").lang("ru").endOf('day').fromNow() }}</div>
                        </div>
                      </template>
                    </v-autocomplete>
                  </v-col>
                  <v-col v-if="!e_imzo" cols="12" class="my-2 py-0 px-0">
                    <label for="username"></label>
                    <v-text-field autofocus color="#203d5b" outlined dense type="text" autocomplete="off"
                      v-model="username" :error-messages="errorlog" :placeholder="$t('username')"
                      :rules="[(v) => !!v || $t('input.required')]"></v-text-field>
                  </v-col>
                  
                  <v-col v-if="!e_imzo" cols="12" class="mt-2 mb-0 py-0 px-0">
                    <v-text-field color="#203d5b" outlined dense type="password" v-model="password" hide-details
                      :error-messages="errorpass" :placeholder="$t('password')"
                      :rules="[(v) => !!v || $t('input.required')]" style="border: 10px soid red !important">
                    </v-text-field>
                    <v-alert v-if="
                      errorAlert && this.username != '' && this.password != ''
                    " dense text outlined type="error" icon="mdi-alert-outline" class="caption py-1">{{
                      $t("profile.incorrect_login") }}</v-alert>
                  </v-col>
                  <v-col cols="12" class="ma-0 py-0 px-0 d-flex align-center">
                    <v-checkbox v-model="checkedRule" class="shrink"></v-checkbox>
                    <label for="checkboxId" class="txt_rulechecked">Shaxsga oid ma'lumotlar bilan ishlash <a @click="dialog = true">siyosati</a>dan habarim bor.</label>
                  </v-col>
                  <v-col cols="6" class="my-0 py-0">
                    <v-img src="img/ldap.jpg" width="80" class="mt-5 mr-auto" contain style="cursor: pointer" @click="
                      e_imzo = false;
                      errorAlert = false;
                      AppLoad();
                    "></v-img>
                  </v-col>
                  <v-col cols="6" class="my-0 py-0">
                    <v-img src="img/imzo.png" width="100" class="mt-5 ml-auto" contain style="cursor: pointer" @click="
                      e_imzo = true;
                      errorAlert = false;
                      AppLoad();
                    "></v-img>
                  </v-col>
                  <v-col cols="12" class="mt-0 mb-6 mt-6 py-0">
                    <v-btn
                        block
                        color="#3FCB5D"
                        right
                        normal
                        :disabled="!checkedRule"
                        elevation="0"
                        @click="e_imzo ? verify() : login()"
                        style="
                          text-transform: none;
                          border-radius: 5px;
                          padding: 5px 20px;
                          color: white;
                        "
                      >
                        {{ $t("Login") }}
                        <v-progress-circular
                          v-if="loading"
                          indeterminate
                          :width="3"
                          :size="18"
                        ></v-progress-circular>
                      </v-btn>
                  </v-col>
                  <!-- <v-col cols="12" class="mb-2 py-0">
                    <v-row>
                      <v-col style="" cols="3" class="mt-10 mb-2 py-0"></v-col>
                      <v-col style="" cols="6" class="mt-10 mb-2 py-0">
                    <v-btn style="" block color="pink" dark @click="eimzoModal = !eimzoModal">
                      {{ $t("E-imzoni topish") }}
                      <v-progress-circular v-if="loading" indeterminate :width="3" :size="18"></v-progress-circular>
                    </v-btn>
                      </v-col>
                      <v-col style="" cols="3" class="mt-10 mb-2 py-0">
                      </v-col>
                    </v-row>
                  </v-col> -->
                </v-row>
              </v-form>
            </v-card-text>
          </v-card>
        </v-row>
        <v-dialog
          v-model="eimzoModal"
          @keydown.esc="eimzoModal = false"
          persistent
          max-width="800"
        >
          <v-card class="">
            <v-card-title class="headline grey lighten-2" primary-title>
              <span class="headline">{{  }}</span>
              <v-spacer></v-spacer>
              <v-btn color="red" outlined x-small fab class @click="eimzoModal = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
            <v-img src="../../assets/eimzoError.jpg" height="500" contain></v-img>
          </v-card>
        </v-dialog>
        <!-- Shaxsga oid ma'lumotlar bilan ishlash siyosati boshlandi -->
        <v-dialog
      v-model="dialog"
      persistent
      max-width="900"
      @keydown.esc="dialog = false"
    >
      <v-card>
        <v-card-title class="text-h5">
          Shaxsga oid ma'lumotlar bilan ishlash siyosati
        </v-card-title>
        <v-card-text>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Mollitia accusantium fugiat voluptatibus inventore obcaecati repudiandae nulla facere debitis a laudantium tempora, nemo perferendis dolorem aperiam? Unde at sit nam voluptatibus saepe quis sint cumque vero iste eaque, possimus incidunt aliquid quaerat error. Quos, quis laudantium? Explicabo quia deleniti odit, ea id voluptatum dolorem aut pariatur repudiandae nihil itaque illo vero. Iusto praesentium animi fugiat nostrum numquam ea deleniti eaque commodi, modi ab nulla omnis ipsam voluptates ipsum ut tenetur, repudiandae cumque autem soluta odio velit voluptatem fuga officia atque? Illum rem suscipit maxime voluptate quisquam assumenda ab inventore amet recusandae harum. Molestias dicta voluptatum ab. Nostrum fuga, harum exercitationem repellat iste numquam reiciendis. Commodi sint incidunt natus voluptatem facilis perferendis.</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="green darken-1"
            text
            @click="dialog = false"
          >
            Yopish
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
        <!-- Shaxsga oid ma'lumotlar bilan ishlash siyosati tugadi -->

      </v-col>
    </v-row>
        <!-- Login card qismi tugadi -->
  </v-app>
</template>

<script>
const axios = require("axios").default;
const Cookies = require("js-cookie");
import Swal from "sweetalert2";
export default {
  data() {
    return {
      selectError: false,
      selectErrorMessage:null,
      eimzoKeys: [],
      eimzoKey: null,
      validTo: null,
      loading: false,
      errorAlert: false,
      eimzoModal: false,
      dialog: false,
      errorlog: [],
      errorpass: [],
      checkedRule: false,
      // errorpass: ['Another error', 'Parol yoki login xato'],
      rememberMe: false,
      username: "",
      password: "",
      darkTheme: true,
      platformName: "UzAutoMotors Workflow",
      e_imzo: false,
      keys: [],
      eimzo_username: "",
      eimzo_name: "",
      eimzo_password: "",
      EIMZO_MAJOR: 3,
      EIMZO_MINOR: 37,
      errorCAPIWS:
        "Ошибка соединения с E-IMZO. Возможно у вас не установлен модуль E-IMZO или Браузер E-IMZO.",
      errorBrowserWS:
        "Браузер не поддерживает технологию WebSocket. Установите последнюю версию браузера.",
      errorUpdateApp:
        'ВНИМАНИЕ !!! Установите новую версию приложения E-IMZO или Браузера E-IMZO.<br /><a href="https://e-imzo.uz/main/downloads/" role="button">Скачать ПО E-IMZO</a>',
      errorWrongPassword: "Пароль неверный.",
    };
  },
  methods: {
    openDialog() {
      this.showDialog = true;
    },
    getDate($date) {
      let date = new Date($date);
      return ('0'+date.getDate()).slice(-2) + '.' + ('0'+(date.getMonth() + 1)).slice(-2) + '.' + date.getFullYear();
    },
    changeEimzoKey() {
      let store = this.$store;
      let eimzo_key = this.eimzo_key;
      this.eimzo_username = this.eimzoKey.name;
      this.eimzo_name = this.eimzoKey.CN;
      this.eimzo_password = this.eimzoKey.serialNumber;
      if(this.eimzoKey){
        this.selectError = false;
        this.selectErrorMessage = null;
      }
      let log = this.log;
      EIMZOClient.loadKey(
        this.eimzoKey,
        function (id) {
          store.commit("setEimzoKey", id);
          eimzo_key = id;
          //document.getElementById("keyId").innerHTML = id;
        },
        function (e, r) { }
      );
    },
    AppLoad() {
      EIMZOClient.API_KEYS = [
        this.$store.state.EIMZO_DOMAIN,
        this.$store.state.EIMZO_API_KEY,
      ];
      let uiLoadKeys = this.uiLoadKeys;
      EIMZOClient.checkVersion(
        function (major, minor) {
          EIMZOClient.installApiKeys(
            function () {
              uiLoadKeys();
            },
            function (e, r) { }
          );
        },
        function (e, r) {
          if (r) {
          } else {
          }
        }
      );
    },
    uiLoadKeys() {
      let uiCreateItem = this.uiCreateItem;
      let changeEimzoKeys = this.changeEimzoKeys;
      EIMZOClient.listAllUserKeys(
        function (o, i) {
          return o.serialNumber;
        },
        function (itemId, vo) {
          var now = new Date();
          vo.expired = dates.compare(now, vo.validTo) > 0;
          return vo;
        },
        function (items, firstId) {
          changeEimzoKeys(items);
        },
        function (e, r) { }
      );
    },
    changeEimzoKeys(eimzoKeys) {
      this.eimzoKeys = eimzoKeys;
    },
    sign() {
      var itm = document.testform.key.value;
      if (itm) {
        var id = document.getElementById(itm);
        var vo = JSON.parse(id.getAttribute("vo"));
        var data = document.testform.data.value;
        var keyId = document.getElementById("keyId").innerHTML;
        if (keyId) {
          EIMZOClient.createPkcs7(
            keyId,
            data,
            null,
            function (pkcs7) {
              document.testform.pkcs7.value = pkcs7;
            },
            function (e, r) {
              if (r) {
                if (r.indexOf("BadPaddingException") != -1) {
                  this.uiShowMessage(this.errorWrongPassword);
                } else {
                  this.uiShowMessage(r);
                }
              } else {
                document.getElementById("keyId").innerHTML = "";
                this.uiShowMessage(this.errorBrowserWS);
              }
              if (e) this.wsError(e);
            }
          );
        } else {
          EIMZOClient.loadKey(
            vo,
            function (id) {
              document.getElementById("keyId").innerHTML = id;
              EIMZOClient.createPkcs7(
                id,
                data,
                null,
                function (pkcs7) {
                  document.testform.pkcs7.value = pkcs7;
                },
                function (e, r) {
                  if (r) {
                    if (r.indexOf("BadPaddingException") != -1) {
                      this.uiShowMessage(this.errorWrongPassword);
                    } else {
                      this.uiShowMessage(r);
                    }
                  } else {
                    document.getElementById("keyId").innerHTML = "";
                    this.uiShowMessage(this.errorBrowserWS);
                  }
                  if (e) this.wsError(e);
                }
              );
            },
            function (e, r) {
              if (r) {
                if (r.indexOf("BadPaddingException") != -1) {
                  this.uiShowMessage(this.errorWrongPassword);
                } else {
                  this.uiShowMessage(r);
                }
              } else {
                this.uiShowMessage(this.errorBrowserWS);
              }
              if (e) this.wsError(e);
            }
          );
        }
      }
    },
    getUserAuth() {
      var itm = document.testform.key.value;
      let localStorage = window.localStorage;
      // let eimzo_key = localStorage.getItem('eimzo_key');
      let store = this.$store;
      let eimzo_key = this.$store.getters.getEimzoKey();
      // console.log(eimzo_key == 'null');
      if (eimzo_key == null || eimzo_key == "null") {
        var id = document.getElementById(itm);
        var vo = JSON.parse(id.getAttribute("vo"));
        this.validTo = vo.validTo;
        EIMZOClient.loadKey(
          vo,
          function (id) {
            // localStorage.setItem('eimzo_key',id);
            store.commit("setEimzoKey", id);
            eimzo_key = id;
            //document.getElementById("keyId").innerHTML = id;
          },
          function (e, r) { }
        );
      }

      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      if (id && id.hasAttribute("vo")) {
        var vo = JSON.parse(id.getAttribute("vo"));
        this.validTo = vo.validTo;
        this.eimzo_username = vo.name;
        this.eimzo_name = vo.CN;
        this.eimzo_password = vo.serialNumber;
        // console.log(vo.name, " - ", vo.UID, " - ", vo.CN);
      } else {
        this.eimzo_username = "";
        this.eimzo_name = "";
        this.eimzo_password = "";
      }
    },
    errorAlertPush(error) {
      this.errorAlert = error;
    },
    verify() {
      if(this.eimzoKey && !this.eimzoKey.expired){
        this.selectError = false;
        this.selectErrorMessage = null;
        let login = this.login;
        let swal = Swal;
        let eimzo_key = this.$store.getters.getEimzoKey();
        let selectError = this.selectError;
        let selectErrorMessage = this.selectErrorMessage;
        let eimzoWrongPassword = this.eimzoWrongPassword;
        let verified = this.$t('verified');
        let eimzo_wrong = this.$t('eimzo_wrong');
        CAPIWS.callFunction(
          {
            plugin: "pfx",
            name: "verify_password",
            arguments: [
              //Идентификатор ключа
              eimzo_key,
            ],
          },
          function (event, data) {
            if (data.success) {
              login();
              swal.fire({
                position: "top-end",
                icon: "success",
                title: verified,
                showConfirmButton: false,
                timer: 1500,
            });
            } else {
              eimzoWrongPassword();
              swal.fire({
                position: "top-end",
                icon: "error",
                title: eimzo_wrong,
                showConfirmButton: false,
                timer: 1500,
            });
            }
          },
          function (error) {
            window.alert(error);
          }
        );
      } else if(this.eimzoKey && this.eimzoKey.expired){
        this.selectError = true;
        this.selectErrorMessage = this.$t("eimzo_expired");
       } else {
        this.selectError = true;
        this.selectErrorMessage = this.$t('required');
      }
    },
    eimzoWrongPassword(){
      this.selectError = true;
      this.selectErrorMessage = this.$t("eimzo_wrong");
    },
    login() {
      this.loading = true;
      if (this.eimzo_username != "") {
        this.username = this.eimzo_username;
        this.password = this.eimzo_password;
      }
      axios
        .post(this.$store.state.backend_url + "oauth/token", {
          grant_type: "password",
          client_id: "4",
          client_secret: "9ahPAmLUe2PG3uo38HsdUwpfMxDOpU6ueDucV7XH",
          username: this.username,
          password: this.password,
          // eimzo_username: this.eimzo_username,
          eimzo_name: this.eimzo_name,
          // eimzo_password: this.eimzo_password,
          valid_to: this.validTo,
        })
        .then((res) => {
          // Cookies.set(
          //   "access_token",
          //   res.data.token_type + " " + res.data.access_token
          // );
          this.$store.dispatch(
            "setAccessToken",
            res.data.token_type + " " + res.data.access_token
          );
          axios.defaults.headers.common = {
            Accept: "application/json",
            "Content-Type": "application/json",
            Authorization: res.data.token_type + " " + res.data.access_token,
          };
          // setTimeout(function(){
          axios
            .post(this.$store.state.backend_url + "api/users/show", { valid_to: this.validTo })
            .then((data) => {
              // console.log(data);
              let user = data.data;
              let permissions = data.data.roles
                .reduce(
                  (accumulator, currentValue) =>
                    accumulator.concat(currentValue.permissions),
                  []
                )
                .map((v) => v.name);
              data.data.permissions.forEach((element) => {
                permissions.push(element.name);
              });
              let roles = data.data.roles;
              user.roles = null;
              this.$store.dispatch("setUser", data.data);
              this.$store.dispatch("setPermissions", permissions);
              this.$store.dispatch("setRole", roles);
              if (user.eimzo_username) this.$router.push("/");
              else this.$router.push("/personcontrol/profile/" + user.employee_id);
              this.$store.dispatch("setRedirectUrl", null);             
            })
            .catch((e) => {
              this.loading = false;
            });
          // }, 1000);
        })
        .catch((e) => {
          // if(this.username != '' && this.password != ''){
          this.errorAlert = true;
          // }
          // console.error(e);
          this.loading = false;
        });
    },
  },
  watch: {
    e_imzo(value) {
      this.username = "";
      this.password = "";
      this.eimzo_username = "";
      this.eimzo_password = "";
    },
  },
  mounted() {
    this.eimzoKey = null;
  },
};
</script>

<style scoped>
.v-progress-circular {
  margin: 1rem;
}
.logoText_size {
  color: #163e72;
  font-family: Inter;
  font-size: 32px;
  font-style: normal;
  font-weight: 600;
  line-height: normal;
}
.welcomeText {
  color: #000;
  font-family: Inter;
  font-size: 16px;
  font-style: normal;
  font-weight: 500;
  line-height: normal;
}
.form-add_employee .input_text {
  border: 1px solid #dce5ef !important;
  border-radius: 1px;
}
.input_text {
  border: 1px solid #dce5ef !important;
  border-radius: 5px;
  height: 40px;
}
.text_span {
  color: #476887;
  font-family: Inter;
  font-size: 14px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
}
.txt_rulechecked {
  color: #212529;
  font-size: 12px;
  font-family: "Inter";
  font-weight: bold;
}
</style>