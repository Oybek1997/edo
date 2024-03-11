<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Инвентаризация Фойдаланувчилари") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="filter.search"
            append-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            dense
            hide-details
            solo
            single-line
          ></v-text-field>       
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;" @click="getList">
              <v-icon color="#00B950" left>mdi-magnify</v-icon>Search
            </v-btn>
          <v-btn  v-if="$store.getters.checkPermission('mobile-inventory-user-change')" class="filterBtn px-2" style="background: #fff; height: 34px;" @click="changeComm">
              <v-icon color="#00B950" left>mdi-account-multiple-plus</v-icon>Add
            </v-btn>            
        </div>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
          single-expand
          :expanded="expanded"
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="is_active_true"
            item-key="id"
            show-expand
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            disable-sort
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
            <template v-slot:item.number="{ item,index }">
              {{ from+index  }}
            </template>
            <template v-slot:item.actions="{ item }">
                <td class="d-flex">
                  <v-tooltip v-if="$store.getters.checkPermission('mobile-inventory-user-change')" bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-switch
                        v-bind="attrs"
                        v-on="on"
                        class="ma-0 pa-0"
                        text
                        small
                        v-model="item.manual"
                        @click="changeSwitch(item)"
                      ></v-switch>
                    </template>
                    <span>{{ $t("Ўзгартиришга рухсат") }}</span>
                  </v-tooltip>

                  <v-tooltip v-if="$store.getters.checkPermission('mobile-inventory-user-change')" bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        v-bind="attrs"
                        v-on="on"
                        class="px-1"
                        color="blue"
                        style="min-width: 25px"
                        small
                        text
                        @click="changePasswords(item)"
                      >
                        <v-icon size="18">mdi-form-textbox-password</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ $t("Паролни ўзгартиришга") }}</span>
                  </v-tooltip>
                </td>
              </template>

            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-3">
                <v-card class="my-2">                
                  <v-container fluid class="pa-1 blue-grey lighten-5">
                  <table 
                      style="width: 100%">
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("Name") }}
                    </th>                   
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("Surname") }}
                    </th>                   
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("Tabel") }}
                    </th>                   
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("Commission number") }}
                    </th>                   
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("Registered") }}
                    </th>                   
                    <th style="max-width: 100px; white-space: normal">
                      {{ $t("Manual Change") }}
                    </th>                   
                  </tr>
                  <tr v-for="itema in is_active_false.filter((v)=>v.employee_id==item.employee_id)">
                    <td style="max-width: 200px; white-space: normal;text-align: center;">
                      {{ is_active_false.indexOf(itema) + 1 }}
                    </td>                    
                    <td style="max-width: 200px; white-space: normal;text-align: center;">
                      {{ itema.firstname}}
                    </td>                    
                    <td style="max-width: 200px; white-space: normal;text-align: center;">
                      {{ itema.lastname}}
                    </td>                    
                    <td style="max-width: 200px; white-space: normal;text-align: center;">
                      {{ itema.tab}}
                    </td>                    
                    <td style="max-width: 200px; white-space: normal;text-align: center;">
                      {{ itema.commission_number}}
                    </td>                    
                    <td style="max-width: 200px; white-space: normal;text-align: center;">
                      {{ itema.created_at}}
                    </td>                    
                    <td style="max-width: 200px; white-space: normal;text-align: center;">
                      {{ itema.manual==1?'Editable':'not Editable'}}
                    </td>                    
                  </tr>
                </table>
                </v-container>
                </v-card>
              </td>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>   
    <v-dialog v-model="UserModal" max-width="450px" persistent>
        <v-card class="pa-5">
          <span class="dialogTitle">{{
            $t("Янги фойдаланувчини қўшиш")
          }}</span>
          <v-divider color="#DCE5EF" class="my-1"></v-divider>
          <v-card-text class="pl-0">
            <v-text-field
              class="txtDial mt-0"
              v-model="form.tabel"
              placeholder="Фойдаланувчини табел рақами"
              maxlength="4"
              outlined
              dense
              @change="usertbn"
            ></v-text-field>           
            <v-text-field
            class="txtDial"
              v-model="form.firstname"
              label="Фойдаланувчи Исми"
              outlined
              dense
              disabled
            ></v-text-field>
            <v-text-field
            class="txtDial"
            v-model="form.lastname"
              label="Фойдаланувчини Фамилияси"
              outlined
              dense
              disabled
            ></v-text-field>
            <v-text-field
            class="txtDial"
            v-model="form.commissionnumber"
              label="Комиссия рақамини киритинг"
              outlined
              dense
              :disabled="form.lastname == null"
            ></v-text-field>
            <v-switch
              class="txtDial"
              v-model="form.manual"
              label="Manual editable"
              outlined
              dense
              :disabled="form.commissionnumber == null"
            ></v-switch>
            <v-card
            class="mt-0 text-end"
              outlined
              elevation="0"
              style="border: none"
            >
              <v-btn
                class="mr-3"
                color="#3FCB5D"
                right
                @click="save"
                :disabled="form.commissionnumber == null"
                small
                dark
                elevation="0"
                style="
                  text-transform: none;
                  border-radius: 5px;
                  padding: 5px 20px;
                "
              >
                {{ $t("save") }}
              </v-btn>
              <v-btn
                class
                color="#EB4034"
                right
                small
                dark
                elevation="0"
                @click="UserModal = false"
                @keydown.esc="UserModal = false"
                style="
                  text-transform: none;
                  border-radius: 5px;
                  padding: 5px 20px;
                "
              >
                {{ $t("Отменить") }}
              </v-btn>
            </v-card>
          </v-card-text>
        </v-card>
      </v-dialog>
    <v-dialog v-model="changePass" max-width="250px" persistent>
        <v-card class="ma-0 pa-3" style="border-radius: 1px">
          <span class="dialog-head_title">{{
            $t("Паролни алмаштириш")
          }}</span>
          <v-divider
            class="mt-1 mb-3"
            style="border-color: #dce5ef"
          ></v-divider>
          <v-card-text class="pl-0">
            <v-text-field
              class="txtDial mt-0"
              v-model="form.pass"
              type="password"
              center
              maxlength="4"
              outlined
              dense
            ></v-text-field>                      
            <v-card
              class="mt-0 text-end"
              outlined
              elevation="0"
              style="border: none"
            >
              <v-btn
                color="#3FCB5D"
                right
                @click="setPasswods"
                small
                :disable="form.pass==null"
                dark
                elevation="0"
                style="
                  margin:0px 20px 0px 0px;
                  text-transform: none;
                  border-radius: 5px;
                  padding: 5px 20px;
                "
              >
                {{ $t("save") }}
              </v-btn>
              <v-btn
                color="#EB4034"
                right
                small
                dark
                elevation="0"
                @click="changePass = false"
                @keydown.esc="changePass = false"
                style="
                  text-transform: none;
                  border-radius: 5px;
                  padding: 5px 20px;
                "
              >
                {{ $t("Отменить") }}
              </v-btn>
            </v-card>
          </v-card-text>
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
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300"  @keydown.esc="downloadExcel = false">
      <v-card class="pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">Excel га юклаб олиш</span>
        <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
                        <v-btn        
                        color="success"                    
                            @click="downloadExcel = false"
                            style="text-transform: none; border-radius: 5px"
                            class="ma-3 pa-3"
                            text
                            right
                            small
                            dark
                        >
                            <download-excel
                                :data="application_excel"
                                :name="'Inv_' + today + '.xls'"
                            >
                                <span style="color: #4caf50">{{ $t("download") }}</span>
                                <v-icon color="success" height="20">mdi-download</v-icon>
                            </download-excel>
                        </v-btn>
                        <v-btn
                            color="red"
                            @click="downloadExcel = false"
                            style="text-transform: none; border-radius: 5px"
                            class="ma-3 pa-3"
                            text
                            right
                            small
                            dark><span style="color: red">{{ $t("close") }}</span>     
                            <v-icon color="red" height="20">mdi-close</v-icon>
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
// import Swal from "sweetalert2";
export default {
  data: () => ({   
    items: [],   
    expanded: [], 
    switchItem: [
      {id:null,text:'All'},
      {id:true,text:'Yes'},
      {id:false,text:'No'},
    ], 
    filter: {}, 
    form: {
      manual:0,
      itemID:null,
      search:null,
      firstname:null,
      lastname:null,
      tabel:null,
      employee_id:null,
      commissionnumber:null,
      ismanual: null,
      pass:null,
    }, 
    search: "",
    UserModal:false,
    changePass:false,
    loading:false,
    is_active_true: [], 
    is_active_fasle: [], 
    application_excel: [], 
    downloadExcel:false,
    today: moment().format("YYYY-MM-DD"),
    page: 1,
    server_items_length: -1,
    dataTableOptions: { page: 1, itemsPerPage: 20 },
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 210;
    },   
    headers() {
      return [
        { text: "", value: "data-table-expand",class:'blue-grey lighten-5', width: 30 },
        { text: "#", value: "number", align: "center", class:'blue-grey lighten-5',width: 30 },
        
        {
          text: this.$t("User name"),align: "center",
          value: "user_name",
          class:'blue-grey lighten-5',
        },        
        {
          text: this.$t("Name"),align: "center",
          value: "firstname",
          class:'blue-grey lighten-5',
        },        
        {
          text: this.$t("Surname"),align: "center",
          value: "lastname",
          class:'blue-grey lighten-5',
        },        
        {
          text: this.$t("Tabel"),align: "center",
          value: "tab",
          class:'blue-grey lighten-5',
        },        
        {
          text: this.$t("Commission number"),align: "center",
          value: "commission_number",
          class:'blue-grey lighten-5',
        },        
        {
          text: this.$t("Registered"),align: "center",
          value: "created_at",
          class:'blue-grey lighten-5',
        },        
       
        {text: this.$t("actions"),value: "actions",
        class:'blue-grey lighten-5',
        width: 100,align: "center",} 
       
      ,
       
      ]
      // .filter(
      //   (v) =>
      //     v.value != "actions" ||
      //     this.$store.getters.checkPermission("department-update") ||
      //     this.$store.getters.checkPermission("department-delete")
      // );
    },
  },
  methods: {   
    changeSwitch(item) {
      var st = "";
      if (item.manual == 0) {
        st = "ўзгартиришга рухсат";
        this.form.manual=1;
      } else {
        st = "ўзгартиришга рухсат эмас";
        this.form.manual=0;
      }
      ///////////////////
      Swal.fire({
        title: this.$t("Ўзгартиришга рухсат"),
        text:
          "Сиз ростдан хам " +
          item.lastname+' '+item.firstname+
          "ни " +
          st +
          " холатга олиб ўтишни хохлайсизми?",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("ХА"),
        cancelButtonText: this.$t("close"),
      }).then((result) => {
        if (result.value) {
          this.form.tabel=item.tab;
          axios
            .post(
              this.$store.state.backend_url + "api/commissionUser/update-manual",
              {form:this.form,}
            )
            .then((res) => {
              if (res.data == 1) {
                const Toast = Swal.mixin({
                  toast: true,
                  position: "top-end",
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true,
                });
                this.getUserlist();
                Toast.fire({
                  icon: "success",
                  title: this.$t("create_update_operation"),
                });
              }
              this.loading = false;
            });
        }
        // this.getUserlist();
      });
      ///////////////////
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    changeComm() {
      this.form.itemID=null;
      this.form.search=null;
      this.form.firstname=null;
      this.form.lastname=null;
      this.form.tabel=null;
      this.form.employee_id=null;
      this.form.commissionnumber=null;
      this.form.manual=null;
      this.form.pass=null;
      this.UserModal=true;
    },
    changePasswords(item) {
      this.form.itemID=item.id;
      this.form.commissionnumber=item.commission_number;
      this.form.firstname=item.firstname;
      this.form.lastname=item.lastname;
      this.form.tabel=item.tab;
      this.changePass=true; 
    },
    setPasswods() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/user-password-change", {
          filter:this.form,
        })
        .then((res) => {
          if (res.data.code == 200) {            
              Swal.fire({
              position: "top-end",
              icon: "success",
              title: res.message,
              text:this.form.firstname+' ' +this.form.lastname+' пароли алмаштирилди',
              showConfirmButton: false,
              timer: 2000
            });
          }
            else{
              Swal.fire({
              position: "top-end",
              icon: "error",
              title: res.message,
              text: 'Хатолик аниқланди',
              showConfirmButton: false,
              timer: 2000
            });
          }
          this.changePass = false;
          this.loading = false;
          this.getList();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/commissionUser/get-info", {
          filter:this.filter,
          pagination: this.dataTableOptions,
        })
        .then((res) => {
          this.is_active_true = res.data.is_active.data;
          this.is_active_false =res.data.isnot_active;         
          this.server_items_length = res.data.is_active.total;
          this.from = res.data.is_active.from;
          this.loading = false;          
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },  
    getDetailExcel(page) {
            let new_array = [];
            this.loading = true;
            axios
                .post(this.$store.state.backend_url + "api/get-info/get-excel", {
                    filter: this.filter,
                    search: this.search,
                    type: 1,
                    pagination: {
                        page: page,
                        itemsPerPage: 1000,
                    },
                })
                .then((response) => {
                    response.data.map((v, index) => {
                        new_array.push({
                            "№": index + page,
                            'PartNumber': v.PartNumber,
                            'PartName': v.PartName,
                            'EOName': v.EOName,
                            'WHID': v.WHID,
                            'AddressName': v.AddressName,
                            'ScanerFact': v.ScanerFact,
                            'ManualFact': v.ManualFact,
                            'UniqNumber': v.UniqNumber,
                            'Duplicat': v.Duplicat,
                            'Smesh': v.Smesh,
                            'CreatedBy': v.CreatedBy,
                            'Quater': v.Quater,
                           
                        });
                    });
                    this.application_excel = this.application_excel.concat(new_array);
                    if (response.data.length == 1000) {
                        this.getDetailExcel(++page);
                    } else {
                        this.loading = false;
                        this.downloadExcel = true;
                    }
                })
                .catch((error) => {
                    console.log(error);
                    this.loading = false;
                });
        },  
    save() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/commissionUser/create", {
          form: this.form,
        })
        .then((resp) => {
          if (resp.data.code == 200) {
                Swal.fire({
                title: "Фойдаланувчи мавжуд !!!",
                text:resp.data.data.firstname+' '+resp.data.data.lastname+' '+resp.data.data.commissionnumber+' комисияга бириктирилган',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: this.form.commissionnumber+" га бириктир"
              }).then((result) => {
                if (result.isConfirmed) {
                  axios
                  .post(this.$store.state.backend_url + "api/commissionUser/update", {
                    form: this.form,
                  })
                  .then((res) => {
                    if (res.data.code == 201) {
                      Swal.fire({
                      position: "top-end",
                      icon: "success",
                      title: res.message,
                      text:res.data.data.firstname+' '+res.data.data.lastname+' '+res.data.data.commissionnumber+' комисияга бириктирилди',
                      showConfirmButton: false,
                      timer: 1500
                    });
                    this.UserModal = false;
                    }
                    else{
                      Swal.fire({
                      position: "top-end",
                      icon: "error",
                      title: res.message,
                      text: 'Хатолик аниқланди',
                      showConfirmButton: false,
                      timer: 1500
                    });
                    this.UserModal = false;
                    }
                  });
                }
          });
          } else if (resp.data.code == 201) {
            Swal.fire({
              position: "top-end",
              icon: "success",
              title: resp.message,
              text:resp.data.data.firstname+' '+resp.data.data.lastname+' '+resp.data.data.commissionnumber+' комисияга бириктирилди',
              showConfirmButton: false,
              timer: 1500
          });
          this.UserModal = false;
          } else {
            Swal.fire({
            position: "top-end",
            icon: "error",
            title: res.message,
            text: 'Хатолик аниқланди',
            showConfirmButton: false,
            timer: 1500
            });
            this.UserModal = false;
          }
        });
      this.getList();
      this.loading = false;
    },
    
    usertbn() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url +"api/commissionUser/cheack-user" ,{ 
              tabel: this.form.tabel,
            })
        .then((resp) => {
          if (resp.data.code == 200) {
            this.form.firstname=resp.data.data.firstname;
            this.form.lastname=resp.data.data.lastname;
            this.form.employee_id=resp.data.data.employee_id;
          }
          if (resp.data.code == 404) {
            Swal.fire({
              position: "center",
              icon: "error",
              width: "400px",
              title: this.form.tabel+" EDO тизимидан аниқланмади ",
              background: "rgb(0, 0, 0, 0.1)",
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true,
            });}
       
               });
               this.getList();
        this.loading = false;
    },
  },
  mounted() {    
    this.getList();   
  },
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