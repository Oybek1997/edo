<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title>
        KPI
        <v-spacer></v-spacer>
        <v-toolbar flat dense class="mx-1">
          <v-spacer></v-spacer>
          <v-autocomplete
            label="Bo`limlar"
            v-model="filter.res_id"
            :items="departments"
            hide-details
            dense
            outlined
            item-text="text"
            item-value="id"
            @change="getlist"
            class="mx-1"
          ></v-autocomplete>         
        </v-toolbar>
      </v-card-title>
      <v-card-text v-if="filter.res_id">
        <v-simple-table dense>
          <template v-slot:default>
            <thead>
              <tr>
                <th
                  style="border: 1px solid #ddd"
                  class="text-center text-bold"
                  rowspan="2"
                >
                  #
                </th>
                <th
                  style="border: 1px solid #ddd; font-weight: bold"
                  class="text-center text-bold"
                  rowspan="2"
                >
                  Ko'rsatkichlar
                </th>
                <th
                  style="border: 1px solid #ddd; white-space: normal"
                  class="text-center text-bold"
                  rowspan="2"
                >
                  Ko'rsatkichlar salmog'i
                </th>
                <th
                  style="
                    border: 1px solid #ddd;
                    max-width: 100px;
                    white-space: normal;
                  "
                  class="text-center text-bold"
                  rowspan="2"
                >
                  Mukofot miqdori
                </th>
                <th
                  style="
                    border: 1px solid #ddd;
                    max-width: 100px;
                    white-space: normal;
                  "
                  class="text-center text-bold"
                  rowspan="2"
                >
                  O'lchov birligi
                </th>
                <th
                  style="border: 1px solid #ddd"
                  class="text-center text-bold"
                  colspan="6"
                >
                  <!-- {{  d_d_name ? quarter[filter.quarter - 1].text : ''  }} -->
                  {{ filter.quarter + " - chorak" }}
                </th>

              </tr>
              <tr>
                <th
                  style="border: 1px solid #ddd"
                  class="text-center text-bold"
                >
                  Min
                </th>
                <th
                  style="border: 1px solid #ddd"
                  class="text-center text-bold"
                >
                  Maqsad
                </th>
                <th
                  style="border: 1px solid #ddd"
                  class="text-center text-bold"
                >
                  Max
                </th>
                <th
                  style="border: 1px solid #ddd; background-color: #eaffea"
                  class="text-center text-bold"
                >
                  Fakt
                </th>
                <th
                  style="border: 1px solid #ddd; background-color: #eaffea"
                  class="text-center text-bold"
                >
                  Maqsadga erishish
                </th>
                <th
                  style="border: 1px solid #ddd; background-color: #eaffea"
                  class="text-center text-bold"
                >
                  Mukofot miqdori
                </th>
                <!-- <th
                  v-for="(comission, index) in comissions"
                  :key="index"
                  style="border: 1px solid #ddd"
                  class="text-center text-bold"
                >
                  {{ comission.employee.firstname_uz_latin }}
                  <br />
                  {{ comission.employee.lastname_uz_latin }}
                </th> -->
              </tr>
            </thead>
            <tbody>
              <tr v-for="(detail, k) in document_details" :key="k">
                <td style="border: 1px solid #ddd">{{ k + 1 }}</td>
                <td
                  style="
                    border: 1px solid #ddd;
                    min-width: 300px;
                    white-space: normal;
                    text-align: justify;
                  "
                >
                  {{
                    detail.document_detail_attribute_values.find(
                          (v) =>
                            v.d_d_attribute_id ==
                            attributes[filter.quarter - 1][0]
                        ).attribute_value
                  }}
                  <!-- {{
                    detail.document_detail_attribute_values.find(
                      (v) => v.d_d_attribute_id == 1321
                    ).attribute_value
                  }} -->
                </td>
                <td style="border: 1px solid #ddd">
                  {{
                   detail.document_detail_attribute_values.find(
                          (v) =>
                            v.d_d_attribute_id ==
                            attributes[filter.quarter - 1][1]
                        ).attribute_value
                  }}
                  <!-- {{
                    detail.document_detail_attribute_values.find(
                      (v) => v.d_d_attribute_id == 1322
                    ).attribute_value
                  }} -->
                </td>
                <td style="border: 1px solid #ddd">
                  {{
                   detail.document_detail_attribute_values.find(
                          (v) =>
                            v.d_d_attribute_id ==
                            attributes[filter.quarter - 1][2]
                        ).attribute_value
                  }}
                  <!-- {{
                    detail.document_detail_attribute_values.find(
                      (v) => v.d_d_attribute_id == 1323
                    ).attribute_value
                  }} -->
                </td>
                <td style="border: 1px solid #ddd">
                  {{ 
                   detail.document_detail_contents.find(
                          (v) =>
                            v.d_d_attribute_id ==
                            attributes[filter.quarter - 1][3]
                        ).value
                  }}
                  <!-- {{
                    detail.document_detail_contents.find(
                      (v) => v.d_d_attribute_id == 1324
                    ).value
                  }} -->
                </td>
                <td
                  style="
                    border: 1px solid #ddd;
                    max-width: 250px;
                    white-space: normal;
                    text-align: justify;
                  "
                >
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        d_d_attribute_id[filter.quarter - 1].min
                    ).attribute_value
                  }}
                </td>
                <td
                  style="
                    border: 1px solid #ddd;
                    max-width: 250px;
                    white-space: normal;
                    text-align: justify;
                  "
                >
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        d_d_attribute_id[filter.quarter - 1].aim
                    ).attribute_value
                  }}
                </td>
                <td 
                style="
                    border: 1px solid #ddd;
                    min-width: 300px;
                    white-space: normal;
                    background-color: #eaffea;
                  ">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        d_d_attribute_id[filter.quarter - 1].max
                    ).attribute_value
                  }}
                </td>
                <td
                  style="
                    border: 1px solid #ddd;
                    min-width: 300px;
                    white-space: normal;
                    background-color: #eaffea;
                  "
                >
                 
                  <template>
                    {{ detail.document_detail_fakt.fakt }}
                  </template>
                </td>
                <td style="border: 1px solid #ddd; background-color: #eaffea">
                  
                  <template>
                    {{ detail.document_detail_fakt.achieving_goal }}
                  </template>
                </td>
                <td style="border: 1px solid #ddd; background-color: #eaffea">
                  <template>
                    {{ detail.document_detail_fakt.reward_amount }}
                  </template>
                </td>                
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
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
    <v-card v-if="document_details.length > 0"  >
      <v-card-title> {{"Izox qoldirish oynasi"}} </v-card-title>
      <hr />
      <v-card-text class="pt-0" style="border: 1px solid blue">      
        <!-- <v-toolbar flat dense class="pt-5" style="max-width: 90%"> -->
          <v-textarea
            v-model="comment"
            rows="1"
            outlined
            class="my-4"
            @keyup.native.shift.enter="saveComments"
          ></v-textarea>
          <v-btn
            class="m-10 mt-n6 float-right"
            outlined
            small
            color="primary"
            @click="saveComments"
          >
          {{"SEND"}}
            <!-- <v-icon>mdi-arrow-right-bold</v-icon> -->
          </v-btn>
        <!-- </v-toolbar> -->
      </v-card-text>
    </v-card>
    <v-card v-if="documentFiles.length > 0" class="ma-1 pa-1">
      <v-card-title
        >Yuklangan fayllar
        <v-spacer></v-spacer>       
      </v-card-title>
      <v-card-text class="pt-0">
        <v-simple-table         
          dense
          class="mt-2"
          style="border: 1px solid #aaa"
        >
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">#</th>
                <th class="text-left">
                  {{ $t("document.file_name") }}
                </th>
                <th class="text-left"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in documentFiles" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ item.file_name }}</td>
                <td class="text-lg-right" width="100px">
                  <a
                    :href="
                      $store.state.backend_url + 'kpi/file-download/' + item.id
                    "
                  >
                    <v-icon class="px-1" color="success">mdi-eye</v-icon>
                  </a>
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
import axios from "axios";
const Swal = require("sweetalert2");
const moment = require("moment");
export default {
  data() {
    return {
      // okComment:'',
      d_d_id: null,
      d_d_name: [],
      textComments: null,
      comment: "",
      search: "",
      comment_window: false,
      kpi_user: false,
      status_user: false,
      loading: false,
      departments: [],
      years: ["2022", "2023"],
      percent: [0, 80, 100, 120],
      d_d_attribute_id: [
        { min: 1325, aim: 1326, max: 1327 },
        { min: 1328, aim: 1329, max: 1330 },
        { min: 1331, aim: 1332, max: 1333 },
        { min: 1334, aim: 1335, max: 1336 },
      ],
      quarter: [
        { text: this.$t("1-chorak"), value: 1 },
        { text: this.$t("2-chorak"), value: 2 },
        { text: this.$t("3-chorak"), value: 3 },
        { text: this.$t("4-chorak"), value: 4 },
      ],
      attributes: [
        [1321, 1322, 1323, 1324],
        [2078, 2079, 2080, 2081],
        [2082, 2083, 2084, 2085],
        [2086, 2087, 2088, 2089],
      ],
      filter: {
        year: moment().format("YYYY"),
        quarter: null,
        department_id: null,
        res_id: null,
      },
      employee: null,
      resalution_employee: [],
      access_reaction: false,
      comissions: [],
      document_details: [],
      modalDocumentFile: false,
      selectFiles: [],
      formData: null,
      documentFiles: [],
      saveComission: false,
      saveOwner: false,
      change_data: false,
      change_reaction: false,
      selectvideo: null,
      tabno: "",
    };
  },
  computed: {
    user() {
      return this.$store.getters.getUser();
    },
    comissionLength() {
      return this.comissions.length;
    },
    accessReaction() {
      return this.access_reaction;
    },
  },
  methods: {
    sendVideoFile() {
      if (this.$refs.videoFileForm.validate()) {
        this.formData.append("files[]", this.selectvideo);
        this.documentFiles.push({
          id: Date.now() + Math.floor(Math.random() * 1000),
          file_name: this.selectvideo.name,
        });
      }
    },
    saveComments() {
      axios
        .post(this.$store.state.backend_url + "api/kpi/asistent-comments", {
          comment: this.comment,
          filter: this.filter,
        })
        .then((res) => {
          console.log(res.data);
          if (res.data == "success") {
            Swal.fire({
              icon: "success",
              title: "OK!",
              text: "Izox saqlandi va junatildi",
            });
          }
          //  console.log(res);
          // this.comment = "";
          // this.getComments();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getComments() {
      this.filter.d_d_id = this.d_d_id;
      axios
        .post(this.$store.state.backend_url + "api/documents/kpi-getcomments", {
          filter: this.filter,
        })
        .then((res) => {
          this.textComments = res.data;
          // console.log(res);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getFiles() {
      this.documentFiles = [];
      axios
        .post(this.$store.state.backend_url + "api/kpi/kpi-getfiles", {
          filter: this.filter,
        })
        .then((res) => {
          this.formData = new FormData();
          this.documentFiles = res.data.map((v) => ({
            id: v.id,
            file_name: v.file_name,
          }));
          // console.log(this.documentFiles);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getDepartments() {
      axios
        .post(this.$store.state.backend_url + "api/kpi/resolution-departments", {
          locale: this.$i18n.locale,
        })
        .then((res) => {
          this.departments = res.data.map((v) => {
            v.text = v.kpi_objektresolution.department_resolution ? (v.kpi_objektresolution.department_resolution.department_code + " " + v.kpi_objektresolution.department_resolution.name_uz_latin) : v.kpi_objektresolution.id;
            return v;
          });
          // console.log(this.departments);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getDocumentFile() {
      this.modalDocumentFile = true;
    },
   
    percent_reward_amount(detail) {
      let a = detail.document_detail_attribute_values.find(
        (v) => v.d_d_attribute_id == 1323
      ).attribute_value;
      detail.document_detail_fakt.reward_amount =
        (detail.document_detail_fakt.achieving_goal * a) / 100;
    },
    getlist() {
      this.access_reaction = false;
      this.comment_window = true;
      this.change_data = false;
      this.change_reaction = false;
      axios
        .post(this.$store.state.backend_url + "api/kpi/get-attributes", {
          filter: this.filter,
        })
        .then((response) => {
          // console.log(response);
          this.filter.quarter = response.data.quarter;
          this.comment = response.data.comment;
          // this.getlist();
            this.getFiles();
          response.data.document_details.forEach((e) => {
            // response.data.document_details.map((e) => {
            this.d_d_name.push({
              id: Date.now(),
              d_id: e.id,
              name: e.document_detail_attribute_values.find(
                (v) => v.d_d_attribute_id == 1321
              ).attribute_value,
            });
          });
          this.change_data = response.data.change_data;
          this.change_reaction = response.data.change_reaction;
          // this.filter.quarter = response.data.quarter;
          this.document_details = response.data.document_details.map((v) => {
            if (v.document_detail_fakt == null) {
              v.document_detail_fakt = {
                id: Date.now(),
                d_d_id: v.id,
                fakt: null,
                year: this.filter.year,
                quarter: this.filter.quarter,
                achieving_goal: null,
                reward_amount: null,
                comissions: [],
              };
            } else if (v.document_detail_fakt.comissions.length > 0) {
              v.document_detail_fakt.comissions =
                v.document_detail_fakt.comissions.sort(
                  (a) => new Date(a.created_at) * -1
                );
            }
            return v;
          });
        })  
        .catch((error) => {
          console.log(error);
        });
    },
  },
  mounted() {
      this.getDepartments();
      // this.getlist();
    this.employee = JSON.parse(localStorage.getItem("user")).employee;
  },
};
</script>
