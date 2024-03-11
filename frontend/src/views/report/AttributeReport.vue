<template>
  <div class="fullHeight">
    <v-card class="pa-0 heightFull" elevation="0">
      <!-- --------------------- -->
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2" v-if="$route.params.menu_item == 'my'">{{
          $t("my_documents")
        }}</span>
        <span
          class="headerTitle mb-2"
          v-if="$route.params.menu_item == 'all'"
          >{{ $t("all_documents") }}</span
        >
        <span
          class="headerTitle mb-2"
          v-if="$route.params.menu_item == 'selected'"
          >{{ $t("Template report") }}</span
        >
        <span
          class="headerTitle mb-2"
          v-if="($route.params.menu_item == 'my-inbox')"
          >{{ $t("my_documents") }}2</span
        >
        <div class="headerSearch d-flex align-center">
          <v-autocomplete
            class="txt_search1"
            clearable
            v-model="template_id"
            :items="templates"
            style="width: 20px !important"
            dense
            hide-details
            solo
            item-value="id"
            item-text="name_uz_latin"
          ></v-autocomplete>
          <v-text-field
            v-if="has_employee && $route.params.menu_item != 'my'"
            v-model="tabno"
            append-icon="mdi-magnify"
            class="txt_search2"
            style="width: 80px !important"
            :placeholder="$t('employee.tabel')"
            dense
            hide-details
            solo
            single-line
          ></v-text-field>
          <v-autocomplete
            class="txt_search2"
            v-model="selected_reaction"
            :items="reaction_status"
            dense
            hide-details
            solo
            background-color="#FFFFFa"
            :label="$t('document.reaction_status')"
            multiple
            style="max-width: 300px; min-width: 200px"
          >
            <template v-slot:selection="{ item }">
              <v-chip
                :class="
                  item.value == 2
                    ? 'orange lighten-1'
                    : item.value == 3
                    ? 'success'
                    : item.value == 6
                    ? 'error'
                    : ''
                "
                small
                :dark="item.reaction_status == 0 ? false : true"
                class="ma-0 mr-1 px-1"
                >{{ item.text }}</v-chip
              >
            </template>
            <template v-slot:item="{ item }">
              <v-list-item-content>
                <v-list-item-title v-text="item.text"></v-list-item-title>
              </v-list-item-content>
            </template>
          </v-autocomplete>
          <v-text-field
            v-model="menu"
            class="txt_search2"
            label="Sanadan"
            type="date"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-text-field
            v-model="menu2"
            class="txt_search2"
            label="Sanagacha"
            type="date"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-text-field
            class="txt_search2"
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            style="width: 100px !important"
            :placeholder="$t('search')"
            background-color="#FFFFFa"
            solo
            dense
            single-line
            hide-details
          ></v-text-field>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="getList()"
            >Izlash
            <v-icon color="info" center>mdi-magnify</v-icon>
          </v-btn>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="uploadExcel('table', 'Lorem Table')"
          >
            {{ $t("Excel") }}
            <v-icon color="green" right>mdi-download-multiple</v-icon>
          </v-btn>
        </div>
      </v-card-title>
      <!-- --------------------- -->
      <!-- <v-card-title class="pa-1">
        <span v-if="$route.params.menu_item=='my'">{{ $t("my_documents") }}</span>
        <span v-if="$route.params.menu_item=='all'">{{ $t("all_documents") }}</span>
        <span v-if="$route.params.menu_item=='selected'">{{ $t("Template report") }}</span>
        <v-spacer></v-spacer>
        <v-autocomplete
          class="mr-2"
          clearable
          v-model="template_id"
          :items="templates"
          hide-details="auto"
          background-color="#FFFFFa"
          style="width: 20px !important"
          dense
          item-value="id"
          item-text="name_uz_latin"
          outlined
        ></v-autocomplete>
        <v-text-field
        v-if="has_employee&&$route.params.menu_item!='my'"
          v-model="tabno"
          append-icon="mdi-magnify"
          class="mr-2"
          background-color="#FFFFFa"
          style="width: 80px !important"
          :placeholder="$t('employee.tabel')"
          outlined
          dense
          single-line
          hide-details
        ></v-text-field>
        <v-autocomplete
          v-model="selected_reaction"
          :items="reaction_status"
          outlined
          background-color="#FFFFFa"
          dense
          :label="$t('document.reaction_status')"
          multiple
          hide-details
          style="max-width: 300px; min-width: 200px"
          class="mr-2"
        >
          <template v-slot:selection="{ item }">
            <v-chip
              :class="
                item.value == 2
                  ? 'orange lighten-1'
                  : item.value == 3
                  ? 'success'
                  : item.value == 6
                  ? 'error'
                  : ''
              "
              small
              :dark="item.reaction_status == 0 ? false : true"
              class="ma-0 mr-1 px-1"
              >{{ item.text }}</v-chip
            >
          </template>
          <template v-slot:item="{ item }">
            <v-list-item-content>
              <v-list-item-title v-text="item.text"></v-list-item-title>
            </v-list-item-content>
          </template>
        </v-autocomplete>
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
                style="max-width: 150px; min-width: 50px"
                class="mx-2"
                dense
                background-color="#FFFFFa"
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
                style="max-width: 150px; min-width: 50px"
                class="mx-2"
                v-model="date2"
                label="Sana oxiri"
                outlined
                background-color="#FFFFFa"
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
        <v-text-field
          v-model="search"
          prepend-inner-icon="mdi-magnify"
          class="mr-2"
          style="width: 100px !important"
          :placeholder="$t('search')"
          outlined
          background-color="#FFFFFa"
          dense
          single-line
          hide-details
        ></v-text-field>
        <v-btn depressed dense color="success" @click="getList()">
          Izlash
        </v-btn>
        <v-btn class="mx-3" @click="uploadExcel('table', 'Lorem Table')">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn>
      </v-card-title> -->
      <!-- :style="{ height: screenHeight + 'px' }" -->
      <v-simple-table class="mainTable" id="table" dense fixed-header>
        <thead>
          <tr>
            <th>#</th>
            <th>Ҳужжат рақами</th>
            <th>Ҳужжат санаси</th>
            <th>Ҳужжат ҳолати</th>
            <th>Ҳодим</th>
            <th>Табел</th>
            <template v-for="(item, index) in headers">
              <th>{{ item }}</th>
            </template>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items">
            <td>
              {{ index + from + 1 }}
            </td>
            <td>
              <v-btn
                dense
                outlined
                small
                rounded
                style="color: white"
                :class="
                  item['k'][1]
                    ? [1, 2].includes(item['k'][1].status)
                      ? 'orange lighten-1'
                      : [3, 4, 5].includes(item['k'][1].status)
                      ? 'success'
                      : item['k'][1].status == 6
                      ? 'error'
                      : ''
                    : ''
                "
                :to="'/document/' + item['k'][0].pdf_file_name"
              >
                {{ item["k"][1] ? item["k"][1].document_number : "" }}
              </v-btn>
            </td>
            <td>
              {{
                item["k"][0].document_date
                  ? item["k"][0].document_date.substring(0, 10)
                  : ""
              }}
            </td>
            <td>
              {{
                item["k"][1].status
                  ? [1, 2].includes(item["k"][1].status)
                    ? "Жараёнда"
                    : [3, 4, 5].includes(item["k"][1].status)
                    ? "Тасдиқланган"
                    : item["k"][1].status == 6
                    ? "Бекор қилинган"
                    : ""
                  : ""
              }}
            </td>
            <td>{{ item["k"][0].employee_fio }}</td>
            <td>{{ item["k"][0].tabel }}</td>
            <template v-for="row in item['k']">
              <td v-if="row">{{ row.value }}</td>
              <td v-else></td>
            </template>
          </tr>
        </tbody>
      </v-simple-table>
      <v-row class="my-0">
        <v-col></v-col>
        <v-col xl="1" lg="2" md="4">
          <v-select
            v-model="pagination.per_page"
            :items="[0, 20, 100, 200, 1000, 2000, 50000]"
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
      template_id: null,
      selected_reaction: [2, 3, 6],
      has_employee: 0,
      tabno: null,
      today: moment().format("YYYY-MM-DD"),
      reaction_status: [
        {
          text: this.$t("document.process"),
          value: 2,
        },
        {
          text: this.$t("document.ok"),
          value: 3,
        },
        {
          text: this.$t("document.cancel"),
          value: 6,
        },
        // {
        //   text: this.$t("substantiate"),
        //   value: 4,
        // },
      ],
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
      TableToExcel.convert(document.getElementById("table"), {
        name: "TemplateReport_" + this.today + ".xlsx",
      });
    },
    getList() {
      this.loading = true;
      this.items = [];
      let template = this.templates.filter((v) => v.id == this.template_id);
      // console.log(template);
      if (template[0] && template[0].has_employee == 1) {
        this.has_employee = 1;
      } else {
        this.has_employee = 0;
      }
      console.log("template");
      axios
        // .get(this.$store.state.backend_url + "api/kpi/otchet")
        // .post("https://b-edo.uzautomotors.com/api/kpi/otchet", {
        //   search: this.search,
        //   filter: this.filter,
        // })
        .post("https://b-edo.uzautomotors.com/api/attributeReport", {
          template: this.template_id,
          reaction: this.selected_reaction,
          pagination: this.pagination,
          menu_item: this.$route.params.menu_item,
          employee: this.has_employee,
          tabno: this.tabno,
          startdate: this.date,
          enddate: this.date2,
        })
        .then((response) => {
          let data = response.data[1];
          this.headers = response.data[0];
          data = Object.entries(data).map((v) => {
            return v[1];
          });
          data.forEach((e, k) => {
            this.items.push({ k: e.slice(0, this.headers.length) });
          });
          this.items = this.items.sort().reverse();
          this.from = response.data[3];
          this.total = response.data[2];
          this.to =
            response.data[3] + this.pagination.per_page > response.data[2]
              ? response.data[2]
              : response.data[3] + this.pagination.per_page;
          this.last_page = Math.floor(
            response.data[2] / this.pagination.per_page
          );
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
    getTemplates() {
      this.loading = true;
      let url = "";
      if (this.$route.params.menu_item == "selected") {
        url =
          "https://b-edo.uzautomotors.com/api/selected-templates-for-report";
      } else {
        url = "https://b-edo.uzautomotors.com/api/all-templates-for-vayvooo";
      }
      axios
        .get(url)
        .then((response) => {
          this.templates = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
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
    this.getTemplates();
    // this.getList();
  },
};
</script>
<style scoped>
.fullHeight {
  height: calc(100% - 10px);
}
.heightFull {
  border-radius: 10px 10px 10px 10px;
}

.hover :hover {
  font-size: 20px;
  color: #0a73bb;
}
.hover_color :hover {
  color: rgb(0, 0, 0);
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.headerSearch {
  width: 100%;
  height: 34px;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_search2 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_searchBtn {
  background: #ff9f0e;
  border: 0.2px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px !important;
  height: 34px !important;
  border-radius: 1px;
  width: 25px;
  padding: 0 13px;
}

.filterBtn {
  color: #000;
  font-size: 12px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  border: 1px solid #e6e6e6;
  /* border-right: 0px; */
  border-left: 0px;
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
}
</style>