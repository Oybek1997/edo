<template>
  <div class="fullHeight">
    <v-card class="pa-8 pt-0 heightFull" style="background-color: #f1f5f8">
      <v-card-title class="mb-4">
      </v-card-title>        
      <v-row class="mx-0">
        <v-col class="pl-4 pt-0" xs="12" >
          <v-data-table            
            dense
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="noDataText"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="items"
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, 200],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
            @update:sort-by="updatePage"
            @update:expanded="toggleExpand"
            @dblclick:row="rowClick"
            single-expand
          >
            <template v-slot:body.prepend>
              <tr class="py-0 my-0 prepend_height">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense" style="width: 100px">
                  <v-text-field
                    class="my-1"
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    type="text"
                    clearable
                    dense
                    outlined
                    hide-details
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                >
                <v-text-field
                type="text"
                clearable
                dense
                outlined
                hide-details
              ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-menu
                    ref="rangeMenu"
                    offset-y
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-bind="attrs"
                        v-on="on"
                        hide-details
                        dense
                        outlined
                      ></v-text-field>
                    </template>
                  </v-menu>
                </td>

                <td class="py-0 my-0 dense">
                  <v-text-field
                    type="text"
                    hide-details
                    dense
                    outlined
                    clearable
                  ></v-text-field>
                </td>

                <td class="py-0 my-0 dense">
                  <v-autocomplete
                    class="py-0"
                    hide-details
                    dense
                    outlined
                    item-value="id"
                  >
                    <template v-slot:selection="{ item }"></template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title
                          v-text="item.text"
                        ></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    type="text"
                    hide-details
                    dense
                    outlined
                    clearable
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                >
                  <v-text-field
                    type="text"
                    hide-details
                    dense
                    outlined
                    clearable
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                >
                  <v-text-field
                    type="text"
                    hide-details
                    dense
                    outlined
                    clearable
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                >
                  <v-text-field
                    type="text"
                    hide-details
                    dense
                    outlined
                    clearable
                  ></v-text-field>
                </td>
              </tr>
            </template>
          </v-data-table>
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
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import Axios from "axios";
export default {
  data() {
    return {
      loading: false,
      dialog: false,
      items: [],
      form: {},
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      menu: [],
      noDataText: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 320;
    },
    headers() {
      let headers = [
        { text: "", value: "data-table-expand", 
        class: "blue-grey lighten-5 rounded-left",
      },
        {
          text: this.$t("ID"),
          value: "id",
          width: 50,
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          align: "center",
          width: 150,
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.document_date"),
          value: "act_date",
          width: 150,
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.document_date"),
          value: "document_date",
          width: 150,
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.title"),
          value: "title",
          width: 150,
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.document_type"),
          value: "document_template",
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.creator"),
          value: "creator",
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.short_content"),
          value: "summary",
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.number"),
          value: "registration",
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.correspondent"),
          value: "korrespondent",
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.type"),
          value: "type",
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.department_send"),
          value: "from_department",
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.department_receiver"),
          value: "to_department",
          class: "blue-grey lighten-5",
          sortable: false,
        },
        {
          text: this.$t("document.pending_action"),
          value: "pending_action",
          class: "blue-grey lighten-5",
          sortable: false,
        },
      ];
      let filtered_headers = headers.filter((header) => {
        if (this.$route.params.document_type != 4) {
          return (
            header.value != "summary" &&
            header.value != "registration" &&
            header.value != "korrespondent" &&
            header.value != "type"
          );
        } else return headers;
      });

      return filtered_headers;
    },
  },
  methods: {
    pageNext($event)
    {
      this.dataTableOptions.page+1;
      this.getList();
    },
    pageNext($event)
    {
      this.dataTableOptions.page-1;
      this.getList();
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    toggleExpand($event) {},
    rowClick(item, row) {
      row.expand(!row.isExpanded);
    },
    getList() {
      this.noDataText = this.$t("loadingText");
      axios
        .post(this.$store.state.backend_url + "api/documents/filter", {
          pagination: this.dataTableOptions,
          filter: this.filter,
        })
        .then((response) => {
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
<style scoped>
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background-color: #f1f5f8;
}
.custom-row-class {
  min-height: 45px;
  max-height: 45px;
}
.mainTable {
  margin-top: 10px;
}
.v-data-table {
  width: 100%;
  height: 103%;
  line-height: 13px !important;
  margin: 0px 0px 0px 0px;
  border-radius: 10px 0px 10px 10px;
  border: 1px solid #aaa;
}
.txt_search1 {
  height: 5px;
  border-radius: 10px 0px 1px 0px;
  margin: -10px 0.8px 0px 0px;
}
.v-item--active {
  background-color: #f1f5f8 !important;
}
.dense {
  padding: 0px;
  height: 10px !important;
}

.dense .v-text-field__details {
  display: none !important;
}

.dense .v-text-field {
  padding: 0px;
  margin: 0px;
}
.dense div div div {
  margin-bottom: 0px !important;
}
.splite {
  /*V-card uchun */
  height: 260px;
  margin-top: -20px;
}
.splite .border_left {
  /*V-Card Tamplate холати*/
  border-radius: 5px;
  background-color: #fffffa;
}
.splite .elevation-1 {
  /*V-Card устига сичқонча олиб борилган холати*/
  -webkit-box-shadow: 0px 2px 1px -1px rgba(0, 0, 0, 0.2),
    0px 1px 1px 0px rgba(231, 0, 0, 0.14), 0px 1px 3px 0px rgba(0, 0, 0, 0.12) !important;
  box-shadow: 5px 5px 5px -3px #a1a8b1, -3px -3px 3px -3px #a1a8b1 !important;
}
.customspan {
  border: 0.5px solid rgb(240, 237, 237);
  border-radius: 5px;
  width: 100% !important;
  height: 30px !important;
  color: blue-grey;
}
.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  white-space: normal!important;
}
.txt_searchBtn {
  height: 38px !important;
  border-radius: 0px;
  margin: 23px 0.8px 0px 0px;
}
.text_nowrap {
  /*v- card ichidagi Tamplate name uchun */
  width: 100%;
  display: block;
  word-break: keep-all;
  display: -webkit-box;
  /* border:1px solid red; */
  /* max-width: 50px; */
  /* border:1px solid rgb(58, 241, 21); */
  height: 40px;
  /* margin: -22px 0px 0px 0px; */
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.department_text {
  /*v-card ичидаги бўлим номи */
  white-space: normal;
  display: inline-block;
  display: -webkit-box;
  /* max-width: 50px; */
  color: #ffffff;
  height: 25px;
  width: 100%;
  /* border:1px solid red; */
  margin: -10px 5px auto;
  background-color: #2C8DFF;
  font-size: 15px;
  font-weight: 100;
  line-height: 1.6;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.divBTN
{
  margin: -10px 5px auto;
  /* border:1px solid red; */
}
.divPagination
{
  display: flex; 
  justify-content: end;
}
</style>
