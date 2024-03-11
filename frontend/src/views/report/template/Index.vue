<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("message.reports") }}</span>
        <v-spacer></v-spacer>

        <v-btn
          v-if="$store.getters.checkPermission('report_template-create')"
          color="#6ac82d"
          x-small
          dark
          fab
          to="template/create"
        >
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
        :footer-props="{
          itemsPerPageOptions: [50, 100, 200],
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
          items
            .map(function (x) {
              return x.id;
            })
            .indexOf(item.id) + 1
        }}</template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            v-if="$store.getters.checkPermission('report_'+item.permission) || $store.getters.checkPermission('report_template-update')"
            color="success"
            small
            icon
            :to="'unv-report/' + item.id"
          >
            <v-icon>mdi-eye</v-icon>
          </v-btn>
          <v-btn
            v-if="$store.getters.checkPermission('report_template-update')"
            color="blue"
            small
            icon
            :to="'template/update/' + item.id"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            v-if="$store.getters.checkPermission('report_template-delete')"
            color="red"
            small
            icon
            @click="deleteItem(item)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

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
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      page: 1,
      from: 0,
      server_items_length: -1,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 50, sortable: false },
        { text: this.$t("name"), value: "name_" + this.$i18n.locale },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 100,
          align: "center",
          sortable: false,
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
        .post(this.$store.state.backend_url + "api/report-template/list")
        .then((response) => {
          this.items = response.data.filter(v=>{
            if(this.$store.getters.checkPermission("report_template-list") || this.$store.getters.checkPermission('report_'+v.permission)){
              return v;
            }
          });
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    deleteItem(item) {
      if (this.$store.getters.checkPermission("report_template-delete")) {
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
                  "api/report-template/delete/" +
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
                });
                console.log(err);
              });
          }
        });
      }
    },
  },
  mounted() {
    this.getList();
  },
  created() {},
};
</script>