<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("Moddiy javobgarlar") }}</span>
        <v-spacer></v-spacer>

        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          class="mr-2"
          style="width: 100px !important"
          :placeholder="$t('search')"
          @keyup.native.enter="getList"
          outlined
          dense
          single-line
          hide-details
        ></v-text-field>

        <v-btn
          v-if="$store.getters.checkPermission('sap-menu')"
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
        hide-default-footer
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        :options.sync="dataTableOptions"
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
        single-expand
        item-key="id"
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
            items
              .map(function (x) {
                return x.id;
              })
              .indexOf(item.id) + 1
          }}
        </template>
        <template v-slot:item.warehouse="{ item }">
          {{
            item.warehouse[0].code +
            " " +
            item.warehouse[0].name +
            " (" +
            item.warehouse[0].workplace.code+ ")"
          }}
        </template>
        <template v-slot:item.employee="{ item }">
          {{
            item.employee_responsible.tabel +
            " " +
            item.employee_responsible["firstname_" + $i18n.locale] +
            " " +
            item.employee_responsible["lastname_" + $i18n.locale] +
            " " +
            item.employee_responsible["middlename_" + $i18n.locale]
          }}
        </template>
        <template v-slot:item.accountant="{ item }">
          {{
            item.accountant_responsible.tabel +
            " " +
            item.accountant_responsible["firstname_" + $i18n.locale] +
            " " +
            item.accountant_responsible["lastname_" + $i18n.locale] +
            " " +
            item.accountant_responsible["middlename_" + $i18n.locale]
          }}
        </template>
        <template v-slot:item.actions="{ item }">
          <!-- <v-btn
            v-if="$store.getters.checkPermission('sap-menu')"
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn> -->
          <v-btn
            v-if="$store.getters.checkPermission('sap-menu')"
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
          <v-form @keyup.native.enter="save">
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("Ombor") }}</label>
                <v-autocomplete
                  class="mr-2"
                  clearable
                  v-model="form.warehouse"
                  :items="warehouses"
                  hide-details="auto"
                  dense
                  item-value="id"
                  item-text="text"
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("Moddiy javobgar") }}</label>
                <select-employee
                   v-model="form.responsible_employee_id"
               ></select-employee>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t("Biriktirilgan hisobchi") }}</label>
                <select-employee
                   v-model="form.responsible_accountant_id"
               ></select-employee>
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
      dialog: false,
      editMode: null,
      items: [],
      warehouses: [],
      form: {},
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: -1 },
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("Ombor"),
          value: "warehouse",
          width: 250,
        },
        {
          text: this.$t("Moddiy javobgar"),
          value: "employee",
          width: 250,
        },
        {
          text: this.$t("Biriktirilgan hisobchi"),
          value: "accountant",
          width: 250,
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
        },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("hr-language-update") ||
          this.$store.getters.checkPermission("hr-language-delete")
      );
    },
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/sap/warehouse-responsible",{
          search: this.search
        })
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
      this.dialog = true;
      if (this.$store.getters.checkPermission("hr-language-create")) {
        this.dialogHeaderText = this.$t("Moddiy javobgar qo'shish");
        this.form = {
          id: Date.now(),
          warehouse: "",
          responsible_employee_id: "",
          responsible_accountant_id: "",
        };

        this.editMode = false;
      }
    },
    // editItem(item) {
    //   if (this.$store.getters.checkPermission("hr-language-update")) {
    //     this.dialogHeaderText = this.$t("edit");
    //     this.form = Object.assign({}, item);
    //     this.dialog = true;
    //     this.editMode = true;
    //   }
    // },
    save() {
      if(this.form.warehouse&&this.form.responsible_employee_id&&this.form.responsible_accountant_id){

        axios
          .post(
            this.$store.state.backend_url + "api/sap/warehouse-responsible/update",
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
      }
      else{
        alert('Barcha maydonlarni toldiring');
      }
    }, //document-types
    deleteItem(item) {
      if (this.$store.getters.checkPermission("hr-language-delete")) {
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
                  "api/sap/warehouse-responsible/delete/" +
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
      }
    },
    getWarehouses() {
        axios
          .post(this.$store.state.backend_url + "api/warehouse-finder/getWarehouses")
          .then((response) => {
            this.warehouses = response.data;
            this.warehouses = this.warehouses.map(v => ({
            code: v.code,
            id: v.id,
            text:
              v.code +
              " " +
              v.name +
              " (" +
              v.workplace.code + ")"
          }));
            
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      },
  },
  mounted() {
    this.getList();
    this.getWarehouses();
  },
  created() {},
};
</script>
