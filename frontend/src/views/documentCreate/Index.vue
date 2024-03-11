<template>
  <div>
    <v-data-table
      dense
      fixed-header
      :loading-text="$t('loadingText')"
      :no-data-text="$t('noDataText')"
      :loading="loading"
      :headers="headers"
      :items="items"
      sort-by="calories"
      class="mainTable elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat color="white">
          <v-toolbar-title>{{ $t("documents.documents") }}</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog
            v-model="dialog"
            @keydown.esc="dialog = false"
            persistent
            max-width="800px"
          >
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-2" v-on="on">{{
                $t("department.create")
              }}</v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.document_type_id"
                        label="document type"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.document_number"
                        label="document number"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.document_created_at"
                        label="document created at"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.document_sending_department"
                        label="sending department"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.should_receive_all_departments"
                        label="sending all department"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.document_title"
                        label="document title"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.document_content"
                        label="document content"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.document_status"
                        label="document status"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">{{
                  $t("cancel")
                }}</v-btn>
                <v-btn color="blue darken-1" text @click="save">{{
                  $t("Save")
                }}</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
        <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
      </template>
      <template v-slot:no-data>
        <v-btn color="primary">Reset</v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>
const axios = require("axios").default;
export default {
  data: () => ({
    loading: false,
    dialog: false,
    items: [],
    editedIndex: -1,
    editedItem: {
      document_type_id: "",
      document_number: "",
      document_created_at: "",
      document_sending_department: "",
      should_receive_all_departments: "",
      document_title: "",
      document_content: "",
      document_status: "",
    },
    defaultItem: {
      name: "",
      calories: 0,
      fat: 0,
      carbs: 0,
      protein: 0,
    },
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    formTitle() {
      return this.editedIndex === -1
        ? this.$t("new_document")
        : this.$t("edit_document");
    },
    headers() {
      return [
        { text: "Hujjat turi", value: "document_type_id" },
        { text: "Hujjat raqami", value: "document_number" },
        { text: "Hujjat yareatilgan sana", value: "document_created_at" },
        { text: "Yuborayotgan bo'lim", value: "document_sending_department" },
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
  watch: {
    dialog(val) {
      val || this.close();
    },
  },

  created() {
    this.getlist();
  },

  methods: {
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/documents")
        .then((response) => {
          this.items = response.data.documents;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },

    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      const index = this.items.indexOf(item);
      confirm("Are you sure you want to delete this item?") &&
        axios
          .delete(
            this.$store.state.backend_url + "api/documents/delete/" + item.id
          )
          .then((res) => {
            this.getlist(this.page, this.itemsPerPage);
            this.dialog = false;
          })
          .catch((err) => {
            console.log(err);
          });
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      if (this.editedIndex > -1) {
        axios
          .post(this.$store.state.backend_url + "api/documents/update", {
            id: this.editedItem.id,
            document_type_id: this.editedItem.document_type_id,
            document_number: this.editedItem.document_number,
            document_created_at: this.editedItem.document_created_at,
            document_sending_department:
              this.editedItem.document_sending_department,
            should_receive_all_departments:
              this.editedItem.should_receive_all_departments,
            document_title: this.editedItem.document_title,
            document_content: this.editedItem.document_content,
            document_status: this.editedItem.document_status,
          })
          .then((response) => {
            this.getlist(this.page, this.itemsPerPage).catch((error) => {
              console.log(error);
            });
          });
      } else {
        axios
          .post(this.$store.state.backend_url + "api/documents/update", {
            document_type_id: this.editedItem.document_type_id,
            document_number: this.editedItem.document_number,
            document_created_at: this.editedItem.document_created_at,
            document_sending_department:
              this.editedItem.document_sending_department,
            should_receive_all_departments:
              this.editedItem.should_receive_all_departments,
            document_title: this.editedItem.document_title,
            document_content: this.editedItem.document_content,
            document_status: this.editedItem.document_status,
          })
          .then((response) => {
            this.getlist(this.page, this.itemsPerPage);
          })
          .catch((error) => {
            console.log(error);
          });
      }

      this.close();
    },
  },
};
</script>
