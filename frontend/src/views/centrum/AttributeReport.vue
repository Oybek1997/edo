<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">Hisobot</span>
        <div class="headerSearch d-flex align-center act_report_select">
          <v-text-field
            class="txt_search1"
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-autocomplete
              class="txt_search2"
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
            ></v-autocomplete>
            <v-autocomplete
            v-model="selected_reaction"
            :items="reaction_status"
            outlined
            dense
            :placeholder="$t('document.reaction_status')"
            multiple
            hide-details
            style="max-width: 520px; min-width: 466px"
            class="txt_search2"
            @change="getList()"
          >
            <template v-slot:selection="{ item }">
              <v-chip
                :class="
                  item.value == 1
                    ? 'primary'
                    : item.value == 2
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
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
            <v-icon color="#00B950" left>mdi-filter-outline</v-icon>Фильтр
          </v-btn>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
              Столбцы <v-icon color="#00B950" right>mdi-checkbox-marked-outline</v-icon>
          </v-btn>
          <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="txt_searchBtn ml-2"
                outlined
                v-bind="attrs"
                v-on="on"
              >
                <v-icon size="18" color="white"
                  >mdi-format-list-bulleted</v-icon
                >
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title
                  @click="uploadExcel('table', 'Lorem Table')"
                  >
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    Скачать таблицу Excel
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu> 
        </div>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-simple-table 
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            id="table"             
            fixed-header>
            <thead>
              <tr style="background: #f6f9fb!important;">
                <th style="background: #f6f9fb!important;">#</th>
                <th style="background: #f6f9fb!important;">Hujjat raqami</th>
                <th style="background: #f6f9fb!important;">Holati</th>
                <template v-for="(item, index) in headers">
                  <th>{{ item }}</th>
                </template>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in items" >
                <td>
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
                <td v-if="item['k'][1].status==1"><v-btn
                    dense
                    text
                    small
                    rounded
                    color="primary"
                  >
                    Yangi
                  </v-btn></td>
                <td v-if="[2,4,5].includes(item['k'][1].status)"><v-btn
                    dense
                    text
                    small
                    rounded
                    color="warning"
                  >
                    Jarayonda
                  </v-btn></td>
                <td v-if="item['k'][1].status==3"><v-btn
                    dense
                    text
                    small
                    rounded
                    color="success"
                  >
                    Tasdiqlangan
                  </v-btn></td>
                <td v-if="item['k'][1].status==6"><v-btn
                    dense
                    text
                    small
                    rounded
                    color="error"
                  >
                    Rad etilgan
                  </v-btn></td>
                <template v-for="row in item['k']">
                  <td v-if="row">{{ row.value }}</td>
                  <td v-else></td>
                </template>
              </tr>
            </tbody>
          </v-simple-table>
        </v-col>
      </v-row>
      <v-row class="my-0 px-5">
        <v-col xl="1" lg="2" md="4" class="act_report_select">
          <v-select
            style="border-radius: 5px;"
            class="txt_search2"
            v-model="pagination.per_page"
            :items="[20, 50, 100, 200, 500, 1000]"
            color="#e6e6e6"
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
            color="#e6e6e6"
            style="border-color: #e6e6e6;"
            outlined
            class="mx-1"
            @click="firstPage"
            ><v-icon size="18">mdi-arrow-collapse-left</v-icon></v-btn
          >
          <v-btn
            :disabled="arrow2"
            color="#e6e6e6"
            style="border-color: #e6e6e6;"
            outlined
            class="mx-1"
            @click="prevPage"
            ><v-icon size="18">mdi-arrow-left</v-icon></v-btn
          >
          {{ from }}-{{ to }} of {{ total }}
          <v-btn
            :disabled="arrow3"
            color="#e6e6e6"
            style="border-color: #e6e6e6;"
            outlined
            class="mx-1"
            @click="nextPage"
            ><v-icon size="18">mdi-arrow-right</v-icon></v-btn
          >
          <v-btn
            :disabled="arrow4"
            color="#e6e6e6"
            style="border-color: #e6e6e6;"
            outlined
            class="mx-1"
            @click="lastPage"
            ><v-icon size="18">mdi-arrow-collapse-right</v-icon></v-btn
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
      selected_reaction: [1,2,3,6],
      today: moment().format("YYYY-MM-DD"),
      reaction_status: [
        {
          text: this.$t("document.new"),
          value: 1,
        },
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
        .post("https://b-edo.uzautomotors.com/api/centrum/attributeReport", {
          template: this.template_id,
          reaction: this.selected_reaction,
          pagination: this.pagination,
          search: this.search
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
    getTemplates() {
      this.loading = true;
      axios
        // .get(this.$store.state.backend_url + "api/kpi/otchet")
        // .post("https://b-edo.uzautomotors.com/api/kpi/otchet", {
        //   search: this.search,
        //   filter: this.filter,
        // })
        .get("https://b-edo.uzautomotors.com/api/centrum-template")
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
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialogTitle {
  color: #000;
  font-size: 16px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.labelTitle {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
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
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > tbody > tr > td {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropdown-list .v-list-item .v-list-item__title {
  color: #000;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialog-head_title {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-dialog > .v-card > .v-card__text {
  padding: 0px 0px 0px 0px;
}
.doc-template_data-table table > tbody > tr > td {
  white-space: normal;
  max-width: 50px;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>