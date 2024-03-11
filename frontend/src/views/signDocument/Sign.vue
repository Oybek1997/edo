<template>
  <v-app>
    <v-row class="login-layout">
      <v-col cols="12" class="mt-1">
        <v-img src="img/uzautodark.png" height="35" contain></v-img>
              <label v-show="false" id="message"></label>
              <v-form @keyup.native.enter="login" name="testform">
                <v-row>
                  <v-col v-show="e_imzo" cols="12" class="mx-2">
                    <select
                      name="key"
                      id="key"
                      @change="cbChanged(this)"
                      style="border: 1px solid black; width:400px;"
                      class="pa-2 mx-2 v-input__control d-inline"
                    ></select>
                    <v-btn
                      color="#203d5b"
                      class="d-inline"
                      dark
                      @click="verify()"
                    >
                      {{ 'Tasdiqlash' }}
                    </v-btn>
                    <label v-show="false" id="keyId"></label>
                  </v-col>
                  <v-col cols="12" class="mx-2">
                    <iframe v-if="base64" :src="'data:application/pdf;base64,'+base64" style="height:87vh;" width="99%"></iframe>
                  </v-col>
                </v-row>
              </v-form>
      </v-col>
    </v-row>
    <v-progress-circular
                        v-if="loading"
                        indeterminate
                        :width="3"
                        :size="18"
                      ></v-progress-circular>
  </v-app>
</template>

<script>
const axios = require("axios").default;
const Cookies = require("js-cookie");
import Swal from "sweetalert2";
export default {
  data() {
    return {
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
      e_imzo: true,
      keys: [],
      eimzo_username: "",
      eimzo_name: "",
      base64: null,
      eimzo_password: "",
      keyId: null,
      agreement_hash:null,
      user_id:null,
      EIMZO_MAJOR: 3,
      EIMZO_MINOR: 37,
      errorCAPIWS:
        "Ошибка соединения с E-IMZO. Возможно у вас не установлен модуль E-IMZO или Браузер E-IMZO.",
      errorBrowserWS:
        "Браузер не поддерживает технологию WebSocket. Установите последнюю версию браузера.",
      errorUpdateApp:
        'ВНИМАНИЕ !!! Установите новую версию приложения E-IMZO или Браузера E-IMZO.<br /><a href="https://e-imzo.uz/main/downloads/" role="button">Скачать ПО E-IMZO</a>',
      errorWrongPassword: "Пароль неверный."
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
                  uiShowMessage(r);
                } else {
                  // this.wsError(e);
                }
              }
            );
          }
        },
        function(e, r) {
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
          option.text = "E-IMZO kalitingizni tanlang!";
          combo.add(option);
          // combo.append(<option value="">Select</option>);
          for (var itm in items) {
            combo.append(items[itm]);
          }
          // if (firstId) {
          //   var id = document.getElementById(firstId);
          //   id.setAttribute("selected", "true");
          // }
        },
        function(e, r) {
	      console.log(e,r);
          //uiShowMessage(this.errorCAPIWS);
        }
      );
    },
    cbChanged(c) {
      document.getElementById("keyId").innerHTML = "";
      this.getUserAuth();
    },
    getUserAuth() {
      var itm = document.testform.key.value;
      let localStorage = window.localStorage;
      let eimzo_key = localStorage.getItem('eimzo_key');
      console.log(eimzo_key == "null");
      // if (eimzo_key == "null" || eimzo_key == null || eimzo_key == undefined) 
      {
        var id = document.getElementById(itm);
        var vo = JSON.parse(id.getAttribute("vo"));
        EIMZOClient.loadKey(
          vo,
          function (id) {
            localStorage.setItem('eimzo_key',id);
            //document.getElementById("keyId").innerHTML = id;
            // console.log(id);
          },
          function (e, r) {}
        );
      }

      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      if (id && id.hasAttribute("vo")) {
        var vo = JSON.parse(id.getAttribute("vo"));
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
    verify() {

      // CAPIWS.callFunction(
      //   {
      //     plugin: "pkcs7",
      //     name: "append_pkcs7_attached",
      //     arguments: [base64, eimzo_key],
      //   },
      //   function (event, data) {
      //     if (data["success"]) {
      //       documentReaction(reac, data["signer_serial_number"]);
      //       setBase64(data["pkcs7_64"]);
      //     }
      //   },
      //   function (error) {}
      // );

      let localStorage = window.localStorage;
      let keyId = localStorage.getItem('eimzo_key');
      let saveData = this.saveData;
      EIMZOClient.createPkcs7(
            keyId,
            this.base64,
            null,
            function (pkcs7) {
              console.log(pkcs7)
              CAPIWS.callFunction({
                  plugin    :"pkcs7",
                  name      :"get_pkcs7_attached_info",
                  arguments :[
                    //Ранее созданный документ PKCS#7/CMS в кодировке BASE64
                    pkcs7,
                    //Идентификатор хранилища доверенных сертификатов (полученный из фукнции других плагинов), если требуется верификация сертификатов, иначе может быть пустым
                    ''
                  ]
                },
                function(event, data){
                  console.log(data);
                  saveData(pkcs7,data);
                },
                function(error){
                  window.alert(error);
                }
              );
              // setBase64(pkcs7);
              // if (doc.status == 0) {
              //   modalDocumentReaction = false;
              //   publish();
              // } else {
              //   documentReaction(reac, vo.serialNumber);
              // }
            },
            function (e, r) {
              if (r) {
                if (r.indexOf("BadPaddingException") != -1) {
                } else {
                }
              } else {
                document.getElementById("keyId").innerHTML = "";
              }
            }
          );

    },
    saveData(eimzo_base64, pkcs7_info){
      // agreement_hash
      // https://xarid.uzautomotors.com/file_oferta_save
      axios
        .post("https://xarid.uzautomotors.com/file_oferta_save",
        {
          agreement_hash: this.agreement_hash,
          user_id: this.user_id,
          eimzo_base64: eimzo_base64,
          pdf_base64: this.base64,
          pkcs7_info: pkcs7_info,
        })
        .then((data) => {
          window.location.href = 'https://xarid.uzautomotors.com/agreement/'+this.agreement_hash;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
  },
  mounted(){
    this.agreement_hash = this.$route.params.agreement_hash;
    this.user_id = this.$route.params.user_id;
    this.AppLoad();
    // https://xarid.uzautomotors.com/file_oferta
    axios
    .get("https://xarid.uzautomotors.com/file_oferta/"+this.agreement_hash+'/'+this.user_id)
    .then((res) => {
        //  this.user_id = res.data.user_id;
      axios
        .post("https://b-edo.uzautomotors.com/api/generate-pdf-for-sign-document",
        {
          html: res.data.file
        })
        .then((data) => {
          this.base64 = data.data;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    })
    .catch((err) => {
      console.log(err);
      this.loading = false;
    });
  },
};
</script>
<style scoped>
.v-progress-circular {
  margin: 1rem;
}
</style>