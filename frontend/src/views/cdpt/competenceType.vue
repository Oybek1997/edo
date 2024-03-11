<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t('cdpt.type_competencies') }}</span>
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
        style="border: 1px solid #aaa;"
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100],
          itemsPerPageAllText:$t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right'
        }"
        @update:sort-desc="updatePage"
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
      >
        <template v-slot:body.prepend="{ item }">
          <tr class="prepend_height">
            <td></td>
            <td>
              <v-text-field v-model="filter.name" hide-details dense @keyup.enter="getList"></v-text-field>
            </td>
            <td>
              <v-autocomplete
                clearable
                v-model="filter.type"
                :items="
                  types.map(v => ({
                    text: v.text,
                    value: v.value,
                  }))
                "
                hide-details
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td></td>
          </tr>
        </template>
        <template
          v-slot:item.id="{ item }"
        >{{items.map(function(x) {return x.id; }).indexOf(item.id) + from}}</template>
        <template v-slot:item.type="{ item }">
          <template v-if="item.type == 1">Kompetensiya turlari</template>
          <template v-else-if="item.type == 2">Funktsional bilimlar</template>
          <template v-else>Yetakchilik / shaxsiy kompetensiyalar</template>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn color="blue" small text @click="editItem(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
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
                <label for>{{ $t('cdpt.name_specific') }}</label>
                <v-text-field
                  v-model="form.name"
                  :rules="[v => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                  :label="$t('cdpt.type')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.type"
                  :items="types"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details
                  dense
                ></v-autocomplete>
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
      deviceBranches: [],
      filter: {
        name: "",
        type: ""
      },
      dialog: false,
      editMode: null,
      items: [],
      form: {},
      dialogHeaderText: "",
      types: [
        {
          value: 1,
          text: "Kompetensiya turlari"
        },
        {
          value: 2,
          text: "Funktsional bilimlar"
        },
        {
          value: 3,
          text: "Yetakchilik / shaxsiy kompetensiyalar"
        }
      ],
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },

        { text: this.$t("cdpt.name_specific"), value: "name" },
        { text: this.$t("cdpt.type"), value: "type" },

        {
          text: this.$t("actions"),
          value: "actions",
          width: 130,
          align: "center"
        }
      ];
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
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/cdpt/competence-types", {
          pagination: this.dataTableOptions,
          filter: this.filter
        })
        .then(response => {
          this.items = response.data.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      // if (this.$store.getters.checkPermission("cdpt_admin")) {
      this.dialogHeaderText = this.$t("cdpt.add_competencie");
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
            this.$store.state.backend_url + "api/cdpt/competence-types/update",
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
                "api/cdpt/competence-types/delete/" +
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
    this.getList();
  },
  created() {}
};
</script>
<style scoped>
</style>
