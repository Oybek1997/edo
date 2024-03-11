<template>
    <div>
      <v-card class="ma-1 pa-1">
        <v-card-title class="pa-1">
          <!-- {{ material }} -->
          <span v-if="material && material.length>4" style="color: green">{{ $t("Material info: ") + material }}</span>
          <span v-if="material && material==1" style="color: red">{{ $t("Material info: ") + "material not found" }}</span>
          <span v-if="material==null">{{ $t("Material info: ")}}</span>
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
  
          <v-btn color="primary" dark @click="getList">
            <!-- <v-icon>mdi-plus</v-icon> -->
            Izlash
          </v-btn>
        </v-card-title>
      </v-card>
      <v-card v-if="dataFind" class="ma-1 pa-1">
        <v-card-title class="pa-1 justify-center">
        </v-card-title>
        <v-row>
          <v-col :cols="(counters.length-1)/4" v-for="(item, key) in counters" v-show="key!='Total'">
            <h3 align="center">{{key}}</h3>
            <h2 align="center" class="green--text">{{item}}</h2>
            <h3 align="center">{{ Math.floor(item*100/total*100)/100 }} %</h3>
          </v-col>
          
        </v-row>
        <!-- <v-card-text class="text-center" style="font-size: 48px; font-weight: 700;">TOTAL: {{ total }}</v-card-text> -->
        
      </v-card>
      <!-- {{ tab }} -->
      <v-card v-if="dataFind" class="ma-1 pa-1">

        <v-tabs
            v-model="tab"
            centered
            slider-color="primary"
          >
            <v-tab
              v-for="(i, key) in datat"
              :key="key"
              :href="`#tab-${i}`"
            >
              {{ i }}
            </v-tab>
          </v-tabs>
          <v-tabs-items v-model="tab">
        <v-tab-item
        v-for="i in datat"
        :key="i"
        :value="`tab-${i}`"
        >
          <v-card flat :style="[tab=='tab-Container yard' ? {'max-width':'100%'}: '']">
            <v-row>
              <v-col :cols="containerInfo==true ? 10 : 12">

                <!-- <h2 class="text-center">{{i}}</h2> -->
                <v-data-table
                  dense
                  fixed-header
                  hide-default-footer
                  :loading-text="$t('loadingText')"
                  :no-data-text="$t('noDataText')"
                  :height="screenHeight"
                  :loading="loading"
                  :headers="headers.filter((v) => v.visible)"
                  :items="items"
                  class="mainTable"
                  style="border: 1px solid #aaa"
                  single-expand
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
                >
                  <template v-slot:item.id="{ item }">{{
                    items
                      .map(function (x) {
                        return x.id;
                      })
                      .indexOf(item.id) + 1
                  }}</template>
                  <template v-slot:item.Container="{item}">
                  <v-btn text @click="getContainerInfo(item.Container)">{{item.Container ? item.Container : '-'}}</v-btn>
                  </template>
                </v-data-table>
              </v-col>
              <v-col  cols="2">
                <h2 class="text-center">Container info</h2>
                <table v-if="tab=='tab-Container yard' && containerData" class="containerTable " style="width: 100%;">
                  <thead>
                  </thead>
                  <tbody>
                    <tr >

                      <td>Hudud</td>
                      <td class="containerSt">{{containerData.hudud}}</td>
                    </tr>
                    <tr>

                      <td>Konteyner</td>
                      <td class="containerSt">{{containerData.konteyner}}</td>
                    </tr>
                    <tr>

                      <td>Pachka</td>
                      <td class="containerSt">{{containerData.pachka}}</td>
                    </tr>
                    <tr>

                      <td>Qator</td>
                      <td class="containerSt">{{containerData.qator}}</td>
                    </tr>
                    <tr>

                      <td>Qavat</td>
                      <td class="containerSt">{{containerData.qavat}}</td>
                    </tr>
                  </tbody>
                </table>
              </v-col>
            </v-row>
          </v-card>
        </v-tab-item>
      </v-tabs-items>
      
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
                  <label for>{{ $t("name_uz_latin") }}</label>
                  <v-text-field
                    v-model="form.name_uz_latin"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <label for>{{ $t("name_uz_cyril") }}</label>
                  <v-text-field
                    v-model="form.name_uz_cyril"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <label for>{{ $t("name_ru") }}</label>
                  <v-text-field
                    v-model="form.name_ru"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-form>
            <small color="red">{{ $t("input_required") }}</small>
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
    </div>
  </template>
  <script>
  const axios = require("axios").default;
  import Swal from "sweetalert2";
  export default {
    data() {
      return {
        loading: false,
        search: null,
        // search: 24296201,
        material: null,
        dialog: false,
        dataFind: false,
        editMode: null,
        counters: {},
        allItems: [],
        // items: [],
        total: null,
        containers: [],
        warehouses: [],
        containerData: [],
        containerInfo: false,
        tranzit: [],
        production: [],
        form: {},
        datat: {},
        dialogHeaderText: "",
        dataTableOptions: { page: 1, itemsPerPage: -1 },
        tab: 1,
        text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
      };
    },
    computed: {
      headers() {
        let headers = [
          { text: "#", value: "id", align: "center", width: 30, visible: true },
          {
            text: this.$t("Location"),
            value: "Slocation",
            width: 100,
            sortable: false,
            visible: true
          },
          {
            text: this.$t("St.Type"),
            value: "StorageType",
            width: 50,
            sortable: false,
            visible: this.tab !='tab-Production shops'
          },
          {
            text: this.$t("StorageBin"),
            value: "StorageBin",
            width: 100,
            sortable: false,
            visible: this.tab !='tab-Production shops'
          },
          {
            text: this.$t("Container"),
            value: "Container",
            width: 100,
            sortable: false,
            visible: this.tab=='tab-Container yard'
          },

          {
            text: this.$t("Stock"),
            value: "Stock",
            width: 50,
            sortable: false,
            visible: true
          },
        ];
        return headers;
      },
      items: function () {
          switch (this.tab) {
              case 'tab-Production shops':
                this.containerInfo=false;
                this.containerData = null;
                return this.production;
              case 'tab-Warehouse':
                this.containerInfo=false;
                this.containerData = null;
                return this.warehouses;
              case 'tab-Container yard':
                this.containerInfo=true;
                return this.containers;
              case 'tab-Transit':
                this.containerInfo=false;
                this.containerData = null;
                return this.tranzit;
              default:
                  return [];
          }
        },
      screenHeight() {
        return window.innerHeight - 175;
      },
      
    },
    methods: {
      getList() {
        if(this.search.length>0){
          this.material = null;
          this.dataFind = true;
          this.loading = true;
          this.datat =[];
          this.containers =[];
          this.warehouses =[];
          this.tranzit =[];
          this.production =[];
          this.counters ={};
          axios
            .post(this.$store.state.backend_url + "api/sap/find-parts", {
              matnr: this.search
            })
            .then((response) => {
              if(!response.data.xxml){
                this.material = 1;
              }
              else{
                this.material = response.data.p1;

              }
              if(response.data.grs){
                this.datat = Object.keys(response.data.grs);
                this.containers = response.data.grs["Container yard"];
                this.warehouses = response.data.grs["Warehouse"];
                this.tranzit = response.data.grs["Transit"];
                this.production = response.data.grs["Production shops"];
                let wareCount = 0;
                let containerCount = 0;
                let transitCount = 0;
                let productionCount = 0;
                let total = 0;
                this.warehouses.forEach(element => {
                  wareCount += parseInt(element.Stock);
                });
                this.containers.forEach(element => {
                  containerCount += parseInt(element.Stock);
                });
                this.tranzit.forEach(element => {
                  transitCount += parseInt(element.Stock);
                });
                if(this.production){

                  this.production.forEach(element => {
                    productionCount += parseInt(element.Stock);
                  });
                  this.counters['Production shops'] = productionCount;
                }
                total = wareCount + containerCount+transitCount+productionCount;
                this.counters['Warehouse'] = wareCount;
                this.counters['Container Yard'] = containerCount;
                this.counters['Transit'] = transitCount;
                this.total = total;
              }
              this.loading = false;
            })
            .catch((error) => {
              console.log(error);
              this.loading = false;
            });
        }
        else{
          this.dataFind = false;
        }
      },
      getContainerInfo(item) {
        this.containerInfo = true;
        axios
          .post(this.$store.state.backend_url + "api/sap/container-info", {
            container: item
          })
          .then((response) => {
            this.containerData = response.data;
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      },
      newItem() {
        this.dialog = true;
        if (this.$store.getters.checkPermission("hr-party-create")) {
          this.dialogHeaderText = this.$t("hr_party.add");
          this.form = {
            id: Date.now(),
            name_ru: "",
            name_uz_cyril: "",
            name_uz_latin: "",
          };
  
          this.editMode = false;
        }
      },
      editItem(item) {
        if (this.$store.getters.checkPermission("hr-party-update")) {
          this.dialogHeaderText = this.$t("edit");
          this.form = Object.assign({}, item);
          this.dialog = true;
          this.editMode = true;
        }
      },
      save() {
        axios
          .post(this.$store.state.backend_url + "api/hr-party/update", this.form)
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
      }, //document-types
      deleteItem(item) {
        if (this.$store.getters.checkPermission("hr-party-delete")) {
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
                  this.$store.state.backend_url + "api/hr-party/delete/" + item.id
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
      // this.getList();
    },
    created() {},
  };
  </script>
  <style>
.textCent{
  width: 100%;
  text-align: center;
}
.containerTable td{
    padding: 5px;
}
.containerSt{
  text-align: right;
  font-weight: 900;
}
</style>