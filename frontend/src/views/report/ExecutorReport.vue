<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>Executor Report</span>
        <v-spacer></v-spacer>

        <v-col cols="12" sm="6" md="2">
          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                dense
                v-model="date"
                label="Sana boshi"
                outlined
                hide-details
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker v-model="date" @input="menu = false"></v-date-picker>
          </v-menu>
        </v-col>
        <v-col cols="12" sm="6" md="2">
          <v-menu
            v-model="menu2"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                dense
                v-model="date2"
                label="Sana oxiri"
                outlined
                hide-details
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="date2"
              @input="menu2 = false"
            ></v-date-picker>
          </v-menu>
        </v-col>
        <v-btn depressed dense color="success" @click="getList()">
          Izlash
        </v-btn>
      </v-card-title>
      <v-data-table
        dense
        fixed-header
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :items="items"
        
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
      >
        <template v-slot:item.id="{ item }">{{
          items.indexOf(item) + 1
        }}</template>

        <!-- <template #item.document_template_id="{ item }">
          <td style="width: 7%">{{ item.document_template_id }}</td>
        </template>
        <template #item.act_name="{ item }">
          {{ item.document_template.name_uz_latin }}
        </template>
        <template #item.all="{ item }">
          {{ item.approve + item.cancel + item.process }}
        </template>
        <template #item.approve="{ item }">
          {{ item.approve ? item.approve : 0 }}
        </template>
        <template #item.cancel="{ item }">
          {{ item.cancel ? item.cancel : 0 }}
        </template>
        <template #item.process="{ item }">
          {{ item.process ? item.process : 0 }}
        </template> -->
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
      items: [],
      date: null,
      menu: false,
      menu2: false,
      date2: null,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("profile.tabel"), value: "tabel" },
        { text: this.$t("document.fio"), value: "fio" },
        { text: this.$t("document.document_type_id"), value: "name_uz_latin" },
        { text: this.$t("date"), value: "month" },
        { text: this.$t("employee.all"), value: "cnt" },
        { text: this.$t("document.ex_agreement"), value: "solg" },
        { text: this.$t("document.ex_approval"), value: "utv" },
        { text: this.$t("document.ex_execute"), value: "isp", color: "error" },
        { text: this.$t("document.ex_creator"), value: "soz" },
        { text: this.$t("document.ex_control"), value: "kon" },
        { text: this.$t("document.ex_editor"), value: "red" },
        { text: this.$t("document.ex_broad"), value: "ras" },
        { text: this.$t("document.ex_execute_main"), value: "isp2" },
      ];
    },
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .post("https://b-edo.uzautomotors.com/api/executor-report", {
          startdate: this.date,
          enddate: this.date2,
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
  },
  mounted() {
    // this.getList();
  },
};
</script>
