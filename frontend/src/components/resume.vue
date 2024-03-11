<template>
  <div>
    <v-card class="ma-2 pa-2">
      <v-card-title>
        <v-row>
          <v-col cols="4">
            {{ tabel }}
            <template>
              <router-link :to="'/employees/resume/'" style="text-decoration: none" target="_blank">
                <v-icon color="success">mdi-pencil</v-icon>
              </router-link>
            </template>
          </v-col>
          <v-col cols="4"> </v-col>
          <v-col style="text-align: center" cols="4">
            {{ '"Uz Auto Motors" AJda manfaatlar' }}<br />
            {{ "to'qnashuvini boshqarish bo'yicha" }}<br />
            {{ "Nizomga 1-ilova" }}
          </v-col>
        </v-row>
      </v-card-title>
      <v-card-text>
        <div style="text-align: center; margin-top: 30px">
          <b>
            {{ "Nomzod/xodimning" }}
          </b>
        </div>
        <div style="
            text-align: center;
            font-size: 30px;
            margin-top: 20px;
            margin-bottom: 50px;
          ">
          <b>
            {{ "Ma'lumotnomasi" }}
          </b>
          <br />
          <b>
            {{
              employee
              ? employee["lastname_" + $i18n.locale] +
              " " +
              employee["firstname_" + $i18n.locale] +
              " " +
              employee["middlename_" + $i18n.locale]
              : ""
            }}
          </b>
        </div>
        <v-row>
          <v-col cols="9">
            <table>
              <tr>
                <td>
                  {{
                    employee && employee.employee_staff
                    ? employee.employee_staff[0].enter_order_date
                    : ""
                  }}
                </td>
                <td>
                  <i>
                    {{ " (oxirgi ish joyida ish boshlagan sanasi):" }}
                  </i>
                </td>
              </tr>
              <tr>
                <td>
                  {{
                    employee && employee.employee_staff
                    ? employee.employee_staff[0].staff.position.name_uz_latin
                    : ""
                  }}
                  <br />
                  {{
                    employee && employee.employee_staff
                    ? employee.employee_staff[0].staff.department
                      .name_uz_latin
                    : ""
                  }}
                </td>
                <td>
                  <i>
                    {{ " (oxirgi egallagan lavozimi)" }}
                  </i>
                </td>
              </tr>
            </table>
            <hr class="blue" />
            <table style="margin-top: 20px">
              <tr>
                <td>
                  {{ "Tug'ilgan sanasi:" }}
                </td>
                <td>
                  {{ "Tug'ilgan joyi:" }}
                </td>
              </tr>
              <tr>
                <td style="border-bottom: 1px solid green">
                  {{ employee.born_date }}
                </td>
                <td style="border-bottom: 1px solid green">
                  {{
                    employee.employee_addresses &&
                    employee.employee_addresses[0] &&
                    employee.employee_addresses[0].region[
                    "name_" + $i18n.locale
                    ]
                    ? employee.employee_addresses[0].region[
                    "name_" + $i18n.locale
                    ]
                    : ""
                  }}
                  {{
                    employee.employee_addresses &&
                    employee.employee_addresses[0] &&
                    employee.employee_addresses[0].district[
                    "name_" + $i18n.locale
                    ]
                    ? employee.employee_addresses[0].district[
                    "name_" + $i18n.locale
                    ]
                    : ""
                  }}
                </td>
              </tr>
              <tr>
                <td>
                  {{ "Millati:" }}
                </td>
                <td>
                  {{ "Partiyaviyligi:" }}
                </td>
              </tr>
              <tr>
                <td style="border-bottom: 1px solid green">
                  {{
                    employee.nationality &&
                    employee.nationality["name_" + $i18n.locale]
                    ? employee.nationality["name_" + $i18n.locale]
                    : ""
                  }}
                </td>

                <td style="border-bottom: 1px solid green">
                  {{
                    employee.employee_parties &&
                    employee.employee_parties.hr_party &&
                    employee.employee_parties.hr_party["name_" + $i18n.locale]
                    ? employee.employee_parties.hr_party["name_" + $i18n.locale]
                    : (Parties.find((v) => v.id == form.employee_parties) &&
                      Parties.find((v) => v.id == form.employee_parties)[
                      "name_" + $i18n.locale
                      ]) ??
                    "Yo'q"
                  }}
                </td>
              </tr>
              <tr>
                <td>
                  {{ "Ma'lumoti:" }}
                </td>
                <td>
                  {{ "Tugatgan:" }}
                </td>
              </tr>
              <tr>
                <td v-if="employee.employee_education_histories" style="border-bottom: 1px solid green">
                  <div v-for="(
                      eeh, index
                    ) in employee.employee_education_histories" :key="index">
                    {{
                      eeh.study_degree["name_" + $i18n.locale]
                      ? eeh.study_degree["name_" + $i18n.locale]
                      : ""
                    }}
                  </div>
                </td>
                <td v-if="employee.employee_education_histories" style="border-bottom: 1px solid green">
                  <div v-for="(
                      eeh, index
                    ) in employee.employee_education_histories" :key="index">
                    {{ eeh.end_date ? eeh.end_date : "" }}
                  </div>
                </td>
              </tr>
              <tr>
                <td style="border-bottom: 1px solid green">
                  {{ "Ta'lim mutaxassisligi:" }}
                </td>
                <td v-if="employee.employee_education_histories" style="border-bottom: 1px solid green">
                  <div v-for="(
                      eeh, index
                    ) in employee.employee_education_histories" :key="index">
                    {{
                      eeh.major["name_" + $i18n.locale]
                      ? eeh.major["name_" + $i18n.locale]
                      : ""
                    }}
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  {{ "Ilmiy darajasi:" }}
                </td>
                <td>
                  {{ "Ilmiy unvoni:" }}
                </td>
              </tr>
              <tr>
                <td v-if="employee.employee_education_histories" style="border-bottom: 1px solid green">
                  <div v-for="(
                      eeh, index
                    ) in employee.employee_education_histories" :key="index">
                    {{ eeh.academic_degree ? eeh.academic_degree : "yo'q" }}
                  </div>
                </td>
                <td v-if="employee.employee_education_histories" style="border-bottom: 1px solid green">
                  <div v-for="(
                      eeh, index
                    ) in employee.employee_education_histories" :key="index">
                    {{ eeh.academic_title ? eeh.academic_title : "yo'q" }}
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  {{ "Qaysi chet tillarini biladi:" }}
                </td>
                <td>
                  {{ "Harbiy (maxsus) unvoni:" }}
                </td>
              </tr>
              <tr>
                <td v-if="employee.employee_languages &&
                  employee.employee_languages[0]
                  " style="border-bottom: 1px solid green">
                  <div v-for="(el, index) in employee.employee_languages" :key="index">
                    {{
                      el.hr_language
                      ? el.hr_language["name_" + $i18n.locale]
                      : "yo'q"
                    }}
                  </div>
                </td>
                <td v-if="employee.employee_military_ranks &&
                  employee.employee_military_ranks[0]
                  " style="border-bottom: 1px solid green">
                  <div v-for="(emr, index) in employee.employee_military_ranks" :key="index">
                    {{
                      emr && emr.hr_military_rank
                      ? emr.hr_military_rank["name_" + $i18n.locale]
                      : "yo'q"
                    }}
                  </div>
                </td>
                <td style="border-bottom: 1px solid green" v-else>
                  {{ "yo'q" }}
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  {{ "Davlat mukofotlari bilan taqdirlanganmi (qanday)?:" }}
                </td>
              </tr>
              <tr>
                <td colspan="2" v-if="employee.employee_state_awards &&
                  employee.employee_state_awards[0]
                  " style="border-bottom: 1px solid green">
                  <div v-for="(esa, index) in employee.employee_state_awards" :key="index">
                    {{
                      esa && esa.hr_state_award
                      ? esa.hr_state_award["name_" + $i18n.locale]
                      : "yo'q"
                    }}
                  </div>
                </td>
                <td colspan="2" style="border-bottom: 1px solid green" v-else>
                  {{ "yo'q" }}
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  {{
                    "Xalq deputatlari, respublika, viloyat, shahar va tuman Kengashi deputatimi yoki boshqa saylanadigan organlarning a'zosimi (to'liq ko'rsatilishi lozim):"
                  }}
                </td>
              </tr>
              <tr>
                <td colspan="2" style="border-bottom: 1px solid green">
                  {{
                    employee.employee_parties &&
                    employee.employee_parties.hr_party &&
                    employee.employee_parties.hr_party["name_" + $i18n.locale]
                    ? employee.employee_parties.hr_party[
                    "name_" + $i18n.locale
                    ]
                    : "Yo'q"
                  }}
                </td>
              </tr>
            </table>
          </v-col>
          <v-col cols="3">
            <v-avatar tile style="float: center" width="150px" height="200px" color="grey">
              <!-- <v-avatar tile width="103" height="133" color="grey"> -->
              <!-- <v-icon>mdi-account-outline</v-icon> -->
              <v-icon v-if="!base64">mdi-account-outline</v-icon>
              <img v-if="base64" :src="'data:application/jpg;base64,' + base64" />
            </v-avatar>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" style="text-align: center; margin-top: 20px">
            <b> {{ "MEHNAT FAOLIYATI" }} </b>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <table>
              <tr class="mytd">
                <td style="text-align: center">{{ "Ish boshlagan sanasi" }}</td>
                <td style="text-align: center">
                  {{ "Ish yakunlagan sanasi" }}
                </td>
                <td style="text-align: center">{{ "Ish joyi" }}</td>
              </tr>
              <tr class="mytd" v-for="(ewh, index) in employee.employee_work_histories" :key="index">
                <td>
                  {{ ewh.begin_date ?? "" }}
                </td>
                <td>
                  {{ ewh.end_date ?? "" }}
                </td>
                <td width="60%">
                  {{ ewh.work_place ?? "" }}
                </td>
              </tr>
            </table>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" style="text-align: center; margin-top: 20px">
            <b>
              {{
                employee
                ? employee["lastname_" + $i18n.locale] +
                " " +
                employee["firstname_" + $i18n.locale] +
                " " +
                employee["middlename_" + $i18n.locale]
                : ""
              }}
              {{ "yaqin qarindoshlari to'g'risida" }} <br>
              {{ "MA\'LUMOT" }}
            </b>
          </v-col>
        </v-row>


        <v-row>
          <v-col>
            <table>
              <tr class="mytd">
                <td style="text-align: center">{{ "Qarindoshligi" }}</td>
                <td style="text-align: center">
                  {{ "Familiyasi, ismi va otasining ismi" }}
                </td>
                <td style="text-align: center">
                  {{ "Tug'ilgan yili va joyi" }}
                </td>
                <td style="text-align: center">{{ "Ish joyi va lavozimi" }}</td>
                <td style="text-align: center">{{ "Turar joyi" }}</td>
              </tr>
              <tr class="mytd" v-for="(er, index) in employee.employee_relative" :key="index">
                <td>
                  {{ er.family_relative["name_" + $i18n.locale] ?? "" }}
                </td>
                <td>
                  {{
                    (er.last_name ?? "") +
                    " " +
                    (er.first_name ?? "") +
                    " " +
                    (er.middle_name ?? "")
                  }}
                </td>
                <td>
                  {{ (er.born_date ?? "") + " " + (er.born_place ?? "") }}
                </td>
                <td>
                  {{ er.work_place ?? "" }}
                </td>
                <td>
                  {{ er.living_place ?? "" }}
                </td>
              </tr>
            </table>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" style="text-align: center; margin-top: 20px">
            <b>
              {{
                employee
                ? employee["lastname_" + $i18n.locale] +
                " " +
                employee["firstname_" + $i18n.locale] +
                " " +
                employee["middlename_" + $i18n.locale]
                : ""
              }}
              {{ "yaqin qarindoshlari ustav kapitalida ulushga ega yoki boshqaruvida ishtirok etayotgan yuridik shaxslarga                            doir" }}<br>
              {{ "MA\'LUMOTLAR" }}
            </b>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <table>
              <tr class="mytd">
                <td style="text-align: center">{{ "Yuridik shaxs nomi,ro\'yxatdan o\'tgan raqami" }}</td>
                <td style="text-align: center">{{ "Ro'yxatdan o'tgan raqami Egalik sub'ekti va qarindoshlik" }}</td>
                <td style="text-align: center">{{ "Egalik ulushi /boshqaruvdagi roli" }}</td>
                <td style="text-align: center">{{ "Yuridik shaxsning asosiy faoliyat turi" }}</td>
                <td style="text-align: center">
                  {{ "Amallar" }}
                  <v-btn color="#6ac82d" x-small dark fab @click="newItem(1)">
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </td>
              </tr>
              <tr class="mytd" v-for="(ec, index) in (employee.employee_capital ? employee.employee_capital.filter((v) => v.type == 1) : '')" :key="index">
                <td>{{ ec.legal_name + ', ' + ec.legal_register_number }}</td>
                <td>{{ ec.capital_register_relatives }}</td>
                <td>{{ ec.capital_register_role }}</td>
                <td>{{ ec.capital_register_activity }}</td>
                <td>
                  <v-btn color="blue" class="my-1" x-small text @click="editItem(ec)">
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>                  
                </td>
              </tr>
            </table>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" style="text-align: center; margin-top: 20px">
            <b>
              {{ "Tijorat toshkilotlarining ustav kapitalida ishtirok etayotgan" }}
              {{
                employee
                ? employee["lastname_" + $i18n.locale] +
                " " +
                employee["firstname_" + $i18n.locale] +
                " " +
                employee["middlename_" + $i18n.locale]
                : ""
              }}
              {{ " va unga aloqador shahslarga * doir" }}<br>
              {{ "MA\'LUMOTLAR" }}
            </b>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <table>
              <tr class="mytd">
                <td style="text-align: center">{{ "Tijorat tashkiloti nomi, ro'yxatdan o'tgan raqami" }}</td>
                <td style="text-align: center">{{ "Egalik ulushi" }}</td>
                <td style="text-align: center">
                  {{ "Amallar" }}
                  <v-btn color="#6ac82d" x-small dark fab @click="newItem(2)">
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </td>
              </tr>
              <tr class="mytd" v-for="(ec, index) in (employee.employee_capital ? employee.employee_capital.filter((v) => v.type == 2): '')" :key="index">
                <td>{{ ec.capital_organization_name + ', ' + ec.organization_register_number }}</td>
                <td>{{ ec.ownership_stake }}</td>
                <td>
                  <v-btn color="blue" class="my-1" x-small text @click="editItem(ec)">
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>                 
                </td>
              </tr>
            </table>
          </v-col>
        </v-row>

      </v-card-text>
      <v-dialog v-model="loading" width="300" hide-overlay>
        <v-card color="primary" dark>
          <v-card-text style="color: white">
            {{ $t("loadingText") }}
            <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-dialog
        v-model="dialog"
        @keydown.esc="dialog = false"
        persistent
        max-width="800px"
      >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ "Qo'shish" }}</span>
          <v-spacer></v-spacer>          
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text v-if="form.type == 1">
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="ma-0 pa-0">
              <v-col cols="12" class="ma-0 pa-1">
                <label for>{{ $t("Yuridik shaxs nomi") }}</label>
                <v-text-field
                  v-model="form.legal_name"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="ma-0 pa-1">
                <label for>{{ $t("Ro'yxatdan o'tgan raqami") }}</label>
                <v-text-field
                  v-model="form.legal_register_number"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="ma-0 pa-1">
                <label for>{{ $t("Ro'yxatdan o'tgan raqami Egalik sub'ekti va qarindoshlik") }}</label>
                <v-text-field
                  v-model="form.capital_register_relatives"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="ma-0 pa-1">
                <label for>{{ $t("Egalik ulushi /boshqaruvdagi roli") }}</label>
                <v-text-field
                  v-model="form.capital_register_role"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="ma-0 pa-1">
                <label for>{{ $t("Yuridik shaxsning asosiy faoliyat turi") }}</label>
                <v-text-field
                  v-model="form.capital_register_activity"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-text v-else>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="ma-0 pa-0">
              <v-col cols="12" class="ma-0 pa-1">
                <label for>{{ $t("Yuridik shaxs nomi") }}</label>
                <v-text-field
                  v-model="form.capital_organization_name"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="ma-0 pa-1">
                <label for>{{ $t("Ro'yxatdan o'tgan raqami") }}</label>
                <v-text-field
                  v-model="form.organization_register_number"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="ma-0 pa-1">
                <label for>{{ $t("Ro'yxatdan o'tgan raqami Egalik sub'ekti va qarindoshlik") }}</label>
                <v-text-field
                  v-model="form.ownership_stake"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>            
             
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    </v-card>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  props: ["resumeEmployee", "tabel"],
  watch: {
    // resumeEmployee: function (elementNew, elementOld) {
    //   this.getAvatar(elementNew.id);
    // },
    tabel: function (elementNew, elementOld) {
      // this.filter.tabel = elementNew;
      // this.getAvatar(elementNew.id);
      this.getList(elementNew);
    },
  },
  data() {
    return {
      loading: false,
      filter: {
        tabel: "",
      },
      form: {},
      // employee
      currentUrl: window.location.href,
      dialog: false,
      employeeParties: false,
      viewId: "",
      editMode: null,
      base64: null,
      employee: [],
      Parties: [],
      isActives: [
        {
          value: 0,
          text: "Not Active",
        },
        {
          value: 1,
          text: "Active",
        },
      ],
      dialogHeaderText: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
    getList(tabel) {
      this.filter.tabel = tabel;
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/employeesView", {
          filter: this.filter,
        })
        .then((response) => {
          this.employee = response.data.employees.data[0];
          // console.log(response.data.employees.data[0]);
          this.getAvatar(this.employee.id);
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem(e){
      this.dialog = true;
      this.form.type = e;
    },
    editItem(ec){
      this.form = Object.assign({}, ec);
      this.dialog = true;
    },
    
    save() {
      this.loading = true;
      axios
      .post(this.$store.state.backend_url + "api/complaens/resumecapital/update", {
        form: this.form,
      })
      .then((response) => {
        if(response.data.status == 200){
            this.dialog = false;
            this.getList(this.tabel);
            console.log(this.form);
          }else{
            // console.log(response);
          }          
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getParties() {
      axios
        .get(this.$store.state.backend_url + "api/hr-party")
        .then((response) => {
          this.Parties = response.data;
          // console.log(this.Parties);
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getAvatar(id) {
      axios
        .get(this.$store.state.backend_url + "api/employees/get-avatar/" + id)
        .then((response) => {
          this.base64 = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getConsole() {
      console.log(this.form);
    },
  },
  mounted() {
    this.getList(this.tabel);
    this.getParties();
    // this.filter.tabel = this.tabel;
  },
  created() { },
};
</script>
<style scoped>
table {
  width: 100%;
}

td {
  /* border: 1px solid red; */
  white-space: normal;
  /* word-break: keep-all; */
}

.mytd td {
  border: 1px solid red;
  /* white-space: normal; */
  /* word-break: keep-all; */
}

.v-card__text {
  font-size: 1.275rem;
  font-weight: 400;
  line-height: 2.075rem;
  letter-spacing: 0.0071428571em;
  color: rgba(0, 0, 0, 1) !important;
}
</style>
