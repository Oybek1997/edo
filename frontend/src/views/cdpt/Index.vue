<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t('CDPT Hr') }}</span>
        <v-spacer></v-spacer>
        <v-btn
          v-if="$store.getters.checkPermission('cdpt_admin')"
          color="#6ac82d"
          x-small
          dark
          fab
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>
      <v-simple-table>
        <thead>
          <tr>
            <th class="text-left">Id</th>
            <th class="text-left">Name</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.name">
            <td>{{ item.id }}</td>
            <td>{{ item.name }}</td>
          </tr>
        </tbody>
      </v-simple-table>
    </v-card>
    <v-dialog v-model="dialog" @keydown.esc="dialog = false" persistent max-width="600">
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
              <v-col cols="12">
                <label for>{{ $t('device.branch_name') }}</label>
                <v-text-field
                  v-model="form.name"
                  :rules="[v => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
      filter: {
        name: ""
      },
      dialog: false,
      editMode: null,
      items: [
        { id: 1, name: "Test1" },
        { id: 2, name: "Test2" },
        { id: 3, name: "Test3" },
        { id: 4, name: "Test4" }
      ],
      form: {},
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    }
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },

    getList() {
      //   this.loading = true;
      //   axios
      //     .post(this.$store.state.backend_url + "api/orgtex/device-branches", {
      //       pagination: this.dataTableOptions,
      //       filter: this.filter
      //     })
      //     .then(response => {
      //       this.items = response.data.data;
      //       this.server_items_length = response.data.total;
      //       this.from = response.data.from;
      //       this.loading = false;
      //     })
      //     .catch(error => {
      //       console.log(error);
      //       this.loading = false;
      //     });
    },
    newItem() {
      // if (this.$store.getters.checkPermission("cdpt_admin")) {
      this.dialogHeaderText = this.$t("add");
      this.form = {
        id: Date.now(),
        name: ""
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      // }
    },
    editItem(item) {
      // if (this.$store.getters.checkPermission("cdpt_admin")) {
      this.dialogHeaderText = this.$t("edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      // }
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/orgtex/device-branches/update",
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
              title: this.$t("create_update_operation")
            });
          })
          .catch(err => {
            console.log(err);
          });
    },
    deleteItem(item) {
      // if (this.$store.getters.checkPermission("cdpt_admin")) {
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
              this.$store.state.backend_url +
                "api/orgtex/device-branches/delete/" +
                item.id
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
              });
              console.log(err);
            });
        }
      });
      // }
    }
  },
  mounted() {
    // this.getList();
  }
  //   created() {}
};
</script>
<style scoped>
</style>
