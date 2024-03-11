<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>Авиа отчет</span>
        <v-spacer></v-spacer>
        <!-- <v-autocomplete
          class="mr-2"
          clearable
          v-model="template_id"
          :items="templates"
          hide-details="auto"
          style="width: 20px !important"
          dense
          item-value="id"
          item-text="name_uz_latin"
          outlined
          @change="getList"
        ></v-autocomplete> -->
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
        <v-btn class="mx-3" @click="uploadExcel('table', 'Lorem Table')">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn>
      </v-card-title>
       <!-- :style="{ height: screenHeight + 'px' }" -->
      <v-simple-table class="mainTable" id="table" dense fixed-header>
        <thead >
          <tr>
            <th  style="font-weight: 900; background-color: #e6e6e6 !important; font-size: 14px">#</th>
            <th  style="font-weight: 900; background-color: #e6e6e6 !important; font-size: 14px">Номер документа</th>
            <th  style="font-weight: 900; background-color: #e6e6e6 !important; font-size: 14px">Дата документа</th>
            <template v-for="(item, index) in headers">
              <th style="font-weight: 900; background-color: #e6e6e6 !important; font-size: 14px">{{ item }}</th>
            </template>
          </tr>
        </thead>
        <tbody v-for="(item, index) in items">
          <tr >
            <td >
              {{ index + from +1 }}
            </td>
            <td>
              <v-btn
                dense
                outlined
                small
                rounded
                :to="'/document/' + item['k'][0].pdf_file_name"
              >
                {{ item['k'][1] ? item['k'][1].document_number : '' }}
              </v-btn>
            </td>
            <td>
                {{item['k'][1].document_date.substr(0,11)}}
              </td>
            <template v-for="row in item['k']">
              <td v-if="row">{{ row.value }}</td>

            </template>
            <td  v-for="td in headers.length-item['k'].length"></td>

          </tr>
        </tbody>
      </v-simple-table>
      <v-row class="my-0">
        <v-col></v-col>
        <v-col xl="1" lg="2" md="4">
          <v-select
            v-model="pagination.per_page"
            :items="[20, 50, 100, 200, 500, 1000]"
            color="#78909C"
            outlined
            dense
            hide-details
            @change="perPageUpdate"
          >
          </v-select>
        </v-col>
        <v-col xl="4" lg="6" md="7">
          <v-btn
            :disabled="arrow1"
            color="#78909C"
            outlined
            class="mx-1"
            @click="firstPage"
            ><v-icon>mdi-arrow-collapse-left</v-icon></v-btn
          >
          <v-btn
            :disabled="arrow2"
            color="#78909C"
            outlined
            class="mx-1"
            @click="prevPage"
            ><v-icon>mdi-arrow-left</v-icon></v-btn
          >
          {{ from }}-{{ to }} of {{ total }}
          <v-btn
            :disabled="arrow3"
            color="#78909C"
            outlined
            class="mx-1"
            @click="nextPage"
            ><v-icon>mdi-arrow-right</v-icon></v-btn
          >
          <v-btn
            :disabled="arrow4"
            color="#78909C"
            outlined
            class="mx-1"
            @click="lastPage"
            ><v-icon>mdi-arrow-collapse-right</v-icon></v-btn
          >
        </v-col>
      </v-row>
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
import Template from "../blankTemplate/Template.vue";
const moment = require("moment");
const axios = require("axios").default;
export default {
  components: { Template },
  data() {
    return {
      loading: false,
      search: "",
      items: [],
      headers: [],
      templates: [],
      template_id: 4,
    //   selected_reaction: [1,3],
      today: moment().format("YYYY-MM-DD"),
    //   reaction_status: [

    //       {
    //         text: this.$t("document.process"),
    //         value: 1,
    //       },
    //       {
    //         text: this.$t("document.ok"),
    //         value: 3,
    //       },
    //   ],
      pagination: {
        page: 1,
        per_page: 20,
      },
      items_count: null,
      arrow1: true,
      arrow2: true,
      arrow3: true,
      arrow4: true,
      last_page: null,
      from: 0,
      total: 0,
      to: 0,
      date: null,
      menu: false,
      menu2: false,
      date2: null,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"), {name: "TemplateReport_" + this.today + ".xlsx"});
    },
    getList() {
      this.loading = true;
      this.items = []
      axios
        // .get(this.$store.state.backend_url + "api/kpi/otchet")
        // .post("https://b-edo.uzautomotors.com/api/kpi/otchet", {
        //   search: this.search,
        //   filter: this.filter,
        // })
        .post("https://b-edo.uzautomotors.com/api/report/aviaReport", {
          template: 242,
        //   template: this.template_id,
        //   reaction: this.selected_reaction,
          pagination: this.pagination,
          search: this.search,
          startdate: this.date,
          enddate: this.date2,
        })
        .then((response) => {
          let data = response.data[1];
          this.headers = response.data[0];
          data = Object.entries(data).map((v)=>{ return v[1];})
          data.forEach((e,k) => {
            this.items.push({k: e.slice(0, this.headers.length)})
          });
          this.items = this.items.sort().reverse();
          this.from = response.data[3];
          this.total = response.data[2];
          this.to = response.data[3] + this.pagination.per_page>response.data[2]?response.data[2]:response.data[3] + this.pagination.per_page;
          this.last_page = Math.floor(response.data[2]/this.pagination.per_page);
          if (this.to != this.total) {
            this.arrow3 = false;
            this.arrow4 = false;
          } else {
            this.arrow3 = true;
            this.arrow4 = true;
          }
          if (this.from != 0) {
            this.arrow1 = false;
            this.arrow2 = false;
          } else {
            this.arrow1 = true;
            this.arrow2 = true;
          }
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    // getTemplates() {
    //   this.loading = true;
    //   axios
    //     // .get(this.$store.state.backend_url + "api/kpi/otchet")
    //     // .post("https://b-edo.uzautomotors.com/api/kpi/otchet", {
    //     //   search: this.search,
    //     //   filter: this.filter,
    //     // })
    //     .get("https://b-edo.uzautomotors.com/api/avia-report-template")
    //     .then((response) => {
    //       this.templates = response.data;
    //       this.loading = false;
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //       this.loading = false;
    //     });
    // },
    nextPage() {
      this.pagination.page += 1;
      this.updatePage();
    },
    prevPage() {
      this.pagination.page -= 1;
      this.updatePage();
    },
    lastPage() {
      this.pagination.page = this.last_page + 1;
      this.updatePage();
    },
    firstPage() {
      this.pagination.page = 1;
      this.updatePage();
    },
    perPageUpdate() {
      this.pagination.page = 1;
      this.updatePage();
    },
    updatePage() {
      this.getList();
    },
    screenWidth() {
      return window.innerWidth;
    },
    updatePerPage() {
      this.getList();
    },
  },
  mounted() {
    // this.getTemplates();
    this.getList();
  },
};
</script>
