<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>
          {{ $t("department.incoming") }}
        </span>
        <v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          hide-details
          outlined
          dense
          :label="$t('searchInText')"
          class="mr-1"
          @keyup.native.enter="getList()"
        ></v-text-field>
        <!-- <v-btn class="px-1 ml-5" color="blue" small @click="changeItem()">
            <span style="color:white">{{'create'}}</span>
          </v-btn> -->
      </v-card-title>
      <v-data-table
          :height="screenHeight"
          dense
          fixed-header
          :headers="headers"
          :items="items"
          :disable-pagination="true"
          :server-items-length="server_items_length"
          :options.sync="dataTableOptions"
          :footer-props="{
          itemsPerPageOptions: [50, 100, 200],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right'
        }"
          item-key="id"
          @update:page="updatePage"
          @update:items-per-page="updatePerPage"
          class="mainTable ma-1"
          style="border: 1px solid #aaa"
      >
        <template v-slot:body.prepend>
          <tr class="py-0 my-0 prepend_height">
            <!-- <td class="py-0 my-0 dense"></td> -->
            <td class="py-0 my-0 dense"></td>
            
            <td class="py-0 my-0 dense">
              <v-menu
                  ref="rangeMenu"
                  :close-on-content-click="false"
                  :return-value.sync="filter.document_range"
                  offset-y
                  min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      v-model="filter.document_range"
                      v-bind="attrs"
                      @keyup.native.enter="getList()"
                      v-on="on"
                      hide-details
                      clearable
                  ></v-text-field>
                </template>
                <v-date-picker v-model="date" range no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn
                      text
                      color="primary"
                      @click="$refs.rangeMenu.isActive = false"
                  >Cancel
                  </v-btn
                  >
                  <v-btn
                      text
                      color="primary"
                      @click="
                      $refs.rangeMenu.save(date);
                      filter.document_range = date;
                      getList();
                    "
                  >OK
                  </v-btn
                  >
                </v-date-picker>
              </v-menu>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                  v-model="filter.reg_num"
                  type="text"
                  hide-details
                  @keyup.native.enter="getList()"
                  clearable
              ></v-text-field>
            </td>
            <!-- <td class="py-0 my-0 dense">
              <v-autocomplete
                class="py-0"
                clearable
                v-model="filter.documentType"
                :items="documentType"
                item-value="id"
                :item-text="'name_' + $i18n.locale"
                hide-details
                @change="getList()"
              >               
              </v-autocomplete>
            </td> -->
            <td class="py-0 my-0 dense">
              <v-text-field
                  v-model="filter.out_num"
                  type="text"
                  hide-details
                  @keyup.native.enter="getList()"
                  clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-menu
                  ref="range2Menu"
                  :close-on-content-click="false"
                  :return-value.sync="filter.document_out_range"
                  offset-y
                  min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      v-model="filter.document_out_range"
                      v-bind="attrs"
                      @keyup.native.enter="getList()"
                      v-on="on"
                      hide-details
                      clearable
                  ></v-text-field>
                </template>
                <v-date-picker v-model="date" range no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn
                      text
                      color="primary"
                      @click="$refs.range2Menu.isActive = false"
                  >Cancel
                  </v-btn
                  >
                  <v-btn
                      text
                      color="primary"
                      @click="
                      $refs.range2Menu.save(date);
                      filter.document_out_range = date;
                      getList();
                    "
                  >OK
                  </v-btn
                  >
                </v-date-picker>
              </v-menu>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                  v-model="filter.correspondent"
                  type="text"
                  hide-details
                  @keyup.native.enter="getList()"
                  clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense">
              <v-text-field
                  v-model="filter.description"
                  type="text"
                  hide-details
                  @keyup.native.enter="getList()"
                  clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <!-- <td class="py-0 my-0 dense"></td> -->
            <!-- <td class="py-0 my-0 dense"></td> -->
          </tr>
        </template>
        <template v-slot:item.num="{ item }">
          {{
            items
              .map(function (x) {
                return x.id;
              })
              .indexOf(item.id) + from
          }}
        </template>
        <template v-slot:item.depcreator="{ item }">
          {{
            $i18n.locale == "uz_latin"
              ? item.employee && item.employee.employee_staff[0]
                ? item.employee.employee_staff[0].staff.department.name_uz_latin
                : ""
              : $i18n.locale == "uz_cyril"
              ? item.employee && item.employee.employee_staff[0]
                ? item.employee.employee_staff[0].staff.department.name_uz_cyril
                : ""
              : item.employee && item.employee.employee_staff[0]
              ? item.employee.employee_staff[0].staff.department.name_ru
              : ""
          }}
        </template>
        <template v-slot:item.document_number="{ item }">
          <!-- <v-tab :to="'/document/' + item.pdf_file_name" target="_blank"> -->
          <v-tab :to="'/ides/show/' + item.ides_document_id" target="_blank">
            <v-btn small style=" font-size: 0.8em;" rounded :color="item.status == 3 ? 'success': ides_items.filter(v=>v.id == item.ides_document_id)[0]['document_signers'].find(s=>s.organization_id==16 && s.seen_datetime)?'primary': 'danger'">
              {{ item.document_number }}
            </v-btn>
            <!-- <v-btn
              text
              style=" font-size: 0.8em;"
              small
              class="ma-0 pa-0"
              rounded
              :color="item.status == 0 ? 'black' : 'primary'"
              >{{ item.document_number }}</v-btn
            > -->
          </v-tab>
        </template>
        <template v-slot:item.document_template="{ item }">
          {{
            item.document_template &&
            item.document_template["name_" + $i18n.locale]
          }}
        </template>
        <template v-slot:item.creator="{ item }">
          {{
            item.creator && item.creator.user && item.creator.user.fio[$i18n.locale]
                ? item.creator.user.fio[$i18n.locale] : ""
          }}
        </template>
        <template v-slot:item.creator_department="{ item }">
          {{
            item.creator && item.creator.user && item.creator.user.fio[$i18n.locale]
                ? item.creator.user['department_' + $i18n.locale]
                : ""
          }}
        </template>
        <template v-slot:item.signed_dating="{ item }">
          {{
            item.document_signers.filter(v=>v.status==1&&v.action_type_id==2).length>0
              ? format_date2(item.document_signers.filter(v=>v.status==1&&v.action_type_id==2)[0].updated_at)
              : ""
          }}

        </template>
        <template v-slot:item.out_num="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 635 ||
                v.d_d_attribute_id == 721 ||
                v.d_d_attribute_id == 757 ||
                v.d_d_attribute_id == 784 ||
                v.d_d_attribute_id == 838 ||
                v.d_d_attribute_id == 811 ||
                v.d_d_attribute_id == 864 ||
                v.d_d_attribute_id == 695
            )
              ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 635 ||
                    v.d_d_attribute_id == 721 ||
                    v.d_d_attribute_id == 757 ||
                    v.d_d_attribute_id == 784 ||
                    v.d_d_attribute_id == 838 ||
                    v.d_d_attribute_id == 811 ||
                    v.d_d_attribute_id == 864 ||
                    v.d_d_attribute_id == 695
                ).value
              : ""
          }}
        </template>
        <template v-slot:item.out_date="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 653 ||
                v.d_d_attribute_id == 722 ||
                v.d_d_attribute_id == 758 ||
                v.d_d_attribute_id == 785 ||
                v.d_d_attribute_id == 839 ||
                v.d_d_attribute_id == 812 ||
                v.d_d_attribute_id == 865 ||
                v.d_d_attribute_id == 696
            )
              ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 653 ||
                    v.d_d_attribute_id == 722 ||
                    v.d_d_attribute_id == 758 ||
                    v.d_d_attribute_id == 785 ||
                    v.d_d_attribute_id == 839 ||
                    v.d_d_attribute_id == 812 ||
                    v.d_d_attribute_id == 865 ||
                    v.d_d_attribute_id == 696
                ).value
              : ""
          }}
        </template>
        <template v-slot:item.correspondent="{ item }">
          <div >
            {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
                (v) =>
                  v.d_d_attribute_id == 657 ||
                  v.d_d_attribute_id == 726 ||
                  v.d_d_attribute_id == 762 ||
                  v.d_d_attribute_id == 843 ||
                  v.d_d_attribute_id == 789 ||
                  v.d_d_attribute_id == 816 ||
                  v.d_d_attribute_id == 869 ||
                  v.d_d_attribute_id == 700
              )
                ? item.document_details[0].document_detail_contents.find(
                    (v) =>
                      v.d_d_attribute_id == 657 ||
                      v.d_d_attribute_id == 726 ||
                      v.d_d_attribute_id == 762 ||
                      v.d_d_attribute_id == 843 ||
                      v.d_d_attribute_id == 789 ||
                      v.d_d_attribute_id == 816 ||
                      v.d_d_attribute_id == 869 ||
                      v.d_d_attribute_id == 700
                  ).value
                : ""
            }}
          </div>
        </template>
        <template v-slot:item.dop_kor="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 658 ||
                v.d_d_attribute_id == 727 ||
                v.d_d_attribute_id == 763 ||
                v.d_d_attribute_id == 790 ||
                v.d_d_attribute_id == 844 ||
                v.d_d_attribute_id == 817 ||
                v.d_d_attribute_id == 870 ||
                v.d_d_attribute_id == 701
            )
              ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 658 ||
                    v.d_d_attribute_id == 727 ||
                    v.d_d_attribute_id == 763 ||
                    v.d_d_attribute_id == 790 ||
                    v.d_d_attribute_id == 844 ||
                    v.d_d_attribute_id == 817 ||
                    v.d_d_attribute_id == 870 ||
                    v.d_d_attribute_id == 701
                ).value
              : ""
          }}
        </template>
        <template v-slot:item.description="{ item }">
          <span v-if="item.description">{{item.description}}</span>
          <!-- <v-row v-else
            style="max-width: 200px"
            @click="
              modalText(
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 665 ||
                    v.d_d_attribute_id == 711 ||
                    v.d_d_attribute_id == 774 ||
                    v.d_d_attribute_id == 801 ||
                    v.d_d_attribute_id == 828 ||
                    v.d_d_attribute_id == 854 ||
                    v.d_d_attribute_id == 877 ||
                    v.d_d_attribute_id == 738
                ).value
              )
            "
          > -->
            <p v-else>
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 665 ||
                    v.d_d_attribute_id == 711 ||
                    v.d_d_attribute_id == 774 ||
                    v.d_d_attribute_id == 801 ||
                    v.d_d_attribute_id == 828 ||
                    v.d_d_attribute_id == 854 ||
                    v.d_d_attribute_id == 877 ||
                    v.d_d_attribute_id == 738
                )
                  ? item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 665 ||
                        v.d_d_attribute_id == 711 ||
                        v.d_d_attribute_id == 774 ||
                        v.d_d_attribute_id == 801 ||
                        v.d_d_attribute_id == 828 ||
                        v.d_d_attribute_id == 854 ||
                        v.d_d_attribute_id == 877 ||
                        v.d_d_attribute_id == 738
                    ).value
                  : ""
              }}
            </p>
          <!-- </v-row> -->
        </template>
        <template v-slot:item.podpisant="{ item }">
          {{ item.podpisant }}
        </template>
        <template v-slot:item.doc_expired_date="{ item }">
          {{
              item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 660 ||
                    v.d_d_attribute_id == 771 ||
                    v.d_d_attribute_id == 798 ||
                    v.d_d_attribute_id == 851 ||
                    v.d_d_attribute_id == 825 ||
                    v.d_d_attribute_id == 872 ||
                    v.d_d_attribute_id == 735
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 660 ||
                    v.d_d_attribute_id == 771 ||
                    v.d_d_attribute_id == 798 ||
                    v.d_d_attribute_id == 851 ||
                    v.d_d_attribute_id == 825 ||
                    v.d_d_attribute_id == 872 ||
                    v.d_d_attribute_id == 735
                ).value
                : ""
          }}
        </template>
        <template v-slot:item.filesList="{ item }">
          <div v-for="(file, key) in item.files" :key="key" style="font-size: 12px">
            <a
                :href="
                $store.state.backend_url + 'api/ides/file/' + file.id"

            >{{ file.file_name }}</a>
          </div>
        </template>
        
        <template v-slot:item.creator="{ item }">
          {{
            item.from_manager
          }}
        </template>
        <template v-slot:item.creator_department="{ item }">
          {{
            item.from_department
          }}
        </template>
        <template v-slot:item.podpisant="{ item }">
          {{ item.podpisant }}
        </template>
        <!-- <template v-slot:item.actions="{ item }">
          <v-btn
            class="px-1"
            color="blue"
            small
            text
            @click="changeItem(item.id)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn class="px-1" color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template> -->
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
    <!-- ides -->
    <v-dialog v-model="updateControlPunktDialog" fullscreen>
      <!-- <ides-create
        @closeDialog="updateControlPunktDialog = false"
        :document-id="thisId"
      ></ides-create> -->
    </v-dialog>
    <v-dialog v-model="showControlPunktDialog" fullscreen>
      <!-- <ides-show
        @closeDialog="showControlPunktDialog = false"
        :document-id="thisId"
      ></ides-show> -->
    </v-dialog>
    <!-- ides -->
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
// import IdesCreate from "@/components/IdesCreate";
// import IdesShow from "@/components/IdesShow";
import Swal from "sweetalert2";
import Axios from "axios";
const Cookies = require("js-cookie");
// import moment from 'moment';

export default {
  //   components: {
  //     IdesCreate,
  //     IdesShow,
  //   },
  name: "Login",
  data: () => ({
    date: null,
    documentType: [],
    changePasswordDialog: false,
    updateControlPunktDialog: false,
    showControlPunktDialog: false,
    search: "",
    thisId: null,
    loading: false,
    changePasswordForm: {},
    changeItemDialog: false,
    changeItemForm: {},
    organization: [],
    server_items_length: -1,
    from: 1,
    roles: [],
    items: [],
    ides_items: [],
    dataTableOptions: { page: 1, itemsPerPage: 50, total: 0 },
    filter: {
      id: "",
      document_number: "",
      documentType: "",
      menu_item: "",
      // document_start_date: "",
      // document_end_date: "",
      title: "",
    },
    // title_pages:  this.$t("document.document_number"),
  }),
  computed: {
    headers() {
      return [
        {
          text: this.$t("#"),
          value: "num",
          // width: 50,
          sortable: false
        },


        {
          text: this.$t("document.document_date"),
          value: "document_date",
          width: 100,
          sortable: false,
        },
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          align: "center",
          width: 150,
          sortable: false,
        },

        {
          text: this.$t("register.out_num"),
          value: "out_num",
          // width: 150,
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("register.out_date"),
          value: "out_date",
          width: 90,
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("document.correspondent"),
          value: "correspondent",
          // width: 150,
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("register.dop_kor"),
          value: "dop_kor",
          width: 90,
          sortable: false,
          visible: true,
        },
        
        // {
        //   text: this.$t("document.title"),
        //   value: "title",
        //   width: 500,
        //   sortable: false
        // },
        {
          text: this.$t("register.content"),
          value: "description",
          width: 300,
          sortable: false,
        },
        {
          text: this.$t("register.department"),
          value: "depcreator",
          sortable: false,
        },
        {
          text: this.$t("register.author"),
          value: "creator",
          sortable: false,
        },
        {
          text: this.$t("register.signer"),
          value: "podpisant",
          sortable: false,
        },
        {
          text: this.$t("register.doc_expired_date"),
          value: "doc_expired_date",
          width: 90,
          sortable: false,
          visible: true,
        },
        // {
        //   text: this.$t("register.due_date"),
        //   value: "due_date",
        //   sortable: false,
        // },
        {
          text: this.$t('files'),
          value: 'filesList',
          width: 200,
          sortable: false,
        },
      ];
    },

    screenHeight() {
      return window.innerHeight - 200;
    },
  },
  methods: {
    updatePerPage() {
      this.getList();
    },
    updatePage() {
      this.getList();
    },
    changeItem(item) {
      // console.log(item);
      this.thisId = item;
      this.updateControlPunktDialog = true;
      // this.$router.push("/documents/update/" + item);
    },
    viewItem(item) {
      // console.log('index: '+item);
      this.thisId = item;
      this.showControlPunktDialog = true;
      // this.$router.push("/documents/update/" + item);
    },
    getList() {
      this.loading == true;
      axios
      .post(this.$store.state.backend_url + "api/ides/index-new", {
        filter: this.filter,
        search: this.search,
        pagination: this.dataTableOptions,
        // path: "in",
      })
      .then((res) => {
        // console.log(res.data.data[0].document_type.name_ru);
        this.items = res.data.avtoprom_docs;
        // console.log(this.items);
        this.ides_items = res.data.ides_docs.data;
        this.server_items_length = res.data.ides_docs.total;
        this.from = res.data.ides_docs.from;
      });
      // this.loading == false;
    },
    format_date2(value) {
      if (value) {
        return moment(String(value)).format("YYYY-MM-DD");
      }
    },
    idesLogin() {
      axios
        .get(this.$store.state.backend_url + "api/ides/login")
        .then((res) => {
          // console.log(res.data);
          this.getList();
        });
    },
    // getOrgGroup() {
    //   axios
    //     .get(this.$store.state.backend_url + "api/documents/get-ref")
    //     .then((resp) => {
    //       this.documentType = resp.data.documentType;
    //       // this.documentType = resp.data.documentType.map(v=>{
    //       //   return v['name_'+ this.$i18n.locale]
    //       // });
    //       // console.log(this.documentType);
    //     });
    // },
  },
  watch: {
    $route(to, from) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.getList();
    },
  },
  mounted() {
    this.idesLogin();
    if (this.$route.params.menu_item) {
      this.filter.menu_item = this.$route.params.menu_item;
    }
    // this.getList();
    // this.getOrgGroup();
  },
};
</script>
<style scoped>
.v-item--active {
  background-color: #fff !important;
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
</style>
