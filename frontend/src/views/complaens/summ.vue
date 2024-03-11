<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">OKD</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-text-field
            class="txt_search1"
            v-model="search.from_date"
            label="Sanadan"
            type="date"
            outlined
            dense
            style="max-width: 15%; border-left: 0px; border-radius: 0px"
            clearable
            single-line
            hide-details
            @change="getList"
          ></v-text-field>
          <v-text-field
            class="txt_search1"
            v-model="search.to_date"
            label="Sanagacha"
            type="date"
            outlined
            dense
            style="max-width: 15%; border: border-left: 0px; border-radius: 0px;"
            clearable
            single-line
            hide-details
            @change="getList"
          ></v-text-field>
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
        <!-- <v-row class="pl-10 mx-0">
          <v-col cols="2">
            <v-text-field
              class="mt-4"
              v-model="search.from_date"
              label="Sanadan"
              type="date"
              outlined
              dense
              clearable
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-text-field
              class="mt-4"
              v-model="search.to_date"
              label="Sanagacha"
              type="date"
              outlined
              dense
              clearable
            ></v-text-field>
          </v-col>
          <v-col>
            <v-btn @click="getList()" class="ml-1 mt-4" color="info">
              <v-icon>mdi-magnify</v-icon>
            </v-btn>
          </v-col>
        </v-row> -->
        <!-- <v-btn  @click="uploadExcel('table', 'Lorem Table')">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn> -->
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-simple-table 
            id="table" 
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header>
            <template v-slot:default>
              <thead style="text-align: center">
                <tr>
                  <th style="text-align: center">{{'T/R'}}</th>
                  <th style="width:10%">{{'DEPARTMEN CODE'}}</th>
                  <!-- <td>{{'DEPARTMENT TYPE'}}</td> -->
                  <th>{{'DEPARTMENT NAME'}}</th>
                  <th>{{'UZS'}}</th>
                  <th>{{'USD'}}</th>
                  <th>{{'RUB'}}</th>
                </tr>
              </thead>
              <tbody v-for="(item, i) in items" :key="i">
                <tr v-if="item.uzs !=0 || item.usd != 0 || item.rub != 0">
                  <td style="text-align: center">{{items.filter((v) => v.uzs !=0|| v.usd != 0 || v.rub != 0).indexOf(item) + 1}}</td>
                  <td style="">{{item.dep_code}}</td>
                  <!-- <td>{{item.dep_type}}</td> -->
                  <td>{{item.dep_name}}</td>
                  <!-- <td>{{item.uzs}}</td> -->
                  <td>
                    {{(item.uzs).toLocaleString('uz-US', {
                    currency: 'UZS',
                    })}}
                  </td>
                  <td>
                    {{(item.usd != 0)? (item.usd).toLocaleString('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    }): 0}}
                  </td>
                  <td>
                    {{(item.rub !=0)? (item.rub).toLocaleString('ru-US', {
                    style: 'currency',
                    currency: 'RUB',
                    }): 0}}
                  </td>
                  <!-- <td>{{item.usd}}</td> -->
                  <!-- <td>{{item.rub}}</td> -->
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
      </v-row>
      <v-dialog v-model="loading" width="300" hide-overlay>
        <v-card color="primary" dark>
          <v-card-text>
            {{ $t("loadingText") }}
            <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-card>
  </div>
</template>
<script>
import TableToExcel from "@linways/table-to-excel";
const axios = require("axios").default;
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data() {
    return {
      search: {
        from_date: moment()
          .startOf("month")
          .format("YYYY-MM-DD"),
        to_date: moment(new Date()).format("YYYY-MM-DD")
      },
      items: [],
      loading: false
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        {
          text: "#",
          value: "id",
          width: 30,
          sortable: false
        }
      ];
    }
  },
  methods: {
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/complaens/summ", {
          language: this.$i18n.locale,
          // pagination: this.dataTableOptions,
          search: this.search
        })
        .then(response => {
          this.items = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          // this.loading = false;
        });
    }
  },
  mounted() {
    this.getList();
  }
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
.headerSearch
  .v-text-field--enclosed.v-input--dense:not(.v-text-field--solo).v-text-field--outlined
  .v-input__append-inner {
  margin-top: 4px;
  font-size: 18px;
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
  /* border-left: 0px; */
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
}
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > thead > tr > th {
  background: #f6f9fb !important;
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
  /* max-width: 50px; */
  /* height: 43px; */
  margin: 0 auto;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  border-bottom: thin solid rgba(0, 0, 0, 0.12);
}
</style>