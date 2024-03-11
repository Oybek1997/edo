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
        <template v-slot:item.id1="{ item, index }">{{ index + 1 }}</template>
        <template v-slot:item.id="{ item, index }">
          <v-btn
            dense
            outlined
            small
            rounded
            :to="'/document/' + item.pdf_file_name"
          >
            {{ item.id }}
          </v-btn>
        </template>
        <template v-slot:item.status="{ item, index }"><td :style="item['status'] == 5 ? 'background-color:#9f9;' : ''">{{item['status'] == 5 ? 'Tasdiqlandi' :  "Jarayonda"}}</td></template>
        <template v-slot:item.1="{ item, index }"><td :style="item['1'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['1'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.39="{ item, index }"><td :style="item['39'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['39'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.14="{ item, index }"><td :style="item['14'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['14'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.5687="{ item, index }"><td :style="item['5687'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['5687'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.40="{ item, index }"><td :style="item['40'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['40'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.65="{ item, index }"><td :style="item['65'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['65'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.93="{ item, index }"><td :style="item['93'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['93'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.934="{ item, index }"><td :style="item['934'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['934'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.2260="{ item, index }"><td :style="item['2260'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['2260'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.2307="{ item, index }"><td :style="item['2307'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['2307'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.4312="{ item, index }"><td :style="item['4312'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['4312'] == 1 ? 'OK' : ''}}</td></template>
        <template v-slot:item.4559="{ item, index }"><td :style="item['4559'] == 1 ? 'background-color:#efe; text-align:center;' : ''">{{item['4559'] == 1 ? 'OK' : ''}}</td></template>
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
        { text: "#", value: "id1", align: "center", width: 30,  },
        { text: "Bo'lim kodi", value: "department_code",  },
        { text: "Nomi",  value: "name_uz_latin",  },
        { text: "Hujjat ID", align: "center", value: "id",  },
        // { text: "Hujjat nomeri", align: "center", value: "document_number",  },
        // { text: "Hujjat datasi", align: "center", value: "document_date",  },
        { text: "Hujjat statuss", align: "center", value: "status",  },
        { text: "Tasdiq soni", align: "center", value: "quantity",  },
        { text: "Bo Andersson", align: "center", value: "1",  },
        { text: "Tairov Ilxom", align: "center", value: "39",  },
        { text: "Djumartov Pardabay", align: "center", value: "14",  },
        { text: "Oripov Laziz", align: "center", value: "5687",  },
        { text: "Shermatov Muzaffar", align: "center", value: "40",  },
        { text: "Abdulaxatov Jamoliddin", align: "center", value: "65",  },
        { text: "Murodov Mohirjon", align: "center", value: "93",  },
        { text: "Egamberdiyev Ozodbek", align: "center", value: "934",  },
        { text: "Orifov Farxodbek", align: "center", value: "2260",  },
        { text: "Azizov Djaxangir", align: "center", value: "2307",  },
        { text: "Otaxonov Faxriddin", align: "center", value: "4312",  },
        { text: "Sabirov Daniyar", align: "center", value: "4559",  },
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
        .get(this.$store.state.backend_url + "api/kpi/otchet4")
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
