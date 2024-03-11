<template>
  <div class="fullHeight">
      <v-card class="heightFull" style="border-radius: 10px; border: 1px solid #dce5ef;" elevation="0">
        <v-tooltip bottom >
          <template v-slot:activator="{ on, attrs }">
            <v-btn v-bind="attrs" v-on="on" icon @click="goBack">
              <v-icon large color="grey" class="mt-4 ml-7">mdi-keyboard-backspace</v-icon>
            </v-btn>
          </template>
          <span>{{ $t("Oldingi saxifaga qaytish") }}</span>
        </v-tooltip>
        <v-card-title class="px-4 py-0" style="margin:-28px 0px 10px 35px">
         {{   'Report documents' }}
      </v-card-title>
      
      <v-card-text>
        <v-data-table
        class="mainTable"
          :headers="headers"
          :items="items"
          dense
          fixed-header
          :expanded="expanded"
          single-expand
          >
          <template v-slot:item.tr="{ item }">
            {{items.map(function(x) {return x.id; }).indexOf(item.id) + 1}}
          </template>
          <template v-slot:item.id="{ item }">
            {{
              item.id
            }}
          </template>
          <template v-slot:item.document_date="{ item }">
            {{
              item.document_date.substring(0,10)
            }}
          </template>
          <template v-slot:item.document_number="{ item }">
            <v-tab class="hover" :to="'/documentsidebar/document/' + item.pdf_file_name" >
              {{ item.document_number_reg ? item.document_number_reg : item.document_number }}
            </v-tab>
          </template>
          <template v-slot:item.title="{ item }">
            {{
              item.title
            }}
          </template>           
           
          <template v-slot:item.actions="{ item }">
            <v-btn
              color="success"
              class="px-1"
              small
              icon
              @click="$router.push('/documentsidebar/document/' + item.pdf_file_name)"
            >
              <v-icon>mdi-eye-outline</v-icon>
            </v-btn>
            
          </template>
          <!-- <template
            v-slot:expanded-item="{ headers, item }">
            <td :colspan="headers.length">
              <v-row class="justify-center">
                <v-col cols="12" md="10" lg="9" xl="7" class="py-1 ma-2">
                  <v-card class="pa-3">
                    <div v-for="(document_detail, detail_index) in item.documents.document_details" :key="detail_index">
                      <strong style="float: left" v-if="item.documents.document_details.length > 1">{{
                          detail_index + 1
                        }}.</strong>
                      <p class="text-left font-weight-black my-1 pl-6" v-html="document_detail.content"></p>
                    </div>
                  </v-card>
                </v-col>
              </v-row>
            </td>
          </template> -->
        </v-data-table>
      </v-card-text>
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
import Cookies from "js-cookie";

const axios = require("axios").default;
import Swal from "sweetalert2";

const moment = require("moment");
export default {
  data() {
    return {
      search: '',
      okditem: [],
      expanded: [],
      document_templates: [],
      document_type: [],
      dep: [],
      items: [],
      loading: false,
      filter: {
        id: '',
        // title: "",
        document_type_id: '',
        document_template_id: '',
        // document_start_date: "",
        // document_end_date: "",
        // attributes: [],
        // menu_item: "",
        document_number: "",
        // created_by: "",
        type: "",
        // registration: "",
        // send_by: "",
        // receive_by: "",
        // content: "",
        // pending_action: "",
        // reaction_status: [0, 1, 2, 3, 4],
        // staff_id: null,
      },
       param:'',
      params:{
        0: this.$t("rep.params_0"),
        1: this.$t("rep.params_1"),
        2: this.$t("rep.params_2"),
        3: this.$t("rep.params_3"),
        4: this.$t("rep.params_4"),
        5: this.$t("rep.params_5"),
        6: this.$t("rep.params_6"),
        7: this.$t("rep.params_7"),
        8: this.$t("rep.params_8"),
      },

    };
  },
  computed: {
    headers() {
      return [
        {text: "T/R:", value: 'tr', align: "center", width: 50,
          sortable: false,},
        {text: "ID:", value: 'id', align: "center", width: 50,
          sortable: false,},
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          align: "center",
          width: 200,
          sortable: false,
          // width: '200'
        },
        {
          text: this.$t("document.document_date"),
          value: "document_date",
          align: "center",
          width: 200,
          sortable: false,
          // width: '200'
        },
        {
          text: this.$t("document.title"),
          value: "title",
          width: '400',
          sortable: false,
        },
        {
          text: this.$t("document.document_type"),
          value: "name_uz_latin",
          sortable: false,
        },
        // {
        //   text: this.$t("actions"),
        //   value: "actions",
        //   sortable: false,
        // },
      ]
    }

  },
  methods: {
    goBack() {
      this.$router.go(-1);
    },
    getFilter() {
      this.showFilter = true;
      this.document_type = this.document_type.find((v) => {
        return v.id == this.filter.document_type_id;
      });
      this.getList();
    },

    getDocumentType() {
      this.filter.attributes = [];
      axios
        .get(this.$store.state.backend_url + "api/document-types")
        .then((response) => {
          this.document_type = response.data;
          // console.log(this.document_type);
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },

    getDocumentTemplate() {
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-list",
          {
            language: this.$i18n.locale,
          }
        )
        .then((res) => {
          this.document_templates = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getList() {
      let route = this.$route.params.type;
      let route_array = route.split("&");
      // console.log(route_array);
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/report/my-reports-item", {
          route_array: route_array,
          language: this.$i18n.locale,
          filter: this.filter,
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
    // toggleExpand($event) {
    // },
    // rowClick(item, row) {
    //   row.expand(!row.isExpanded);
    //   // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
    //   // else this.expanded = [item];
    // },
  },
  watch: {
    $route(to, from) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.filter.document_type_id = this.$route.params.document_type;
      this.filter.staff_id = null;
      Cookies.set("filter", this.filter);
      this.getList();
    },
  },
  mounted() {
    this.getDocumentTemplate();
    this.getDocumentType();
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
  background: #fff;
}
.hover {
  color: white !important;
  background-color: #0b93d5;
  border-radius: 25px;
  padding: 5px;
}

.hover :hover {
  font-size: 20px !important;
  color: #0a73bb;
}
</style>
