<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title>
        {{ "KPI*" }}

        <v-spacer></v-spacer>
        <v-toolbar flat dense class="mx-1">
          <v-spacer></v-spacer>
          <v-btn v-if="asistant_comment.length > 0 && $store.getters.checkPermission('kpi-comission') && false
            " color="success" outlined @click="asistant()" class="ml-1">
            {{ $t("Asistant(Comment)") }}
          </v-btn>
          <span v-if="$store.getters.checkPermission('kpi-comission') && false" style="">
            <v-autocomplete label="Xodimlar" v-model="resolution_filter.resolution_comission_id"
              :items="resalution_employee" hide-details dense outlined item-text="text" item-value="id"
              class="mx-1"></v-autocomplete>
          </span>
          <v-btn v-if="$store.getters.checkPermission('kpi-comission') && false" color="success" outlined
            @click="sendEmployee()" class="ml-1">
            {{ $t("Send") }}
          </v-btn>
          <span v-if="doc_count && false" style="max-width: 200px">
            <v-autocomplete label="Document" v-model="filter.doc_id" :items="doc" hide-details @change="getlist" dense
              outlined item-value="id" item-text="document_number" class="mx-1"></v-autocomplete>
          </span>
          <span v-if="filter.department_id" style="max-width: 100px">
            <v-autocomplete label="Yil" v-model="filter.year" :items="year" hide-details @change="getlist" dense outlined
              item-value="value" item-text="text" class="mx-1"></v-autocomplete>
          </span>
          <span v-if="filter.department_id" style="max-width: 130px">
            <v-autocomplete label="Chorak" v-model="filter.quarter" :items="quarter" hide-details dense outlined
              @change="getlist" item-value="value" item-text="text" class="mx-1"></v-autocomplete>
          </span>
          <v-autocomplete
            v-if="$store.getters.checkPermission('kpi-comission') || $store.getters.checkPermission('kpi-manager')"
            label="Bo`limlar (Imzolangan / Hammasi)!" v-model="filter.department_id" :items="departmentsPlan" hide-details
            dense outlined item-text="text" item-value="id" @change="getlist" class="mx-1">
          </v-autocomplete>
          <!-- {{ '1-'+ kpi_user }}
          {{ '2-'+ kpi_view }}
          {{ '3-'+ saveOwner }}
          {{ '4-'+ saveComission }} -->
          <v-btn v-if="(saveComission) || user.id == 16" color="success" outlined @click="save()" class="ml-1">
            {{ $t("Saqlash!") }}
          </v-btn>

          <!-- <v-btn
            v-if="
              false &&
              !$store.getters.checkPermission('kpi-comission') &&
              !document_details.some(
                (v) =>
                  v.document_detail_fakt.achieving_goal == null ||
                  v.document_detail_fakt.reward_amount == null ||
                  v.document_detail_fakt.fakt == null
              ) &&
              document_details.some((v) => v.document_detail_fakt.status != 1)
            "
            color="success"
            outlined
            @click="closeAllDetails()"
            class="ml-1"
          >
            {{ $t("Saqlash va yakunlash") }}
          </v-btn> -->

          <!-- {{   
             ($store.getters.checkPermission('kpi-comission')) 
            }} -->


          <v-btn v-if="!$store.getters.checkPermission('kpi-comission') &&
            !document_details
              .filter((v) => !!v.document_detail_fakt.fakt)
              .some((v) => v.document_detail_fakt.status != 2) &&
            (kpi_user || kpi_view) && !validateCreateReportDoc
            && !document_details.filter((v) => v.document_detail_attribute_values.find((s) =>
              s.d_d_attribute_id == attributes[filter.quarter - 1][1]
            ).attribute_value != 0).some((v) => v.document_detail_fakt.comissions.length < 11)
            && (filter.quarter == depquarter[0])
            " color="primary" outlined dark @click="createDoc()" class="ml-1">
            {{ $t("Hujjat yasash") }}
          </v-btn>
          <!-- {{depquarter}} -->
          <!-- <span>{{
            !document_details.filter((v) =>v.document_detail_attribute_values.find((s) => 
            s.d_d_attribute_id == attributes[filter.quarter - 1][1]).attribute_value != 0).some((v) =>v.document_detail_fakt.comissions.length < 11 )            
            }}</span> -->
          <!-- <span>{{
            !document_details.some((v) =>v.document_detail_fakt.comissions.length < 11 )            
            }}</span> -->
        </v-toolbar>
        <!-- $store.getters.checkPermission('kpi-manager') -->
      </v-card-title>
      <v-card-text>
        <v-simple-table dense>
          <template v-slot:default>
            <thead>
              <tr>
                <th style="border: 1px solid #ddd" class="text-center text-bold" rowspan="2">
                  #
                </th>
                <th style="border: 1px solid #ddd; font-weight: bold" class="text-center text-bold" rowspan="2">
                  Ko'rsatkichlar
                </th>
                <th style="border: 1px solid #ddd; white-space: normal" class="text-center text-bold" rowspan="2">
                  Ko'rsatkichlar salmog'i
                </th>
                <th style="
                    border: 1px solid #ddd;
                    max-width: 100px;
                    white-space: normal;
                  " class="text-center text-bold" rowspan="2">
                  Mukofot miqdori
                </th>
                <th style="
                    border: 1px solid #ddd;
                    max-width: 100px;
                    white-space: normal;
                  " class="text-center text-bold" rowspan="2">
                  O'lchov birligi
                </th>
                <th style="border: 1px solid #ddd" class="text-center text-bold" colspan="3"
                  v-if="quarter[filter.quarter - 1]">
                  {{ quarter[filter.quarter - 1].text }}
                  <!-- {{ filter.quarter-1 }} -->
                </th>
                <th v-if="comissionLength" style="border: 1px solid #ddd" class="text-center text-bold"
                  :colspan="comissionLength">
                  Komissiyalar tasdiqlash joyi
                  <!-- {{ filter.quarter-1 }} -->
                </th>
              </tr>
              <tr>
                <th style="border: 1px solid #ddd" class="text-center text-bold">
                  Min
                </th>
                <th style="border: 1px solid #ddd" class="text-center text-bold">
                  Maqsad
                </th>
                <th style="border: 1px solid #ddd" class="text-center text-bold">
                  Max
                </th>
                <!-- <th style="border: 1px solid #ddd; background-color: #eaffea" class="text-center text-bold">
                  Fakt
                </th>
                <th style="border: 1px solid #ddd; background-color: #eaffea" class="text-center text-bold">
                  Maqsadga erishish
                </th>
                <th style="border: 1px solid #ddd; background-color: #eaffea" class="text-center text-bold">
                  Mukofot miqdori
                </th> -->
                <th v-for="(comission, index) in comissions" :key="index" style="border: 1px solid #ddd"
                  class="text-center text-bold">
                  {{ comission.employee.firstname_uz_latin }}
                  <br />
                  {{ comission.employee.lastname_uz_latin }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(detail, k) in document_details" :key="k">
                <td style="border: 1px solid #ddd">{{ k + 1 }}</td>
                <td style="
                    border: 1px solid #ddd;
                    min-width: 300px;
                    white-space: normal;
                    text-align: justify;
                  ">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        attributes[filter.quarter - 1][0]
                    ).attribute_value
                  }}
                </td>
                <td style="border: 1px solid #ddd">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        attributes[filter.quarter - 1][1]
                    ).attribute_value
                  }}
                </td>
                <td style="border: 1px solid #ddd">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        attributes[filter.quarter - 1][2]
                    ).attribute_value
                  }}
                </td>
                <td style="border: 1px solid #ddd">
                  {{
                    detail.document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        attributes[filter.quarter - 1][3]
                    ).value
                  }}
                </td>
                <td style="
                    border: 1px solid #ddd;
                    max-width: 250px;
                    white-space: normal;
                    text-align: justify;
                  ">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        d_d_attribute_id[filter.quarter - 1].min
                    ).attribute_value
                  }}
                </td>
                <td style="
                    border: 1px solid #ddd;
                    max-width: 250px;
                    white-space: normal;
                    text-align: justify;
                  ">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        d_d_attribute_id[filter.quarter - 1].aim
                    ).attribute_value
                  }}
                </td>
                <td style="
                    border: 1px solid #ddd;
                    white-space: normal;
                    min-width: 200px;
                  ">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) =>
                        v.d_d_attribute_id ==
                        d_d_attribute_id[filter.quarter - 1].max
                    ).attribute_value
                  }}
                </td>
                <td v-for="(comission, index) in detail.document_detail_fakt
                  .comissions" :key="index" width="100" style="border: 1px solid #ddd">
                  <div style="display: flex; justify-content: center">
                    <v-icon color="success" v-if="comission.status == 1">
                      mdi-check-bold
                    </v-icon>
                    <v-icon color="error" v-else-if="comission.status == 0">
                      mdi-close-thick
                    </v-icon>

                    <template v-if="change_reaction && comission.employee_id == employee.id
                      ">
                      <!-- <template
                      v-if="
                        change_reaction &&
                        comission.employee_id == employee.id &&
                        detail.document_detail_fakt.status < 1
                      "
                    >                    -->
                      <!-- comission.employee_id == employee.id &&
                        detail.document_detail_fakt.status == 1 &&
                        comission.status == null -->
                      <!-- <span>{{ change_reaction }}</span> -->
                      <v-btn class="ml-2 my-1 float-right" x-small outlined color="error"
                        @click="changeStatus(detail.document_detail_fakt, 0)">
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                      <v-btn style="margin-left: 5px;" class="mr-2 my-1 float-right" x-small outlined color="primary"
                        @click="changeStatus(detail.document_detail_fakt, 1)">
                        <v-icon>mdi-check</v-icon>
                      </v-btn>
                    </template>
                  </div>
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
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="asistantCommentDialog" width="800" persistent hide-overlay>
      <v-card>
        <v-card-title>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="asistantCommentDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-card v-for="(value, index) in asistant_comment" :key="index">
            <v-card-title>
              {{
                value.resolution_employee.firstname +
                " " +
                value.resolution_employee.lastname +
                " " +
                value.resolution_employee.middlename
              }}
            </v-card-title>
            <v-card-text style="font-size: 20px">
              {{ value.comments }}
            </v-card-text>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="modalCommentFile" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.add_file") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalCommentFile = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form>
            <v-row>
              <v-col cols="10" style="min-width: 100px; max-width: 100%" class="flex-grow-1 flex-shrink-0">
                <label for>{{ $t("document.fileses") }}</label>
                <v-file-input v-model="selectCommentFiles" outlined dense multiple prepend-icon
                  append-icon="mdi-file-pdf-box-outline" accept=".pdf, application/pdf" small-chips show-size
                  hide-details="auto"></v-file-input>
              </v-col>
              <v-col cols="2" style="min-width: 100px" class="px-0">
                <v-btn :disabled="!selectCommentFiles || selectCommentFiles.length == 0" class="mt-6" color="success"
                  block @click="addCommentFiles">
                  +
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="modalDocumentFile" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.add_file") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalDocumentFile = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="10" style="min-width: 100px; max-width: 100%" class="flex-grow-1 flex-shrink-0">
                <label for>{{ $t("document.files") }}</label>
                <v-file-input v-model="selectFiles" outlined dense multiple prepend-icon
                  append-icon="mdi-file-pdf-box-outline" accept=".pdf, application/pdf" small-chips show-size
                  hide-details="auto"></v-file-input>
              </v-col>
              <v-col cols="2" style="min-width: 100px" class="px-0">
                <v-btn :disabled="!selectFiles || selectFiles.length == 0" class="mt-6" color="success" block
                  @click="addFiles">
                  +
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card  class="ma-1 pa-1">
      <v-card-title>Yuklangan fayllar
        <v-spacer></v-spacer>     

       
      </v-card-title>
      <v-card-text class="pt-0">
        <v-simple-table v-if="true ||
          (document_details.length > 0 &&
            documentFiles &&
            documentFiles.length)
          " dense class="mt-2" style="border: 1px solid #aaa">
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
              <tr v-for="(item, index) in doc.files" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ item.file_name }}</td>
                <td class="text-lg-right" width="100px">
                  <a :href="$store.state.backend_url + 'kpi/file-download/' + item.id
                    ">
                    <v-icon class="px-1" color="success">mdi-eye</v-icon>
                  </a>
                  <v-icon v-if="!$store.getters.checkPermission('kpi-comission') &&
                    !$store.getters.checkPermission('kpi-manager') && change_data
                    " class="px-1" color="error" @click="deleteFile(item)">mdi-delete</v-icon>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
    </v-card>
    <v-card v-if="true || change_data || change_reaction" class="ma-1 pa-1">
      <!-- <v-card-title v-if="!$store.getters.checkPermission('kpi-manager')" -->
      <v-card-title>Muloqot oynasi
        <v-autocomplete  label="Ko'rsatkich" v-model="d_d_a_v_id" :items="d_d_a_v_name"
          hide-details dense outlined item-value="ddav_id" item-text="name" class="mx-1"
          @change="getComments()"></v-autocomplete>
      </v-card-title>
      <v-toolbar flat dense class="py-5" v-if="d_d_a_v_id" style="max-width: 90%">
        <template>
          <v-textarea v-model="comment" rows="1" outlined class="my-4">
            <template v-slot:prepend>
              <v-icon @click="commentFiles" color="blue" large>mdi-paperclip</v-icon>
            </template>
            <template v-slot:append>
              <v-btn class="mb-5" text @click="saveComments">
                <v-icon color="green" large>mdi-send</v-icon>
              </v-btn>
            </template>

          </v-textarea>

          <!-- <v-textarea
            v-model="comment"
            rows="1"
            outlined
            class="my-4"
            @keyup.native.shift.enter="saveComments"
          >
            <template v-slot:append>    
              <v-btn  text @click="saveComments">         
                <v-icon  color="green" large >mdi-send</v-icon>   
              </v-btn>           
            </template>
          </v-textarea> -->
        </template>
      </v-toolbar>
      <v-card-text>
        <v-card v-for="(item, index) in document_details" :key="index" class="my-2">
          <v-card-title>
            {{
             item.document_detail_attribute_values.find(
                (v) =>
                  v.d_d_attribute_id == attributes[filter.quarter - 1][0]
              ).attribute_value
            }}
          </v-card-title>
          <v-card-text>
            <!-- <div v-for="(textComment, i) in item.kpi_comments.filter(
              (v) =>
                v.kpi_objekt.quarter == filter.quarter &&
                v.kpi_objekt.years == filter.year
            )" :key="i"> -->
            <div v-for="(textComment, i) in item.kpi_comments" :key="i">
              <v-alert border="right" dense outlined color="green" class="my-1">
                <div>
                  <b v-if="textComment.employee">
                    {{
                      textComment.employee.firstname_uz_latin +
                      " " +
                      textComment.employee.lastname_uz_latin +
                      " " +
                      momentTime(textComment.created_at)
                    }}</b>
                </div>
                <div>
                  {{ textComment.comment }}
                  <v-chip small v-for="(file, i) in textComment.files" :key="i">
                    {{ file.file_name }}
                    <a :href="$store.state.backend_url + 'kpi/file-download/' + file.id">
                      <v-icon class="px-1" color="success">mdi-eye</v-icon>
                    </a>
                  </v-chip>
                </div>
              </v-alert>
            </div>
          </v-card-text>
        </v-card>
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
      validateCreateReportDoc: false,
      d_d_id: null,
      d_d_a_v_id: null,
      d_d_name: [],
      d_d_a_v_name: [],
      kpi_documents: null,
      textComments: null,
      comment: "",
      asistant_comment: "",
      asistantCommentDialog: false,
      search: "",
      comment_window: false,
      kpi_user: false,
      kpi_view: false,
      status_user: false,
      loading: false,
      doc_count: false,
      departmentsPlan: [],
      years: [2023, 2024],
      year: [
        { text: moment().subtract(1, 'years').format('YYYY'), value: moment().subtract(1, 'years').format('YYYY') },
        { text: moment().format("YYYY"), value: moment().format("YYYY") }
      ],
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
      // d_d_attribute_id: [
      //   [1321,1322,1323,1324],
      //   [2078,2079,2080,2081],
      //   [2082,2083,2084,2085],
      //   [2086,2087,2088,2089],
      // ],
      resolution_filter: {
        resolution_comission_id: "",
        document_id: "",
      },
      filter: {
        year: null,
        quarter: 1,
        year: moment().format("YYYY"),
        // quarter: 1,
        // department_id: null,
        department_id: null,
        d_d_id: null,
      },
      employee: null,
      resalution_employee: [],
      access_reaction: false,
      validatesaveowner: false,
      comissions: [],
      doc: [],
      document_details: [],
      modalDocumentFile: false,
      modalCommentFile: false,
      selectFiles: [],
      selectCommentFiles: [],
      formData: null,
      documentFiles: [],
      saveComission: false,
      saveOwner: false,
      change_data: false,
      change_reaction: false,
      selectvideo: null,
      depquarter: null,
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
    momentTime(time) {
      return moment(time).format("DD.MM.YYYY hh:mm A");
    },

    saveComments() {
      axios
        .post(this.$store.state.backend_url + "api/documents/kpi-comments-plan", {
          comment: this.comment,
          filter: this.filter,
        })
        .then((res) => {
          axios
            .post(
              this.$store.state.backend_url +
              "api/documents/kpi-commentfiles/" +
              res.data,
              this.formData,
              {
                headers: {
                  "Content-Type": "multipart/form-data",
                },
              }
            )
            .then((resp) => {
              this.loading = false;
              Swal.fire({
                icon: "success",
                title: "OK!",
                text: "Ma`lumotlar saqlandi!",
              });
            });
          this.comment = "";
          this.selectCommentFiles = [];
          this.getlist();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getComments() {
      this.filter.d_d_a_v_id = this.d_d_a_v_id;  
      // console.log(this.document_details.find((v) => v.document_detail_attribute_values.find((s) => s.id  == this.d_d_a_v_id)).id);  

      this.filter.d_d_id = this.document_details.find((v) => v.document_detail_attribute_values.find((s) => s.id  == this.d_d_a_v_id)).id;    
    },
    getFiles() {
      this.documentFiles = [];
      axios
        .post(this.$store.state.backend_url + "api/documents/kpi-getfiles-new", {
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

    changeStatus(fact, status) {
      this.document_details = this.document_details.map((d) => {
        d.document_detail_fakt.comissions =
          d.document_detail_fakt.comissions.map((c) => {
            if (c.employee_id == this.employee.id && c.d_d_fakt_id == fact.id) {
              c.status = status;
            }
            return c;
          });
        return d;
      });
    },
    getDepartments(e) {
      // if(this.saveComission || this.saveOwner || this.user.id == 6){
      axios
        .post(this.$store.state.backend_url + "api/documents/kpi-departments-plan", {
          locale: this.$i18n.locale,
          years: this.filter.year,
          // quarter:  this.filter.quarter,
          // quarter: e ? e[0] : this.filter.quarter,
        })
        .then((res) => {
          this.departmentsPlan = res.data.departments.map((v) => {
            v.text =
              v.department_code +
              " " +
              v.name_uz_latin +
              " " +
              (parseInt(v.ok ? v.ok : 0) + parseInt(v.cancel ? v.cancel : 0)) +
              "/" +
              (parseInt(v.ok ? v.ok : 0) +
                parseInt(v.cancel ? v.cancel : 0) +
                parseInt(v.notok ? v.notok : 0));
            return v;
          });
          if (e == 0) {
            // this.filter.quarter = e==0 ? res.data.chorak : e;
            // this.filter.quarter = res.data.chorak;
          }
          // this.depquarter = res.data.chorak;
        })
        .catch((error) => {
          console.log(error);
        });
      // }
    },
    getDocumentFile() {
      this.modalDocumentFile = true;
    },
    commentFiles() {
      this.modalCommentFile = true;
    },
    addFiles() {
      this.selectFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
        this.documentFiles.push({
          id: Date.now() + Math.floor(Math.random() * 1000),
          file_name: v.name,
        });
      });
      // console.log(this.documentFiles);
      this.selectFiles = [];
      this.modalDocumentFile = false;
    },
    addCommentFiles() {
      this.selectCommentFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
        // this.documentFiles.push({
        //   id: Date.now() + Math.floor(Math.random() * 1000),
        //   file_name: v.name,
        // });
      });
      // console.log(this.documentFiles);
      this.selectFiles = [];
      this.modalCommentFile = false;
    },
    deleteFile(item) {
      if (!this.$store.getters.checkPermission("kpi-comission")) {
        axios
          .delete(
            this.$store.state.backend_url +
            "api/documents/delete-files/" +
            item.id
          )
          .then((res) => {
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

        this.formData.delete(item.file_name);
        this.documentFiles = this.documentFiles.filter((v) => v.id != item.id);
      }
    },
    save() {
      this.loading = true;

      axios
        .post(
          this.$store.state.backend_url + "api/documents/save-reactions",
          {
            document_details: this.document_details,
            filter: this.filter,
          }
        )
        .then((res) => {
          this.loading = false;
          this.getlist();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    validateCreateDoc() {
      this.validateCreateReportDoc = true;
      axios
        .post(this.$store.state.backend_url + "api/kpi/validate-kpi-create", {
          filter: this.filter,
        })
        .then((res) => {
          // this.validateCreateReportDoc = res.data;
          if (res.data == "no report_doc_id!") {
            this.validateCreateReportDoc = false;
            // console.log('sasa_'+this.validateCreateReportDoc)
            // } else {
            // this.validateCreateReportDoc = false;
            // console.log('dada_'+this.validateCreateReportDoc)
            // console.log('dada');
          }
          // console.log(res.data);
          // console.log(this.validateCreateReportDoc);
          // console.log(this.validateCreateReportDoc);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    createDoc() {
      axios
        .post(this.$store.state.backend_url + "api/kpi-fact-create", {
          document_details: this.document_details,
          filter: this.filter,
          locale: this.$i18n.locale,
        })
        .then((res) => {
          // if(this.user.username != 'ja2923'){
          this.$router.push("/document/" + res.data.pdf_file_name);
          // }
          this.validateCreateReportDoc = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    percent_reward_amount(detail) {
      let a =
       detail.document_detail_attribute_values.find(
            (v) =>
              v.d_d_attribute_id ==
              this.attributes[this.filter.quarter - 1][2]
          ).attribute_value;
      detail.document_detail_fakt.reward_amount =
        (detail.document_detail_fakt.achieving_goal * a) / 100;
    },
    document(response) {
      // console.log(this.filter.doc_id);
      // console.log(response.doc);
      this.kpi_documents = (response.doc && response.doc.length == 1) ? response.doc :
        (response.doc && response.doc.length > 1) ? response.doc.filter((v) => v.id == this.filter.doc_id) : '';
    },
    getlist() {
      // console.log(this.filter);
      this.access_reaction = false;
      this.comment_window = true;
      this.change_data = false;
      this.change_reaction = false;
      // this.asistantComment();
      // this.validateCreateDoc();
      this.getDepartments(this.depquarter);
      // console.log('sasa');
      if (this.filter.department_id || !this.$store.getters.checkPermission("kpi-comission"))
        // console.log(this.filter);
        this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/documents/get-attributes-plan",
          {
            filter: this.filter,
          }
        )
        .then((response) => {
          this.loading = false;
          this.validatesaveowner = response && response.data && response.data.document_details ?
            response.data.document_details.some((v) => v.document_detail_fakt && v.document_detail_fakt.status && v.document_detail_fakt.status != 2) :
            false;
          this.document(response.data);


          if (response.data.kpi_user) {

            this.kpi_view = true;
          }
          if (response.data.document) {
            this.doc = response.data.document;
            // console.log(this.doc);
          }
          this.d_d_a_v_name = [];
          // console.log('change_data',this.change_data);
          // console.log(response);
          if (!this.$store.getters.checkPermission("kpi-comission")) {
            this.getFiles();
          }
          if (
            this.$store.getters.checkPermission("kpi-comission") &&
            this.filter.department_id != null
          ) {
            this.getFiles();
          }

          if (response.data && response.data.document_details && response.data.document_details[0].document_id) {
            this.resolution_filter.document_id =
              response.data.document_details[0].document_id;

            response.data.document_details.forEach((e) => {
              // response.data.document_details.map((e) => {
              this.d_d_a_v_name.push({
                id: Date.now(),
                d_id: e.id,                               
                ddav_id: e.document_detail_attribute_values.find(
                      (v) =>  v.d_d_attribute_id == this.attributes[this.filter.quarter - 1][0]
                    ).id,
                name: e.document_detail_attribute_values.find(
                      (v) =>  v.d_d_attribute_id == this.attributes[this.filter.quarter - 1][0]
                    ).attribute_value,
              });
            });

          }

          this.change_data = response.data.change_data;
          this.change_reaction = response.data.change_reaction;
          // this.filter.quarter = response.data.quarter;
          if (response.data && response.data.document_details) {
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
          }
          // console.log(this.filter);
          // console.log(response);
          // console.log(this.attributes[this.filter.quarter - 1][0]);
        })
        .then(() => {
          if (this.$store.getters.checkPermission("kpi-comission")) {
            this.document_details = this.document_details.map((v) => {
              if (
                this.$store.getters.checkPermission("kpi-comission") &&
                v.document_detail_fakt.comissions.length < 11 &&
                !v.document_detail_fakt.comissions.some(
                  (c) => c.employee_id == this.employee.id
                )
              ) {
                v.document_detail_fakt.comissions.push({
                  comment: null,

                  created_at: new Date().toISOString().slice(0, 16),
                  d_d_fakt_id: v.document_detail_fakt.id,
                  employee: {
                    id: this.employee.id,
                    firstname_ru: this.employee.firstname_ru,
                    firstname_uz_cyril: this.employee.firstname_uz_cyril,
                    firstname_uz_latin: this.employee.firstname_uz_latin,
                    lastname_ru: this.employee.lastname_ru,
                    lastname_uz_cyril: this.employee.lastname_uz_cyril,
                    lastname_uz_latin: this.employee.lastname_uz_latin,
                    middlename_ru: this.employee.middlename_ru,
                    middlename_uz_cyril: this.employee.middlename_uz_cyril,
                    middlename_uz_latin: this.employee.middlename_uz_latin,
                    tabel: this.employee.tabel,
                  },
                  employee_id: this.employee.id,
                  id: null,
                  status: null,
                });
              }
              v.document_detail_fakt.comissions =
                v.document_detail_fakt.comissions.sort(
                  (a) => new Date(a.created_at) * -1
                );
              return v;
            });
          }
          this.loading = false;
        })
        .then(() => {
          this.comissions = this.document_details[0] && this.document_details[0].document_detail_fakt ? this.document_details[0].document_detail_fakt.comissions : '';
          this.document_details.forEach((d) => {
            d.document_detail_fakt.comissions.forEach((c) => {
              if (c.employee_id == this.employee.id && c.status == null) {
                this.access_reaction = true;
              }
            });
          });
        })
        .then(() => {
          this.saveComission = false;
          this.saveOwner = false;
          // if(this.$store.getters.checkPermission('kpi-comission') && !this.document_details.some((v) =>v.document_detail_fakt.status != 1) &&
          //     this.document_details.some((v) => v.document_detail_fakt.comissions.some((c) =>c.status == null && c.employee_id == this.employee.id)))

          if (this.change_reaction && this.$store.getters.checkPermission('kpi-comission')) {
            this.saveComission = true;
          }
          // if(this.$store.getters.checkPermission('kpi-owner') && this.document_details.some((v) =>v.document_detail_fakt.status != 1))

          if (this.change_data) {
            this.saveOwner = true;
          }
          // console.log(this.kpi_user)
          if ((this.document_details.length > 0 && this.document_details[0].document.created_employee_id == this.employee.id)) {
            // console.log('sasas')
            this.kpi_user = true;
          }
          if (this.validatesaveowner) {
            this.saveOwner = true;
          }
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    resolutionEmployee() {
      axios
        .post(this.$store.state.backend_url + "api/kpi/resolution-employee", {
          language: this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale,
        })
        .then((res) => {
          this.resalution_employee = res.data.map((v) => {
            v.text = v.tabel + " " + v.firstname + " " + v.lastname;
            return v;
          });
        })
        .catch((error) => {
          console.log(error);
        });
    },
    sendEmployee() {
      axios
        .post(
          this.$store.state.backend_url + "api/kpi/send-resolution-employee",
          {
            resolution_filter: this.resolution_filter,
            filter: this.filter,
          }
        )
        .then((res) => {
          if (res.data == "success") {
            Swal.fire({
              icon: "success",
              title: "OK!",
              text: "Kpi junatildi",
            });
          }
          if (res.data == "double") {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: "Allaqachon junatilgan",
            });
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    asistant() {
      this.asistantCommentDialog = true;
    },
    asistantComment() {
      axios
        .post(this.$store.state.backend_url + "api/kpi/asistant-comment", {
          filter: this.filter,
          language: this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale,
        })
        .then((res) => {
          this.asistant_comment = res.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getquarter() {
      axios
        .get(this.$store.state.backend_url + "api/kpi/quarter")
        .then((response) => {
          // console.log(response)
          this.filter.quarter = response.data[0];
          this.filter.year = response.data[1];
          this.depquarter = response.data;
          this.getlist();
        })
        .catch((error) => {
          console.log(error);
        });
    },

  },
  mounted() {
    // this.validateCreateReportDoc = true;
    // this.resolutionEmployee();

    // if (
    //   this.$store.getters.checkPermission("kpi-comission") ||
    //   this.$store.getters.checkPermission("kpi-manager")
    // ) {
    //   this.getDepartments(0);
    // }
    // this.getDepartments(0);
    // this.getquarter();
    this.getlist();
    // if (!this.$store.getters.checkPermission("kpi-comission")) {
    //   this.getlist();
    // }
    this.employee = JSON.parse(localStorage.getItem("user")).employee;
    this.formData = new FormData();
    // console.log(this.employee);
  },
};
</script>
