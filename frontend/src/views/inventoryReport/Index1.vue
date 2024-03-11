<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("Inventarizatsiya hisoboti") + " " }} </span>
        <v-spacer></v-spacer>
        <div> {{ Math.round(summ.topildi*10000/summ.qoldiq)/100 }} %</div>
        <v-spacer></v-spacer>
      </v-card-title>
      <v-card-text class="pb-1">
        <v-data-table
          dense
          fixed-header
          :loading-text="$t('loadingText')"
          :no-data-text="$t('noDataText')"
          :height="screenHeight"
          :loading="loading"
          :headers="headers"
          :items="items"
          :search="search"
          class="mainTable ma-1"
          style="border: 1px solid #aaa"
          item-key="id"
          :server-items-length="server_items_length"
          :options.sync="dataTableOptions"
          :disable-pagination="true"
          :footer-props="{
            itemsPerPageOptions: [50, 100, 200, -1],
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
        >
          <template v-slot:item.id="{ item }">
            {{
              items
                .map(function (x) {
                  return x.id;
                })
                .indexOf(item.id) +
              from +
              1
            }}
          </template>
          <template v-slot:item.qoldiq="{ item }">
            {{ item.qoldiq ? Math.round(item.qoldiq * 100) / 100 : "" }}
          </template>
          <template v-slot:item.topildi="{ item }">
            {{ item.topildi ? Math.round(item.topildi * 100) / 100 : "" }}
          </template>
          <template v-slot:item.bajarildi="{ item }">
            <span :style="(item.bajarildi ? Math.round(item.bajarildi * 100) / 100 : 0) > 100 ? 'background-color:#fcc; display:block;' : ''">
            {{ item.bajarildi ? Math.round(item.bajarildi * 100) / 100 : "" }}
            </span>
          </template>
          <template v-slot:item.actions="{ item }">
            <v-btn color="blue" small text @click="editItem(item)">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn color="red" small text @click="deleteItem(item)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
export default {
  data() {
    return {
      form: {
        id: 0,
        address_name: null,
        warehouse_id: null,
      },
      filter: {},
      warehouses: [],
      search: "",
      isLoading: false,
      loading: false,
      items: [],
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      page: 1,
      from: 0,
      server_items_length: -1,
      errorEmpMessage: true,
      summ: {
        qoldiq: 0,
        topildi: 0,
      },
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 220;
    },
    headers() {
      return [
        {
          text: "id",
          value: "whid",
          align: "center",
          width: 30,
          sortable: false,
        },
        {
          text: this.$t("Ombor"),
          value: "whname",
        },
        {
          text: this.$t("Qoldiq"),
          value: "qoldiq",
          align: "right",
        },
        {
          text: this.$t("Topildi"),
          value: "topildi",
          align: "right",
        },
        {
          text: this.$t("Blanka Soni"),
          value: "blanka_soni",
          align: "right",
        },
        {
          text: this.$t("Bajarildi (%)"),
          value: "bajarildi",
          align: "right",
        },
      ];
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/inventory/report")
        .then((response) => {
          this.items = response.data;
          this.items.map(v => {
            this.summ.qoldiq += v.qoldiq ? parseFloat(v.qoldiq) : 0;
            this.summ.topildi += v.topildi ? parseFloat(v.topildi) : 0;
          })
          console.log(this.summ);
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
