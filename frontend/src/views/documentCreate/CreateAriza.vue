<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t('documentTemplates.index') }}</span>
        <v-spacer></v-spacer>
        <v-btn color="#6ac82d" x-small dark fab @click="newItem">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        show-expand
        :expanded="expanded"
        class="mainTable ma-1"
        style="border: 1px solid #aaa;"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100, -1],
          itemsPerPageAllText:$t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right'
        }"
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        disable-sort
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
      >
        <template
          v-slot:item.id="{ item }"
        >{{items.map(function(x) {return x.id; }).indexOf(item.id) + 1}}</template>
        <template v-slot:item.roles="{ item }">
          <span v-for="(i, idx) in item.roles" :key="idx">{{ i.name + ', ' }}</span>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn color="blue" small text @click="editItem(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>

        <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="headers.length" class="pa-3">
            <v-card class="my-2">
              <div>


                <v-system-bar window color="#eee">
                  <v-icon color="success" medium @click="newAttribute(item)">mdi-plus</v-icon>
                </v-system-bar>
              </div>
              <v-container fluid class="pa-0">
                <h3>{{item.department["name_"+ $i18n.locale]}}</h3>
                <table class="infoTable ma-0 pa-0">
                  <tr>
                    <th>{{$t('documentTemplates.attributeName')}}</th>
                    <th>{{$t('documentTemplates.value_min_lenght')}}</th>
                    <th>{{$t('documentTemplates.value_max_lenght')}}</th>
                    <th>{{$t('documentTemplates.description')}}</th>
                    <th>{{$t('documentTemplates.dataType')}}</th>
                    <th>{{$t('actions')}}</th>

                  </tr>
                  <tr  v-for="(itm, idx) in item.document_attributes" :key="idx">
                    <td>{{itm['attribute_name_'+$i18n.locale]}}</td>
                    <td>{{itm.value_min_lenght}}</td>
                    <td>{{itm.value_max_lenght}}</td>
                    <td>{{itm.description}}</td>
                    <td>{{itm.data_type['name_'+$i18n.locale]}}</td>


                    <td class style="max-width: 60px;">
                      <v-btn
                        color="blue"
                        class="my-1"
                        x-small
                        text
                        @click="editStaff(itm)"
                      >
                        <v-icon>mdi-pencil</v-icon>
                      </v-btn>
                      <v-btn
                        color="red"
                        class="my-1"
                        x-small
                        text
                        @click="deleteStaff(itm)"
                      >
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </td>
                  </tr>
                </table>
              </v-container>
            </v-card>
          </td>
        </template>

        <template v-slot:item.document_type_id="{ item }">
          <span
            style="white-space:normal;max-width: 100px;"
            v-if="item.document_type"
          >{{ item.document_type["name_"+ $i18n.locale] }}</span>
        </template>

        <template v-slot:item.signer_group_id="{ item }">
          <span
            style="white-space:normal;max-width: 100px;"
            v-if="item.signer_group"
          >{{ item.signer_group["name_"+ $i18n.locale] }}</span>
        </template>

        <template v-slot:item.department_id="{ item }">
          <span
            style="white-space:normal;max-width: 100px;"
            v-if="item.department"
          >{{ item.department["name_"+ $i18n.locale] }}</span>
        </template>

      </v-data-table>
    </v-card>
    <v-dialog v-model="dialog" @keydown.esc = "dialog = false" persistent max-width="800px">
      <v-card>
        <v-card-title>
          <span class="headline">{{ $t('documentTemplates.index') }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              
              <v-col cols="12" sm="12">
                <label for>{{ $t('documentTypes.index') }}</label>
                <v-autocomplete
                  v-model="form.document_type_id"
                  :items="documentType"
                  clearable
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  @change="bringAttributes(form.document_type_id)"
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" sm="6" v-for="(item, index) in documentAttributes" :key="index">
                <label for>{{ $t(item.name) }}</label>
                <span v-if="item.data_type_id == 1">
                 <label for>{{ $t('minimum') }}</label>
                  <v-text-field v-model="item.value"></v-text-field>
                    <label for>{{ $t('maksimum') }}</label>
                  <v-text-field v-model="item.value"></v-text-field>
                </span>
                <span v-else-if="item.data_type_id == 2">
                  <v-text-field type="number" v-model="item.value"></v-text-field>
                  <v-text-field type="number" v-model="item.value"></v-text-field>
                </span>
                <span v-else-if="item.data_type_id == 3">
                  <v-text-field type="date" v-model="item.value"></v-text-field>
                  <v-text-field type="date" v-model="item.value"></v-text-field>
                </span>
              </v-col>    
 
 
            </v-row>
          </v-form>
          <small color="red">{{ $t('input_required') }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveStaff">{{ $t('save') }}</v-btn>
        </v-card-actions>
      </v-card>

      
    </v-dialog>

    <v-dialog v-model="StaffDialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ "" }}</span>
          <v-spacer></v-spacer>

          <v-btn color="red" outlined x-small fab class @click="StaffDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="StaffSave" ref="staffDialogform">
            <v-row>
              <v-col cols="6">
                <label for>{{ $t('documentTemplates.attribute_name_uz_latin') }}</label>
                <v-text-field
                  v-model="StaffForm.attribute_name_uz_latin"
                  :rules="[v => !!v || $t('input.required')]"
                  clearable
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>


              <v-col cols="6">
                <label for>{{ $t('documentTemplates.attribute_name_uz_cyril') }}</label>
                <v-text-field
                  v-model="StaffForm.attribute_name_uz_cyril"
                  :rules="[v => !!v || $t('input.required')]"
                  clearable
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{ $t('documentTemplates.attribute_name_ru') }}</label>
                <v-text-field
                  v-model="StaffForm.attribute_name_ru"
                  :rules="[v => !!v || $t('input.required')]"
                  clearable
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{ $t('documentTemplates.value_min_lenght') }}</label>
                <v-text-field
                  v-model="StaffForm.value_min_lenght"
                  :rules="[v => !!v || $t('input.required')]"
                  clearable
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{ $t('documentTemplates.value_max_lenght') }}</label>
                <v-text-field
                  v-model="StaffForm.value_max_lenght"
                  :rules="[v => !!v || $t('input.required')]"
                  clearable
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <label for>{{ $t('documentTemplates.description') }}</label>
                <v-text-field
                  v-model="StaffForm.description"
                  :rules="[v => !!v || $t('input.required')]"
                  clearable
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
 <v-col cols="6">
                <label for>{{ $t('documentTemplates.dataType') }}</label>
                <v-autocomplete
                  v-model="StaffForm.data_type_id"
                  :items="dataTypes"
                  clearable
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveStaff(item)">{{$t('save')}}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
      StaffDialog:false,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      dataTableValue: [],
      loading: false,
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      attributes: [],
      regions: [],
      form: {},
      formAttribute:{},
      dialogHeaderText: "",
      createdAtMenu2: false,
      department:[],
      documentType:[],
      signerGroup:[],
      expanded: [],
      dataTypes:[],
      StaffForm:{},
      documentAttributes:[],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: 'center', width: 30 },
        { text: this.$t("name_"+this.$i18n.locale), value: "name_"+this.$i18n.locale },
        { text: this.$t("description_"+this.$i18n.locale), value: "decription_"+this.$i18n.locale },
        { text: this.$t("department_id"), value: "department_id"},
        { text: this.$t("documentTypes.index"), value: "document_type_id"},
        { text: this.$t("signerGroup.signer_group_id"), value: "signer_group_id"},
        // { text: this.$t("name_uz_cyril"), value: "name_uz_cyril" },
        // { text: this.$t("name_ru"), value: "name_ru" },
        { text: this.$t("actions"), value: "actions", width:'2%', align: 'center' }
      ];
    }
  },
  methods: {
    bringAttributes(item)
    {
        axios
          .post(this.$store.state.backend_url + "api/document_templates/bringDocumentDetailtAttribute", {item:item})
          .then(res => {
            this.documentAttributes = res.data.attrubutes.map(v => {
              v.value = '';
              return v;
            });
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
              title: this.$t("create_update_operation")
            });
          })
          .catch(err => {
            console.log(err);
          });
    },
    saveStaff() {
      if (this.$refs.staffDialogform.validate())
        axios
          .post(this.$store.state.backend_url + "api/document_templates/newDocument", this.formAttribute)
          .then(res => {
            this.getList();
            this.StaffDialog = false;
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
              title: this.$t("create_update_operation")
            });
          })
          .catch(err => {
            console.log(err);
          });
    },


    editStaff(item) {
      this.dialogHeaderText = this.$t("edit staff");
      this.StaffForm = Object.assign({}, item);
      this.StaffDialog = true;
      this.editMode = true;
      if (this.$refs.staffDialogform) this.$refs.staffDialogform.resetValidation();
    },
    deleteStaff(item)    {
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
              this.$store.state.backend_url + "api/document_templates/destroyDetailAttribute/" + item.id
            )
            .then(res => {
              this.getList(this.page, this.itemsPerPage);
              this.StaffDialog = false;
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
    },

    newAttribute(item) {
      this.StaffForm = {
        id: Date.now(),
        document_template_id: item.id,
        attribute_name_uz_latin: "",
        attribute_name_uz_cyril: "",
        attribute_name_ru: "",
        value_min_lenght: "",
        value_max_lenght: "",
        description: "",
        data_type_id:"",
      };
      this.StaffDialog = true;
      this.editMode = false;
      if (this.$refs.staffDialogform) this.$refs.staffDialogform.reset();
    },

    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/document_templates",{pagination: this.dataTableOptions,})
        .then(response => {
          this.items = response.data.document_templates.data;
          this.from = response.data.document_templates.from;
          this.server_items_length = response.data.document_templates.total;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getRef() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/document_templates/getRef",{pagination: this.dataTableOptions,})
        .then(response => {

          this.department = response.data.department.map(v => {
            return { text: v['name_'+this.$i18n.locale], value: v.id };
          });
          this.signerGroup = response.data.signerGroup.map(v => {
            return { text: v['name_'+this.$i18n.locale], value: v.id };
          });

          this.documentType = response.data.documentType.map(v => {
            return { text: v.name, value: v.id };
          });

          this.dataTypes = response.data.dataTypes.map(v=>{
              return { text: v['name_'+this.$i18n.locale], value: v.id };
          });

          this.from = response.data.document_templates.from;
          this.server_items_length = response.data.document_templates.total;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("document_templates.newDistrict");
      this.form = {
        id: Date.now(),
        department_id: "",
        document_type_id: "",
        signer_group_id: "",
        name_uz_latin: "",
        name_uz_cyril: "",
        name_ru: "",
        decription_uz_latin: "",
        decription_uz_cyril: "",
        decription_ru: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("document_templates.newDistrict");
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
            this.$store.state.backend_url + "api/document_templates/update",
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
              this.$store.state.backend_url + "api/document_templates/delete/" + item.id
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
    this.getRef();
  }
};
</script>

<style>
.text-start {
  width:50px !important;
}
</style>



