<template>
     <div class="fullHeight">
      <v-card class="pa-0 heightFull" elevation="0">
      <v-row>
        <v-col cols="12" sm="6">
          
          <v-card-title class="pa-1">
            <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn v-bind="attrs" v-on="on" icon @click="goBack">
              <v-icon large color="grey">mdi-keyboard-backspace</v-icon>
            </v-btn>
          </template>
          <span>{{ $t("Oldingi saxifaga qaytish") }}</span>
        </v-tooltip>
            <span>{{ $t("mailing") }}</span>
            <v-spacer></v-spacer>
            <v-btn color="#6ac82d" x-small dark  @click="newItem">
              {{'create'}}
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
           <template v-slot:item.id="{ item }">
              {{                
                item.employee.id                    
              }}
            </template>
            <template v-slot:item.fio="{ item }">
              {{
                $i18n.locale == "uz_latin"
                  ? item.employee.lastname_uz_latin +
                    " " +
                    item.employee.firstname_uz_latin.substr(0, 1) +
                    "." +
                    item.employee.middlename_uz_latin.substr(0, 1) +
                    "."
                  : item.employee.lastname_uz_cyril +
                    " " +
                    item.employee.firstname_uz_cyril.substr(0, 1) +
                    "." +
                    item.employee.middlename_uz_cyril.substr(0, 1) +
                    "."
              }}
            </template>
            <!-- <template v-slot:item.id="{ item }">{{
              items
                .map(function (x) {
                  return x.id;
                })
                .indexOf(item.id) + 1
            }}</template> -->

            <template v-slot:item.actions="{ item }">
              <!-- <v-btn color="blue" small text @click="editItem(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn> -->
              <v-btn color="red" small text @click="deleteItem(item)">
                <v-icon>mdi-playlist-remove</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
        <v-col cols="12" sm="6">
          <v-card-title class="pa-1">
            <span>{{ $t("templates") }}</span>
            <v-spacer></v-spacer>
           <v-btn color="#6ac82d" x-small dark  @click="newItem_temp">
              {{'create'}}
            </v-btn>
          </v-card-title>
          <v-data-table
            dense
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers_temp"
            :items="items_template"
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
            <template v-slot:item.id="{ item }">
              {{                
                item.document_template.id                    
              }}
            </template>
            <template v-slot:item.template="{ item }">
              {{                
                item.document_template['name_'+ $i18n.locale]                    
              }}
            </template>
            <template v-slot:item.actions_temp="{ item }">
              <v-btn color="red" small text @click="deleteItemTemplate(item)">
                <v-icon>mdi-playlist-remove</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800"
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
              <v-col cols="12" sm="3">
                <label for>{{ $t("tabel") }}</label>
                <v-text-field
                  v-model="form.tabel"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  @change="employe_fio()"
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" sm="9">
                <label for>{{ $t("fio") }}</label>
                <v-text-field
                  v-model="form.fio"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  disabled
                  dense
                  outlined
                ></v-text-field>
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
     <v-dialog
      v-model="dialog_temp"
      @keydown.esc="dialog_temp = false"
      persistent
      max-width="800"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("regions.dialogHeaderText") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog_temp = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save_temp" ref="dialogForm_temp">
            <v-row>
              <!-- <v-col cols="12" sm="6">
                <label for>{{ $t("tabel") }}</label>
                <v-text-field
                  v-model="form.template"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>    -->
              <v-col cols="6">
                <v-autocomplete
                  :label="$t('template')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form_template.template_id"
                  :items="templates"
                  item-value="id"
                  :item-text="'name_'+$i18n.locale"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>          
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save_temp">{{ $t("save") }}</v-btn>
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
      dialog: false,
      dialog_temp: false,
      items: [],
      items_template: [],
      employee: [],
      templates: [],
      form: {},
      form_template: {},
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
        { text: this.$t("tabel"), value: "employee.tabel" },
        { text: this.$t("fio"), value: "fio" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
        },
      ];
    },
    headers_temp() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("template"), value: "template" },
        // { text: this.$t("fio"), value: "fio" },
        {
          text: this.$t("actions"),
          value: "actions_temp",
          width: 50,
          align: "center",
        },
      ];
    },
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    },
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/mailing")
        .then((response) => {
          this.items = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getListTemp() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/selected-templates")
        .then((resp) => {
          this.items_template = resp.data;
           console.log(resp);
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getListAllTemp() {
      this.loading = true;
      // let locale = this.$i18n.locale;
      axios
        .get(this.$store.state.backend_url + "api/all-templates")
        .then((resp) => {
          this.templates = resp.data;
          // .map(function (x) {
          //   // x.text = x['name_' + locale];
          // return x;
          // })
           console.log(this.$i18n.locale);
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    employe_fio() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/employees/get-employee", {
          tabel: this.form.tabel,
          language: this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale,
        })
        .then((res) => {
          console.log(res.data);
          this.employee = res.data;
          this.loading = false;
          this.form.employee_id = this.employee.employee[0].id;
          this.form.fio =
            this.employee.employee[0].firstname +
            " " +
            this.employee.employee[0].lastname +
            " " +
            this.employee.employee[0].middlename;
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
        tabel: "",
        fio: "",
        employee_id: "",
      };
      this.dialog = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    newItem_temp() {
      this.dialogHeaderText = this.$t("regions.newDistrict");
      this.form_template = {
        id: Date.now(),
        template: "",
        template_id: "",
        // fio: "",
        // employee_id: "",
      };
      this.dialog_temp = true;
      // if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(this.$store.state.backend_url + "api/mailing/update", this.form)
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
    save_temp() {
      if (this.$refs.dialogForm_temp.validate())
        axios
          .post(this.$store.state.backend_url + "api/selected-templates/update", this.form_template)
          .then((res) => {
            this.getListTemp();
            this.dialog_temp = false;
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
    deleteItemTemplate(item) {
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
              this.$store.state.backend_url + "api/selected-templates/delete/" + item.id
            )
            .then((res) => {
              this.getListTemp(this.page, this.itemsPerPage);
              this.dialog_temp = false;
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
              this.$store.state.backend_url + "api/mailing/delete/" + item.id
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
    this.getListTemp();
    this.getListAllTemp();
  },
};
</script>
<style scoped>
.fullHeight {
  height: calc(100% - 10px);
}
.heightFull {
  border-radius: 10px 10px 10px 10px;
}
.hover :hover {
  font-size: 20px;
  color: #0a73bb;
}
.hover_color :hover {
  color: rgb(0, 0, 0);
}
</style>
