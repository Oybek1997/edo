<template>
  <div>
    <v-card
      class="ma-1 pa-1"
      v-if="$store.getters.checkPermission('as400_query_control')"
    >
      <v-card-title class="pa-1">
        <span>So'rovlar ro'yxati</span>
        <v-spacer></v-spacer>
        <v-btn
          v-if="$store.getters.checkPermission('as400_query_control')"
          color="#6ac82d"
          x-small
          dark
          fab
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>
      <v-data-table
        dense
        fixed-header
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        class="mainTable ma-1"
        style="border: 1px solid #aaa;"
        show-expand
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100, -1],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right',
        }"
      >
        <template v-slot:item.id="{ item }">{{
          items
            .map(function (x) {
              return x.id;
            })
            .indexOf(item.id) + 1
        }}</template>

        <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="headers.length" class="pa-3 innerTable">
            <v-spacer></v-spacer>
            <div
              style="
                display: flex;
                align-items: center;
                justify-content: flex-end;
              "
            >
              <v-text-field
                @keyup.native.enter="getEmployee"
                @input="getEmployee"
                v-model="employeeTabel"
                dense
                outlined
                type="text"
                hide-details
                label="Tabel raqami"
                style="
                  max-width: 170px;
                  min-width: 140px;
                  float: right;
                  margin-bottom: 10px;
                  order: 99;
                  margin-left: 10px;
                "
                class="mr-3 white"
              ></v-text-field>

              <p v-if="employeeInfo" dense>
                {{ employeeInfo["lastname_" + $i18n.locale] }}
                {{ employeeInfo["firstname_" + $i18n.locale] }}
                {{ employeeInfo["middlename_" + $i18n.locale] }}
              </p>
              <v-btn
                v-if="employeeInfo"
                @click="addEmployee(item)"
                class="success"
              >
                Qo'shish
              </v-btn>
            </div>

            <table class="ma-0 pa-0">
              <thead>
                <tr>
                  <td class="font-weight-bold">{{ $t("#") }}</td>
                  <td class="font-weight-bold">
                    Ism familiya
                  </td>
                  <td class="font-weight-bold" style="max-width: 50px;">
                    {{ $t("actions") }}
                  </td>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(permission, index) in item.as400_permissions"
                  :key="index"
                >
                  <td>{{ index + 1 }}</td>
                  <td>
                    <router-link
                      :to="'/personcontrol/profile/' + permission.employee_id"
                      style="text-decoration: none;"
                    >
                      <span>
                        {{ permission.employee["firstname_" + $i18n.locale] }}
                        {{ permission.employee["lastname_" + $i18n.locale] }}
                        {{ permission.employee["middlename_" + $i18n.locale] }}
                      </span>
                    </router-link>
                  </td>
                  <td class style="max-width: 40px;">
                    <v-btn
                      v-if="$store.getters.checkPermission('critical-delete')"
                      color="red"
                      class="my-1"
                      x-small
                      text
                      @click="deletePermission(permission)"
                    >
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </template>

        <template #item.full_name="{ item }">
          <a target="_blank" :href="`${link}/#/personcontrol/profile/${item.created_by.employee_id}`">
            {{ item.created_by.employee["lastname_" + $i18n.locale] }} {{ item.created_by.employee["firstname_" + $i18n.locale] }}
            {{ item.created_by.employee["middlename_" + $i18n.locale] }}
          </a>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            v-if="$store.getters.checkPermission('as400_query_control')"
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            v-if="$store.getters.checkPermission('as400_query_control')"
            color="red"
            small
            text
            @click="deleteItem(item)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="12" sm="12">
                <label for>So'rov nomi</label>
                <v-text-field
                  v-model="form.query_name"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="12">
                <label for>So'rov</label>
                <v-textarea
                  v-model="form.query"
                  :rules="[(v) => !!v || $t('input_required')]"
                  auto-grow
                  outlined
                  rows="3"
                  row-height="25"
                ></v-textarea>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
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
  data() {
    return {
      loading: false,
      search: "",
      expanded: [],
      employees: [],
      employeeTabel: "",
      employeeInfo: null,
      dialog: false,
      editMode: null,
      items: [],
      addEmpBtn: [false],
      errorEmp: [false],
      form: {},
      link: "https://edo.uzautomotors.com",
      dialogHeaderText: "",
      createdAtMenu2: false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: "So'rov nomi", value: "query_name" },
        { text: "So'rov", value: "query" },
        { text: "So'rov qo'shuvchi", value: "full_name" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
        },
      ];
    },
    myEmployees() {
      return this.employees;
    },
  },
  methods: {
    getEmployee() {
      if (this.employeeTabel.length >= 4) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url +
              "api/as400query-findemployee/" +
              this.employeeTabel
          )
          .then((response) => {
            this.employeeInfo = response.data;
            this.loading = false;
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      }
    },
    deletePermission(item) {
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
                "api/as400query-deletePermission/" +
                item.id
            )
            .then((res) => {
              this.getList();
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
    addEmployee(item) {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url +
            `api/as400query-attachemployee/${item.id}/${this.employeeInfo.id}`
        )
        .then((response) => {
          if (response.data) item.as400_permissions.push(response.data);
          this.employeeTabel = null;
          this.employeeInfo = null;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
  
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/as400queries")
        .then((response) => {
          this.items = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = "Yangi so'rov qo'shish";
      this.form = {
        id: Date.now(),
        query_name: "",
        query: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = "So'rovni o'zgartirish";
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
            this.$store.state.backend_url + "api/as400queries/update",
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
              title: $t("create_update_operation"),
            });
          })
          .catch((err) => {
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
        confirmButtonText: this.$t("swal_delete"),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/as400queries/delete/" +
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
    if (this.$store.getters.checkPermission("as400_query_control")) {
      this.getList();
    }
  },
};
</script>
