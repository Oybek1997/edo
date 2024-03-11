<template>
  <div>
    <v-card class="ma-2 pl-3 pr-3 pb-3">
      <v-card-title>
        {{ dep['name_' + $i18n.locale] + ' ('+params[param]+')' }}
      </v-card-title>
      <v-card-text>
        <v-data-table
        class="mainTable"
          :headers="headers"
          :items="okditem"
          dense
          fixed-header
          :expanded="expanded"
          single-expand
          show-expand>
          <!-- <template class="dense" v-slot:body.prepend>
            <tr class="py-0 my-0 dense">
              <td class="py-0 my-0 dense"></td>
              <td class="py-0 my-0 dense">
                <v-text-field
                class="py-0"
                  v-model="filter.id"
                  type="text"
                  hide-details
                  @keyup.native.enter="getList()"
                  clearable
                ></v-text-field>

              </td>
              <td class="py-0 my-0 dense">
                <v-text-field
                class="py-0"
                  v-model="filter.document_number"
                  type="text"
                  hide-details
                  @keyup.native.enter="getList()"
                  clearable
                ></v-text-field>
              </td>
              <td class="py-0 my-0 dense"></td>
              <td class="py-0 my-0 dense">
                <v-autocomplete
                  class="py-0"
                  clearable
                  v-model="filter.document_type_id"
                  :items="document_type"
                  hide-details
                  item-value="id"
                  @change="getList()"
                >
                  <template v-slot:selection="{ item }">{{ item['name_' + $i18n.locale] }}</template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title >{{ item['name_' + $i18n.locale] }}</v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </td>               
              <td class="py-0 my-0 dense"></td>
              <td class="py-0 my-0 dense"></td>
              <td class="py-0 my-0 dense"></td>
              <td class="py-0 my-0 dense"></td>
              <td class="py-0 my-0 dense"></td>
              <td class="py-0 my-0 dense"></td>
            </tr>
          </template> -->
          <template v-slot:item.id="{ item }">
            {{
              item.documents.id
            }}
          </template>
          <template v-slot:item.document_number="{ item }">
            <v-tab class="hover" :to="'/document/' + item.documents.pdf_file_name">
              {{ item.documents.document_number_reg ? item.documents.document_number_reg : item.documents.document_number }}
            </v-tab>
          </template>
           <template v-slot:item.title="{ item }">
            {{
              item.documents.title
            }}
          </template>           
           <template v-slot:item.status_signer="{ item }">
            {{
              signer_status[item.status]
            }}
          </template>
           <template v-slot:item.action_type_id="{ item }">
            {{
              action_type[item.action_type_id][$i18n.locale]
            }}
          </template>
          <template v-slot:item.document_type_id="{ item }">
            {{
              item.documents.document_type['name_' + $i18n.locale]
            }}
          </template>
          <!--            <template v-slot:item.due_date="{ item }">-->
          <!--              {{item.document_signers[0].due_date}}-->
          <!--            </template>-->
          <!--            <template v-slot:item.sign_date="{ item }">-->
          <!--              {{item.document_signers[0].sign_at}}-->
          <!--            </template>-->
          <template v-slot:item.creator="{ item }">
            {{
              $i18n.locale == "uz_latin"
                ? item.documents.employee.lastname_uz_latin +
                " " +
                item.documents.employee.firstname_uz_latin.substr(0, 1) +
                "." +
                item.documents.employee.middlename_uz_latin.substr(0, 1) +
                "."
                : item.documents.employee.lastname_uz_cyril +
                " " +
                item.documents.employee.firstname_uz_cyril.substr(0, 1) +
                "." +
                item.documents.employee.middlename_uz_cyril.substr(0, 1) +
                "."
            }}
          </template>
          <template v-slot:item.actions="{ item }">
            <v-btn
              color="success"
              class="px-1"
              small
              icon
              @click="$router.push('/document/' + item.documents.pdf_file_name)"
            >
              <v-icon>mdi-eye-outline</v-icon>
            </v-btn>
            <v-btn
              class="px-1"
              v-if="
              $store.getters.checkPermission('document-update') &&
              item.documents.status < 1
            "
              color="blue"
              small
              icon
              @click="$router.push('/document/update/' + item.documents.id)"
            >
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
              v-if="item.documents.status == 0"
              color="red"
              class="px-1"
              small
              icon
              @click="deleteItem(item)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
          <template
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
          </template>
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
      action_type: {
        1:{uz_latin:'Rozilik',uz_cyril:'Розилик',ru:'Согласование'},
        2:{uz_latin:'Tasdiq',uz_cyril:'Тасдиқ',ru:'Утверждение'},
        3:{uz_latin:'Yuboruvchi',uz_cyril:'Юборувчи',ru:'Отправитель'},
        4:{uz_latin:'Ijro uchun',uz_cyril:'Ижро учун',ru:'Для исполнения'},
        5:{uz_latin:'Ma`lumot uchun',uz_cyril:'Маълумот учун',ru:'Для информации'},
        6:{uz_latin:'Hujjat yaratuvchisi',uz_cyril:'Ҳужжат яратувчиси',ru:'Создатель документа'},
        8:{uz_latin:'Komissiya a`zolari',uz_cyril:'Комиссия аъзолари',ru:'Члены комиссии'},
        9:{uz_latin:'Komissiya raisi',uz_cyril:'Комиссия раиси',ru:'Председатель комиссии'},
        10:{uz_latin:'Komissiya kotibi',uz_cyril:'Комиссия котиби',ru:'Секретарь комиссии'},
        11:{uz_latin:'Nazorat',uz_cyril:'Назорат',ru:'Контроль'},
        12:{uz_latin:'Kuzatuvchi',uz_cyril:'Кузатувчи',ru:'Наблюдатель'},
        13:{uz_latin:'Tahrirlovchi',uz_cyril:'Таҳрирловчи',ru:'Редактор'},
        14:{uz_latin:'Tarqatuvchi',uz_cyril:'Тарқатувчи',ru:'Рассылки'},
      },
      dep: [],
      items: [],
      loading: false,
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
      signer_status:{
        0: this.$t("document.new"),
        1: this.$t("document.ok"),
        2: this.$t("document.cancel"),
        3: this.$t("document.process"),
        4: this.$t("rep.to_be_executed")
      },
      filter: {
        id: '',
        // title: "",
        document_type_id: '',
        document_template_id: '',
        status_signer: "",
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

    };
  },
  computed: {
    headers() {
      return [
        {text: "ID:", value: 'id', align: "center"},
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          align: "center",
          width: 200,
          sortable: false,
          // width: '200'
        },
        // {
        //   text: 'due date',
        //   value: 'due_date',
        //   width: '150'
        // },
        // {
        //   text: 'sign date',
        //   value: 'sign_date'
        // },
        {
          text: this.$t("document.title"),
          value: "title",
          // width: '600',
          sortable: false,
        },
        {
          text: this.$t("document.document_type"),
          value: "document_type_id",
          sortable: false,
        },
        {
          text: this.$t("document.due_date"),
          value: "due_date",
          sortable: false,
          // width: '100'
        },
        {
          text: this.$t("document.signed_at"),
          value: "sign_at",
          sortable: false,
        },
        {
          text: this.$t("document.action_type"),
          value: "action_type_id",
          sortable: false,
        },
        {
          text: this.$t("document.signer_status"),
          value: "status_signer",
          sortable: false,
        },
        {
          text: this.$t("document.creator"),
          value: "creator",
          sortable: false,
        },
        {
          text: this.$t("actions"),
          value: "actions",
          sortable: false,
        },
      ]
    }
  },
  methods: {

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
          // console.log(this.document_type.find(v=>v.id == 4));
          // console.log(this.document_type.find(v=>v.id == 4)); 1 ta qaytaradi
          // console.log(this.document_type.filter(v=>v.id == 4)); shart boyicha xamasini qaytaradi
          // console.log(this.document_type.sam(v=>v.id == 4)); true false qaytaradi
          // console.log(this.document_type.map(v=>v.id*10)); ozgartirib shart bilan qaytaradi
          // console.log(this.document_type.map(v=>{
          //   return 'salom'
          // }));
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
        .post(this.$store.state.backend_url + "api/okd-report-item", {
          route_array: route_array,
          language: this.$i18n.locale,
          filter: this.filter,
        })
        .then((response) => {
          // console.log(response.data[1][0].documents.document_number)
          this.okditem = response.data[1];
          this.dep = response.data[0][0];
          this.param = response.data[2];
          // this.staffs = response.data;

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
