<template>
  <div>
    <v-card
      class="ma-1 pa-1"
      v-if="
        $store.getters.checkPermission('as400_query_control') ||
        $store.getters.checkPermission('as400_download_excel')
      "
    >
      <v-card-title class="pa-1">
        <span>So'rovlardan foydalanish va tarix</span>
        <v-spacer></v-spacer>
        <v-col cols="3">
          <v-autocomplete
            v-model="queryName"
            :items="queries"
            item-text="query_name"
            item-value="id"
            x-small
            dense
            label="So'rovni tanlang"
            v-on:change="getInputs"
          ></v-autocomplete>
        </v-col>
      </v-card-title>

      <v-data-table
        dense
        fixed-header
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        class="mainTable ma-1"
        style="border: 1px solid #aaa;"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100, -1],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right',
        }"
      >
        <template v-slot:item.id="{ item, index }">{{ index + 1 }}</template>
        <template #item.query_name="{ item }">
          <v-btn v-for="(query, index) in item.as400_query"
                  :key="index" color="primary" text @click="getQueryDialog(item)">
            {{ query.query_name }}
          </v-btn>
        </template>
        <template #item.full_name="{ item }">
          <a target="_blank" :href="`${link}/#/personcontrol/profile/${item.query_by.employee_id}`">
            {{ item.query_by.employee["lastname_" + $i18n.locale] }}
            {{ item.query_by.employee["firstname_" + $i18n.locale] }}
            {{ item.query_by.employee["middlename_" + $i18n.locale] }}
          </a>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog
      v-model="queryView"
      @keydown.esc="queryView = false"
      persistent
      width="500"
    >
      <v-card>
        <v-card-title class="text-h5 grey lighten-2">
          {{ showQuery.query_name }}
        </v-card-title>

        <v-card-text>
          {{ showQuery.query }}
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="queryView = false"> Ok </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="inputDialog"
      @keydown.esc="inputDialog = false"
      persistent
      max-width="600"
    >
      <v-form ref="attributeForm">
        <v-card>
          <v-card-title class="headline grey lighten-2" primary-title>
            <span class="headline">{{ dialogHeaderText }}</span>
            <v-spacer></v-spacer>
            <v-btn
              color="red"
              outlined
              x-small
              fab
              class
              @click="inputDialog = false"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-form @keyup.native.enter="save" ref="dialogForm">
              <v-row>
                <v-col
                  cols="12"
                  sm="12"
                  v-for="(input, index) in inputArr"
                  :key="index"
                >
                  <label
                    ><b>{{ input.label.toUpperCase() }}</b></label
                  >
                  <v-text-field
                    dense
                    outlined
                    v-model="input.value"
                    hide-details
                    @keyup="replaceData"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
          <v-card-title>
            <v-text-field
              dense
              outlined
              v-model="password"
              :label="$t('Mahfiy kod')"
              hide-details="auto"
              :rules="[
                v => !!v || $t('Ushbu joy bo\'sh bo\'lmasligi kerak'),
                v =>
                  (!!v && v.length > 5) ||
                  $t('Parol uzunligi 5 dan katta bo\'lishi kerak'),
              ]"
            ></v-text-field>
          </v-card-title>
          <v-card-title>
            <span class="text-overline">
              {{ lastQuery }}
            </span>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn
              color="primary"
              :href="
                'https://edo-db2.uzautomotors.com/api/test/download/' + filename
              "
              v-if="filename"
              ><v-icon>mdi-download</v-icon> {{ filename }}</v-btn
            >
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="download"
              ><v-icon>mdi-fire</v-icon> {{ $t("Run query") }}</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
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
import Swal from "sweetalert2";
const downloadJs = require("downloadjs");
export default {
  data() {
    return {
      inputDialog: false,
      loading: false,
      search: "",
      queryName: null,
      dialog: false,
      items: [],
      queries: [],
      query: null,
      password: null,
      filename: null,
      lastQuery: null,
      queryView: false,
      showQuery: [],
      inputArr: [],
      form: {},
      link: "https://edo.uzautomotors.com",
      createdAtMenu2: false,
      employee_excel: [],
      dialogHeaderText: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        { text: "So'rov nomi", value: "query_name" },
        { text: "So'rov yuboruvchi", value: "full_name" },
        { text: "So'rov qo'shilgan vaqt", value: "use_date_time" },
      ];
    },
  },
  methods: {
    getQueryDialog(item) {
      this.queryView = true;
      this.showQuery = item;
    },
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/as400download-history")
        .then(response => {
          this.items = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getQueryList() {
      axios
        .get(this.$store.state.backend_url + "api/as400queries/getQueryList")
        .then((response) => {
          this.queries = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    download() {
      if (this.$refs.attributeForm.validate()) {
        this.loading = true;
        axios
          .post("https://edo-db2.uzautomotors.com/api/test/run-query", {
            query: this.lastQuery,
            password: this.password,
          })
          .then(res => {
            if (res.data.code == 200) {
              this.filename = res.data.message;
              this.save();
            } else {
              Swal.fire("Xatolik", res.data.message, "error");
            }
            this.loading = false;
          })
          .catch(err => {
            this.loading = false;
            Swal.fire("Xatolik", err, "error");
          });
      }
    },
    replaceData() {
      let tmp = this.query.query;
      this.inputArr.forEach(v => {
        tmp = tmp.replace("@" + v.label + "@", v.value);
      });
      this.lastQuery = tmp;
    },
    getInputs($event) {
      this.loading = true;
      this.inputDialog = true;
      this.query = this.queries.find(v => v.id == $event);
      this.lastQuery = this.query.query;
      if (this.query.query.match(/@(.*?)@/g)) {
        this.inputArr = this.query.query
          .match(/@(.*?)@/g)
          .map(v => ({ label: v.replaceAll("@", ""), value: null }));
      } else this.inputArr = [];
      this.loading = false;
    },
    save() {
      axios
        .post(
          this.$store.state.backend_url + "api/as400download-history/update",
          {
            query_name: this.queryName,
            query: this.lastQuery,
          }
        )
        .then(res => {
          this.getList();
        })
        .catch(err => {
          console.log(err);
        });
    },
  },
  mounted() {
    if (
      this.$store.getters.checkPermission("as400_query_control") ||
      this.$store.getters.checkPermission("as400_download_excel")
    ) {
      this.getQueryList();
      this.getList();
    }
  },
};
</script>
