<template>
  <div>
    <v-card class="ma-1 pa-1">
        <div class="container">
    <div class="large-12 medium-12 small-12 cell">
      <label>File
        <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
      </label>
        <button v-on:click="submitFile()">Submit</button>
    </div>


     <v-btn
                      text
                      color="cyan"
                      target="_blank"
                    @click="getDownload"
                    >
                      <v-icon>mdi-download</v-icon>
                    </v-btn>

  </div>
      </v-card>


  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
      dialog: false,
      editMode: null,
      form: {},
      dialogHeaderText: "",
      file: '',
    imageUrl: null
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: 'center', width: 30 },
        { text: this.$t("districts.region_id"), value: "region.name_uz_latin" },
        { text: this.$t("name_uz_latin"), value: "name_uz_latin" },
        { text: this.$t("name_uz_cyril"), value: "name_uz_cyril" },
        { text: this.$t("name_ru"), value: "name_ru" },
        { text: this.$t("actions"), value: "actions", width: 50, align: 'center' }
      ];
    }
  },
  methods: {
    handleFileUpload()
    {
    this.file = this.$refs.file.files[0];
    },
    submitFile(){
  let formData = new FormData();
  formData.append('file', this.file);
   axios.post( this.$store.state.backend_url+'api/document_templates/file_upload',
                formData,
                {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
              }
            ).then(function(){
          console.log('SUCCESS!!');
          this.file='';
        })
        .catch(function(){
          console.log('FAILURE!!');
        });
      },


getDownload()
{
       axios
        .post(this.$store.state.backend_url + "api/download_test",{file:3})
        .then(response => {
        })
        .catch(error => {
          console.log(error);
        });
},

    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/districts")
        .then(response => {
          this.items = response.data.districts;
          this.regions = response.data.regions.map(v => {
            return { text: v.name_uz_latin, value: v.id };
          });
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("districts.newDistrict");
      this.form = {
        id: Date.now(),
        region_id: "",
        name_uz_latin: "",
        name_uz_cyril: "",
        name_ru: ""
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("districts.newDistrict");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/districts/update",
            this.form
          )
          .then(res => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: toast => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
              }
            });

            Toast.fire({
              icon: "success",
              title: $t("create_update_operation")
            });
          })
          .catch(err => {
            console.log(err);
          });
    }, //document-types
    deleteItem(item) {
      const index = this.items.indexOf(item);
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url + "api/districts/delete/" + item.id
            )
            .then(res => {
              this.getList(this.page, this.itemsPerPage);
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    }
  },
  mounted() {
    this.getList();

  }
};
</script>
