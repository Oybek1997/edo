<template>
  <div>

    <v-dialog
      v-model="addTaskDialog"
      persistent
      max-width="60%"
      @keydown.esc="addTaskDialog = false"
    >
      <v-card>
        <v-card-title>
          <span class="headline">Yangi topshiriq qo`shish</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            x-small
            outlined
            fab
            class
            @click="addTaskDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <fieldset class="my-2" style="border: none">
                <legend class="legend">{{ $t("Punkt") }}</legend>
                <v-form ref="addTaskForm">
                  <v-row>
                    <v-col cols="6">
                      <v-text-field
                        v-model="task.title"
                        :label="$t('Topshiriq nomi')"
                        outlined
                        dense
                        hide-details="auto"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="6">
                      <v-autocomplete
                        :items="priority"
                        item-value="id"
                        :item-text="'text'"
                        v-model="task.priority"
                        :label="$t('control_punkt.priority')"
                        outlined
                        dense
                        hide-details="auto"
                        clearable
                      ></v-autocomplete>
                    </v-col>
                    <v-col cols="12">
                      <v-textarea
                        v-model="task.content"
                        rows="2"
                        :label="$t('Topshiriq haqida')"
                        outlined
                        dense
                        hide-details="auto"
                      ></v-textarea>
                    </v-col>
                    <v-col cols="6">
                      <v-autocomplete
                        :items="taskCategory"
                        item-value="id"
                        item-text="name"
                        v-model="task.taskCategory"
                        :label="$t('Kategoriya')"
                        outlined
                        dense
                        hide-details="auto"
                        clearable
                      ></v-autocomplete>
                    </v-col>
                    <v-col cols="4">
                      <v-btn
                        color="success"
                        title
                        outlined
                        @click="getDocumentFile"
                      >
                        <v-icon left style="font-size: 28px"
                          >mdi-file-upload-outline</v-icon
                        >
                        {{ $t("uploadFiles") }}
                      </v-btn>
                    </v-col>
                    <v-card-text class="pt-0">
                      <v-simple-table
                        v-if="documentFiles && documentFiles.length"
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
                            <tr
                              v-for="(item, index) in documentFiles"
                              :key="index"
                            >
                              <td>{{ index + 1 }}</td>
                              <td>{{ item.file_name }}</td>
                              <td class="text-lg-right" width="100px">
                                <v-icon class="px-1" color="success"
                                  >mdi-eye</v-icon
                                >
                                <v-icon
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

                    <v-col cols="12">
                      <v-card outlined>
                        <v-card-title class="grey lighten-4 py-0">
                          {{ $t("Bajaruvchilar") }}
                          <v-spacer></v-spacer>
                          <v-btn
                            color="success"
                            outlined
                            @click="addSignerForTaskSigner"
                            >{{ $t("add") }}</v-btn
                          >
                        </v-card-title>
                        <v-card-text>
                          <v-form ref="addDoerForm">
                            <v-row class="py-5">
                              <v-col cols="6">
                                <v-autocomplete
                                  clearable
                                  :items="users"
                                  v-model="newDoer.user"
                                  @keyup="getEmployeeList()"
                                  :search-input.sync="search"
                                  :rules="[(v) => !!v || $t('input.required')]"
                                  hide-details="auto"
                                  :label="$t('Hodim')"
                                  dense
                                  outlined
                                  item-text="fullname"
                                  item-value="employee.id"
                                >
                                </v-autocomplete>
                              </v-col>
                              <v-col cols="4">
                                <v-text-field
                                  v-model="newDoer.due_datetime"
                                  :label="$t('Muddati')"
                                  type="datetime-local"
                                  outlined
                                  dense
                                  clearable
                                ></v-text-field>
                              </v-col>
                            </v-row>
                          </v-form>
                        </v-card-text>
                        <v-simple-table
                          dense
                          v-if="selectedUser && selectedUser.length"
                        >
                          <template v-slot:default>
                            <thead>
                              <tr>
                                <th>{{ "id" }}</th>
                                <th>{{ $t("Ism sharifi") }}</th>
                                <th>{{ $t("Muddati") }}</th>
                                <th>{{ $t("actions") }}</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="(item, index) in selectedUser"
                                :key="index"
                              >
                                <td>{{ index + 1 }}</td>
                                <td>
                                  {{
                                    selectedUser.find((o) => o.id == item.id)
                                      ? selectedUser.find(
                                          (o) => o.id == item.id
                                        ).lastname_uz_latin +
                                        " " +
                                        item.firstname_uz_latin +
                                        " " +
                                        item.middlename_uz_latin
                                      : ""
                                  }}
                                </td>
                                <td>
                                  {{
                                    task.user.find(
                                      (o) => o.employee_id == item.id
                                    ).due_datetime
                                  }}
                                </td>
                                <td>
                                  <v-btn
                                    color="error"
                                    @click="deleteDoer(item)"
                                    small
                                    text
                                    ><v-icon>mdi-delete</v-icon></v-btn
                                  >
                                </td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-form>
              </fieldset>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions class="pt-0">
          <v-spacer></v-spacer>
          <v-btn color="green" dark @click="taskSave">{{ $t("save") }}</v-btn>
          <v-btn color="green" dark @click="taskSavePublish" v-if="editItem == false">{{
            $t("Saqlash va E'lon qilish")
          }}</v-btn>
          <!--                        <v-btn color="red darken-1" dark @click="onClickOutside">{{ $t('close') }}</v-btn>-->
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="modalTaskFile" persistent width="800">
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
                @click="modalTaskFile = false"
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
                      >+</v-btn
                    >
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
          </v-card>
        </v-dialog>
   </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
export default {
  data: () => ({
    task: {
      // id: Date.now(),
      content: "",
      title: "",
      priority: null,
      taskCategory: null,
      files: null,
      task_type_id: null,
      status: null,
      user: [],
    },
    addTaskDialog: false,
    newDoer: {
      user: [],
      due_datetime: "",
    },
    taskCategory: [],
    modalTaskFile: false,
    selectFiles: [],
    formData: [],
    documentFiles: [],
    checkPublish: false,
    priority: [
      {
        id: 1,
        text: "Yuqori",
      },
      {
        id: 2,
        text: "O'rta",
      },
      {
        id: 3,
        text: "Past",
      },
    ],
    checkbox: {
      groupsigners: true,
      signers: true,
    },
    organizationDialogForm: {
      organization_ids: null,
    },
    addOrganizationDialog: false,
    organizationGroups: [],
    OrganizationGroupItem: [],
    organizations: [],
    actionTypes: [],
    search: "",
    users: [],
    selectedUser: [],
    formData: [],
    taskType: [],
    modalTaskFile: false,
    dtp: false,
    editItem: false,
    headerSigner: [
      { text: "#", value: "id", align: "center", width: 30, sortable: false },
      {
        text: "organization",
        value: "organization_id",
        sortable: true,
      },
    ],
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 200;
    },
  },
  methods: {
    getEmployeeList() {
      // Formadagi autocomplete yordamida foydalanuvchini izlashda ishlatiladigan funksiya
      axios
        .post(this.$store.state.backend_url + "api/user-search", {
          search: this.search,
        })
        .then((res) => {
          this.users = res.data.data.map((v) => {
            v.fullname =
              v.employee["lastname_" + this.$i18n.locale] +
              " " +
              v.employee["firstname_" + this.$i18n.locale] +
              " " +
              v.employee["middlename_" + this.$i18n.locale] +
              " " +
              v.username;
            return v;
          });
          // console.log(this.users)
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getCategory(){
      axios.get(this.$store.state.backend_url + "api/worktask-category")
          .then((res) => {
            this.taskCategory = res.data;
            console.log(this.taskCategory);
          });
    },
    deleteDoer(item) {
      this.selectedUser = this.selectedUser.filter((v) => v.id != item.id);
      this.task.user = this.task.user.filter((v) => v.employee_id != item.id);
    },
    addTaskFromParent(){
      this.addTaskDialog = true;
      this.getCategory();
    },
    editTaskFromParent(id){
      this.addTaskDialog = true;
      this.getCategory();
      this.editItem = true;
      console.log(id);
      if (id > 0) {
        axios
          .post(this.$store.state.backend_url + "api/worktask-taskItem", {
            id: id,
          })
          .then((res) => {
            this.task = res.data[0];
            console.log(this.task);
            if (this.task.priority == 1) {
              this.task.priority = 1;
            } else if (this.task.priority == 2) {
              this.task.priority = 2;
            } else if (this.task.priority == 3) {
              this.task.priority = 3;
            }
            this.task.taskCategory = this.task.category_id;
            this.task.user.forEach((v) => {
              if (v.assignment_type == 0) {
                this.selectedUser.push(v.doer);
              }
            });
          });
      }
    },
    addSignerForTaskSigner() {
      // console.log(this.users.fullname);
      this.selectedUser.push(this.users[0].employee);
      if (this.newDoer.user) {
        this.task.user.push({
          id: this.task.id,
          employee_id: this.newDoer.user,
          due_datetime: this.newDoer.due_datetime,
        });
        // console.log(this.task.user);
        // console.log(this.users)
      }
      // if (this.newSignerForm.organization_ids) {
      //   this.newSignerForm.organization_ids.forEach((o) => {
      //     this.task.task_signers.push({
      //       id: Date.now() + o,
      //       document_id: this.document.id,
      //       organization_id: o,
      //       action_type_id: this.newSignerForm.action_type_id,
      //       description: this.newSignerForm.description,
      //       due_datetime: this.newSignerForm.due_datetime,
      //     });
      //   });
      // }
      this.$refs.addDoerForm.reset();
      // this.newDoer = null;
    },
    gettaskFile() {
      this.modalTaskFile = true;
    },
    addFiles() {
      this.selectFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
        this.documentFiles.push({
          id: Date.now() + Math.floor(Math.random() * 1000),
          file_name: v.name,
        });
      });
      this.selectFiles = [];
      this.modalTaskFile = false;
    },
    deleteFile(item) {
      this.documentFiles = this.documentFiles.filter((v) => v.file_name != item.file_name);
      // let id = parseInt(this.$route.params.id);
      // this.formData.delete(item.file_name);
      // axios
      //   .delete(
      //     this.$store.state.backend_url + "api/documents/deletefile/" + item.id
      //   )
      //   .then((resp) => {
      //     // console.log(resp);
      //     this.documentFiles = this.documentFiles.filter((v) => {
      //       if (v.file_name != item.file_name) {
      //         return v;
      //       }
      //     });
      //   });
    },
    taskSavePublish() {
      this.task.status = 1;
      this.taskSave();
    },
    getDocumentFile() {
      this.modalTaskFile = true;
    },
    taskSave() {
      // console.log(this.task);
       if(this.selectedUser.length>0){
        
         axios
           .post(
             this.$store.state.backend_url + "api/worktask-create",
             Object.assign(this.task)
           )
           .then((response) => {
             axios.post(
               this.$store.state.backend_url +
                 "api/worktask/updatefiles/" +
                 response.data,
               this.formData,
               {
                 headers: {
                   "Content-Type": "multipart/form-data",
                 },
               }
             );
             this.editItem = false;
             this.$emit("getTask");
            // this.$router.push("/worktask/index");
            //  this.$emit("showTask", response.data);
             
             this.addTaskDialog = false;
             this.$refs.addTaskForm.reset();
         this.clear();
           })
           .catch((err) => {
             console.error(err);
           });
         
       }
       else{
        alert("Hodim biriktirilsin!")
       }
    },
    UpdateTask() {
      let id = parseInt(this.$route.params.documentId);
      if (id > 0) {
        axios
          .post(this.$store.state.backend_url + "api/worktask-taskItem", {
            id: id,
          })
          .then((res) => {
            this.task = res.data[0];
            if (this.task.priority == 1) {
              this.task.priority = 1;
            } else if (this.task.priority == 2) {
              this.task.priority = 2;
            } else if (this.task.priority == 3) {
              this.task.priority = 3;
            }

            this.task.user.forEach((v) => {
              if (v.assignment_type == 0) {
                this.selectedUser.push(v.doer);
              }
            });
          });
      }
    },
    getOrgGroup() {
      axios
        .get(this.$store.state.backend_url + "api/documents/get-ref")
        .then((resp) => {
          // this.organizationGroups = res.data.organizationGroups;
          this.organizations = resp.data.organizations;
          // this.OrganizationGroupItem = res.data.OrganizationGroupItem;
          this.actionTypes = resp.data.actionTypes;
          this.taskType = resp.data.taskType;
        });
      axios
        .post(
          this.$store.state.backend_url +
            "api/organization/getorganizationsgroup"
        )
        .then((res) => {
          this.organizationGroups = res.data;
        });
    },
    clear() {
      this.selectedUser = [];
      this.task.title = ''
      this.task.content = ''
      this.task.priority = null
      this.task.taskCategory= null,
      this.task.files= null,
      this.task.task_type_id= null,
      this.task.status= null,
      this.task.user = [];
      this.documentFiles = [];
      this.newDoer.user = []
      this.newDoer.due_datetime = ''
    },
  },
  mounted() {
    this.UpdateTask();
    this.formData = new FormData();
    
  },
};
</script>

<style scoped>
.v-card {
  transition: opacity 0.4s ease-in-out;
}

.v-card:not(.on-hover) {
  opacity: 1;
}
.theme--light.v-card > .v-card__text,
.theme--light.v-card > .v-card__subtitle {
  color: #000;
}
.show-btns {
  color: rgba(255, 255, 255, 1) !important;
}
</style>
