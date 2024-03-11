<template>
  <div>
    <v-form ref="dialogForm">
      <v-card class="ma-1 pa-1 h-100">
        <v-card-title class="pa-1">
          <h4>{{this.fileName}}</h4>
          <v-spacer></v-spacer>
          <v-btn color="success" dense @click="downloadFile()">
            Yuklash
          </v-btn>

          <v-btn dense class="ml-2" @click="$router.push('/blank-templates/get-blank')">
            Ortga
          </v-btn>
        </v-card-title>
      </v-card>
      <v-card class="ma-1 pa-1">
        <div>
          <v-system-bar window color="#eee">
            <span class="font-weight-bold">{{
              $t("document_detail_templates.index")
            }}</span>
          </v-system-bar>
        </div>
        <v-card-text>
          <v-row class="pa-1 mt-1">
            <v-col v-for="(item, index) in blank_attribute_templates"
                   :key="index" cols="6" md="4" class="py-1 px-2">
              <label>{{ item.attribute_name}}</label>
              <v-text-field
                v-model="item.set_name"
                :type="setType(item.data_type_id)"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
              ></v-text-field>
            </v-col>

          </v-row>
        </v-card-text>
      </v-card>
    </v-form>
  </div>
</template>
<script>
  const axios = require("axios").default;
  import Swal from "sweetalert2";

  export default {
    data() {
      return {
        page: 1,
        from: 0,
        server_items_length: -1,
        dataTableOptions: {page: 1, itemsPerPage: 50},
        dataTableValue: [],
        loading: false,
        search: "",
        dialog: false,
        editMode: null,
        items: [],
        blank_attribute_templates: [],
        set_name: "",
        fileName: "",
        fileType: "",
        dialogHeaderText: "",
        expanded: [],
        dataTypes: [],
        StaffForm: {},
        tableLists: [],
      };
    },
    computed: {
      screenHeight() {
        return window.innerHeight - 170;
      },
    },
    methods: {
      setType(id) {
        if (id == 1) {
          return "text"
        }
        else if (id == 2) {
          return "number"
        }
        else if (id == 3) {
          return "date"
        }
        else if (id == 7) {
          return "datetime-local"
        }
      },
      deleteBlankAttribute(item) {
        this.blank_attribute_templates = this.blank_attribute_templates.filter(
          (v) => v.id != item.id
        );
        if (item.blank_id) {
          axios
            .delete(
              this.$store.state.backend_url + "api/blank-templates/delete-attribute/" + item.id
            )
            .then((res) => {
            })
            .catch((err) => {
              console.log(err);
            });
        }
      },
      getRef() {
        axios
          .post(this.$store.state.backend_url + "api/blank-templates/get-ref/" + this.$route.params.id)
          .then((res) => {
            this.fileName = res.data.fileName;
            this.fileType = res.data.fileType;
            this.dataTypes = res.data.dataType.map((v) => ({
              value: v.id,
              text: v["name_" + this.$i18n.locale],
            }));
            this.blank_attribute_templates = res.data.blankAttributeTemplate.map((v) => ({
              id: v.id,
              attribute_name: v.attribute_name,
              blank_id: v.blank_id,
              parameter_name: v.parameter_name,
              data_type_id: v.data_type_id,
            }));
          })
          .catch((error) => {
            console.error(error);
            this.loading = false;
          });
      },
      updatePage($event) {
        this.getList();
      },
      updatePerPage($event) {
        this.getList();
      },
      downloadFile() {
        if (this.$refs.dialogForm.validate())
          axios
            .post(
              this.$store.state.backend_url + "api/blank-templates/download", {
                blank_attribute_templates: this.blank_attribute_templates,
                fileType: this.fileType
              }
            ).then(res => {
            let downloadUrl = this.$store.state.backend_url + res.data;
            window.open(downloadUrl);
            axios
              .post(
                this.$store.state.backend_url + "api/blank-templates/delete-file", {
                  file: res.data
                })
          })

      },
      deleteItem(item) {
        const index = this.items.indexOf(item);
        Swal.fire({
          title: this.$t("swal_title"),
          text: this.$t("swal_text"),
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: this.$t("swal_delete"),
        }).then((result) => {
          if (result.value) {
            axios
              .delete(
                this.$store.state.backend_url +
                "api/document-templates/delete/" +
                item.id
              )
              .then((res) => {
                this.getList(this.page, this.itemsPerPage);
                this.dialog = false;
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
          }
        });
      },
    }
    ,
    mounted() {
      this.getRef();
    }
    ,
  }
  ;
</script>
