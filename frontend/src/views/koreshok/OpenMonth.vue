<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("message.koreshok") }}</span>
        <v-spacer></v-spacer>
        <v-btn
          v-if="$store.getters.checkPermission('organizations-create')"
          color="#6ac82d"
          x-small
          dark
          fab
          @click="save"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-simple-table style="width: 100%">
          <template v-slot:default>
            <tbody>
              <tr
                v-for="(item, index) in items"
                :key="index"
                style="padding: 10px !important"
              >
                <td>
                  <v-btn text block color="green"
                    >{{ item[0].yiloy.substr(0, 4) }}
                  </v-btn>
                </td>
                <td v-for="(itm, idx) in item" :key="idx">
                  <v-btn
                    block
                    :color="itm.sts == 1 ? '' : 'error'"
                    text
                    @click="changeStatus(itm)"
                  >
                    {{ itm.month_name }}
                  </v-btn>
                </td>
                <td
                  v-for="(n,k) in item && item.length < 12 ? 12 - item.length : []"
                  :key = "k"
                ></td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
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
const moment = require("moment");
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
      page: 1,
      from: 0,
      add: 1,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    orderedItems: function () {
      return this.items.orderBy(this.items, index);
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("month"), value: "yiloy" },
        { text: this.$t("status"), value: "sts" },
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
        .post(this.$store.state.backend_url + "api/koreshoks")
        .then((response) => {
          this.items = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    changeStatus(item) {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/koreshoks/update-status",
          item
        )
        .then(() => {
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
          // Toast.fire({
          //   icon: "success",
          //   title: this.$t("create_update_operation"),
          // });
        })
        .catch((err) => {
          console.log(err);
          this.dialog = false;
        });
    },
    save() {
      axios
        .post(this.$store.state.backend_url + "api/koreshoks", this.add)
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
  },
  mounted() {
    this.getList();
  },
};
</script>
