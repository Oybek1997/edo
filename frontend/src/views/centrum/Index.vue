<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{
          $i18n.locale == "uz_latin" ? '"UZLOGISTIC" MChJ' : '"UZLOGISTIC" МЧЖ'
        }}</span>
      </v-card-title>
      <v-card-text class="py-12">
        <div
          style="text-align: center; width: 60%"
          class="mt-12 mx-auto centrum_upload_file"
        >
          <v-file-input
            v-model="file"
            append-icon="mdi-microsoft-excel"
            color="#E6E6E6"
            counter
            outlined
            dense
            hide-details
            :label="$t('uploadFiles')"
            show-size
            truncate-length="50"
          >
          </v-file-input>
          <vue-dropzone
          class="dropzone_margin mt-5"
          style="color: grey; border: 3px dotted #d8d4d4; width: 100%"
          ref="myVueDropzone"
          id="dropzone"
          height="10px"
          :options="dropzoneOptions"
          @vdropzone-success="handleSuccess"
          @vdropzone-complete="handleComplete"
          v-on:vdropzone-removed-file="removeThisFile"
        ></vue-dropzone>
        </div>
        <div style="text-align: center" class="mt-12">
          <span class="btn_file_upload" :disabled="!file" @click="changeFile">
            {{ $t("Yangi yaratish") }}
          </span>
        </div>
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";
export default {
  components: {
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
      loading: false,
      search: "",
      file: null,
      page: 1,
      from: 0,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      dialog: false,
      editMode: null,
      items: [],
      parts: [],
      part: null,
      form: {},
      item: null,
      dialogHeaderText: "",
      eimzoKeys: [],
      eimzoKey: null,
      eimzoDialog: false,
      new_count: 0,
      success_count: 0,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 90;
    },
  },
  methods: {
    handleSuccess(file, response) {
      // this.uploadedFiles.push(file);
      this.file = file;
    },
    handleComplete(file) {},
    downloadFile(file) {},
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
    createDocument() {},
    changeFile() {
      var formData = new FormData();
      formData.append("file", this.file);
      axios
        .post(this.$store.state.backend_url + "api/centrum-upload", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          this.loading = false;
          console.log("deff", response.status);
          // console.log("id", response.data.doc_id);
          if (response.data.id) {
            this.$router.push("/documentsidebar/document/" + response.data.pdf_file_name);
          } else {
            let errmess = "";
            if (response.data.doc_id) {
              errmess =
                response.data.message +
                response.data.doc_id +
                " Yuboruvchi " +
                response.data.employee +
                " Holati: " +
                response.data.status +
                " Duplikat aniqlangan joy: " +
                response.data.attr +
                ": " +
                response.data.attr_value;
            } else {
              errmess = response.data.message;
            }
            Swal.fire({
              icon: "error",
              title: "Error",
              text: errmess,
              footer: "",
            });
          }
        })
        .catch((error) => {
          console.log("abc", error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: error,
            footer: "",
          });
          this.loading = false;
        });
    },
    log(text) {
      console.log(text);
    },
    getPdf() {
      if (this.items.length > 0) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url +
              "api/post-order/get-pdf-new/" +
              this.part
          )
          .then((response) => {
            this.loading = false;
            if (response.data) {
              this.item = response.data;
              this.sign(response.data);
            }
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      }
      // eimzoDialog = true;
    },
  },
  mounted() {},
};
</script>
<style scoped>
.fullHeight {
  height: 100vh;
  height: calc(100vh - 90px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 16px;
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
.txt_search2 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
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
.btn_file_upload {
  cursor: pointer;
  padding: 5px 15px;
  border-radius: 5px;
  border: 1px solid #2c8dff !important;
  background: #2c8dff !important;
  color: #fff !important;
  font-family: Inter;
  font-size: 12px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
}
.centrum_upload_file .v-input__control .v-input__slot .v-text-field__slot {
  color: #18884f !important;
}
</style>
