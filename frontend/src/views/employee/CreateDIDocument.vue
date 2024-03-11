<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title primary-title>{{ $t('transfer.transfer_employee') }}<v-spacer></v-spacer>
      <v-btn color="success" @click="createDocument()">{{$t('transfer.create_document')}}</v-btn>
      </v-card-title>
      <v-card-text>
      <v-row>
        <v-col>
          <v-autocomplete
            v-model="form.staff"
            rounded
            solo
            hide-details
            :label="$t('transfer.new_staff')"
            :items="staffList"
            @update:search-input="getStaff($event)"
            clearable
            no-filter
          >
            <template
                slot="item"
                slot-scope="{ parent, item, tile }"
            >
            <v-chip class="mr-2" color="green" small dark>{{item.rate_count - item.employee_staff.length + '/' + item.rate_count}}</v-chip>
            {{item.department.department_code+' '+ item.department['name_'+$i18n.locale]}}
            <br>
            {{item.position['name_'+$i18n.locale]}}

            </template>
            <template
                slot="selection"
                slot-scope="{ parent, item, tile }"
            >
                <v-chip class="mr-2" color="green" small dark>{{item.rate_count - item.employee_staff.length + '/' + item.rate_count}}</v-chip>
                {{item.department.department_code+' '+ item.department['name_'+$i18n.locale]}}
                <br>
                {{item.position['name_'+$i18n.locale]}}
            </template>

          </v-autocomplete>
        </v-col>
        <v-col>
          <v-autocomplete
            v-model="form.employee"
            rounded
            solo
            hide-details
            :label="$t('transfer.employee')"
            :items="employeesList"
            @update:search-input="getEmployees($event)"
            clearable
            no-filter
          >
            <template
                slot="item"
                slot-scope="{ parent, item, tile }"
            >
              {{ item.tabel+' '+ item['firstname_'+$i18n.locale]+' '+item['lastname_'+$i18n.locale]+' '+item['middlename_'+$i18n.locale] }}
              <br>
              {{item.main_staff && item.main_staff[0] && item.main_staff[0].department ? (item.main_staff[0].department.department_code + ' ' + item.main_staff[0].department['name_'+$i18n.locale]) : ''}}
              <br>
              {{(item.main_staff.position ? item.main_staff.position['name_'+$i18n.locale] : '')}}
            </template>
            <template
                slot="selection"
                slot-scope="{ parent, item, tile }"
            >
            {{ item.tabel+' '+ item['firstname_'+$i18n.locale]+' '+item['lastname_'+$i18n.locale]+' '+item['middlename_'+$i18n.locale] }}
              <br>
              {{item.main_staff && item.main_staff[0] && item.main_staff[0].department ? (item.main_staff[0].department.department_code + ' ' + item.main_staff[0].department['name_'+$i18n.locale]) : ''}}
              <br>
              {{(item.main_staff.position ? item.main_staff.position['name_'+$i18n.locale] : '')}}
            </template>

          </v-autocomplete>
        </v-col>
          <v-btn
            rounded
            hide-details
            class="mt-4"
            color="primary"
            @click="addItem"
          ><v-icon>mdi-plus</v-icon></v-btn>
      </v-row>
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">{{$t('transfer.new_staff')}}</th>
                <th class="text-left">{{$t('transfer.employee')}}</th>
                <th class="text-left"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item,key) in items" :key="key">
                <td>
                {{ item.staff.department['name_'+$i18n.locale] }}
                <br>
                {{ item.staff.position['name_'+$i18n.locale] }}
                </td>
                <td>{{ item.employee.tabel+' '+ item.employee['firstname_'+$i18n.locale]+' '+item.employee['lastname_'+$i18n.locale]+' '+item.employee['middlename_'+$i18n.locale] }}
                    <br>
                    {{item.employee.main_staff && item.employee.main_staff[0] && item.employee.main_staff[0].department ? (item.employee.main_staff[0].department.department_code + ' ' + item.employee.main_staff[0].department['name_'+$i18n.locale]) : ''}}
                    <br>
                    {{(item.position ? item.position['name_'+$i18n.locale] : '')}}
                </td>
                <td>
                  <v-btn color="error" icon @click = "items.splice(key,1)"><v-icon>mdi-delete</v-icon></v-btn>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data: () => ({
    items:[],
    employees:[],
    staffs:[],
    form:{
      employee:null,
      staff:null,
    },
  }),
  computed:{
    employeesList(){
      return this.employees.filter(v => v.employee_staff.length > 0);
    },
    staffList(){
      return this.staffs.filter(v => v.employee_staff.length < v.rate_count);
    },
  },
  methods: {
    createDocument(){
      if(this.items.length>0)
      axios
        .post(
          this.$store.state.backend_url + "api/employees/create-document-transfer",
          {
            items: this.items,
            locale: this.$i18n.locale
          }
        )
        .then(res => {
          // this.$router.push("document/" + res.data.pdf_file_name);
          console.log(res.data);
        })
        .catch(error => {
          console.log(error);
        });
    },
    addItem(){
      if(this.form.staff != null && this.form.employee != null){
        this.items.push({
          staff:this.form.staff,
          employee:this.form.employee,
        });
        this.form = {
          employee:null,
          staff:null,
        };
      }
    },
    getEmployees(search){
      if(search && search.length>0)
      axios
        .post(
          this.$store.state.backend_url + "api/employees/search-employee",
          {
            search: search
          }
        )
        .then(res => {
          this.employees = res.data.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getStaff(search){
      if(search && search.length>0)
      axios
        .post(
          this.$store.state.backend_url + "api/staff/search-staff",
          {
            search: search
          }
        )
        .then(res => {
          this.staffs = res.data.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
  },
  mounted(){
    this.getEmployees('');
    this.getStaff('');
  }
}
</script>
<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title primary-title>{{ $t('transfer.transfer_employee') }}<v-spacer></v-spacer>
      <v-btn color="success" @click="createDocument()">{{$t('transfer.create_document')}}</v-btn>
      </v-card-title>
      <v-card-text>
      <v-row>
        <v-col>
          <v-autocomplete
            v-model="form.staff"
            rounded
            solo
            hide-details
            :label="$t('transfer.new_staff')"
            :items="staffList"
            @update:search-input="getStaff($event)"
            clearable
            no-filter
          >
            <template
                slot="item"
                slot-scope="{ parent, item, tile }"
            >
            <v-chip class="mr-2" color="green" small dark>{{item.rate_count - item.employee_staff.length + '/' + item.rate_count}}</v-chip>
            {{item.department.department_code+' '+ item.department['name_'+$i18n.locale]}}
            <br>
            {{item.position['name_'+$i18n.locale]}}

            </template>
            <template
                slot="selection"
                slot-scope="{ parent, item, tile }"
            >
                <v-chip class="mr-2" color="green" small dark>{{item.rate_count - item.employee_staff.length + '/' + item.rate_count}}</v-chip>
                {{item.department.department_code+' '+ item.department['name_'+$i18n.locale]}}
                <br>
                {{item.position['name_'+$i18n.locale]}}
            </template>

          </v-autocomplete>
        </v-col>
        <v-col>
          <v-autocomplete
            v-model="form.employee"
            rounded
            solo
            hide-details
            :label="$t('transfer.employee')"
            :items="employeesList"
            @update:search-input="getEmployees($event)"
            clearable
            no-filter
          >
            <template
                slot="item"
                slot-scope="{ parent, item, tile }"
            >
              {{ item.tabel+' '+ item['firstname_'+$i18n.locale]+' '+item['lastname_'+$i18n.locale]+' '+item['middlename_'+$i18n.locale] }}
              <br>
              {{item.main_staff && item.main_staff[0] && item.main_staff[0].department ? (item.main_staff[0].department.department_code + ' ' + item.main_staff[0].department['name_'+$i18n.locale]) : ''}}
              <br>
              {{(item.main_staff.position ? item.main_staff.position['name_'+$i18n.locale] : '')}}
            </template>
            <template
                slot="selection"
                slot-scope="{ parent, item, tile }"
            >
            {{ item.tabel+' '+ item['firstname_'+$i18n.locale]+' '+item['lastname_'+$i18n.locale]+' '+item['middlename_'+$i18n.locale] }}
              <br>
              {{item.main_staff && item.main_staff[0] && item.main_staff[0].department ? (item.main_staff[0].department.department_code + ' ' + item.main_staff[0].department['name_'+$i18n.locale]) : ''}}
              <br>
              {{(item.main_staff.position ? item.main_staff.position['name_'+$i18n.locale] : '')}}
            </template>

          </v-autocomplete>
        </v-col>
          <v-btn
            rounded
            hide-details
            class="mt-4"
            color="primary"
            @click="addItem"
          ><v-icon>mdi-plus</v-icon></v-btn>
      </v-row>
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">{{$t('transfer.new_staff')}}</th>
                <th class="text-left">{{$t('transfer.employee')}}</th>
                <th class="text-left"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item,key) in items" :key="key">
                <td>
                {{ item.staff.department['name_'+$i18n.locale] }}
                <br>
                {{ item.staff.position['name_'+$i18n.locale] }}
                </td>
                <td>{{ item.employee.tabel+' '+ item.employee['firstname_'+$i18n.locale]+' '+item.employee['lastname_'+$i18n.locale]+' '+item.employee['middlename_'+$i18n.locale] }}
                    <br>
                    {{item.employee.main_staff && item.employee.main_staff[0] && item.employee.main_staff[0].department ? (item.employee.main_staff[0].department.department_code + ' ' + item.employee.main_staff[0].department['name_'+$i18n.locale]) : ''}}
                    <br>
                    {{(item.position ? item.position['name_'+$i18n.locale] : '')}}
                </td>
                <td>
                  <v-btn color="error" icon @click = "items.splice(key,1)"><v-icon>mdi-delete</v-icon></v-btn>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data: () => ({
    items:[],
    employees:[],
    staffs:[],
    form:{
      employee:null,
      staff:null,
    },
  }),
  computed:{
    employeesList(){
      return this.employees.filter(v => v.employee_staff.length > 0);
    },
    staffList(){
      return this.staffs.filter(v => v.employee_staff.length < v.rate_count);
    },
  },
  methods: {
    createDocument(){
      if(this.items.length>0)
      axios
        .post(
          this.$store.state.backend_url + "api/employees/create-document-transfer",
          {
            items: this.items,
            locale: this.$i18n.locale
          }
        )
        .then(res => {
          // this.$router.push("document/" + res.data.pdf_file_name);
          console.log(res.data);
        })
        .catch(error => {
          console.log(error);
        });
    },
    addItem(){
      if(this.form.staff != null && this.form.employee != null){
        this.items.push({
          staff:this.form.staff,
          employee:this.form.employee,
        });
        this.form = {
          employee:null,
          staff:null,
        };
      }
    },
    getEmployees(search){
      if(search && search.length>0)
      axios
        .post(
          this.$store.state.backend_url + "api/employees/search-employee",
          {
            search: search
          }
        )
        .then(res => {
          this.employees = res.data.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getStaff(search){
      if(search && search.length>0)
      axios
        .post(
          this.$store.state.backend_url + "api/staff/search-staff",
          {
            search: search
          }
        )
        .then(res => {
          this.staffs = res.data.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
  },
  mounted(){
    this.getEmployees('');
    this.getStaff('');
  }
}
</script>
