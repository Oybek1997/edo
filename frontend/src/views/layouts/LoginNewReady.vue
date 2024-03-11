<template>
  <v-app>
    <v-row class="login-layout">
      <v-col cols="6" style="background-color: #8fabdd">
        <v-row class="mx-0">
          <v-col
            cols="12"
            style="
              height: 50vh;
              display: flex;
              justify-content: center;
              align-items: center;
            "
          >
          <v-card
              class="px-0 ma-4"
              elevation="0"
              style="margin: 0 auto; border-radius: 0px; width: 650px;"
            >
              <div class="py-2">
                <span class="text_span px-4 mt-4">О компании</span>
              </div>
                <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Наименование предприятия: СП "Уз Компани"</span>
              </div>
              <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Адрес: г. Андижан, ул. индустриальная 148</span>
              </div>
              <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Основной вид деятельности: Производство автомобильных</span>
              </div>
              <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Руководитель: Солие Солижон (31.12.2023 г)</span>
              </div>
              <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Акционеры (учредители): кол-во: 2 50% "Indian" 50% "Indian" 50% "Uzauto"</span>
              </div>
            </v-card>
          </v-col>
          <v-col
            cols="12"
            style="
              height: 50vh;
              display: flex;
              justify-content: center;
              align-items: center;
            "
          >
            <v-card
              class="px-0 ma-4"
              elevation="0"
              style="margin: 0 auto; border-radius: 0px; width: 650px;"
            >
              <div class="py-2">
                <span class="text_span px-4 mt-4">Показатели</span>
              </div>
                <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Всего активы предприятия: 503 396 612 274</span>
              </div>
              <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Чистые активы предприятия: -89 950  -51 471</span>
              </div>
              <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Нераспределенный прибыль/убыток: -173 115 -133 592</span>
              </div>
              <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Остаток ТМЦ, в т.ч.: 269 244 266 659</span>
              </div>
              <v-divider style="border: 1px solid #E6E6E6;"></v-divider>
              <div class="py-2">
                <span class="text_span px-4 mt-4">Оборачиваемость ТМЦ: 89 дней 62 дней</span>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
      <v-col
        cols="6"
        style="display: flex; justify-content: center; align-items: center"
      >
        <!-- Login card qismi boshlandi -->
        <v-row class="">
          <v-col cols="12">
            <v-card
              class="elevation-5 pa-4"
              width="450"
              style="margin: 0 auto; border-radius: 15px"
            >
              <v-row>
                <v-col cols="12">
                  <h1 class="logoText_size my-0 py-0">UzAuto</h1>
                </v-col>
                <v-col cols="12" class="my-0 py-0">
                  <span class="welcomeText"
                    >Добро пожаловать в Uzautomotors</span
                  >
                </v-col>
                <v-col cols="12" class="mt-5">
                  <span class="welcomeText">Введите свои учетные данные</span>
                </v-col>
              </v-row>
              <v-card-text>
                <v-form
                  @keyup.native.enter="e_imzo ? verify() : login()"
                  name="testform"
                >
                  <v-row>
                    <!-- E-imzo page qismo boshlandi -->
                    <v-col v-show="e_imzo" cols="12" class="my-2 py-0">
                      <br />
                      <v-autocomplete
                        :label="$t('profile.select_key')"
                        v-model="eimzoKey"
                        :items="eimzoKeys"
                        @change="changeEimzoKey"
                        :error="selectError"
                        :error-messages="selectErrorMessage"
                        elevation="0"
                        item-text="search"
                        return-object
                        hide-details
                        item-value="serialNumber"
                        solo
                        class="labelInput"
                      >
                      <template v-slot:selection="{ item }">
                          <div
                            style="
                              font-size: 14px;
                              width: 100%;
                              margin: 5px 0 0;
                            "
                          >
                            <img
                              src="img/pfx.ico"
                              width="20"
                              height="20"
                              style="margin-bottom: -5px"
                            />
                            № СЕРТИФИКАТА: {{ item.serialNumber }}<br />
                            <b>ИНН:</b> {{ item.UID }}<br />
                            <b>ПИНФЛ:</b> {{ item.PINFL }}<br />
                            <b>Ф.И.О.:</b> {{ item.CN }}<br />
                            <div
                              :style="
                                item.expired ? 'color:red;' : 'color:black;'
                              "
                            >
                              Срок действия сертификата ({{
                                getDate(item.validFrom)
                              }}
                              - {{ getDate(item.validTo) }})
                            </div>
                            <div
                              style="color: red"
                              v-if="
                                !item.expired &&
                                moment(item.validTo, 'YYYYMMDD')
                                  .lang('ru')
                                  .endOf('day')
                                  .fromNow()
                                  .search('дней назад') != -1
                              "
                            >
                              Срок действия сертификата истекает
                              {{
                                moment(item.validTo, "YYYYMMDD")
                                  .lang("ru")
                                  .endOf("day")
                                  .fromNow()
                              }}
                            </div>
                            <div style="color: red" v-else-if="item.expired">
                              Срок действия сертификата истек
                              {{
                                moment(item.validTo, "YYYYMMDD")
                                  .lang("ru")
                                  .endOf("day")
                                  .fromNow()
                              }}
                            </div>
                          </div>
                        </template>
                        <template v-slot:item="{ item }">
                          <div
                            style="
                              width: 100%;
                              border-bottom: 1px solid #eee;
                              margin-bottom: 15px;
                            "
                          >
                            <img
                              src="img/pfx.ico"
                              width="20"
                              height="20"
                              style="margin-bottom: -5px"
                            />
                            № СЕРТИФИКАТА: {{ item.serialNumber }}<br />
                            <b>ИНН:</b> {{ item.UID }}<br />
                            <b>ПИНФЛ:</b> {{ item.PINFL }}<br />
                            <b>Ф.И.О.:</b> {{ item.CN }}<br />
                            <div
                              :style="
                                item.expired ? 'color:red;' : 'color:black;'
                              "
                            >
                              Срок действия сертификата ({{
                                getDate(item.validFrom)
                              }}
                              - {{ getDate(item.validTo) }})
                            </div>
                            <div
                              style="color: red"
                              v-if="
                                !item.expired &&
                                moment(item.validTo, 'YYYYMMDD')
                                  .lang('ru')
                                  .endOf('day')
                                  .fromNow()
                                  .search('дней назад') != -1
                              "
                            >
                              Срок действия сертификата истекает
                              {{
                                moment(item.validTo, "YYYYMMDD")
                                  .lang("ru")
                                  .endOf("day")
                                  .fromNow()
                              }}
                            </div>
                            <div style="color: red" v-else-if="item.expired">
                              Срок действия сертификата истек
                              {{
                                moment(item.validTo, "YYYYMMDD")
                                  .lang("ru")
                                  .endOf("day")
                                  .fromNow()
                              }}
                            </div>
                          </div>
                        </template>
                    </v-autocomplete>
                    </v-col>
                    <!-- E-imzo page qismo tugadi -->
                    <v-col v-if="!e_imzo" cols="12" class="my-2 py-0">
                      <label for="username"></label>
                      <v-text-field
                        class="input_text input_sector white"
                        autofocus
                        color="#203d5b"
                        outlined
                        dense
                        type="text"
                        autocomplete="off"
                        v-model="username"
                        :error-messages="errorlog"
                        :placeholder="$t('username')"
                      ></v-text-field>
                    </v-col>
                    <v-col v-if="!e_imzo" cols="12" class="mt-2 mb-0 py-0">
                      <v-text-field
                        class="input_text input_sector white"
                        color="#203d5b"
                        outlined
                        dense
                        type="password"
                        v-model="password"
                        :error-messages="errorpass"
                        :placeholder="$t('password')"
                        style="border: 10px soid red !important"
                      >
                      </v-text-field>
                      <v-alert
                        v-if="
                          errorAlert &&
                          this.username != '' &&
                          this.password != ''
                        "
                        dense
                        text
                        outlined
                        type="error"
                        icon="mdi-alert-outline"
                        class="caption py-1 mt-2"
                        >{{ $t("profile.incorrect_login") }}</v-alert
                      >
                    </v-col>
                    <v-col cols="6" class="my-0 py-0">
                      <v-img
                        src="img/ldap.jpg"
                        width="80"
                        class="mt-5 mr-auto"
                        contain
                        style="cursor: pointer"
                        @click="
                          e_imzo = false;
                          errorAlert = false;
                          AppLoad();
                        "
                      ></v-img>
                    </v-col>
                    <v-col cols="6" class="my-0 py-0">
                      <v-img
                        src="img/imzo.png"
                        width="100"
                        class="mt-5 ml-auto"
                        contain
                        style="cursor: pointer"
                        @click="
                          e_imzo = true;
                          errorAlert = false;
                          AppLoad();
                        "
                      ></v-img>
                    </v-col>
                    <!-- <v-col cols="6" class="my-0 py-0">
                      <v-checkbox
                        v-model="rememberMe"
                        cl
                        :label="$t('rememberMe')"
                      ></v-checkbox>
                    </v-col> -->
                    <v-col cols="12" class="mt-6 mb-6 py-0">
                      <v-btn
                        block
                        color="#3FCB5D"
                        right
                        small
                        dark
                        elevation="0"
                        @click="e_imzo ? verify() : login()"
                        style="
                          text-transform: none;
                          border-radius: 5px;
                          padding: 5px 20px;
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
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
        <!-- Login card qismi tugadi -->
        <!-- E-imzo uchun dialog qism boshlandi -->
        <v-dialog
          v-model="eimzoModal"
          @keydown.esc="eimzoModal = false"
          persistent
          max-width="800"
        >
          <v-card class="">
            <v-card-title class="headline grey lighten-2" primary-title>
              <span class="headline">{{}}</span>
              <v-spacer></v-spacer>
              <v-btn
                color="red"
                outlined
                x-small
                fab
                class
                @click="eimzoModal = false"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
            <v-img
              src="../../assets/eimzoError.jpg"
              height="500"
              contain
            ></v-img>
          </v-card>
        </v-dialog>
        <!-- E-imzo uchun dialog qism tugadi -->
      </v-col>
    </v-row>
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
      selectErrorMessage: null,
      eimzoKeys: [],
      eimzoKey: null,
      validTo: null,
      loading: false,
      errorAlert: false,
      eimzoModal: false,
      errorlog: [],
      errorpass: [],
      rememberMe: false,
      username: "",
      password: "",
      about_company: "",
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
    getDate($date) {
      let date = new Date($date);
      return (
        ("0" + date.getDate()).slice(-2) +
        "." +
        ("0" + (date.getMonth() + 1)).slice(-2) +
        "." +
        date.getFullYear()
      );
    },
    getAboutCompany() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/get/about-company")
        .then((response) => {
          this.about_company = response.data.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    changeEimzoKey() {
      let store = this.$store;
      let eimzo_key = this.eimzo_key;
      this.eimzo_username = this.eimzoKey.name;
      this.eimzo_name = this.eimzoKey.CN;
      this.eimzo_password = this.eimzoKey.serialNumber;
      if (this.eimzoKey) {
        this.selectError = false;
        this.selectErrorMessage = null;
      }
      let log = this.log;
      EIMZOClient.loadKey(
        this.eimzoKey,
        function (id) {
          store.commit("setEimzoKey", id);
          eimzo_key = id;
        },
        function (e, r) {}
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
            function (e, r) {}
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
        function (e, r) {}
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
            store.commit("setEimzoKey", id);
            eimzo_key = id;
          },
          function (e, r) {}
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
      if (this.eimzoKey && !this.eimzoKey.expired) {
        this.selectError = false;
        this.selectErrorMessage = null;
        let login = this.login;
        let swal = Swal;
        let eimzo_key = this.$store.getters.getEimzoKey();
        let selectError = this.selectError;
        let selectErrorMessage = this.selectErrorMessage;
        let eimzoWrongPassword = this.eimzoWrongPassword;
        let verified = this.$t("verified");
        let eimzo_wrong = this.$t("eimzo_wrong");
        CAPIWS.callFunction(
          {
            plugin: "pfx",
            name: "verify_password",
            arguments: [eimzo_key],
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
      } else if (this.eimzoKey && this.eimzoKey.expired) {
        this.selectError = true;
        this.selectErrorMessage = this.$t("eimzo_expired");
      } else {
        this.selectError = true;
        this.selectErrorMessage = this.$t("required");
      }
    },
    eimzoWrongPassword() {
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
          eimzo_name: this.eimzo_name,
          valid_to: this.validTo,
        })
        .then((res) => {
          this.$store.dispatch(
            "setAccessToken",
            res.data.token_type + " " + res.data.access_token
          );
          axios.defaults.headers.common = {
            Accept: "application/json",
            "Content-Type": "application/json",
            Authorization: res.data.token_type + " " + res.data.access_token,
          };
          axios
            .post(this.$store.state.backend_url + "api/users/show", {
              valid_to: this.validTo,
            })
            .then((data) => {
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
        })
        .catch((e) => {
          this.errorAlert = true;
          // }
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
    this.getAboutCompany();
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
</style>
