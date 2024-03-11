<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-5 py-1">
        <span class="headerTitle mb-2">{{
          $t("linestop.create_new_ticket")
        }}</span>
        <v-spacer></v-spacer>
        <span class="header_same_title"> [Operator] </span>
        <div class="headerSearch d-flex align-center"></div>
      </v-card-title>
      <v-divider color="#d9d9d9"></v-divider>
      <v-row class="mx-0 px-5 pt-5 pb-0">
        <v-col class="ma-0 pa-0" cols="12">
          <p class="titleInputs mb-3">{{ $t("linestop.description") }}</p>
        </v-col>
        <v-col class="py-0 pl-0 pr-4" cols="8">
          <div class="textField">
            <v-textarea
              v-model="description"
              class="text_nowrap mt-0"
              hide-details
              outlined
              style="max-width: auto; border-radius: 5px"
            ></v-textarea>
          </div>
        </v-col>
        <v-col class="ma-0 pa-0" cols="4">
          <v-card
            class="pa-3"
            style="
              border: 1px solid #dce5ef;
              border-radius: 10px;
              box-shadow: none !important;
            "
          >
            <v-row class="mx-0 mb-7">
              <v-col class="pa-0" cols="6">
                <p class="titleInputs mb-0">
                  <strong>{{ $t("linestop.status_ticket") }}</strong>
                </p>
              </v-col>
              <v-col class="pa-0" cols="6">
                <v-btn
                  elevation="0"
                  x-small
                  color="#E62A2A"
                  dark
                  class="tdClassnew"
                >
                  {{ $t("linestop.new") }}
                </v-btn>
              </v-col>
            </v-row>
            <v-row class="mx-0">
              <v-col class="pa-0" cols="6">
                <p class="titleInputs mb-0">
                  <strong>{{ $t("linestop.duration") }}:</strong>
                </p>
              </v-col>
              <v-col class="pa-0" cols="6">
                <v-btn
                  elevation="0"
                  x-small
                  color="#2C8DFF"
                  dark
                  class="tdClass"
                >
                  {{ timeDifference }}
                </v-btn>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
      <!-- Display ticket information qismi boshlandi -->
      <v-row class="mx-0 px-5 pt-5 pb-0">
        <v-col class="ma-0 pa-0" cols="12">
          <p class="titleInputs mb-1">{{ $t("linestop.stop_point") }}</p>
        </v-col>
        <v-col class="py-0 pl-0 pr-5 ma-0" cols="3">
          <v-autocomplete
            class="labelInput"
            v-model="selectedLineid"
            elevation="0"
            :items="lines"
            item-value="id"
            item-text="comment"
            hide-details
            dense
            clearable
            filled
            solo
            :label="$t('linestop.line')"
          ></v-autocomplete>
        </v-col>
        <v-col class="py-0 pl-0 pr-5 ma-0 input_sector_border" cols="3">
          <v-text-field
            hide-details="auto"
            v-model="selectedSector"
            class="input_text input_sector white"
            dense
            clearable
            outlined
            :label="$t('linestop.sector')"
          ></v-text-field>
        </v-col>
        <v-col class="py-0 pl-0 pr-5" cols="3">
          <v-text-field
            v-model="stopLinedate"
            hide-details="auto"
            class="input_text white"
            type="datetime-local"
            :label="$t('linestop.stoptime')"
            dense
            clearable
            outlined
            persistent-placeholder
          ></v-text-field>
        </v-col>
        <v-col class="py-0 pl-0 pr-5" cols="3">
          <v-text-field
            v-model="startLinedate"
            hide-details="auto"
            class="input_text white"
            type="datetime-local"
            :label="$t('linestop.starttime')"
            dense
            clearable
            outlined
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row class="mx-0 px-5 pt-5 pb-0">
        <v-col class="pa-0 ma-0" cols="12">
          <p class="titleInputs mb-1">{{ $t("linestop.reason_stoped") }}</p>
        </v-col>
        <v-col class="py-0 pl-0 pr-5" cols="3">
          <v-autocomplete
            v-model="SelectedReason"
            class="labelInput"
            elevation="0"
            hide-details
            :items="reasons"
            item-value="id"
            item-text="title"
            dense
            filled
            solo
            clearable
            :label="$t('linestop.select_reason')"
            style="color: red"
          ></v-autocomplete>
        </v-col>
        <v-col class="py-0 pl-0 pr-5" cols="3">
          <v-autocomplete
            :items="departments"
            :search-input.sync="search"
            item-value="staff_id"
            @keyup="getDepartments"
            item-text="text"
            v-model="selectedDepartment"
            clearable
            class="labelInput"
            elevation="0"
            hide-details
            dense
            filled
            solo
            return-object
            :label="$t('linestop.select_department')"
            style="color: red"
          ></v-autocomplete>
        </v-col>
        <v-col class="py-0 pl-0 pr-5" cols="3">
          <v-autocomplete
            v-model="SelectedProvider"
            class="labelInput"
            elevation="0"
            hide-details
            :items="providers"
            item-value="id"
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
        <v-col class="py-0 pl-0 pr-5" cols="3">
          <v-autocomplete
            class="labelInput"
            v-model="selectedProductmodel"
            elevation="0"
            hide-details
            :items="productmodels"
            item-value="id"
            item-text="name"
            dense
            filled
            solo
            clearable
            :label="$t('Выберите: производственная модель')"
            style="color: red"
          ></v-autocomplete>
        </v-col>
        <v-col class="py-0 pl-0 pr-5 mt-4 input_sector_border" cols="3">
          <v-text-field
            v-model="detailNumber"
            hide-details="auto"
            class="input_text input_sector white"
            :label="$t('Напиши: номер детали')"
            dense
            clearable
            maxlength="8"
            outlined
          ></v-text-field>
        </v-col>
      </v-row>
      <v-container></v-container>
      <v-container>
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
      </v-container>
      <!-- Umumiy sahranit qismi boshlandi -->
      <v-row>
        <v-col cols="12" class="bottomCol">
          <div style="display: flex">
            <v-btn
              class="mr-3"
              color="#3FCB5D"
              right
              small
              dark
              elevation="0"
              @click="createTicket()"
              style="
                text-transform: none;
                border-radius: 5px;
                padding: 5px 20px;
              "
            >
              {{ $t("save") }}
            </v-btn>
          </div>
        </v-col>
      </v-row>
      <!-- Umumiy sahranit qismi boshlandi -->
      <!-- Edirdor shu joydan tugadi -->
    </v-card>
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
  </div>
</template>

<script>
const axios = require("axios").default;
import { Vue2TinymceEditor } from "vue2-tinymce-editor";
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";
export default {
  components: {
    Vue2TinymceEditor,
    vueDropzone: vue2Dropzone,
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
      uploadedFiles: [],
      ticketId: null,
      ticket: [],
      TicketUser: [],
      reasons: [],
      lines: [],
      SelectedReason: null,
      loading: false,
      dialog: false,
      items: [],
      departments: [],
      productmodels: [],
      providers: [],
      loading: false,
      text: "",
      tab: null,
      detailNumber: "",
      search: "",
      description: "",
      selectedDepartment: null,
      SelectedProvider: null,
      selectedProductmodel: null,
      stopLinedate: "",
      startLinedate: "",
      selectedLineid: "",
      selectedSector: "",
      dialogReason: false,
      dialogDepartment: false,
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
    isButtonDisabled() {
      return !(this.SelectedReason && this.selectedDepartment);
    },
    timeDifference() {
      if (!this.startLinedate || !this.stopLinedate) {
        return "0";
      }

      const startDate = new Date(this.startLinedate);
      const stopDate = new Date(this.stopLinedate);

      const timeDiff = startDate - stopDate;

      const seconds = Math.floor(timeDiff / 1000);
      const minutes = Math.floor(seconds / 60);
      const hours = Math.floor(minutes / 60);

      // Format the result as a string
      const formattedTimeDifference = `${hours} час, ${minutes % 60} мин`;

      return formattedTimeDifference;
    },
  },
  methods: {
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
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
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
    createTicket() {
      let formData = new FormData();
      // Plcdata qismiga yoziladigonlar boshlandi
      formData.append("selectedLineid", this.selectedLineid);
      formData.append("selectedSector", this.selectedSector);
      formData.append("stopLinedate", this.stopLinedate);
      formData.append("startLinedate", this.startLinedate);
      formData.append("statusPlcdata", "1");
      formData.append("createdbyTabel", this.user.employee.tabel);
      // Plcdata qismiga yoziladigonlar tugadi

      // Ticket qismiga yoziladigonlar boshlandi
      formData.append("selectedReason", this.SelectedReason);
      formData.append("selectedProvider", this.SelectedProvider);
      formData.append("selectedProductmodel", this.selectedProductmodel);
      formData.append("detailNumber", this.detailNumber);
      formData.append("statusTicket", "1");
      formData.append("selectedDepartment", this.selectedDepartment.staff_id);
      this.uploadedFiles.forEach((file) => {
        formData.append("files[]", file);
      });
      // Ticket qismiga yoziladigonlar tugadi

      formData.append("user_id", this.user.employee.tabel);
      // Ticket desription qismiga yoziladigon boshlandi
      formData.append("text", this.description);
      // Ticket desription qismiga yoziladigon tugadi

      // ticket_user yoziladigonlar boshlandi
      formData.append(
        "staff_id",
        this.user.employee.employee_staff[0].staff_id
      );
      formData.append("employee_id", this.user.employee.id);
      formData.append(
        "selectedDepartment",
        JSON.stringify(this.selectedDepartment)
      );
      // ticket_user yoziladigonlar tugadi

      axios
        .post(
          this.$store.state.backend_url + "api/linestop/createTicket",
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((res) => {
          this.$router.push("/linestopsidebar/linestop-alltickets");
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getTicketUser() {
      axios
        .post(this.$store.state.backend_url + "api/linestop/get-ticketusers", {
          ticketId: this.ticketId,
        })
        .then((res) => {
          this.TicketUser = res.data;
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
    getLines() {
      axios
        .get(this.$store.state.backend_url + "api/get/lines")
        .then((response) => {
          this.lines = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
  mounted() {
    this.ticketId = this.$route.params.id;
    this.getList();
    this.getTicketUser();
    this.getReasons();
    this.getProviders();
    this.getProductsmodel();
    this.getDepartments();
    this.getUser();
    this.getLines();
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
  border-radius: 15px;
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
.form-add_employee .input_text {
  border: 1px solid #dce5ef !important;
  border-radius: 1px;
}
.input_text {
  border: 1px solid #dce5ef !important;
  border-radius: 5px;
  height: 40px;
}
</style>
