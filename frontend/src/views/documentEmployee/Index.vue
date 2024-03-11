<template>
  <div>
    <v-card class="ma-1 pa-1 pb-8">
      <v-card-title class="pa-1">
        <span>{{ $t('employeeDocument.index') }}</span>
        <v-spacer></v-spacer>
        <v-text-field
          append-icon="mdi-magnify"
          class="mr-2"
          style="width:100px !important;"
          :placeholder="$t('search')"
          @keyup.native.enter="getList"
          outlined
          dense
          single-line
          hide-details
        ></v-text-field>
        <v-btn
          v-if="$store.getters.checkPermission('document-employee-create')"
          color="#6ac82d"
          x-small
          dark
          fab
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>
      <!-- Main employee table -->
      <v-card class="ma-4" v-if="documents">
        <v-row class="mx-0">
          <v-card-text
            v-if="$i18n.locale=='uz_latin'"
            class="font-weight-medium headline"
            color="black"
          >{{ employee.firstname_uz_latin }} {{ employee.lastname_uz_latin }} {{ employee.middlename_uz_latin }}</v-card-text>
          <v-card-text
            v-else
            class="font-weight-medium headline"
            color="black"
          >{{ employee.firstname_uz_cyril }} {{ employee.lastname_uz_cyril }} {{ employee.middlename_uz_cyril }}</v-card-text>
          <v-col md="6" sm="12">
            <v-card-subtitle v-if="employee.tabel" class="py-2">
              {{ $t('employeeDocument.tabel') }}
              <span class="font-weight-bold">{{ employee.tabel }}</span>
            </v-card-subtitle>
            <v-card-subtitle v-if="employee.born_date" class="py-2">
              {{ $t('employeeDocument.born_date') }}
              <span
                class="font-weight-bold"
              >{{ employee.born_date }}</span>
            </v-card-subtitle>
          </v-col>
          <v-col md="6" sm="12">
            <v-card-subtitle v-if="department.department_code" class="py-2">
              {{ $t('employeeDocument.department') }}
              <span
                class="font-weight-bold"
              >{{ department.department_code }}</span>
            </v-card-subtitle>
            <v-card-subtitle v-if="position" class="py-2">
              {{ $t('employeeDocument.employee_position') }}
              <span
                class="font-weight-bold"
              >{{ position["name_"+ $i18n.locale] }}</span>
            </v-card-subtitle>
          </v-col>
        </v-row>
      </v-card>
      <v-card class="ma-4" v-for="(document, i) in documents" :key="i">
        <v-card-text
          class="font-weight-medium headline"
          color="black"
        >{{ document.official_document_type["name_"+ $i18n.locale] }}</v-card-text>
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">{{ $t('employeeDocument.series') }}</th>
                <th class="text-left">{{ $t('employeeDocument.number') }}</th>
                <th class="text-left">{{ $t('employeeDocument.given_by') }}</th>
                <th class="text-left">{{ $t('employeeDocument.date_issue') }}</th>
                <th class="text-left">{{ $t('employeeDocument.valid_until') }}</th>
                <th class="text-left">{{ $t('employeeDocument.status') }}</th>
                <th class="text-left">{{ $t('employeeDocument.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ document.series }}</td>
                <td>{{ document.number }}</td>
                <td>{{ document.given_organization }}</td>
                <td>{{ document.given_date }}</td>
                <td>{{ document.due_date }}</td>
                <td>{{ document.is_active ? "Active" : "Not active" }}</td>
                <td width="50px">
                  <v-btn
                    v-if="$store.getters.checkPermission('document-employee-update')"
                    color="blue"
                    small
                    text
                    @click="editItem(document)"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="$store.getters.checkPermission('document-employee-download')"
                    color="blue"
                    small
                    text
                    @click="editItemFiles(document, i)"
                  >
                    <v-icon>mdi-download</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="$store.getters.checkPermission('document-employee-delete')"
                    color="red"
                    small
                    text
                    @click="deleteItem(document)"
                  >
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card>
    </v-card>

    <v-dialog v-model="dialog" @keydown.esc="dialog = false" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>

          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row>
              <v-col cols="3">
                <label for>{{ $t('employeeDocument.docType') }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.official_document_type_id"
                  :items="docTaypes"
                  :rules="[v => !! v || $t('input.required')]"
                  hide-details
                  dense
                  item-value="id"
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name_ru"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name_ru"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="2">
                <label for>{{ $t('employeeDocument.series') }}</label>
                <v-text-field v-model="form.series" type="text" hide-details="auto" dense outlined></v-text-field>
              </v-col>
              <v-col cols="3">
                <label for>{{ $t('employeeDocument.number') }}</label>
                <v-text-field
                  v-model="form.number"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <label for>{{ $t('employeeDocument.given_by') }}</label>
                <v-text-field
                  v-model="form.given_organization"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="3">
                <label for>{{ $t('employeeDocument.date_issue') }}</label>
                <v-menu
                  v-model="menu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="form.given_date"
                      readonly
                      v-on="on"
                      hide-details
                      class="white"
                      dense
                      outlined
                      :rules="[v => !!v || $t('input.required')]"
                      placeholder="YYYY-MM-DD"
                    ></v-text-field>
                  </template>
                  <v-date-picker no-title v-model="form.given_date" @input="menu = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="3">
                <label for>{{ $t('employeeDocument.valid_until') }}</label>
                <v-menu
                  v-model="menu1"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="form.due_date"
                      readonly
                      v-on="on"
                      hide-details
                      class="white"
                      dense
                      outlined
                      :rules="[v => !!v || $t('input.required')]"
                      placeholder="YYYY-MM-DD"
                    ></v-text-field>
                  </template>
                  <v-date-picker no-title v-model="form.due_date" @input="menu1 = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="3">
                <label for>{{ $t('employeeDocument.title') }}</label>
                <v-text-field v-model="form.title" type="text" hide-details="auto" dense outlined></v-text-field>
              </v-col>
              <v-col cols="3">
                <label for>{{ $t('employeeDocument.status') }}</label>
                <!-- <v-text-field
                  v-model="form.is_active"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>-->
                <v-select v-model="form.is_active" :items="selectStatus" dense outlined></v-select>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t('save') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t('loadingText') }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="fileDialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t('employeeDocument.files') }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="fileDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col
                cols="10"
                style="min-width: 100px; max-width: 100%;"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t('employeeDocument.uploadFiles') }}</label>
                <v-file-input
                  v-model="selectFiles"
                  multiple
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf"
                  small-chips
                  show-size
                  hide-details
                ></v-file-input>
              </v-col>
              <v-col cols="2" style="min-width: 100px;" class="px-0">
                <v-btn
                  :disabled="selectFiles.length == 0"
                  class="mt-6"
                  color="success"
                  block
                  @click="addFiles"
                >+</v-btn>
              </v-col>
            </v-row>

            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th width="20" class="text-left">#</th>
                    <th class="text-left">{{ $t('employeeDocument.index') }}</th>
                    <th width="20" class="text-left"></th>
                    <th width="20" class="text-left"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in fileList" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td style="max-width:340px;">{{ item.file_name }}</td>
                    <td>
                      <v-btn color="primary" text @click="viewPdfFile(item)">
                        <v-icon>mdi-download</v-icon>
                      </v-btn>
                    </td>
                    <td>
                      <v-icon color="error" @click="removeTmpFile(item.id)">mdi-minus-circle-outline</v-icon>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="pdfViewDialog" width="800">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{fileForView.file_name}}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="pdfViewDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text class>
          <pdf
            v-if="fileForView.id > 0"
            :src="$store.state.backend_url + 'staffs/get-file/' + fileForView.id"
          ></pdf>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="green"
            text
            :href="$store.state.backend_url + 'staffs/get-file/' + fileForView.id"
          >{{$t('download')}}</v-btn>
          <v-btn
            color="primary"
            text
            @click="pdfViewDialog = false; fileForView.id=0;"
          >{{$t('close')}}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data: () => ({
    loading: false,
    documents: "",
    employee: {},
    position: "",
    department: "",
    dialog: false,
    editMode: null,
    fullscreen: false,
    dialogHeaderText: "",
    form: {
      files: []
    },
    menu: "",
    menu1: "",
    officialDocument: [],
    disabled: false,
    selectStatus: [0, 1],
    docTaypes: "",
    fileDialog: false,
    pdfViewDialog: false,
    selectFiles: [],
    fileForView: { id: 0 },
    objectTypesList: [],
    objectId: "",
    formData: null
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    fileList() {
      return this.form.files;
    }
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/official-documents")
        .then(response => {
          this.documents = response.data;
          this.employee = response.data[0].employee;
          this.department = response.data[0].employee.staff[0].department;
          this.position = response.data[0].employee.staff[0].position;

          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("employeeDocument.addEmployeeDocument");
      this.form = {
        id: Date.now(),
        series: "",
        number: "",
        given_organization: "",
        given_date: "",
        due_date: "",
        is_active: "",
        title: "",
        employee_id: this.employee.id
      };
      this.dialog = true;
    },
    editItem(item) {
      this.dialogHeaderText = this.$t(
        "employeeDocument.updateEmployeeDocument"
      );
      this.form = {
        id: item.id,
        series: item.series,
        number: item.number,
        given_organization: item.given_organization,
        given_date: item.given_date,
        due_date: item.due_date,
        is_active: item.is_active,
        title: item.title,
        employee_id: item.employee_id,
        official_document_type_id: item.official_document_type_id
      };
      this.dialog = true;
    },
    save() {
      axios
        .post(
          this.$store.state.backend_url + "api/official-documents/update",
          this.form
        )
        .then(res => {
          this.getList();
          this.dialog = false;
          const Toast = Swal.mixin({
            toast: true,
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
    deleteItem(item) {
      // if (this.$store.getters.checkPermission("position-delete")) {
      // const index = this.items.indexOf(item);
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        // if (result.value) {
        axios
          .delete(
            this.$store.state.backend_url +
              "api/official-documents/delete/" +
              item.id
          )
          .then(res => {
            this.getList();
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
        // }
      });
    },
    docTypeList() {
      axios
        .get(this.$store.state.backend_url + "api/official-document-types")
        .then(response => {
          this.docTaypes = response.data;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    viewPdfFile(item) {
      this.fileForView = item;
      this.pdfViewDialog = true;
      // $store.state.backend_url + 'staffs/get-file/'+item.id
    },
    editItemFiles(item, i) {
      this.form.files = this.documents[i].files;
      {
        this.formData = new FormData();
        this.objectId = item.id;
        this.fileDialog = true;
      }
    },
    addFiles() {
      this.formData = new FormData();

      this.selectFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
      });
      axios
        .post(
          this.$store.state.backend_url +
            "api/official-documents/update-files/" +
            this.objectId,
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          }
        )
        .then(res => {
          this.selectFiles = [];
          this.selectObjectType = "";
          this.form.files = res.data.files;
          // editItemFiles();
        })
        .catch(function(e) {});
    },
    removeTmpFile(id) {
      axios
        .delete(
          this.$store.state.backend_url +
            "api/official-documents/delete-files/" +
            id
        )
        .then(res => {
          this.form.files = this.form.files.filter(v => v.id != id);
          // editItemFiles(document, i);
        })
        .catch(function(e) {});
    }
  },
  mounted() {
    this.getList();
    this.docTypeList();
  }
};
</script>
<style scoped>
table {
  width: 100%;
}
table thead tr th,
table tbody tr td {
  padding: 8px 16px;
}
.theme--light.v-card > .v-card__text,
.theme--light.v-card .v-card__subtitle {
  color: rgba(0, 0, 0, 0.8);
}
</style>
