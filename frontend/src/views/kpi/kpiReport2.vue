<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>Kpi Hisobot 2</span>
        <v-spacer></v-spacer>
        <v-btn class="mx-3" @click="uploadExcel('table', 'kpi_report.xlsx')">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn>
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
      </v-card-title>

      <v-data-table
      id="table"
        dense
        fixed-header
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="otchets"
        :search="search"
        :items-per-page="-1"
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100, -1],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right',
        }"
      >
        <template v-slot:item.id="{ item, index }">{{ index + 1 }}</template>
        <template v-slot:item.name_uz_latin="{ item }"><td style="width:40%;">{{ item.name_uz_latin }}</td></template>
        <template v-slot:item.mukofot="{ item }"><td style="text-align:right;">{{ Math.round(item.mukofot * 100) / 100 }}</td></template>
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
import TableToExcel from "@linways/table-to-excel";
const axios = require("axios").default;
export default {
  data() {
    return {
      loading: false,
      search: "",
      otchets: [],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30,  },
        { text: "Bo'lim kodi", value: "department_code", width: 150 },
        { text: "Bo'lim nomi", value: "name_uz_latin",  },
        { text: "Hujjat raqami", value: "document_number",  },
        { text: "Mukofot miqdori", value: "mukofot",  },
        // { text: "Tasdiqlangan", value: "tasdiq",  },
        // { text: "Rad etilgan", value: "otkaz",  },
        // { text: "Imzolanmagan", value: "not_signed",  },
      ];
    },
  },
  methods: {
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"), {name: name});
    },
    getList() {
      this.loading = true;
      axios
        // .get(this.$store.state.backend_url + "api/kpi/otchet")
        // .post("https://b-edo.uzautomotors.com/api/kpi/otchet", {
        //   search: this.search,
        //   filter: this.filter,
        // })
        .get(this.$store.state.backend_url + "api/kpi/otchet2")
        .then((response) => {
          this.otchets = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
