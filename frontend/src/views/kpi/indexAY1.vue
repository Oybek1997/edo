<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title>
        KPI
        <v-spacer></v-spacer>
        <v-toolbar flat dense class="mx-1" v-if="!$store.getters.checkPermission('kpi-manager')">
          <v-spacer></v-spacer>
          <v-btn
            v-if="
              asistant_comment.length > 0 &&
              $store.getters.checkPermission('kpi-comission')
            "
            color="success"
            outlined
            @click="asistant()"
            class="ml-1"
          >
            {{ $t("Asistant(Comment)") }}
          </v-btn>
          <span v-if="$store.getters.checkPermission('kpi-comission')" style="">
            <v-autocomplete
              label="Xodimlar"
              v-model="resolution_filter.resolution_comission_id"
              :items="resalution_employee"
              hide-details
              dense
              outlined
              item-text="text"
              item-value="id"
              class="mx-1"
            ></v-autocomplete>
          </span>
          <v-btn
            v-if="$store.getters.checkPermission('kpi-comission')"
            color="success"
            outlined
            @click="sendEmployee()"
            class="ml-1"
          >
            {{ $t("Send") }}
          </v-btn>
          <span style="max-width: 100px">
            <v-autocomplete
              label="Yil"
              v-model="filter.year"
              :items="years"
              hide-details
              @change="getlist"
              dense
              outlined
              item-value="id"
              class="mx-1"
            ></v-autocomplete>
          </span>
          <span style="max-width: 130px">
            <v-autocomplete
              label="Chorak"
              v-model="filter.quarter"
              :items="quarter"
              hide-details
              dense
              outlined
              @change="getlist"
              item-value="value"
              item-text="text"
              class="mx-1"
            ></v-autocomplete>
          </span>
          <v-autocomplete
            v-if="$store.getters.checkPermission('kpi-comission')"
            label="Bo`limlar (Imzolangan / Hammasi)"
            v-model="filter.department_id"
            :items="departments"
            hide-details
            dense
            outlined
            item-text="text"
            item-value="id"
            @change="getlist"
            class="mx-1"
          ></v-autocomplete>
          <v-btn
            v-if="true"
            color="success"
            outlined
            @click="save()"
            class="ml-1"
          >
            {{ $t("Saqlash") }}
          </v-btn>
          <v-btn
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
          </v-btn>
          <!-- $store.getters.checkPermission('kpi-owner') &&
              !document_details.some((v) => v.document_detail_fakt.comissions.filter((c) => c.status == 1).length < 4) &&
              !document_details.some((v) => v.document_detail_fakt.comissions.filter((c) => c.status == 0).length > 3) &&
              !document_details.some((v) => v.document_detail_fakt.comissions.some((c) => c.status == null)) &&
              !document_details.some((v) => v.document_detail_fakt.status != 1) -->
          <v-btn
            v-if="
             ( !$store.getters.checkPermission('kpi-comission') &&
              !document_details.some(
                (v) => v.document_detail_fakt.status != 2
              ) &&
              kpi_user && !validateCreateReportDoc)
            "
            color="primary"
            outlined
            dark
            @click="createDoc()"
            class="ml-1"
          >
            {{ $t("Create Document") }}
          </v-btn>
          <!-- <span>{{validateCreateReportDoc}}</span> -->
        </v-toolbar>
        <v-toolbar flat dense class="mx-1" v-if="$store.getters.checkPermission('kpi-manager')">
          <v-spacer></v-spacer>
          <span style="max-width: 100px">
            <v-autocomplete
              label="Yil"
              v-model="filter.year"
              :items="years"
              hide-details
              @change="getlist"
              dense
              outlined
              item-value="id"
              class="mx-1"
            ></v-autocomplete>
          </span>
          <span style="max-width: 130px">
            <v-autocomplete
              label="Chorak"
              v-model="filter.quarter"
              :items="quarter"
              hide-details
              dense
              outlined
              @change="getlist"
              item-value="value"
              item-text="text"
              class="mx-1"
            ></v-autocomplete>
          </span>
          <v-autocomplete
            label="Bo`limlar (Imzolangan / Hammasi)"
            v-model="filter.department_id"
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
      <v-card-text>
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
                  v-if="quarter[filter.quarter - 1]"
                >
                  {{ quarter[filter.quarter - 1].text }}
                  <!-- {{ filter.quarter-1 }} -->
                </th>
                <th
                  v-if="comissionLength"
                  style="border: 1px solid #ddd"
                  class="text-center text-bold"
                  :colspan="comissionLength"
                >
                  Komissiyalar tasdiqlash joyi
                  <!-- {{ filter.quarter-1 }} -->
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
                <th
                  v-for="(comission, index) in comissions"
                  :key="index"
                  style="border: 1px solid #ddd"
                  class="text-center text-bold"
                >
                  {{ comission.employee.firstname_uz_latin }}
                  <br />
                  {{ comission.employee.lastname_uz_latin }}
                </th>
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
                      (v) => v.d_d_attribute_id == 1321
                    ).attribute_value
                  }}
                </td>
                <td style="border: 1px solid #ddd">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) => v.d_d_attribute_id == 1322
                    ).attribute_value
                  }}
                </td>
                <td style="border: 1px solid #ddd">
                  {{
                    detail.document_detail_attribute_values.find(
                      (v) => v.d_d_attribute_id == 1323
                    ).attribute_value
                  }}
                </td>
                <td style="border: 1px solid #ddd">
                  {{
                    detail.document_detail_contents.find(
                      (v) => v.d_d_attribute_id == 1324
                    ).value
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
                    white-space: normal;
                    min-width: 200px;
                  "
                >
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
                  <v-textarea
                    rows="2"
                    v-if="true"
                    v-model.lazy="detail.document_detail_fakt.fakt"
                    hide-details
                  ></v-textarea>
                  <template v-else>
                    {{ detail.document_detail_fakt.fakt }}
                  </template>
                </td>
                <td style="border: 1px solid #ddd; background-color: #eaffea">
                  <v-select
                  v-if="true"
                    class="mt-4"
                    v-model.lazy="detail.document_detail_fakt.achieving_goal"
                    :items="percent"
                    :menu-props="{ bottom: true, offsetY: true }"
                    dense
                    hide-details
                    @change="percent_reward_amount(detail)"
                  ></v-select>
                  <template v-else>
                    {{ detail.document_detail_fakt.achieving_goal }}
                  </template>
                </td>
                <td style="border: 1px solid #ddd; background-color: #eaffea">
                  <template>
                    {{ detail.document_detail_fakt.reward_amount }}
                  </template>
                </td>
                <td
                  v-for="(comission, index) in detail.document_detail_fakt
                    .comissions"
                  :key="index"
                  width="100"
                  style="border: 1px solid #ddd"
                >
                  <div style="display: flex; justify-content: center">
                    <v-icon color="success" v-if="comission.status == 1">
                      mdi-check-bold
                    </v-icon>
                    <v-icon color="error" v-else-if="comission.status == 0">
                      mdi-close-thick
                    </v-icon>

                    <template
                      v-if="
                        change_reaction && comission.employee_id == employee.id
                      "
                    >
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
                      <v-btn
                        class="ml-2 my-1 float-right"
                        x-small
                        outlined
                        color="error"
                        @click="changeStatus(detail.document_detail_fakt, 0)"
                      >
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                      <v-btn
                        class="mr-2 my-1 float-right"
                        x-small
                        outlined
                        color="primary"
                        @click="changeStatus(detail.document_detail_fakt, 1)"
                      >
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
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="asistantCommentDialog"
      width="800"
      persistent
      hide-overlay
    >
      <v-card>
        <v-card-title>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="asistantCommentDialog = false"
          >
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
    <v-dialog v-model="modalDocumentFile" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.add_file") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="modalDocumentFile = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col
                cols="10"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("document.files") }}</label>
                <v-file-input
                  v-model="selectFiles"
                  outlined
                  dense
                  multiple
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf, application/pdf"
                  small-chips
                  show-size
                  hide-details="auto"
                ></v-file-input>
              </v-col>
              <v-col cols="2" style="min-width: 100px" class="px-0">
                <v-btn
                  :disabled="!selectFiles || selectFiles.length == 0"
                  class="mt-6"
                  color="success"
                  block
                  @click="addFiles"
                >
                  +
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card v-if="change_data || change_reaction" class="ma-1 pa-1">
      <v-card-title
        >Yuklangan fayllar
        <v-spacer></v-spacer>
        <v-form ref="videoFileForm" style="width: 500px">
          <v-file-input
            v-if="kpi_user"
            v-model="selectvideo"
            outlined
            label="Video hisobot"
            :rules="[
              (value) =>
                !value ||
                value.size < 100000000 ||
                'Avatar size should be less than 2 MB!',
              (value) => !value || !!/(\.mp4)$/i.exec(value.name) || 'MP4 emas',
            ]"
            prepend-inner-icon="mdi-youtube"
            :prepend-icon="null"
            dense
            append-outer-icon="mdi-send"
            @click:append-outer="sendVideoFile()"
            accept=".mp4, application/mp4"
            small-chips
            show-size
            hide-details="auto"
          ></v-file-input>
        </v-form>

        <v-btn
          v-if="kpi_user"
          color="success"
          title
          outlined
          @click="getDocumentFile"
          class="ml-6"
        >
          <v-icon left style="font-size: 28px">mdi-file-upload-outline</v-icon>
          {{ "File-upload" }}
        </v-btn>
      </v-card-title>
      <v-card-text class="pt-0">
        <v-simple-table
          v-if="
            document_details.length > 0 && documentFiles && documentFiles.length
          "
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
                  <v-icon
                    v-if="!$store.getters.checkPermission('kpi-comission') && !$store.getters.checkPermission('kpi-manager')"
                    class="px-1"
                    color="error"
                    @click="deleteFile(item)"
                    >mdi-delete</v-icon
                  >
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
    </v-card>
    <v-card v-if="change_data || change_reaction" class="ma-1 pa-1">
      <v-card-title v-if="!$store.getters.checkPermission('kpi-manager')"
        >Muloqot oynasi
        <v-autocomplete
          label="Ko'rsatkich"
          v-model="d_d_id"
          :items="d_d_name"
          hide-details
          dense
          outlined
          item-value="d_id"
          item-text="name"
          class="mx-1"
          @change="getComments()"
        ></v-autocomplete>
      </v-card-title>
      <v-toolbar flat dense class="py-5" v-if="d_d_id" style="max-width: 90%">
        <v-textarea
          v-model="comment"
          rows="1"
          outlined
          class="my-4"
          @keyup.native.shift.enter="saveComments"
        ></v-textarea>
        <v-btn
          class="mx-2 mt-n5 float-right"
          outlined
          small
          fab
          color="primary"
          @click="saveComments"
        >
          <v-icon>mdi-arrow-right-bold</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text>
        <v-card
          v-for="(item, index) in document_details"
          :key="index"
          class="my-2"
        >
          <v-card-title>
            {{
              item.document_detail_attribute_values.find(
                (v) => v.d_d_attribute_id == 1321
              ).attribute_value
            }}
          </v-card-title>
          <v-card-text>
            <div v-for="(textComment, i) in item.kpi_comments">
              <v-alert border="right" dense outlined color="green" class="my-1">
                <div>
                  <b v-if="textComment.employee">
                    {{
                      textComment.employee.firstname_uz_latin +
                      " " +
                      textComment.employee.lastname_uz_latin +
                      " " +
                      textComment.created_at.substr(0, 16)
                    }}</b
                  >
                </div>
                <div>{{ textComment.comment }}</div>
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
      d_d_name: [],
      textComments: null,
      comment: "",
      asistant_comment: "",
      asistantCommentDialog: false,
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
      resolution_filter: {
        resolution_comission_id: "",
        document_id: "",
      },
      filter: {
        year: moment().format("YYYY"),
        quarter: 1,
        department_id: null,
        d_d_id: null,
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
        .post(this.$store.state.backend_url + "api/documents/kpi-comments", {
          comment: this.comment,
          filter: this.filter,
        })
        .then((res) => {
          //  console.log(res);
          this.comment = "";
          // this.getComments();
          this.getlist();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getComments() {
      this.filter.d_d_id = this.d_d_id;
      // axios
      //   .post(this.$store.state.backend_url + "api/documents/kpi-getcomments", {
      //     filter: this.filter,
      //   })
      //   .then((res) => {
      //     this.textComments = res.data;
      //     // console.log(res);
      //   })
      //   .catch((error) => {
      //     console.log(error);
      //   });
    },
    getFiles() {
      this.documentFiles = [];
      axios
        .post(this.$store.state.backend_url + "api/documents/kpi-getfiles", {
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
    closeAllDetails() {
      axios
        .post(
          this.$store.state.backend_url + "api/documents/close-all-details",
          {
            document_details: this.document_details,
          }
        )
        .then((res) => {
          this.getlist();
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
    getDepartments() {
      axios
        .post(this.$store.state.backend_url + "api/documents/kpi-departments", {
          locale: this.$i18n.locale,
        })
        .then((res) => {
          this.departments = res.data.departments.map((v) => {
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
          this.filter.quarter = res.data.chorak;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getDocumentFile() {
      this.modalDocumentFile = true;
    },
    addFiles() {
      this.selectFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
        this.documentFiles.push({
          id: Date.now() + Math.floor(Math.random() * 1000),
          file_name: v.name,
        });
      });
      console.log(this.documentFiles);
      this.selectFiles = [];
      this.modalDocumentFile = false;
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
      // if (!this.documentFiles.some((v) => v.file_name.toUpperCase().substr(-4,4) == ".MP4")) {
      //   Swal.fire({
      //     icon: "error",
      //     title: "Xatolik",
      //     text: "Video fayl yuklash majburiy",
      //   });
      // } else
      {
        if (true) {
          axios
            .post(this.$store.state.backend_url + "api/documents/save-facts", {
              document_details: this.document_details,
              filter: this.filter,
            })
            .then((res) => {
              // console.log(res);
              axios
                .post(
                  this.$store.state.backend_url +
                    "api/documents/kpi-updatefiles/" +
                    res.data,
                  this.formData,
                  {
                    headers: {
                      "Content-Type": "multipart/form-data",
                    },
                  }
                )
                .then((resp) => {
                  this.selectvideo = null;
                  this.getFiles();
                  this.loading = false;
                  Swal.fire({
                    icon: "success",
                    title: "OK!",
                    text: "Ma`lumotlar saqlandi!",
                  });
                });
              this.getlist();
            })
            .catch((error) => {
              console.log(error);
            });
        } else {
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
        }
      }
    },
    validateCreateDoc(){
      axios
        .post(this.$store.state.backend_url + "api/kpi/validate-kpi-create", {
          filter: this.filter,
        })
        .then((res) => {
          // this.validateCreateReportDoc = res.data;
          if(res.data != 'no report_doc_id!'){
            this.validateCreateReportDoc = true;
          }
          // console.log(res);
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
          this.$router.push("/document/" + res.data.pdf_file_name);
          this.validateCreateReportDoc = false;
        })
        .catch((error) => {
          console.log(error);
        });
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
      this.asistantComment();
      this.validateCreateDoc();
      axios
        .post(this.$store.state.backend_url + "api/documents/get-attributes", {
          filter: this.filter,
        })
        .then((response) => {
          this.d_d_name = [];
          // console.log('change_data',this.change_data);
          if (!this.$store.getters.checkPermission("kpi-comission")) {
            this.getFiles();
          }
          if (
            this.$store.getters.checkPermission("kpi-comission") &&
            this.filter.department_id != null
          ) {
            this.getFiles();
          }
          this.resolution_filter.document_id =
            response.data.document_details[0].document_id;
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
          this.filter.quarter = response.data.quarter;
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
        })
        .then(() => {
          this.comissions =
            this.document_details[0].document_detail_fakt.comissions;
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
          {
            if (this.change_reaction) {
              this.saveComission = true;
            }
          }
          // if(this.$store.getters.checkPermission('kpi-owner') && this.document_details.some((v) =>v.document_detail_fakt.status != 1))
          {
            if (this.change_data) {
              this.saveOwner = true;
            }
          }

          if (
            this.document_details.length > 0 &&
            this.document_details[0].document.created_employee_id ==
              this.employee.id
          ) {
            this.kpi_user = true;
          }
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
  },
  mounted() {
    this.resolutionEmployee();
   
    if (this.$store.getters.checkPermission("kpi-comission") || this.$store.getters.checkPermission("kpi-manager")) {
      this.getDepartments();
    }
    if (!this.$store.getters.checkPermission("kpi-comission")) {
      this.getlist();
    }
    this.employee = JSON.parse(localStorage.getItem("user")).employee;
    this.formData = new FormData();
  },
};
</script>
