<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("department.departments") }}</span>
        <v-spacer></v-spacer>
        <v-btn
          v-if="$store.getters.checkPermission('department-create')"
          color="#6ac82d"
          dark
          fab
          x-small
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col style="border:1px solid red;">
            <v-treeview
              :items="items"
              item-key="id"
              item-text="name_ru"
              return-object
              open-all
              selectable
              expand-icon="mdi-chevron-down"
              indeterminate-icon="mdi-minus-box-outline"
              hoverable
              :active.sync="active"
              dense
              transition
            >
              <template v-slot:prepend="{ item, open }">
                <v-icon color="orange" v-if="!item.file">{{
                  open ? "mdi-folder-open" : "mdi-folder"
                }}</v-icon>
                <v-icon color="orange" v-else>{{ files[item.file] }}</v-icon>
              </template>
              <template v-slot:append="{ item, open }">
                <v-btn
                  class="mx-4"
                  color="blue"
                  small
                  text
                  @click="editItem(item)"
                >
                  <v-icon>mdi-eye</v-icon>
                </v-btn>
              </template>
              <template v-slot:label="{ item, open, selected }">
                <v-row
                  :style="
                    selected ? 'color:red;cursor: pointer;' : open ? 'font-weight:bold;cursor: pointer;' : 'cursor: pointer;'
                  "
                >
                  <v-col
                    cols="8"
                    class="py-0 text-truncate d-inline-block"
                    :title="item['name_' + $i18n.locale]"
                    >{{
                      item.department_code + " " + item["name_" + $i18n.locale]
                    }}</v-col
                  >
                  <v-col
                    cols="4"
                    class="py-0 text-truncate d-inline-block"
                    :title="
                      item.employee_staff && item.employee_staff.employee
                        ? item.employee_staff.employee[
                            'firstname_' + language
                          ] +
                          ' ' +
                          item.employee_staff.employee[
                            'middlename_' + language
                          ] +
                          ' ' +
                          item.employee_staff.employee['lastname_' + language]
                        : ''
                    "
                    >{{
                      item.employee_staff && item.employee_staff.employee
                        ? item.employee_staff.employee[
                            "firstname_" + language
                          ].substr(0, 1) +
                          ". " +
                          item.employee_staff.employee[
                            "middlename_" + language
                          ].substr(0, 1) +
                          ". " +
                          item.employee_staff.employee["lastname_" + language]
                        : ""
                    }}</v-col
                  >
                </v-row>
              </template>
            </v-treeview>
          </v-col>
          <v-col></v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card>
        <v-card-title>
          <span class="headline">{{ $t("department.dialog") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="6">
                <label for>{{ $t("department.parent_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.parent_id"
                  :items="items.map((v) => ({ text: v.name_ru, value: v.id }))"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("department.department_type_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.department_type_id"
                  :items="
                    departmentTypes.map((v) => ({
                      text: v.name_ru,
                      value: v.id,
                    }))
                  "
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("name_uz_latin") }}</label>
                <v-text-field
                  v-model="form.name_uz_latin"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("name_uz_cyril") }}</label>
                <v-text-field
                  v-model="form.name_uz_cyril"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("name_ru") }}</label>
                <v-text-field
                  v-model="form.name_ru"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("department.department_code") }}</label>
                <v-text-field
                  v-model="form.department_code"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
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
export default {
  data: () => ({
    active:[],
    loading: false,
    search: "",
    dialog: false,
    editMode: null,
    items: [],
    departmentTypes: [],
    users: [],
    form: {},
    filterForm: {
      id: Date.now(),
      company_id: "",
      parent_id: "",
      department_type_id: "",
      department_code: "",
      name_uz_latin: "",
      name_uz_cyril: "",
      name_ru: "",
    },
    dialogHeaderText: "",
    staff: [],
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        {
          text: "#",
          value: "id",
          align: "center",
          width: 30,
        },
        {
          text: this.$t("department.name"),
          value: "name",
          width: 200,
        },
        {
          text: this.$t("department.parent_id"),
          value: "parent_id",
          width: 200,
        },
        {
          text: this.$t("department.department_type_id"),
          value: "department_type_id",
          width: 200,
        },
        {
          text: this.$t("department.department_code"),
          value: "department_code",
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
        },
      ];
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
  },
  methods: {
    fetchUsers (item) {
      console.log(item);
    },
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/departments-tree")
        .then((response) => {
          this.items = response.data.departments;
          this.departmentTypes = response.data.departmentType;
          this.staff = response.data.staff;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("user.newUser");
      this.form = {
        id: Date.now(),
        company_id: "",
        parent_id: "",
        department_type_id: "",
        // manager_staff_id: "",
        department_code: "",
        name_uz_latin: "",
        name_uz_cyril: "",
        name_ru: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("department.departments");
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/departments/update",
            this.form
          )
          .then((res) => {
            this.getList();
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

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation"),
            });
          })
          .catch((err) => {
            console.log(err);
          });
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
                "api/departments/delete/" +
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
  },
  mounted() {
    this.getList();
    // Swal.fire({
    //   position: "top-end",
    //   icon: "success",
    //   title: "Your work has been saved",
    //   showConfirmButton: false,
    //   timer: 1500
    // });
  },
};
</script>
