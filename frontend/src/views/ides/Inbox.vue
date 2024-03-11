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
          itemsPerPageOptions: [20, 50, 100],

          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right',
        }"
        item-key="id"
        @update:page="updatePage"
        @update:items-per-page="updatePerPage"
        class="ma-1 mainTable"
        style="border: 1px solid #aaa"
      >
        <template v-slot:body.prepend>
          <tr class="py-0 my-0 prepend_height">
            <!-- <td class="py-0 my-0 dense"></td> -->

            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.id"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.document_number"
                type="text"
                hide-details
                @keyup.native.enter="getList()"
                clearable
              ></v-text-field>
            </td>
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
                    >Cancel</v-btn
                  >
                  <v-btn
                    text
                    color="primary"
                    @click="
                      $refs.rangeMenu.save(date);
                      filter.document_range = date;
                      getList();
                    "
                    >OK</v-btn
                  >
                </v-date-picker>
              </v-menu>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.title"
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
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
          </tr>
        </template>
        <template v-slot:item.document_number="{ item }">
          <v-tab :to="'/ides/show/' + item.id" target="_blank">
            <v-btn
              small
              style="font-size: 0.8em"
              rounded
              :color="item.status == 3 ? 'success' : 'danger'"
            >
              {{ item.document_number }}
            </v-btn>
          </v-tab>
        </template>
        <template v-slot:item.document_date="{ item }">
          {{ moment(item.document_signers.find((v)=>v.organization_id == $store.state.ides_organization_id ).taken_datetime).format("DD-MM-YYYY") }}
        </template>
        <template v-slot:item.document_type="{ item }">{{
          item.document_type ? item.document_type.name_ru : ""
        }}</template>
        <template v-slot:item.creator="{ item }">
          {{
            item.creator &&
            item.creator.user &&
            item.creator.user.fio[$i18n.locale]
              ? item.creator.user.fio[$i18n.locale]
              : ""
          }}
        </template>
        <template v-slot:item.creator_department="{ item }">
          {{
            item.creator &&
            item.creator.user &&
            item.creator.user.fio[$i18n.locale]
              ? item.creator.user["department_" + $i18n.locale]
              : ""
          }}
        </template>
        <template v-slot:item.filesList="{ item }">
          <div
            v-for="(file, key) in item.document_files"
            :key="key"
            style="font-size: 12px"
          >
            <a :href="$store.state.backend_url + 'api/ides/file/' + file.id">{{
              file.file_name
            }}</a>
          </div>
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
    dataTableOptions: { page: 1, itemsPerPage: 20, total: 0 },
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
          text: this.$t("ID"),
          value: "id",
          width: 50,
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
          text: this.$t("document.document_date"),
          value: "document_date",
          width: 150,
          sortable: false,
        },
        {
          text: this.$t("document.title"),
          value: "title",
          width: 150,
          sortable: false,
        },
        // {
        //   text: this.$t("document.document_type"),
        //   value: "document_type",
        //   sortable: false
        // },
        {
          text: this.$t("document.creator"),
          value: "creator",
          sortable: false,
        },
        {
          text: this.$t("document.department"),
          value: "creator_department",
          sortable: false,
        },
        {
          text: this.$t("document.short_content"),
          value: "content",
          sortable: false,
        },
        {
          text: this.$t("files"),
          value: "filesList",
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
      .post(this.$store.state.backend_url + "api/ides/index", {
        filter: this.filter,
        search: this.search,
        pagination: this.dataTableOptions,
        // path: "in",
      })
      .then((res) => {
        // console.log(res.data.data[0].document_type.name_ru);
        this.items = res.data.data;
        this.server_items_length = res.data.total;
        this.from = res.data.from;
      });
      // this.loading == false;
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
