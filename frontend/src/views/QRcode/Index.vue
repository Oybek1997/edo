<template>
  <div>
    <div style="text-align: center" class="mt-10">
      <v-btn
        v-if="!drawingModal && !pdfBase64"
        class="btn-Excel"
        color="green"
        dark
        @click="drawingModal = true"
      >
        {{ $t("Excel yuklash") }}
      </v-btn>
      <div v-if="pdfBase64">
        <iframe
          :src="'data:application/pdf;base64,' + pdfBase64"
          height="1000"
          width="100%"
        ></iframe>
      </div>
    </div>
    <v-dialog
      v-model="drawingModal"
      persistent
      max-width="450px"
      @keydown.esc="drawingModal = false"
    >
      <v-card>
        <v-card-title>
          <span class="headline">{{ $t("Excel yuklash") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" x-small fab class @click="drawingModal = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text class="pb-0 mt-5">
          <v-container>
            <v-row>
              <v-col class="pt-0" cols="12">
                <v-file-input
                  v-model="files"
                  label="Faylni tanlang"
                  outlined
                  dense
                ></v-file-input>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions class="pt-0">
          <v-spacer></v-spacer>
          <v-btn color="green" dark @click="uploadExcel">
            {{ $t("Excel yuklash") }}
          </v-btn>
          <v-spacer></v-spacer>
          <!--                        <v-btn color="red darken-1" dark @click="onClickOutside">{{ $t('close') }}</v-btn>-->
        </v-card-actions>
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
// import XLSX from "xlsx";
export default {
  data() {
    return {
      drawingModal: false,
      files: null,
      loading: false,
      search: "",
      dialog: false,
      editMode: null,
      pdfBase64: null,
      items: [],
      form: {},
      dialogHeaderText: "",
      page: 1,
      from: 0,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      emailRules: [
        (v) =>
          !v ||
          /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
          "E-mail must be valid",
      ],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("name"), value: "name_" + this.$i18n.locale },
        { text: this.$t("user.email"), value: "email" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 120,
          align: "center",
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("organizations-update") ||
          this.$store.getters.checkPermission("organizations-delete")
      );
    },
  },
  methods: {
    onChange(event) {
      this.file = event.target.files ? event.target.files[0] : null;
      let workbook = XLSX.readFile(this.file);
      console.log("workbook1");
      console.log(workbook);
      console.log("SheetNames");
      console.log(workbook.SheetNames);
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    dialogTrue() {
      this.drawingModal = true;
    },
    uploadExcel() {
      this.dialog = true;
      let formData = new FormData();
      formData.append("file", this.files);
      this.files = [];
      axios
        .post(this.$store.state.backend_url + "api/qrcodeImport", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((res) => {
          this.dialog = false;
          this.files = null;
          this.drawingModal = false;
          this.pdfBase64 = res.data;
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: (toast) => {
              toast.addEventListener("mouseenter", Swal.stopTimer);
              toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
          });
          Toast.fire({
            icon: "success",
            title: this.$t("create_update_operation"),
          });
        });
    },
  },
  mounted() {
    // this.getList();
    // this.dialogTrue();
  },
  created() {},
};
</script>
<style scoped></style>
