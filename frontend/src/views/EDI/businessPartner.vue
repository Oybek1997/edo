<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("edi.business_partner_header") }}</span>
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
        :search="search"
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right',
        }"
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
      >
        <template v-slot:item.id="{ item }">{{
          items.indexOf(item) + from
        }}</template>
         <template v-slot:item.created_by="{ item }">{{
          item.created_by && item.created_by.employee ? item.created_by.employee.fio : ''
        }}</template>
         <template v-slot:item.updated_by="{ item }">{{
          item.updated_by && item.updated_by.employee ? item.updated_by.employee.fio : ''
        }}</template>
        <template v-slot:item.description="{ item }">
          <span style="white-space: normal; max-width: 100px">{{
            item.description
          }}</span>
        </template>
        <template v-slot:item.status="{ item }">
          <v-switch v-model="item.status" class="ma-0" hide-details readonly></v-switch>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            v-if="$store.getters.checkPermission('coefficient-update')"
            color="blue"
            small
            text
            @click="editItem(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            v-if="$store.getters.checkPermission('coefficient-delete')"
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
      max-width="600px"
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
            <table style="width: 100%">
              <tr>
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.business_partner_name") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.name"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.business_partner_type") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.business_partner_type_id"
                    :items="partnerTypes"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.business_partner_address") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.address"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="pa-1 text-right">
                  <label for>{{
                    $t("edi.business_partner_country_code")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.country_code"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>

              <tr>
                <td class="pa-1 text-right">
                  <label for>{{
                    $t("edi.business_partner_duns_number")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.duns_number"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>

              <tr>
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.business_partner_status") }}</label>
                </td>
                <td class="pa-1">
                  <v-switch
                    class="mt-0"
                    v-model="form.status"
                    :label="!!form.status ? 'Active' : 'Inactive'"
                    hide-details
                    dense
                  ></v-switch>
                </td>
              </tr>
              </tr>
            </table>
          </v-form>
          <!-- <small color="red">{{ $t("input_required") }}</small> -->
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
      form: {},
      dialogHeaderText: "",
      createdAtMenu2: false,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      partnerTypes: [],
      selectedDropDownValue: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("edi.business_partner_name"),
          value: "name",
        },
        {
          text: this.$t("edi.business_partner_type"),
          value: "business_partner_type.name",
        },
        {
          text: this.$t("edi.business_partner_address"),
          value: "address",
        },
        {
          text: this.$t("edi.business_partner_status"),
          value: "status",
        },
        {
          text: this.$t("edi.business_partner_country_code"),
          value: "country_code",
        },
        {
          text: this.$t("edi.business_partner_duns_number"),
          value: "duns_number",
        },
        {
          text: this.$t("edi.created_at"),
          value: "created_at",
        },
        {
          text: this.$t("edi.created_by"),
          value: "created_by",
        },
        {
          text: this.$t("edi.updated_at"),
          value: "updated_at",
        },
        {
          text: this.$t("edi.updated_by"),
          value: "updated_by",
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 120,
          align: "center",
        },
      ];
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/edi/business-partners", {
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          // console.log(response);
          this.items = response.data.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getRef() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/edi/business-partners/get-ref"
        )
        .then((response) => {
          this.partnerTypes = response.data.businessPartnerTypes;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      if (this.$store.getters.checkPermission("coefficient-create")) {
        this.dialogHeaderText = this.$t("edi.business_partner_create");
        this.form = {
          id: Date.now(),
          name: null,
          business_partner_type_id: null,
          status: null,
          address: null,
          country_code: null,
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      // if (this.$store.getters.checkPermission("coefficient-update")) {
      this.dialogHeaderText = this.$t("edi.business_partner_edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      //}
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/edi/business-partners/update",
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
    }, //document-types
    deleteItem(item) {
      if (this.$store.getters.checkPermission("personal_type-delete")) {
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
                  "api/edi/business-partners/delete/" +
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
      }
    },
  },
  mounted() {
    this.getRef();
    this.getList();
  },
  created() {},
};
</script>
