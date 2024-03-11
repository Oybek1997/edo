<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <span></span>
      <v-card-title class="px-5 py-2">
        <span class="headerTitle">
          <span @click="justExit()" style="cursor: pointer">
            <v-icon style="color: #1e43a2">mdi-keyboard-backspace</v-icon>
            {{ $t("linestop.back") }}
          </span>
          / T-00{{ $route.params.id }} - {{ getShopValue() }},
          {{ getLineValue() }}, {{ getSectorValue() }}-sector
        </span>
        <v-spacer></v-spacer>
        <!-- <v-btn
          class="mr-3"
          color="black"
          right
          x-small
          outlined
          dark
          v-if="myemployees != 1 && isUserTicketOwner && ticket.status == 2"
          elevation="0"
          style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          @click="redirectTicket()"
        >
          {{ $t("Резолюция") }}
        </v-btn> -->
        <v-btn
          class="mr-3"
          color="#FF0000"
          right
          small
          dark
          elevation="0"
          style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          @click="CloseTicket()"
        >
          {{ $t("linestop.close") }}
        </v-btn>
        <v-btn
          class="mr-3"
          color="#2C8DFF"
          right
          small
          dark
          elevation="0"
          style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          @click="acceptTicket()"
        >
          {{ $t("linestop.accept") }}
        </v-btn>
        <v-btn
          class="mr-3"
          color="#3FCB5D"
          right
          small
          dark
          elevation="0"
          style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          @click="sendFormData()"
        >
          {{ $t("save") }}
          <v-icon right>mdi-check-circle-outline</v-icon>
        </v-btn>
        <span
          class="header_same_title"
          v-if="ticket && ticket.plcdata && ticket.plcdata.status == 1"
          >[{{ $t("linestop.operator_creator") }}]</span
        >
        <span
          class="header_same_title"
          v-else-if="ticket && ticket.plcdata && ticket.plcdata.status == 0"
          >[{{ $t("linestop.auto_creator") }}]</span
        >
        <div class="headerSearch d-flex align-center"></div>
      </v-card-title>
      <v-divider color="#d9d9d9"></v-divider>
      <v-row class="mx-0 pa-5 pb-0">
        <v-col class="ma-0 pa-0" cols="8">
          <v-row class="mx-0 pa-0">
            <v-col class="ma-0 pa-0" cols="12">
              <p class="titleInputs mb-3">{{ $t("linestop.description") }}</p>
            </v-col>
            <v-col class="py-0 pl-0 pr-4" cols="12">
              <div class="textField minHeight">
                <v-text-field
                  :disabled="
                    ticket.status == 2 ||
                    ticket.status == 1 ||
                    ticket.status == 3
                  "
                  outlined
                  v-model="description"
                  hide-details
                ></v-text-field>
              </div>
            </v-col>
          </v-row>
          <v-row class="mx-0 pa-0 mt-2">
            <v-col class="ma-0 pa-0" cols="12">
              <p class="titleInputs mb-1">{{ $t("linestop.stop_point") }}</p>
            </v-col>
            <v-col class="py-0 pl-0 pr-4" cols="12">
              <v-row class="mx-0 pa-0">
                <v-col class="ma-0 py-0 pl-0 pr-5" cols="4">
                  <v-autocomplete
                    class="labelInput"
                    elevation="0"
                    hide-details
                    dense
                    filled
                    solo
                    disabled
                    :label="$t('linestop.shop') + ':' + getShopValue()"
                    style="color: red"
                  ></v-autocomplete>
                </v-col>
                <v-col class="ma-0 py-0 pl-0 pr-5" cols="4">
                  <v-autocomplete
                    class="labelInput"
                    elevation="0"
                    hide-details
                    dense
                    filled
                    solo
                    disabled
                    :label="$t('linestop.line') + ':' + getLineValue()"
                  ></v-autocomplete>
                </v-col>
                <v-col class="ma-0 pa-0" cols="4">
                  <v-autocomplete
                    class="labelInput"
                    elevation="0"
                    hide-details
                    dense
                    filled
                    solo
                    disabled
                    :label="$t('linestop.sector') + ':' + getSectorValue()"
                  ></v-autocomplete>
                </v-col>
              </v-row>
            </v-col>
            <v-col class="ma-0 pa-0" cols="4"></v-col>
          </v-row>
          <v-row class="mx-0 pa-0 mt-2">
            <v-col class="ma-0 pa-0" cols="12">
              <p class="titleInputs mb-1">{{ $t("linestop.reason_stoped") }}</p>
            </v-col>
            <v-col class="py-0 pl-0 pr-4" cols="12">
              <v-row class="mx-0 pa-0">
                <v-col class="ma-0 py-0 pl-0 pr-5" cols="4">
                  <!-- :disabled="isAutocompleteDisabledR" -->
                  <v-autocomplete
                    v-model="SelectedReason"
                    class="labelInput"
                    elevation="0"
                    hide-details
                    :items="reasons"
                    item-value="id"
                    item-text="title"
                    dense
                    :disabled="
                      (isUserTicketOwner &&
                        $store.getters.checkPermission(
                          'linestop-tmpsending'
                        )) ||
                      (isUserTicketOwner && ticket.status != 3)
                    "
                    filled
                    solo
                    clearable
                    :label="$t('linestop.select_reason')"
                    style="color: red"
                  ></v-autocomplete>
                </v-col>
                <v-col class="ma-0 py-0 pl-0 pr-5" cols="4">
                  <!-- :disabled="isAutocompleteDisabled" -->
                  <v-autocomplete
                    :items="departments"
                    :search-input.sync="search"
                    item-value="staff_id"
                    @keyup="getDepartments"
                    item-text="text"
                    v-model="selectedDepartment"
                    clearable
                    :disabled="
                      (isUserTicketOwner &&
                        $store.getters.checkPermission(
                          'linestop-tmpsending'
                        )) ||
                      (isUserTicketOwner && ticket.status != 3)
                    "
                    return-object
                    class="labelInput"
                    elevation="0"
                    hide-details
                    dense
                    filled
                    solo
                    :label="$t('linestop.select_department')"
                    style="color: red"
                  ></v-autocomplete>
                </v-col>
                <v-col class="ma-0 pa-0" cols="4">
                  <!-- :disabled="isAutocompleteDisabledR" -->
                  <v-autocomplete
                    v-model="SelectedProvider"
                    class="labelInput"
                    elevation="0"
                    hide-details
                    :items="providers"
                    item-value="id"
                    :disabled="
                      (isUserTicketOwner &&
                        $store.getters.checkPermission(
                          'linestop-tmpsending'
                        )) ||
                      (isUserTicketOwner && ticket.status != 3)
                    "
                    item-text="name"
                    dense
                    filled
                    solo
                    clearable
                    :label="$t('linestop.select_provider')"
                    style="color: red"
                  >
                  </v-autocomplete>
                </v-col>
              </v-row>
            </v-col>
            <v-col class="py-0 pl-0 pr-4" cols="12">
              <v-row class="mx-0 pa-0">
                <v-col class="ma-0 py-0 pl-0 pr-5 mt-4" cols="4">
                  <v-autocomplete
                    class="labelInput"
                    v-model="selectedProductmodel"
                    elevation="0"
                    hide-details
                    :items="productmodels"
                    item-value="id"
                    item-text="name"
                    :disabled="
                      (isUserTicketOwner &&
                        $store.getters.checkPermission(
                          'linestop-tmpsending'
                        )) ||
                      (isUserTicketOwner && ticket.status != 3)
                    "
                    dense
                    filled
                    solo
                    clearable
                    :label="$t('Выберите: производственная модель')"
                    style="color: red"
                  ></v-autocomplete>
                </v-col>
                <v-col class="py-0 pl-0 pr-5 mt-4 input_sector_border" cols="4">
                  <v-text-field
                    v-model="detailNumber"
                    hide-details="auto"
                    :disabled="
                      (isUserTicketOwner &&
                        $store.getters.checkPermission(
                          'linestop-tmpsending'
                        )) ||
                      (isUserTicketOwner && ticket.status != 3)
                    "
                    class="input_text input_sector white"
                    :label="$t('Номер детали')"
                    dense
                    clearable
                    maxlength="8"
                    outlined
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
          <v-row class="mx-0 pa-0 mt-2" v-if="ticket.status != 3">
            <v-col class="ma-0 pa-0" cols="12">
              <p class="titleInputs mb-1">{{ $t("linestop.attach_files") }}</p>
            </v-col>
            <v-col class="py-0 pl-0 pr-4" cols="12">
              <div class="ma-0 pa-0">
                <div>
                  <vue-dropzone
                    class="dropzone_margin"
                    style="color: grey; border: 3px dotted #d8d4d4; width: 100%"
                    ref="myVueDropzone"
                    id="dropzone"
                    height="10px"
                    :options="dropzoneOptions"
                    @vdropzone-success="handleSuccess"
                    @vdropzone-complete="handleComplete"
                    v-on:vdropzone-removed-file="removeThisFile"
                  ></vue-dropzone>
                  <v-card-text
                    class="pt-0"
                    style="width: 700px"
                    v-show="ticket.ticket_file != 0"
                  >
                    <v-simple-table dense class="mt-2" style="border: none">
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">
                              {{ $t("linestop.file_name") }}
                            </th>
                            <th class="text-center">
                              {{ $t("linestop.created_time") }}
                            </th>
                            <th class="text-center">
                              {{ $t("linestop.action") }}
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(item, index) in ticket.ticket_file"
                            :key="index"
                          >
                            <td class="text-left" style="width: 30px">
                              {{ index + 1 }}
                            </td>
                            <td
                              class="text-left"
                              :id="'file-td-' + item.pythiscal_name"
                              style="width: 400px"
                            >
                              <v-icon class="px-0" color="indigo"
                                >mdi-file-document-outline</v-icon
                              >
                              {{ item.pythiscal_name }}
                            </td>
                            <td
                              class="text-left"
                              style="max-width: 150px; overflow: hidden"
                            >
                              {{ item.created_at.slice(0, 19) }}
                            </td>
                            <td class="text-center" width="50px">
                              <a
                                href="#"
                                style="margin-right: 10px"
                                @click.prevent="
                                  getDownload(item.pythiscal_name)
                                "
                              >
                                <v-icon class="px-1" color="error">
                                  mdi-download</v-icon
                                >
                              </a>
                            </td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-card-text>
                </div>
              </div>
            </v-col>
          </v-row>
          <v-row class="mx-0 pa-0 mt-2">
            <v-col class="py-0 pl-0 pr-4" cols="12">
              <v-tabs class="tabBuyi">
                <v-tab>{{ $t("linestop.comment") }}</v-tab>
                <v-tab>{{ $t("linestop.history_action") }}</v-tab>
                <v-tab-item>
                  <v-row class="ma-0 pa-0">
                    <v-col cols="12" class="ma-0 px-0">
                      <v-row
                        class="mx-0 pa-0"
                        style="border: 1px solid #dce5ef"
                      >
                        <v-col cols="12" class="pa-0">
                          <v-text-field
                            outlined
                            class="rounded-0 comment-text-filed"
                            v-model="comment"
                            @keyup.native.enter="CommentFileSend"
                            hide-details
                            :label="$t('linestop.add_comment')"
                            prepend
                          ></v-text-field>
                        </v-col>
                        <v-col cols="8" class="px-1 py-1">
                          <p class="mb-0 title_timecreated">
                            {{ $t("linestop.tip") }}
                            {{ $t("linestop.tip_description") }}
                          </p>
                        </v-col>
                        <v-col
                          class="pa-1"
                          cols="4"
                          style="display: flex; justify-content: end"
                        >
                          <v-file-input
                            class="pt-0 mt-0 mr-5 file_input"
                            v-model="uploadedCFiles"
                            multiple
                            truncate-length="15"
                            hide-details
                          ></v-file-input>
                          <v-btn
                            x-small
                            outlined
                            dark
                            class="mr-3 mb-0"
                            @click="CommentFileSend()"
                            elevation="0"
                            style="
                              color: #000;
                              text-transform: none;
                              border-radius: 3px;
                              padding: 5px 20px;
                              border: 1px solid #000;
                            "
                          >
                            {{ $t("linestop.add") }}
                          </v-btn>
                        </v-col>
                        <v-col cols="12">
                          <v-card-text class="pa-2 pb-1">
                            <v-row class="mx-0">
                              <v-col cols="12" class="py-0">
                                <div
                                  v-for="(ticketcha, index) in ticketcomment"
                                  :key="index"
                                  class="title_comment"
                                >
                                  <strong>{{ ticketcha.employee.fio }}</strong>
                                  <span class="title_timecreated">{{
                                    ticketcha.created_at.slice(0, 19)
                                  }}</span>
                                  <div
                                    class="title_comment mb-2 ml-2 mr-2 pa-1 pl-4 hideIcon cardTitle border-left warning-outline dialog-head_title"
                                  >
                                    {{ ticketcha.comment }}
                                  </div>
                                  <span
                                    class="title_comment mb-2 ml-2 mr-2 pa-1 pl-4 hideIcon cardTitle border-left warning-outline dialog-head_title"
                                    v-for="ticketchafile in ticketcha.ticket_comment_file"
                                  >
                                    <a
                                      href="#"
                                      style="margin-right: 10px"
                                      @click.prevent="
                                        getDownload(
                                          ticketchafile.pythiscal_name
                                        )
                                      "
                                    >
                                      {{ ticketchafile.pythiscal_name }}
                                    </a>
                                  </span>
                                </div>
                              </v-col>
                            </v-row>
                          </v-card-text>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                  <!-- Kommentariya qismi boshlanidi -->
                </v-tab-item>
                <v-tab-item>
                  <v-card flat>
                    <v-card-text>
                      <v-alert shaped outlined color="success">
                        <span v-for="singleticketuser in ticketuserhisory">
                          <p>{{ singleticketuser.employee.fio }}</p>
                        </span>
                      </v-alert>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
              </v-tabs>
            </v-col>
          </v-row>
          <!-- Display ticket information qismi boshlandi -->
        </v-col>
        <v-col class="ma-0 pa-0" cols="4">
          <v-card
            class="pa-2 mt-7"
            style="
              border: 1px solid #dce5ef;
              border-radius: 10px;
              box-shadow: none !important;
            "
          >
            <h3 style="color: salmon" class="mb-3">
              {{ $t("linestop.about_ticket") }}
            </h3>
            <v-row class="mx-0 mb-5">
              <v-col class="pa-0" cols="6" sm="5">
                <p class="titleInputs mb-0">
                  <strong>{{ $t("linestop.status_ticket") }}</strong>
                </p>
              </v-col>
              <v-col class="pa-0 dialog-head_title" cols="6" sm="7">
                <span v-if="ticket.status == 0">
                  <v-btn
                    style="min-width: 100px"
                    elevation="0"
                    x-small
                    color="primary"
                    dark
                    class="tdClassnew"
                    >{{ $t("linestop.new") }}</v-btn
                  >
                </span>
                <span v-else-if="ticket.status == 1">
                  <v-btn
                    style="min-width: 100px"
                    elevation="0"
                    x-small
                    color="error"
                    dark
                    class="tdClassnew"
                    >{{ $t("linestop.pending") }}</v-btn
                  >
                </span>
                <span v-else-if="ticket.status == 2">
                  <v-btn
                    style="min-width: 100px"
                    elevation="0"
                    x-small
                    color="error"
                    dark
                    class="tdClassnew"
                    >{{ $t("linestop.working") }}</v-btn
                  >
                </span>
                <span v-else-if="ticket.status == 3">
                  <v-btn
                    style="min-width: 100px; color: #000"
                    elevation="0"
                    x-small
                    color="green accent-3"
                    class="tdClassnew"
                    >{{ $t("linestop.closed") }}</v-btn
                  >
                </span>
              </v-col>
            </v-row>
            <v-row class="mx-0 mb-5">
              <v-col class="pa-0" cols="6" sm="5">
                <p class="titleInputs mb-0">
                  <strong>{{ $t("linestop.created_time") }}:</strong>
                </p>
              </v-col>
              <v-col class="pa-0 dialog-head_title" cols="6" sm="7">
                <span>
                  {{
                    ticket && ticket.created_at
                      ? ticket.created_at.slice(0, 16)
                      : "-/-"
                  }}
                </span>
              </v-col>
            </v-row>
            <v-row class="mx-0 mb-5">
              <v-col class="pa-0" cols="7" sm="5">
                <p class="titleInputs mb-0">
                  <strong>{{ $t("linestop.stoptime") }}:</strong>
                </p>
              </v-col>
              <v-col class="pa-0 dialog-head_title" cols="5" sm="7">
                <span v-if="ticket.duration != null">
                  {{ getDuration().slice(0, 7) }}
                </span>
                <span v-else-if="ticket.duration == null">
                  {{ $t("linestop.unknown") }}
                </span>
              </v-col>
            </v-row>

            <!-- <v-row class="mx-0 mb-5">
              <v-col class="pa-0" cols="6" sm="5">
                <p class="titleInputs mb-0">
                  <strong>Подразделение:</strong>
                </p>
              </v-col>
              <v-col class="pa-0 dialog-head_title" cols="6" sm="7">
                <span> Подразделение </span>
              </v-col>
            </v-row> -->
            <!-- <v-row class="mx-0 mb-5">
              <v-col class="pa-0" cols="6" sm="5">
                <p class="titleInputs mb-0">
                  <strong>Поставщик:</strong>
                </p>
              </v-col>
              <v-col class="pa-0" cols="6" sm="7">
                <span> {{ ticket.provider_id }} </span>
              </v-col>
            </v-row> -->
            <v-row class="mx-0 mb-5" v-if="viewers.length > 0">
              <v-col class="pa-0" cols="12" sm="12">
                <div>
                  <h3 style="color: salmon">{{ $t("linestop.viewers") }}</h3>
                  <div class="dialog-head_title">
                    <span v-for="viewer in viewers" :key="viewer.id">
                      {{ viewer.employee.lastname_uz_latin }}
                      {{ viewer.employee.firstname_uz_latin }},
                    </span>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-row class="mx-0 mb-5" v-if="viewers.length > 0">
              <v-col class="pa-0" cols="12" sm="12">
                <div>
                  <h3 style="color: salmon">{{ $t("Процесс:") }}</h3>
                  <div class="dialog-head_title">
                    {{ proccessFIO }}
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
      <!-- Umumiy sahranit qismi boshlandi -->
      <div>
        <v-row>
          <v-col cols="9"></v-col>
          <v-col cols="3" class="bottomCol"></v-col>
        </v-row>
      </div>
      <!-- Umumiy sahranit qismi boshlandi -->
      <!-- Edirdor shu joydan tugadi -->
    </v-card>
    <!-- Riderect dialog boshlandi -->
    <v-dialog
      v-model="redirectTicektDialog"
      hide-overlay
      persistent
      width="480"
      @keydown.esc="redirectTicektDialog = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("Отправить сотруднику") }}</span>
        <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
        <v-row class="dialog-form">
          <v-col cols="12">
            <v-autocomplete
              class="labelInput"
              elevation="0"
              v-model="selectedDepartment"
              hide-details
              return-object
              :items="myemployees"
              dense
              item-value="id"
              item-text="fio"
              filled
              solo
              clearable
              label="Выберите: Сотрудник"
              :label="$t('linestop.select_reason')"
              style="color: red"
            ></v-autocomplete>
          </v-col>
        </v-row>
        <v-card-text style="text-align: center">
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            elevation="0"
            style="text-transform: none; border-radius: 5px"
          >
            {{ $t("Отправлять") }}
            <v-icon right>mdi-check-circle-outline</v-icon>
          </v-btn>
          <v-btn
            class
            color="#EB4034"
            right
            small
            dark
            elevation="0"
            @click="redirectTicektDialog = false"
            style="text-transform: none; border-radius: 5px"
          >
            {{ $t("linestop.cancel") }}
            <v-icon right>mdi-close-box-outline</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <!-- Riderect dialog tugadi -->
    <!-- loading qismi boshlandi -->
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
    <!-- loading qismi tugadi -->
    <!-- Riderect dialog tugadi -->
  </div>
</template>

<script>
const axios = require("axios").default;
import { Vue2TinymceEditor } from "vue2-tinymce-editor";
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";
import Swal from "sweetalert2";
export default {
  components: {
    vueDropzone: vue2Dropzone,
    Vue2TinymceEditor,
  },
  data() {
    return {
      dropzoneOptions: {
        url: "https://httpbin.org/post",
        maxFilesize: 1.5,
        addRemoveLinks: true,
        dictDefaultMessage:
          "<img style='height:20px; margin: -30px 0px -20px 0px;' src='img/cloud-upload-outline.png'> Перетащите файлы или <label  style='color:blue'>загрузите с локальной папки<p style='color:red'>(*Максимальный размер файла- 1.5мб)</p></label>",
      },
      uploadedFiles: [],
      uploadedCFiles: [],
      ticketId: null,
      ticket: [],
      ticketcomment: [],
      ticketuserhisory: [],
      TicketUser: [],
      reasons: [],
      providers: [],
      productmodels: [],
      viewers: [],
      SelectedReason: null,
      loading: false,
      dialog: false,
      redirectTicektDialog: false,
      commentDialog: false,
      dialogReason: false,
      dialogDepartment: false,
      items: [],
      departments: [],
      myemployees: [],
      loading: false,
      text: "",
      tab: null,
      search: "",
      description: "",
      comment: "",
      selectedDepartment: null,
      SelectedProvider: null,
      selectedProductmodel: "",
      redirectOwnStaff: "",
      detailNumber: "",
      riderectedDepartment: null,
      riderectedProvider: null,
      riderectedReason: null,
      options: {
        menubar: true,
        plugins:
          "fullscreen advlist autolink charmap code codesample directionality emoticons preview table lists hr searchreplace",
        toolbar1:
          "fullscreen preview code | undo redo | fontsizeselect bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | lineheight numlist bullist | outdent indent | link table removeformat hr customInsertButton",
        fontsize_formats: "8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt",
        formats: {
          removeformat: [
            { selector: "h1,h2,h3,h4,h5,h6,h7,span,p", remove: "all" },
          ],
        },
        visualblocks_default_state: true,
        forced_root_block: "p",
        content_style:
          "body { font-family:Times,sans-serif; font-size:14pt } p{margin:0px; padding:0px;} h2{font-weight:normal;} .indent{ text-indent:40px;}",
        height: "300px",
        language: "ru",
      },
    };
  },
  computed: {
    user() {
      return this.$store.getters.getUser();
    },
    isUserTicketOwner() {
      return (
        this.ticket?.ticket_user?.[0]?.employee_id === this.user.employee_id
      );
    },
    proccessFIO() {
      if (
        this.ticket &&
        this.ticket.ticket_user &&
        this.ticket.ticket_user.length > 0 &&
        this.ticket.ticket_user[0].employee &&
        this.ticket.ticket_user[0].employee.fio
      ) {
        return this.ticket.ticket_user[0].employee.fio;
      }
      return "~";
    },
    isDisabledButtons() {
      if (
        this.ticket &&
        this.ticket.ticket_user &&
        this.ticket.ticket_user.length > 0
      ) {
        return (
          this.ticket.status !== 2 &&
          this.ticket.status !== 0 &&
          this.ticket.ticket_user[0].employee_id == this.user.employee_id
        );
      } else {
        return false;
      }
    },
  },
  watch: {
    // ticket: {
    //   deep: true,
    //   handler(newTicket) {
    //     if (newTicket && newTicket.ticket_user && newTicket.ticket_user[0]) {
    //       const staffId = newTicket.ticket_user[0].staff_id;
    //       this.setSelectedDepartmentByStaffId(staffId);
    //     }
    //   },
    // },
  },
  methods: {
    justExit() {
      this.$router.push("/linestopsidebar/linestop-alltickets");
    },
    redirectTicket() {
      this.redirectTicektDialog = true;
    },
    acceptTicket() {
      Swal.fire({
        title: "linestop.title",
        text: "linestop.text",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ok",
      }).then((result) => {
        if (result.isConfirmed) {
          const formData = new FormData();
          formData.append("ticketId", this.ticketId);
          axios
            .post(
              this.$store.state.backend_url + "api/linestop/acceptTicket",
              formData
            )
            .then((response) => {
              Swal.fire("Success", "Ticket accepted successfully!", "success");
              window.location.reload();
            })
            .catch((error) => {
              console.log(error);
              Swal.fire("Error", "Failed to accept ticket", "error");
            });
        }
      });
    },
    CloseTicket() {
      Swal.fire({
        title: "linestop.title",
        text: "linestop.text",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ok",
      }).then((result) => {
        if (result.isConfirmed) {
          const formData = new FormData();
          formData.append("ticketId", this.ticketId);

          axios
            .post(
              this.$store.state.backend_url + "api/linestop/closeTicket",
              formData
            )
            .then((response) => {
              Swal.fire("Success", "Ticket closed successfully!", "success");
              window.location.reload();
            })
            .catch((error) => {
              console.log(error);
              Swal.fire("Error", "Failed to close ticket", "error");
            });
        }
      });
    },
    CommentFileSend() {
      const formData = new FormData();
      formData.append("ticketId", this.ticketId);
      formData.append("comment", this.comment);
      formData.append("created_by", this.user.employee.id);

      this.uploadedCFiles.forEach((file) => {
        formData.append("files[]", file);
      });

      axios
        .post(
          this.$store.state.backend_url + "api/linestop/sendFileComment",
          formData
        )
        .then((response) => {
          this.comment = "";
          this.uploadedCFiles = [];
          this.getList();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    handleSuccess(file, response) {
      this.uploadedFiles.push(file);
    },
    handleComplete(file) {
      // Fayl yuklashni tugatganda ishlatiladi
    },
    downloadFile(file) {
      // Faylni yuklab olish uchun xizmat qiladigan metod
    },
    removeFileFromTable(file) {
      this.removeThisFile(file);
    },
    removeThisFile(file) {
      const index = this.uploadedFiles.findIndex(
        (uploadedFile) => uploadedFile.name === file.name
      );
      if (index !== -1) {
        this.uploadedFiles.splice(index, 1);
        this.$refs.myVueDropzone.removeFile(file);
      }
    },
    getUser() {
      let user = this.$store.getters.getUser();
      this.employee = user.employee;
      this.staff = user.employee.employee_staff[0].staff;
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/linestop/getticket", {
          id: this.ticketId,
        })
        .then((response) => {
          this.ticket = response.data.ticket;
          this.ticketuserhisory = response.data.ticketuser;
          this.ticketcomment = response.data.ticket.ticket_comment;
          this.SelectedProvider = this.ticket.provider_id;
          this.SelectedReason = this.ticket.reason_id;
          this.selectedProductmodel = this.ticket.product_id;
          this.detailNumber = this.ticket.detail_number;
          this.selectedDepartment =
            this.ticket &&
            this.ticket.ticket_user &&
            this.ticket.ticket_user.length > 0
              ? this.ticket.ticket_user[0].staff_id
              : null;

          this.description =
            this.ticket && this.ticket.description
              ? this.ticket.description
              : "";
          this.loading = false;
        })
        .catch((error) => {
          console.error("Error fetching ticket:", error);
          this.loading = false;
        });
    },
    getLineValue() {
      return this.ticket?.plcdata?.line?.line || "";
    },
    getShopValue() {
      return this.ticket?.plcdata?.line?.shop?.name || "";
    },
    getSectorValue() {
      return this.ticket?.plcdata?.sector || "";
    },
    getStopDateValue() {
      return this.ticket?.plcdata?.stopDT || "";
    },
    getStartDateValue() {
      return this.ticket?.plcdata?.startDT || "";
    },
    getDuration() {
      return this.ticket?.duration + " сек" || "";
    },
    getDepartments() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/linestop/getDepartments", {
          search: this.search,
        })
        .then((res) => {
          this.departments = res.data.map((v) => {
            v.text =
              v.dep_code + " " + v.fio + " " + v.department + " " + v.position;
            return v;
          });
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getOwnEmployees() {
      axios
        .get(this.$store.state.backend_url + "api/employees/get-skud")
        .then((res) => {
          if (Array.isArray(res.data) && res.data.length > 0) {
            this.myemployees = res.data;
          } else {
            this.myemployees = "1";
            console.warn("Received unexpected data format or empty array");
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getReasons() {
      axios
        .get(this.$store.state.backend_url + "api/get/reasons")
        .then((res) => {
          this.reasons = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getProviders() {
      axios
        .get(this.$store.state.backend_url + "api/linestop-get/providers")
        .then((res) => {
          this.providers = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getProductsmodel() {
      axios
        .get(this.$store.state.backend_url + "api/linestop-get/productmodels")
        .then((res) => {
          this.productmodels = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getViewers() {
      axios
        .post(this.$store.state.backend_url + "api/linestop/getTicketViewers", {
          ticketId: this.ticketId,
        })
        .then((res) => {
          this.viewers = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getDownload(filename) {
      axios({
        url:
          this.$store.state.backend_url +
          `api/linestop/download-ticfile/${filename}`,
        method: "GET",
        responseType: "blob",
      })
        .then((response) => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", filename);
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        })
        .catch((error) => {
          console.error(error);
        });
    },
    sendFormData() {
      Swal.fire({
        title: "linestop.title",
        text: "linestop.textsave",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "OK",
      }).then((result) => {
        if (result.isConfirmed) {
          const formData = new FormData();
          formData.append("ticketId", this.ticketId);
          formData.append(
            "staff_id",
            this.user.employee.employee_staff[0].staff_id
          );
          formData.append("employee_id", this.user.employee.id);
          formData.append(
            "selectedDepartment",
            JSON.stringify(this.selectedDepartment)
          );
          formData.append("plcdataid", this.ticketId);
          formData.append("selectedProvider", this.SelectedProvider);
          formData.append("selectedProductmodel", this.selectedProductmodel);
          formData.append("detailNumber", this.detailNumber);
          formData.append("selectedReason", this.SelectedReason);
          formData.append("duration", this.getDuration());
          formData.append("description", this.description);
          this.uploadedFiles.forEach((file) => {
            formData.append("files[]", file);
          });

          axios
            .post(
              this.$store.state.backend_url + "api/linestop/updateTicket",
              formData
            )
            .then((response) => {
              Swal.fire("Success", "Ticket updated successfully!", "success");
              window.location.reload();
            })
            .catch((error) => {
              console.log(error);
              Swal.fire("Error", "Failed to update ticket", "error");
            });
        }
      });
    },
  },
  mounted() {
    this.ticketId = this.$route.params.id;
    this.getList();
    this.getReasons();
    this.getDepartments();
    this.getProviders();
    this.getProductsmodel();
    this.getViewers();
    this.getUser();
    this.getOwnEmployees();
  },
};
</script>

<style scoped>
.headerTitle {
  color: #1e43a2;
  font-size: 18px;
  font-weight: 400;
  line-height: normal;
  font-style: normal;
  font-family: "Inter", sans-serif;
}
.header_same_title {
  color: #f8a300;
  font-size: 14px;
  font-weight: 500;
  line-height: normal;
  font-style: normal;
  font-family: "Inter", sans-serif;
}
.titleInputs {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  line-height: normal;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-textarea.v-text-field--enclosed.v-text-field--outlined:not(.v-input--dense)
  textarea {
  margin-top: 0px !important;
}
.inline-items {
  display: flex;
  vertical-align: middle;
}

.list-group .list-icons i {
  color: #00b950;
  font-size: 16px;
}

.list-group .list-text div {
  color: #6c869f;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.list-group .list-text span {
  color: #333;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.text_nowrap {
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.tdClass {
  display: block;
  color: #fff;
  font-size: 10px;
  font-style: normal;
  line-height: normal;
  font-weight: 600;
  max-width: 160px;
  height: 16px;
  border-radius: 15px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.tdClassnew {
  display: block;
  color: #fff;
  font-size: 9px;
  font-style: normal;
  line-height: normal;
  font-weight: 400;
  max-width: 90px;
  height: 16px;
  border-radius: 3px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropzone {
  padding: 0px !important;
  min-height: 70px !important;
}
.cardTitle {
  color: black;
  font-size: 14px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 400;
  word-wrap: break-word;
}
.bottomCol {
  display: flex;
  justify-content: center;
  align-items: end;
}

.commentButton {
  border: 1px solid #f8a300;
  background-color: #ffffff !important;
  width: 200px;
}
.dialog-head_title {
  color: #000;
  font-size: 12px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.title_comment {
  color: #000;
  margin-left: 6px;
  font-size: 14px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.title_timecreated {
  color: #797f71;
  margin-left: 6px;
  font-size: 12px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-card__subtitle,
.v-card__text,
.v-card__title {
  padding-bottom: 2px;
}
.hideIcon .v-alert__icon {
  display: none !important;
}
.v-dialog {
  overflow-y: 0 !important;
}
.tabBuyi .v-tabs-bar {
  height: 32px;
}
.input_text {
  border: 1px solid #dce5ef !important;
  border-radius: 5px;
  height: 40px;
}
</style>
