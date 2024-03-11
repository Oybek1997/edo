<template>
    <div>
      <v-card class="ma-1 pa-1">
        <v-card-title class="pa-1">
          <!-- {{ material }} -->
          <!-- <span v-if="material && material.length>4" style="color: green">{{ $t("Material info: ") + material }}</span>
          <span v-if="material && material==1" style="color: red">{{ $t("Material info: ") + "material not found" }}</span> -->
          <span>{{ $t("Moddiy javobgarlik: ")}}</span>
          <v-spacer></v-spacer>
          <v-autocomplete
          v-if="$store.getters.checkPermission('sap-menu')"
          class="mr-2"
          clearable
          v-model="filter.workplace"
          :items="workplaces"
          hide-details="auto"
          style="width: 20px !important"
          label="Завод"
          dense
          item-value="code"
          item-text="text"
          @change="getWarehouses()"
          outlined
          ></v-autocomplete>
          
          <v-autocomplete
          class="mr-2"
          clearable
          v-model="filter.warehouse"
          label="Склад"
          :items="warehouses"
          hide-details="auto"
          style="width: 20px !important"
          dense
          item-value="id"
          item-text="text"
          outlined
          ></v-autocomplete>

          <v-menu
            ref="menu"
            v-model="menu"
            :close-on-content-click="false"
            :return-value.sync="filter.currentDate"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="auto"
            nudge-left="170"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-responsive max-width="120">
                <v-text-field
                  v-model="filter.currentDate"
                  outlined
                  dense
                  readonly
                  v-bind="attrs"
                  v-on="on"
                  hide-details
                ></v-text-field>
              </v-responsive>
            </template>
            <v-date-picker
              v-model="filter.currentDate"
              type="month"
              no-title
              scrollable
              @change="$refs.menu.save(filter.currentDate);"
              >
            </v-date-picker>
          </v-menu>
  
          <v-btn class="ml-2" color="primary" dark @click="getList">
            <!-- <v-icon>mdi-plus</v-icon> -->
            Izlash
          </v-btn>
          <v-btn color="success" class="ma-2" dark @click="createDocument" v-if="items.length>0 && !$store.getters.checkPermission('sap-menu')">
            <!-- <v-icon>mdi-plus</v-icon> -->
            Hujjat yaratish
          </v-btn>
        </v-card-title>
      </v-card>
      <v-card  class="ma-1 pa-1">
        <!-- <v-data-table
          dense
          fixed-header
          :loading-text="$t('loadingText')"
          :no-data-text="$t('noDataText')"
          :height="screenHeight"
          :loading="loading"
          :headers="headers.filter((v) => v.visible)"
          :items="items"
          class="mainTable"
          :options.sync="dataTableOptions"
          style="border: 1px solid #aaa"
        > -->
          <!-- <template v-slot:item.id="{ item }">{{
            item.IntNumber
          }}</template> -->
          <!-- <template v-slot:item.Container="{item}">
          <v-btn text @click="getContainerInfo(item.Container)">{{item.Container ? item.Container : '-'}}</v-btn>
          </template> -->
        <!-- </v-data-table> -->
        <v-tabs
        v-model="tab"
        centered
        
      >
        <v-tab href="#tab-1">
          Material report
        </v-tab>
        <v-tab href="#tab-2">
          Svod
        </v-tab>
      </v-tabs>

      <v-tabs-items v-model="tab" >
        <v-tab-item value="tab-1">
          <v-card flat id="tab1">
          <table  v-if="items.length>0" class="mainTable containerTable mt-2" style="border: 1px solid #0e0d0d; border-collapse: collapse; width: 100%" border="1">
          <thead>
            <tr>
              <th rowspan="2" style="white-space: normal;">№</th>
              <th rowspan="2" style="white-space: normal; max-width: 63px;">Номер счёта</th>
              <th rowspan="2" style="white-space: normal;">Код ОЗМ</th>
              <th rowspan="2" style="white-space: normal;">Наименование обьекта</th>
              <th colspan="3" style="white-space: normal;">Остаток на начало месяца</th>
              <th colspan="3" style="white-space: normal;">Приход за месяц:</th>
              <th colspan="3" style="white-space: normal;">Расход за месяц:</th>
              <th colspan="3" style="white-space: normal;">Остаток на конец месяца</th>
            </tr>
            <tr>
              <th>Кол-во</th>
              <th>Сум</th>
              <th>US $</th>
              <th>Кол-во</th>
              <th>Сум</th>
              <th>US $</th>
              <th>Кол-во</th>
              <th>Сум</th>
              <th>US $</th>
              <th>Кол-во</th>
              <th>Сум</th>
              <th>US $</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, key) in items">
              <td>{{ key + 1 }}</td>
              <td>{{ item.ValuationClass }}</td>
              <td style="white-space: normal; max-width: 130px">{{ item.Material }}</td>
              <td  style="white-space: normal; max-width: 200px">{{ item.ObjectName }}</td>
              <td>{{ item.QuanBegPeriod }}</td>
              <td>{{ item.ValBegPeriod }}</td>
              <td>{{ item.ValBegPeriodUsd }}</td>
              <td>{{ item.ReceiptStock }}</td>
              <td>{{ item.ReceiptValue }}</td>
              <td>{{ item.ReceiptValueUsd }}</td>
              <td>{{ item.IssueStock }}</td>
              <td>{{ item.IssueValue }}</td>
              <td>{{ item.IssueValueUsd }}</td>
              <td>{{ item.QuanEndPeriod }}</td>
              <td>{{ item.ValEndPeriod }}</td>
              <td>{{ item.ValEndPeriodUsd }}</td>
            </tr>
            
          </tbody>
        </table>
          </v-card>
        </v-tab-item>
        <v-tab-item value="tab-2">
          <v-card flat id="tab2">
            <table v-if="items.length>0" class="mainTable containerTable mt-2" style="border: 1px solid #0e0d0d; border-collapse: collapse; width: 100%" border="1">
          <thead>
            <tr>
              <th rowspan="2" style="white-space: normal;">№</th>
              <th rowspan="2" style="white-space: normal; max-width: 63px;">Номер счёта</th>
              <th colspan="2" style="white-space: normal;">Остаток на начало месяца</th>
              <th colspan="2" style="white-space: normal;">Приход за месяц:</th>
              <th colspan="2" style="white-space: normal;">Расход за месяц:</th>
              <th colspan="2" style="white-space: normal;">Остаток на конец месяца</th>
            </tr>
            <tr>
              <th>Кол-во</th>
              <th>Сум</th>
              <th>Кол-во</th>
              <th>Сум</th>
              <th>Кол-во</th>
              <th>Сум</th>
              <th>Кол-во</th>
              <th>Сум</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in svod">
              <td>{{ item.IntNumber }}</td>
              <td style="font-weight: 900;">{{ item.ValuationClass }}</td>
              <td>{{ item.QuanBegPeriod}}</td>
              <td>{{ item.ValBegPeriod }}</td>
              <td>{{ item.ReceiptStock }}</td>
              <td>{{item.ReceiptValue }}</td>
              <td>{{item.IssueStock }}</td>
              <td>{{item.IssueValue }}</td>
              <td>{{item.QuanEndPeriod }}</td>
              <td>{{item.ValEndPeriod }}</td>
            </tr>
            <tr>
              <td colspan="2" style="font-weight: 900; text-align: right;">ИТОГО</td>
              <!-- <td></td> -->
              <td>{{ overall.QuanBegPeriod}}</td>
              <td>{{ overall.ValBegPeriod }}</td>
              <td>{{ overall.ReceiptStock }}</td>
              <td>{{overall.ReceiptValue }}</td>
              <td>{{overall.IssueStock }}</td>
              <td>{{overall.IssueValue }}</td>
              <td>{{overall.QuanEndPeriod }}</td>
              <td>{{overall.ValEndPeriod }}</td>
            </tr>
            
          </tbody>
        </table>
          </v-card>
        </v-tab-item>
      </v-tabs-items>

        
      </v-card>
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
  const moment = require("moment");
  import Swal from "sweetalert2";
  export default {
    data() {
      return {
        loading: false,
        search: null,
        tab: null,
        // search: 24296201,
        material: null,
        dialog: false,
        dataFind: false,
        editMode: null,
        counters: {},
        allItems: [],
        items: [],
        svod: [],
        overall: [],
        total: null,
        workplaces: [],
        warehouses: [],
        containerData: [],
        containerInfo: false,
        tranzit: [],
        production: [],
        form: {},
        datat: {},
        dataTableOptions: { page: 1, itemsPerPage: -1 },
        filter: {
          workplace: '',
          warehouse: '',
          currentDate: new Date().toISOString().substr(0, 7),
          // from_date: moment().subtract(1, 'months').format("YYYY-MM-DD"),
          // to_date: moment(new Date()).format("YYYY-MM-DD"),
        },
        menu: false,
        dialogHeaderText: "",
        tab: 1,
        text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        tabContent: null
      };
    },
    computed: {
      headers() {
        let headers = [
          { text: "#", value: "IntNumber", align: "center", width: 30, visible: true },
          {
            text: this.$t("Material"),
            value: "Material",
            width: 100,
            sortable: false,
            visible: true
          },
          {
            text: this.$t("ObjectName"),
            value: "ObjectName",
            width: 50,
            sortable: false,
            visible: this.tab !='tab-Production shops'
          },
          {
            text: this.$t("ValBegPeriod"),
            value: "ValBegPeriod",
            width: 100,
            sortable: false,
            visible: this.tab !='tab-Production shops'
          },
          {
            text: this.$t("ValBegPeriodUsd"),
            value: "ValBegPeriodUsd",
            width: 100,
            sortable: false,
            visible: this.tab=='tab-Container yard'
          },

          {
            text: this.$t("ValEndPeriod"),
            value: "ValEndPeriod",
            width: 50,
            sortable: false,
            visible: true
          },
          {
            text: this.$t("ValEndPeriodUsd"),
            value: "ValEndPeriodUsd",
            width: 50,
            sortable: false,
            visible: true
          },
          {
            text: this.$t("ValuationClass"),
            value: "ValuationClass",
            width: 50,
            sortable: false,
            visible: true
          },
        ];
        return headers;
      },

      screenHeight() {
        return window.innerHeight - 175;
      },
      
    },
    methods: {
      createDocument(){

        var tab1Element = document.getElementById('tab1');
        var tab2Element = document.getElementById('tab2');

        // Extract the HTML content of the <v-tab-item>s
        var extractedTab1Content = tab1Element.innerHTML;
        if(tab2Element){
          var extractedTab2Content = tab2Element.innerHTML;
        }
        else{
          alert('SVOD oynasini tekshirib chiqing');
        }
        // console.log(extractedContent);
        this.filter['ware'] = (this.warehouses.filter(v=>v.code == this.filter.warehouse));

        if(extractedTab1Content && extractedTab2Content){
          axios
              .post(
                this.$store.state.backend_url + "api/sap/material-responsible/create",
                {content1: extractedTab1Content,
                  content2: extractedTab2Content,
                  filter: this.filter}
              ).then((res) => {
                this.pdf_file_name = res.data.pdf_file_name;
                this.$router.push("/document/" + this.pdf_file_name);
              })
        }
      },
      getList() {
        if(this.filter.warehouse){
          // console.log(this.warehouses.filter(v=>v.id==this.filter.warehouse));
          this.filter.workplace = this.warehouses.filter(v=>v.id==this.filter.warehouse)[0].workplace.code;
          this.filter.warehouse = this.warehouses.filter(v=>v.id==this.filter.warehouse)[0].code;
          this.loading = true;
          this.items= [];
          this.svod= [];
          this.overall= [];
          axios
            .post(this.$store.state.backend_url + "api/warehouse-finder/send", {
              filter: this.filter
            })
            .then((response) => {
              this.items = response.data[0]['item'];
              this.overall = response.data[2]['item'];
              if(Array.isArray(response.data[1]['item'])){
                this.svod = response.data[1]['item'];
              }
              else{
                this.svod.push(response.data[1]['item']);
              }
              // console.log(Array.isArray(this.svod));
              // console.log(this.items);
              this.loading = false;
            })
            .catch((error) => {
              console.log(error);
              this.loading = false;
            });
        }
        else{
          alert('Maydonlarni kiriting!');
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
      getWorkplaces() {
        axios
          .get(this.$store.state.backend_url + "api/warehouse-finder/getWorkplaces")
          .then((response) => {
            if(this.$store.getters.checkPermission('sap-menu')){
              
              this.workplaces = response.data;
              this.workplaces = this.workplaces.map(v => ({
                code: v.code,
                text:
                  v.code +
                  " " +
                  v.name
              }));
            }
            else{

              let items = response.data;
              items.forEach(item => {
                this.warehouses.push(item.warehouse[0]);
              });
                this.warehouses = this.warehouses.map(v => ({
                code: v.code,
                id: v.id,
                name: v.name,
                workplace: v.workplace,
                text:
                  v.code +
                  " (" +
                  v.workplace.code + ")"
              }));
            }
            // console.log(this.warehouses)
            // this.warehouses = response.data[0].warehouse;
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      },
      getWarehouses() {
        axios
          .post(this.$store.state.backend_url + "api/warehouse-finder/getWarehouses", {
            workplace: this.filter.workplace
          })
          .then((response) => {
            this.warehouses = response.data;
            this.warehouses = this.warehouses.map(v => ({
                code: v.code,
                id: v.id,
                name: v.name,
                workplace: v.workplace,
                text:
                  v.code +
                  " (" +
                  v.workplace.code + ")"
              }));
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      },
    },
    mounted() {
      this.getWorkplaces();
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