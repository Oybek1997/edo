<template>
  <v-app>
    <div class="d-flex justify-center pt-5" v-if="false">
      <v-alert type="info" border="left" width="400"
        >This is a test app.</v-alert
      >
    </div>
    <v-row class="login-layout">
      <v-col cols="12" class="mt-6">
        <v-row justify="center" class="mt-6">
          <v-card cols="4" class="elevation-12 pa-4 mt-8" width="450">
            <v-card-title
              primary-title
              class="my-2"
              v-if="$store.state.COMPANY_ID == 1"
            >
              <v-img src="img/gm/logo.jpg" height="70" contain></v-img>
            </v-card-title>
            <v-card-title primary-title class="my-2" v-else>
              <v-img src="img/uzautodark.png" height="35" contain></v-img>
            </v-card-title>
            <v-card-text>
              <label v-show="false" id="message"></label>
              <v-form @keyup.native.enter="login" name="testform">
                <v-row>
                  <v-col v-show="e_imzo" cols="12" class="my-2 py-0">
                    {{ $t("profile.select_key") }}
                    <br />
                    <select
                      name="key"
                      id="key"
                      @change="cbChanged(this)"
                      style="border: 1px solid black"
                      class="pa-2 v-input__control"
                    ></select>
                    <br />
                    <v-alert
                      v-if="errorAlert"
                      dense
                      text
                      outlined
                      type="error"
                      icon="mdi-alert-outline"
                      class="caption py-1"
                      >{{ $t("profile.incorrect_login") }}</v-alert
                    >
                    <label v-show="false" id="keyId"></label>
                  </v-col>
                  <v-col v-if="!e_imzo" cols="12" class="my-2 py-0">
                    <label for="username"></label>
                    <v-text-field
                      autofocus
                      color="#203d5b"
                      outlined
                      dense
                      type="text"
                      autocomplete="off"
                      v-model="username"
                      :error-messages="errorlog"
                      :placeholder="$t('username')"
                      :rules="[(v) => !!v || $t('input.required')]"
                    ></v-text-field>
                  </v-col>
                  <v-col v-if="!e_imzo" cols="12" class="mt-2 mb-0 py-0">
                    <v-text-field
                      color="#203d5b"
                      outlined
                      dense
                      type="password"
                      v-model="password"
                      :error-messages="errorpass"
                      :placeholder="$t('password')"
                      :rules="[(v) => !!v || $t('input.required')]"
                      style="border: 10px soid red !important"
                    ></v-text-field>
                    <v-alert
                      v-if="
                        errorAlert && this.username != '' && this.password != ''
                      "
                      dense
                      text
                      outlined
                      type="error"
                      icon="mdi-alert-outline"
                      class="caption py-1"
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
                  <v-col cols="6" class="my-0 py-0">
                    <v-checkbox
                      v-model="rememberMe"
                      cl
                      :label="$t('rememberMe')"
                    ></v-checkbox>
                  </v-col>
                  <v-col cols="12" class="mt-0 mb-6 py-0">
                    <v-btn
                      block
                      color="#203d5b"
                      dark
                      @click="e_imzo ? verify() : login()"
                    >
                      {{ $t("login") }}
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
        </v-row>
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
      validTo: null,
      loading: false,
      errorAlert: false,
      errorlog: [],
      errorpass: [],
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
    AppLoad() {
      EIMZOClient.API_KEYS = [
        this.$store.state.EIMZO_DOMAIN,
        this.$store.state.EIMZO_API_KEY,
      ];
      this.uiLoading();
      let EIMZO_MAJOR = this.EIMZO_MAJOR;
      let EIMZO_MINOR = this.EIMZO_MINOR;
      let uiLoadKeys = this.uiLoadKeys;
      let uiShowMessage = this.uiShowMessage;
      EIMZOClient.checkVersion(
        function (major, minor) {
          var newVersion = EIMZO_MAJOR * 100 + EIMZO_MINOR;
          var installedVersion = parseInt(major) * 100 + parseInt(minor);
          if (installedVersion < newVersion) {
            this.uiUpdateApp();
          } else {
            EIMZOClient.installApiKeys(
              function () {
                uiLoadKeys();
              },
              function (e, r) {
                if (r) {
                  uiShowMessage(r);
                } else {
                  // this.wsError(e);
                }
              }
            );
          }
        },
        function (e, r) {
          if (r) {
            uiShowMessage(r);
          } else {
            this.uiNotLoaded(e);
          }
        }
      );
    },
    uiShowMessage(message) {
      alert(message);
    },
    uiLoading() {
      var l = document.getElementById("message");
      l.innerHTML = "Загрузка ...";
      l.style.color = "red";
    },
    uiNotLoaded(e) {
      var l = document.getElementById("message");
      l.innerHTML = "";
      if (e) {
        this.wsError(e);
      } else {
        this.uiShowMessage(this.errorBrowserWS);
      }
    },
    uiUpdateApp() {
      var l = document.getElementById("message");
      l.innerHTML = this.errorUpdateApp;
    },
    uiLoadKeys() {
      this.uiClearCombo();
      let uiCreateItem = this.uiCreateItem;
      let uiShowMessage = this.uiShowMessage;
      EIMZOClient.listAllUserKeys(
        function (o, i) {
          var itemId = "itm-" + o.serialNumber + "-" + i;
          return itemId;
        },
        function (itemId, v) {
          return uiCreateItem(itemId, v);
        },
        function (items, firstId) {
          var combo = document.testform.key;
          var option = document.createElement("option");
          option.text = "select";
          combo.add(option);
          // combo.append(<option value="">Select</option>);
          // console.log(items);
          for (var itm in items) {
            // console.log(!JSON.parse(vo).expired);
            var vo = items[itm].getAttribute("vo");
            if (!JSON.parse(vo).expired) {
              combo.append(items[itm]);
            }
            // combo.append(items[itm]);
          }
          // if (firstId) {
          //   var id = document.getElementById(firstId);
          //   id.setAttribute("selected", "true");
          // }
        },
        function (e, r) {
          console.log(e, r);
          //uiShowMessage(this.errorCAPIWS);
        }
      );
    },
    cbChanged(c) {
      document.getElementById("keyId").innerHTML = "";
      this.$store.dispatch("setUser", null);
      this.$store.dispatch("setEimzoKey", null);
      this.$store.dispatch("setPermissions", null);
      this.$store.dispatch("setRole", null);
      this.$store.dispatch("setAccessToken", null);
      this.getUserAuth();
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
    wsError(e) {
      if (e) {
        this.uiShowMessage(this.errorCAPIWS + " : " + e);
      } else {
        this.uiShowMessage(this.errorBrowserWS);
      }
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
      let login = this.login;
      let swal = this.Swal;

      // if (!Cookies.get("eimzo_key")) {
      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      var vo = JSON.parse(id.getAttribute("vo"));
      let localStorage = window.localStorage;
      // let eimzo_key = localStorage.getItem('eimzo_key');
      let eimzo_key = this.$store.getters.getEimzoKey();
      if (eimzo_key == null || eimzo_key == "null") {
        EIMZOClient.loadKey(
          vo,
          function (id) {
            // Cookies.set("eimzo_key", id, { expires: 1 / 4 });
            // localStorage.setItem('eimzo_key', id);
            this.$store.dispatch("setEimzoKey", id);
            eimzo_key = id;
          },
          function (e, r) {}
        );
      }
      let errorAlertPush = this.errorAlertPush;
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
          console.log([event, data]);
          if (data.success) {
            login();
          } else {
            errorAlertPush(true);
          }
          //   Swal.fire({
          //     position: "top-end",
          //     icon: "error",
          //     title: "Wrong E-IMZO password.",
          //     showConfirmButton: false,
          //     timer: 1500,
          // });
        },
        function (error) {
          window.alert(error);
        }
      );
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
          eimzo_username: this.eimzo_username,
          eimzo_name: this.eimzo_name,
          eimzo_password: this.eimzo_password,
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
            .post(this.$store.state.backend_url + "api/users/show",{valid_to:this.validTo})
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
};
</script>

<style scoped>
.v-progress-circular {
  margin: 1rem;
}
</style>
