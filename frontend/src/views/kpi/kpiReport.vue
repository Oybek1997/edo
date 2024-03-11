<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>Kpi Hisobot</span>
        <v-spacer></v-spacer>

        <span style="max-width: 130px">
          <v-autocomplete label="Chorak" v-model="filter.quarter" :items="quarter" hide-details dense outlined
            @change="getList" item-value="value" item-text="text" class="mx-1"></v-autocomplete>
        </span>
        <v-btn class="mx-3" @click="uploadExcel('table', 'kpi_report.xlsx')">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn>
        <v-text-field v-model="search" append-icon="mdi-magnify" class="mr-2" style="width: 100px !important"
          :placeholder="$t('search')" @keyup.native.enter="getList" outlined dense single-line
          hide-details></v-text-field>
      </v-card-title>

      <v-data-table id="table" dense fixed-header :height="screenHeight" :loading="loading" :headers="headers"
        :items="otchets" :search="search" :items-per-page="-1" class="mainTable ma-1" style="border: 1px solid #aaa"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100, -1],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right',
        }">
        <template v-slot:item.id="{ item, index }">{{ index + 1 }}</template>
        <template v-slot:item.document_number="{ item }">
          <td style="width: 10%">{{ item.document_number }}</td>
        </template>
        <template v-slot:item.attribute_value="{ item }">
          <td style="width: 40%">{{ item.attribute_value }}</td>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
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
      filter: {
        quarter: 1
      },
      quarter: [
        { text: this.$t("1-chorak"), value: 1 },
        { text: this.$t("2-chorak"), value: 2 },
        { text: this.$t("3-chorak"), value: 3 },
        { text: this.$t("4-chorak"), value: 4 },
      ],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30, },
        { text: "Bo'lim kodi", value: "department_code", },
        { text: "Bo'lim nomi", value: "name_uz_latin", },
        { text: "Hujjat raqami", value: "document_number", },
        { text: "Ko`rsatkich", value: "attribute_value", },
        { text: "Maqsadga erishish", value: "achieving_goal", },
        { text: "Mukofot miqdori", value: "mukofot", },
        { text: "Tasdiqlangan", value: "tasdiq", },
        { text: "Rad etilgan", value: "otkaz", },
        { text: "Imzolanmagan", value: "not_signed", },
      ];
    },
  },
  methods: {
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"), { name: name });
    },
    getList() {
      this.loading = true;
      axios
        // .get(this.$store.state.backend_url + "api/kpi/otchet")
        // .post("https://b-edo.uzautomotors.com/api/kpi/otchet", {
        //   search: this.search,
        //   filter: this.filter,
        // })
        .post("https://b-edo.uzautomotors.com/api/kpi/otchet", {
          filter: this.filter
        })
        .then((response) => {
          this.otchets = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getquarter() {
      axios
        .get(this.$store.state.backend_url + "api/kpi/quarter")
        .then((response) => {
          this.filter.quarter = response.data[0];
          this.filter.year = response.data[1];
          this.getList();
          // console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
  mounted() {
    // this.getList();
    this.getquarter();
  },
};
</script>
