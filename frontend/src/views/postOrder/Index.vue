<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <v-file-input
              v-model="file"
              append-icon="mdi-microsoft-excel"
              color="#1f6e43"
              counter
              outlined
              dense
              hide-details
              label="Fayl yuklang"
              show-size
              truncate-length="50"
              @change="changeFile"
            ></v-file-input>
        <v-spacer></v-spacer>
        <h1 v-if="new_count+success_count > 0">
          {{ new_count+'/'+(new_count+success_count)}}
        </h1>
        <v-spacer></v-spacer>
        <v-autocomplete
          append-icon="mdi-magnify"
          class="mr-2"
          :items="parts"
          item-value="part"
          item-text="part"
          v-model="part"
          :label="$t('Part')"
          @change="getList(true)"
          outlined
          dense
          single-line
          hide-details
        ></v-autocomplete>
        <v-btn color="success" @click="eimzoDialog = true">Отправить</v-btn>
      </v-card-title>
      <v-card-text>
        <v-simple-table dense>
          <template v-slot:default>
            <thead>
              <tr>
                <th style="">#</th>
                <th style="min-width: 50px; white-space: normal">Diller</th>
                <th style="">Client</th>
                <th style="">Client Region</th>
                <th style="">Address</th>
                <th style="">Client type</th>
                <th style="">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, key) in listItem" :key="key">
                <td>{{ key + 1 }}</td>
                <td style="min-width: 50px; white-space: normal">
                  {{ item.diller }}
                </td>
                <td style="min-width: 50px; white-space: normal">
                  {{ item.client }}
                </td>
                <td style="min-width: 50px; white-space: normal">
                  {{ item.client_region }}
                </td>
                <td style="min-width: 50px; white-space: normal">
                  {{ item.address }}
                </td>
                <td style="min-width: 50px; white-space: normal">
                  {{ item.client_type }}
                </td>
                <td style="min-width: 50px; white-space: normal">
                  <v-icon v-if="item.status == 0">mdi-check</v-icon>
                  <v-icon v-else-if="item.status == 2" color="green">mdi-check-all</v-icon>
                  <v-progress-circular
                    v-else
                    :size="20"
                    color="primary"
                    indeterminate
                  ></v-progress-circular>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
    </v-card>
    <v-dialog
      v-model="eimzoDialog"
      persistent :overlay="false"
      max-width="500px"
      transition="dialog-transition"
    >
    <v-card>
            <v-card-title primary-title>
                title
                <v-spacer></v-spacer>
                <v-icon @click="eimzoDialog = false;">mdi-close</v-icon>
            </v-card-title>
            <v-card-text>
                <v-autocomplete v-model="eimzoKey" :items="eimzoKeys" @change="changeEimzoKey" item-text="search" outlined>
                    <template v-slot:selection="{ item }">
                      <div>
                      <v-list-item-content>
                            <v-list-item-title>
                                <img src="img/pfx.ico" width="20" height="20"></img> № СЕРТИФИКАТА: {{ item.serialNumber
                                }}<br>
                                <b>ИНН:</b> {{ item.UID }}<br>
                                <b>ПИНФЛ:</b> {{ item.PINFL }}<br> <!-- ФИЗИЧЕСКОЕ ЛИЦО -->
                                <b>Ф.И.О.:</b> {{ item.CN }}
                            </v-list-item-title>
                          </v-list-item-content>
                        </div>
                    </template>
                    <template v-slot:item="{ item }">
                        <v-list-item-content>
                          <div>
                            <v-list-item-title>
                                <img src="img/pfx.ico" width="20" height="20"></img> № СЕРТИФИКАТА: {{ item.serialNumber
                                }}<br>
                                <b>ИНН:</b> {{ item.UID }}<br>
                                <b>ПИНФЛ:</b> {{ item.PINFL }}<br> <!-- ФИЗИЧЕСКОЕ ЛИЦО -->
                                <b>Ф.И.О.:</b> {{ item.CN }}
                            </v-list-item-title>
                          </div>
                        </v-list-item-content>
                    </template>
                </v-autocomplete>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer><v-btn @click="getPdf()" color="success" elevation="12">{{ $t("document.accept") }}</v-btn>
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
      search: "",
      file: null,
      page: 1,
      from: 0,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      dialog: false,
      editMode: null,
      items: [],
      parts: [],
      part: null,
      form: {},
      item:null,
      dialogHeaderText: "",
      eimzoKeys: [],
      eimzoKey: null,
      eimzoDialog: false,
      new_count:0,
      success_count:0,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    listItem() {
      return this.items;
    },
  },
  methods: {
    log(text){
      console.log(text);
    },
    changeEimzoKey(){
      let store = this.$store;
      let eimzo_key = this.eimzo_key;
      let log = this.log;
      EIMZOClient.loadKey(
          this.eimzoKey,
          function (id) {
            store.commit("setEimzoKey", id);
            eimzo_key = id;
            //document.getElementById("keyId").innerHTML = id;
          },
          function (e, r) {}
        );
    },
    getPdf() {
      if (this.items.length > 0) {
        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/post-order/get-pdf-new/"+this.part)
          .then((response) => {
            this.loading = false;
            if (response.data) {
              this.item = response.data;
              this.sign(response.data);
            }
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      }
      // eimzoDialog = true;
    },
    sign(item){
      let data = btoa(item.pdf);
      console.log(data,item.pdf);
      let eimzo_key = this.$store.getters.getEimzoKey();
      let setBase64 = this.setBase64;
      if(!eimzo_key){
        this.eimzoDialog = true;
      } else {
      EIMZOClient.createPkcs7(
            eimzo_key,
            data,
            null,
            function(pkcs7) {
                setBase64(pkcs7);
            },
            function(e, r) {
              if (r) {
                if (r.indexOf("BadPaddingException") != -1) {
                } else {
                }
              } else {
                document.getElementById("keyId").innerHTML = "";
              }
            }
          );
      }
    },
    setBase64(base64){
      this.item.base64 = base64;
      this.accept(this.item);
    },
    accept(item) {
      axios
        .post(this.$store.state.backend_url + "api/post-order/accept", item)
        .then((response) => {
          if(response.data != 500){
            this.items = this.items.filter((v) => v.id != item.id);
            this.new_count = response.data.new_count;
            this.success_count = response.data.success_count;
            if(this.items.length == 0){
              this.getList(false);
            }
            else this.getPdf();
          } else alert('Order topilmadi');
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    changeFile() {
      var formData = new FormData();
      formData.append("file", this.file);
      axios
        .post(this.$store.state.backend_url + "api/post-order", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getList(type) {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/post-order/"+this.part)
        .then((response) => {
          this.items = response.data[0];
          this.new_count = response.data[1];
          this.success_count = response.data[2];
          this.loading = false;
          if (!type && this.items.length > 0) {
            this.getPdf();
          }
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getPart() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/post-order-part")
        .then((response) => {
          this.parts = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
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
  },
  mounted() {
    this.getPart();
    this.AppLoad();
  },
};
</script>
