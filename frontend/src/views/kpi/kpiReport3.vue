<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>Kpi Hisobot</span>
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
        <template v-slot:item.33="{ item, index }"><td :style="item['a33'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a33"] }}</td></template>
        <template v-slot:item.46="{ item, index }"><td :style="item['a46'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a46"] }}</td></template>
        <template v-slot:item.916="{ item, index }"><td :style="item['a916'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a916"] }}</td></template>
        <!-- <template v-slot:item.88="{ item, index }"><td :style="item['88'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["88"] }}</td></template> -->
        <!-- <template v-if="false" v-slot:item.88="{ item, index }"><td :style="item['88'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["88"] }}</td></template> -->
        <template v-slot:item.94="{ item, index }"><td :style="item['a94'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a94"] }}</td></template>
        <template v-slot:item.100="{ item, index }"><td :style="item['a100'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a100"] }}</td></template>
        <template v-slot:item.3347="{ item, index }"><td :style="item['a3347'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a3347"] }}</td></template>
        <template v-slot:item.10786="{ item, index }"><td :style="item['a10786'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a10786"] }}</td></template>
        <!-- <template v-slot:item.24439="{ item, index }"><td :style="item['24439'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["24439"] }}</td></template> -->
        <template v-slot:item.68="{ item, index }"><td :style="item['a68'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a68"] }}</td></template>
        <template v-slot:item.12272="{ item, index }"><td :style="item['a12272'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a12272"] }}</td></template>
        <template v-slot:item.8688="{ item, index }"><td :style="item['a8688'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a8688"] }}</td></template>
        <template v-slot:item.8721="{ item, index }"><td :style="item['a8721'] == item['max_detail'] ? 'text-align:center;' : 'background-color:#fcc;color:black;text-align:center;'">{{ item["a8721"] }}</td></template>
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
        // { text: "Muhtoraliyev S.", align: "center", value: "52",  },
        // { text: "Alimov I.I.", align: "center", value: "68",  },
        { text: "#", value: "id", align: "center", width: 30,  },
        { text: "Bo'lim kodi",align: "center", value: "department_code",  },
        { text: "FAKT", align: "center", value: "max_detail",  },
        { text: "Djumartov P.U.", align: "center", value: "33",  },
        { text: "Murodov M.M.", align: "center", value: "46",  },
        { text: "Otaxonov F.M.", align: "center", value: "94",  },
        { text: "Abdulaxatov J.T.", align: "center", value: "100",  },
        { text: "Egamberdiyev O.J.", align: "center", value: "3347",  },
        { text: "Sabirov D.A.", align: "center", value: "10786",  },
        // { text: "Shermatov M.A.", align: "center", value: "88",  },
        { text: "Yusupov A.A.", align: "center", value: "916",  },
        { text: "Mamatov A.S.", align: "center", value: "12272",  },
        // { text: "Bahromov D.F.", align: "center", value: "11869",  },
        { text: "Yakubov A.I.", align: "center", value: "8688",  },
        { text: "Rahmonov I.S.", align: "center", value: "8721",  },
        { text: "Alimov I", align: "center", value: "68",  },
        // { text: "Xasanov A.A.", align: "center", value: "24439",  },
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
        .get(this.$store.state.backend_url + "api/kpi/otchet3")
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
