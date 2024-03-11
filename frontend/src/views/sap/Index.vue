<template>
  <div>
    <div
      style="text-align: center; margin-top: 100px; font-size: 20pt; color: red"
    >
    Ushbu sahifa hali tayyor emas
    </div>
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
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("ranges.code"), value: "code" },
        { text: this.$t("ranges.name"), value: "name" },
        { text: this.$t("ranges.minfond"), value: "minfond" },
        { text: this.$t("ranges.maxfond"), value: "maxfond" },
        { text: this.$t("ranges.order_date"), value: "order_date" },
        { text: this.$t("ranges.order_number"), value: "order_number" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
        },
      ].filter(
        (v) =>
          (v.value != "actions" ||
            this.$store.getters.checkPermission("range-update") ||
            this.$store.getters.checkPermission("range-delete")) &&
          (!(v.value == "minfond" || v.value == "maxfond") ||
            this.$store.getters.checkPermission("range-index_details"))
      );
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
        .post(this.$store.state.backend_url + "api/sap/get-nolekvid-report")
        .then((response) => {
          this.items = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
  },
  mounted() {
    // this.getList();
  },
  created() {},
};
</script>
