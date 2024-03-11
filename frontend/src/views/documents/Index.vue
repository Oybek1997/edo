<template>
  <div>
    <v-row class="mx-2 mt-1">
      <span>{{ $t("user.users") }}</span>
      <v-spacer></v-spacer>
      <v-btn color="#6ac82d" x-small dark fab @click="newItem">
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </v-row>
    <v-row>
      <v-col>
        <v-card class="mx-2">
          <v-data-table
            dense
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :loading="loading"
            :headers="headers"
            :items="items"
            class="mainTable"
          >
            <template v-slot:item.id="{ item }">{{
              items
                .map(function (x) {
                  return x.id;
                })
                .indexOf(item.id) + 1
            }}</template>

            <template v-slot:item.actions="{ item }">
              <v-btn color="blue" small text @click="editItem(item)">
                <v-icon>edit</v-icon>
              </v-btn>
              <v-btn color="red" small text @click="deleteItem(item)">
                <v-icon>delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card>
        <v-card-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <!--     <v-row>
              <v-col cols="6">
                <label for>{{ $t('user.name') }}</label>
                <v-text-field
                  v-model="form.name"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t('user.email') }}</label>
                <v-text-field
                  v-model="form.email"
                  :rules="[
                    v => !!v || 'E-mail is required',
                    v => /.+@.+/.test(v) || $t('input.email'),
                  ]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t('user.phone') }}</label>
                <v-text-field
                  v-model="form.phone"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t('user.department_id') }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.department_id"
                  :items="departments":rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="6">
                <label for>{{ $t('user.position') }}</label>
                <v-text-field
                  v-model="form.position"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label for>{{ $t('user.username') }}</label>
                <v-text-field
                  v-model="form.username"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>-->
          </v-form>
          <small color="red">*indicates required field</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" @click="save">Save</v-btn>
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
    loading: false,
    search: "",
    dialog: false,
    editMode: null,
    items: [],
    departments: [],
    form: {},
    dialogHeaderText: "",
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "Hujjat turi", value: "document_type_id" },
        { text: "Hujjat raqami", value: "document_number" },
        { text: "Hujjat yaratilgan sana", value: "document_created_at" },
        { text: "Yuborayotgan bo`lim", value: "document_sending_department" },
        {
          text: "Barcha bo'limlarga jonatish",
          value: "should_receive_all_departments",
        },
        { text: "Hujjat nomi", value: "document_title" },
        { text: "Hujjat tasnifi", value: "document_content" },
        { text: "Hujjat statusi", value: "document_status" },
        { text: "Actions", value: "actions", sortable: false },
      ];
    },
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/documents")
        .then((response) => {
          this.items = response.data.documents;
          this.departments = response.data.departments.map((v) => {
            return { text: v.department_name, value: v.id };
          });
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },

    newItem() {
      this.dialogHeaderText = this.$t("user.newUser");
      /* this.form = {
          id: Date.now(),
          name: "",
          email: "",
          phone: "",
          department_id: "",
          position: "",
          username: "",
          password: ""
        };*/
      this.dialog = true;
      this.editMode = false;
      this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("user.updateUser");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.form.roleIds = item.roles.map((v) => v.id);
      this.dialog = true;
      this.editMode = true;
      this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .put(this.$store.state.backend_url + "api/users/update", this.form)
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
              title: "Update successfully",
            });
          })
          .catch((err) => {
            console.log(err);
          });
    },
    deleteItem(item) {
      const index = this.items.indexOf(item);
      confirm("Are you sure you want to delete this item?") &&
        axios
          .delete(this.$store.state.backend_url + "api/users/delete/" + item.id)
          .then((res) => {
            this.getList(this.page, this.itemsPerPage);
            this.dialog = false;
          })
          .catch((err) => {
            console.log(err);
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
