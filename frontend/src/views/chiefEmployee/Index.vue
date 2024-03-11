<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("ChiefEmployee") }}</span>
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
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
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

        <template v-slot:item.actions="{ item }">
          <v-btn color="blue" small text @click="editItem(item)" v-if="false">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="red" small text @click="deleteItem(item)">
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
          <span class="headline">{{ $t("regions.dialogHeaderText") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col class="col-12">
                <v-autocomplete
                  v-model="form.helper_employee_id"
                  rounded
                  solo
                  hide-details
                  :label="$t('helper_employee')"
                  :items="hemployees"
                  @update:search-input="getEmployees($event, 'h')"
                  clearable
                  no-filter
                  item-value="id"
                >
                  <template slot="item" slot-scope="{ parent, item, tile }">
                    {{
                      item.tabel +
                      " " +
                      item["firstname_" + $i18n.locale] +
                      " " +
                      item["lastname_" + $i18n.locale] +
                      " " +
                      item["middlename_" + $i18n.locale]
                    }}
                    <br />
                    {{
                      item.main_staff &&
                      item.main_staff[0] &&
                      item.main_staff[0].department
                        ? item.main_staff[0].department.department_code +
                          " " +
                          item.main_staff[0].department["name_" + $i18n.locale]
                        : ""
                    }}
                    <br />
                    {{
                      item.main_staff.position
                        ? item.main_staff.position["name_" + $i18n.locale]
                        : ""
                    }}
                  </template>
                  <template
                    slot="selection"
                    slot-scope="{ parent, item, tile }"
                  >
                    {{
                      item.tabel +
                      " " +
                      item["firstname_" + $i18n.locale] +
                      " " +
                      item["lastname_" + $i18n.locale] +
                      " " +
                      item["middlename_" + $i18n.locale]
                    }}
                    <br />
                    {{
                      item.main_staff &&
                      item.main_staff[0] &&
                      item.main_staff[0].department
                        ? item.main_staff[0].department.department_code +
                          " " +
                          item.main_staff[0].department["name_" + $i18n.locale]
                        : ""
                    }}
                    <br />
                    {{
                      item.main_staff.position
                        ? item.main_staff.position["name_" + $i18n.locale]
                        : ""
                    }}
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col>
                <v-autocomplete
                  v-model="form.chief_employee_id"
                  rounded
                  solo
                  hide-details
                  :label="$t('chief_employee')"
                  :items="cemployees"
                  @update:search-input="getEmployees($event, 'c')"
                  clearable
                  no-filter
                  item-value="id"
                >
                  <template slot="item" slot-scope="{ parent, item, tile }">
                    {{
                      item.tabel +
                      " " +
                      item["firstname_" + $i18n.locale] +
                      " " +
                      item["lastname_" + $i18n.locale] +
                      " " +
                      item["middlename_" + $i18n.locale]
                    }}
                    <br />
                    {{
                      item.main_staff &&
                      item.main_staff[0] &&
                      item.main_staff[0].department
                        ? item.main_staff[0].department.department_code +
                          " " +
                          item.main_staff[0].department["name_" + $i18n.locale]
                        : ""
                    }}
                    <br />
                    {{
                      item.main_staff.position
                        ? item.main_staff.position["name_" + $i18n.locale]
                        : ""
                    }}
                  </template>
                  <template
                    slot="selection"
                    slot-scope="{ parent, item, tile }"
                  >
                    {{
                      item.tabel +
                      " " +
                      item["firstname_" + $i18n.locale] +
                      " " +
                      item["lastname_" + $i18n.locale] +
                      " " +
                      item["middlename_" + $i18n.locale]
                    }}
                    <br />
                    {{
                      item.main_staff &&
                      item.main_staff[0] &&
                      item.main_staff[0].department
                        ? item.main_staff[0].department.department_code +
                          " " +
                          item.main_staff[0].department["name_" + $i18n.locale]
                        : ""
                    }}
                    <br />
                    {{
                      item.main_staff.position
                        ? item.main_staff.position["name_" + $i18n.locale]
                        : ""
                    }}
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="loading" hide-overlay persistent width="300">
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
      dialog: false,
      editMode: null,
      items: [],
      cemployees: [],
      hemployees: [],
      country: [],
      form: {},
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
        { text: this.$t("helper_employee"), value: "hfullname" },
        { text: this.$t("chief_employee"), value: "cfullname" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
        },
      ];
    },
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/employees/get-chief-employee")
        .then((response) => {
          this.items = response.data.data.map((v) => {
            v.hfullname =
              v.helper_employee.firstname_ru +
              " " +
              v.helper_employee.lastname_ru;
            v.cfullname =
              v.chief_employee.firstname_ru +
              " " +
              v.chief_employee.lastname_ru;
            return v;
          });
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("regions.newDistrict");
      this.form = {
        id: Date.now(),
        helper_employee_id: "",
        chief_employee_id: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("regions.newRegion");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    getEmployees(search, type) {
      if (search && search.length > 0)
        axios
          .post(
            this.$store.state.backend_url + "api/employees/search-employee",
            {
              search: search,
            }
          )
          .then((res) => {
            if (type == "c") this.cemployees = res.data.data;
            else this.hemployees = res.data.data;
          })
          .catch((error) => {
            console.log(error);
          });
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/employees/update-chief",
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
                "api/employees/delete-chiefs/" +
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
  },
};
</script>
