<template>
  <div class="fullHeight">
    <v-form ref="dialogForm">
      <v-card class="ma-1 pa-1 h-100">
        <v-card-title class="pa-1">
          <h4>{{fileName}}</h4>
          <v-spacer></v-spacer>
          <v-btn color="success" dense @click="save()">
            Saqlash
          </v-btn>
          <v-btn dense class="ml-2" @click="$router.push('/blank-templates/list')">
            Ortga
          </v-btn>
        </v-card-title>
      </v-card>
      <v-card class="ma-1 pa-1">
        <div>
          <v-system-bar window color="#eee">
            <span class="font-weight-bold">{{
              $t("document_detail_attributes.index")
            }}</span>
            <v-spacer></v-spacer>
            <v-icon color="success" large
                    @click=" blank_attribute_templates.push(
                      {
                        id: Date.now(),
                        blank_id: $route.params.id,
                        attribute_name: '',
                        parameter_name: '',
                        data_type_id: '',
                      }
                   )"
            >mdi-plus-circle
            </v-icon>
          </v-system-bar>
        </div>
        <v-card-text>
          <v-row
            v-for="(item, index) in blank_attribute_templates"
            :key="index" style="border: 1px solid black" class="pa-1 mt-1">

            <v-col cols="12" class="ma-0 pa-0">
              <div>
                <v-system-bar window color="#edf5ff">
                  <v-spacer></v-spacer>
                  <v-icon
                    color="error"
                    medium
                    @click="deleteBlankAttribute(item)"
                  >mdi-delete
                  </v-icon>
                </v-system-bar>
              </div>
            </v-col>
            <v-col cols="6" md="4" class="py-1 px-2">
              <label>{{
                $t("blankTemplate.attribute_name")
                }}</label>
              <v-text-field
                v-model="item.attribute_name"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="4" class="py-1 px-2">
              <label v-if="fileType === 0">{{ $t("blankTemplate.parameter_name") }}</label>
              <label v-else>{{ $t("blankTemplate.cell_address") }}</label>
              <v-text-field
                v-model="item.parameter_name"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="4" class="py-1 px-2">
              <label>{{ $t("blankTemplate.data_type_id") }}</label>
              <v-autocomplete
                clearable
                v-model="item.data_type_id"
                :items="dataTypes"
                :rules="[(v) => !!v || $t('input.required')]"
                hide-details
                dense
                item-text="text"
                item-value="value"
                outlined
                full-width
              ></v-autocomplete>
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
        fileName: '',
        dialogHeaderText: "",
        expanded: [],
        dataTypes: [],
        filName: "",
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
            this.fileType = res.data.fileType;
            this.fileName = res.data.fileName;
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

      save() {
        if (this.$refs.dialogForm.validate())
          axios
            .post(
              this.$store.state.backend_url + "api/blank-templates/update-attribute",
              this.blank_attribute_templates
            )
            .then((res) => {
              this.dialog = false;
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
              this.$router.push('/blank-templates/list');
              Toast.fire({
                icon: "success",
                title: this.$t("create_update_operation"),
              });
            })
            .catch((err) => {
              console.error(err);
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
<style scoped>
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
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
</style>