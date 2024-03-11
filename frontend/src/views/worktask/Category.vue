<template>
  <div>
    <v-card elevation="10" class="mt-3 ml-3">
      <v-card-title primary-title>
        Kategoriya
        <v-spacer></v-spacer>
        <!-- <v-btn @click="taskSave" color="success" outlined>
          <v-icon>mdi-floppy</v-icon>
          {{ $t("save") }}
        </v-btn> -->
      </v-card-title>
      <v-row class="mx-10 mb-10">
        <v-spacer></v-spacer>
        <v-btn color="success" @click="addCategoryDialog = true">
          <v-icon>mdi-playlist-plus</v-icon> Kategoriya qo'shish</v-btn
        >
      </v-row>
      <v-card flat>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="category"
            :options.sync="dataTableOptions"
            hide-default-footer
            class="mainTable elevation-1 pa-1"
          >
            <template v-slot:item.id="{ item }">{{
              category.map((v) => v.id).indexOf(item.id) + 1
            }}</template>
            <template v-slot:item.actions="{ item }">
              <v-btn color="blue" small text @click="editCategory(item)">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn color="red" small text @click="deleteCategory(item)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-card>
    <v-dialog
      v-model="addCategoryDialog"
      persistent
      max-width="50%"
      @keydown.esc="addCategoryDialog = false"
    >
      <v-card>
        <v-card-title primary-title>
          Yangi kategoriya
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            x-small
            outlined
            fab
            class
            @click="addCategoryDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <fieldset class="my-2" style="border: none">
          <v-form ref="addTaskForm">
            <v-col cols="12">
              <v-text-field
                v-model="categoryName"
                :label="$t('Nomi')"
                outlined
                dense
                hide-details="auto"
              ></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-card-actions class="pt-0">
              <v-spacer></v-spacer>
              <v-btn color="green" v-if="edited" dark @click="categoryEdit()">{{
                $t("Tahrirlash")
              }}</v-btn>
              <v-btn color="green" v-else dark @click="categorySave">{{
                $t("save")
              }}</v-btn>
              <!--                        <v-btn color="red darken-1" dark @click="onClickOutside">{{ $t('close') }}</v-btn>-->
            </v-card-actions>
          </v-form>
        </fieldset>
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
      addCategoryDialog: false,
      category: [],
      categoryName: "",
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      edited: false,
      headers: [
        { text: "№", value: "id", sortable: false },
        {
          text: "Nomi",
          align: "start",
          sortable: false,
          value: "name",
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 180,
          align: "center",
        },
      ],
    };
  },
  methods: {
    getCategory() {
      axios
        .get(this.$store.state.backend_url + "api/worktask-category")
        .then((res) => {
          this.category = res.data;
          console.log(this.category);
        })
        .catch((err) => {
          console.error(err);
        });
    },
    editCategory(item) {
      this.addCategoryDialog = true;
      this.categoryName = item.name;
      this.categoryId = item.id;
      this.edited = true;
    },
    categoryEdit() {
      axios
        .post(this.$store.state.backend_url + "api/worktask/category-edit", {
          id: this.categoryId,
          name: this.categoryName,
        })
        .then((res) => {
          this.addCategoryDialog = false;
          this.edited = false;
          this.$refs.addTaskForm.reset();
          this.getCategory();
        })
        .catch((err) => {
          console.error(err);
        });
    },
    categorySave() {
      axios
        .post(this.$store.state.backend_url + "api/worktask/category-add", {
          name: this.categoryName,
        })
        .then((res) => {
          this.addCategoryDialog = false;
          this.$refs.addTaskForm.reset();
          this.getCategory();
        })
        .catch((err) => {
          console.error(err);
        });
    },
    deleteCategory(item) {
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
                "api/worktask/category-delete/" +
                item.id
            )
            .then((res) => {
              this.showTaskDialog = false;
              this.getCategory();
              this.dialog = false;
              Swal.fire("O'chirildi!", this.$t("swal_deleted"), "success");
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
    this.getCategory();

    // this.getTask($id);
  },
};
</script>
<style scoped>
.card_title {
  padding: 0;
  margin: 10px 0;
  text-align: center;
  font-size: 32px;
  text-transform: uppercase;
  white-space: nowrap;
}
th {
  background-color: #2196f3;
  color: white;
}
.theme--light.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  color: white;
}
.mainTable table > thead.v-data-table-header > tr > th {
  background-color: #2196f3 !important;
  font-weight: bold !important;
  color: rgb(255, 255, 255) !important;
  margin: 10px 10px 0 !important;
}

.mainTable table > tbody > tr > td,
.mainTable table > thead > tr > th {
  border: 1px solid #d3d3ff;
  white-space: normal;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mainTable table > tbody > tr > td p,
.mainTable table > thead > tr > th {
  white-space: normal;
  padding: 5px;
}
</style>
