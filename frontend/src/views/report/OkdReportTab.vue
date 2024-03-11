<template>
  <div>
    <v-card class="ma-2 pl-3 pr-3 pb-3">
      <v-card-title>
        {{ params[param] }}
      </v-card-title>
      <v-card-text>
        <v-simple-table
          class="mainTable"
          dense
          fixed-header
          :height="screenHeight"
        >
          <template v-slot:default>
            <thead>
              <tr>
                <th style="font-size: 15px; text-align: center">
                  {{ $t("ID") }}
                </th>
                <th style="font-size: 15px; text-align: center">
                  {{ $t("document.document_number") }}
                </th>
                <th style="font-size: 15px; text-align: center">
                  {{ $t("document.document_date") }}
                </th>
                <th style="font-size: 15px; text-align: center">
                  {{ $t("document.doers") }}
                </th>
                <th style="font-size: 15px; text-align: center">
                  {{ $t("document.short_content") }}
                </th>
                <th style="font-size: 15px; text-align: center">
                  {{ $t("document.signer_status") }}
                </th>
                <th style="font-size: 15px; text-align: center">
                  {{ $t("document.due_date") }}
                </th>
                <th style="font-size: 15px; text-align: center">
                  {{ $t("document.correspondent") }}
                </th>
              </tr>
            </thead>
            <tbody
              style="text-align: center"
              v-for="(okd, i) in okdTab"
              :key="i"
              class="mt-4"
            >
              <tr style="background-color: #696969; color: white">
                <td colspan="8">{{ okd[0][0]["name_" + $i18n.locale] }}</td>
              </tr>
              <tr v-for="(ok, t) in okd[1]" :key="t">
                <td>{{ ok.documents.id }}</td>
                <!-- <td>{{ ok.documents.document_number }}</td> -->
                <td>
                  <v-btn
                    outlined
                    small                   
                    rounded
                    :class="
                      ok.documents.action_type_id == 5
                        ? 'info'
                        : ok.documents.reaction_status == 1
                        ? 'success'
                        : ok.documents.reaction_status == 2
                        ? 'error'
                        : ok.documents.reaction_status == 3
                        ? 'deep-purple'
                        : ok.documents.reaction_status == 4
                        ? 'orange lighten-1'
                        : ''
                    "
                    :to="'/document/' + ok.documents.pdf_file_name"
                    >{{
                      ok.documents.document_number_reg
                        ? ok.documents.document_number_reg
                        : ok.documents.document_number
                    }}</v-btn
                  >
                </td>
                <td>{{ ok.documents.document_date }}</td>
                <td @click="modalSigners(ok.documents)">
                  <template
                    v-for="document_signer in ok.documents.document_signers.slice(
                      0,
                      3
                    )"
                  >
                    <div
                      style="font-size: 11px; color: blue"
                      :key="document_signer.id"
                      class="ma-0"
                      v-if="ok.documents.status != 6"
                    >
                      <div v-if="document_signer.signer_employee">
                        {{
                          document_signer.signer_employee &&
                          document_signer.signer_employee[
                            "lastname_" + language
                          ] +
                            " " +
                            document_signer.signer_employee[
                              "firstname_" + language
                            ].substr(0, 1) +
                            "." +
                            document_signer.signer_employee[
                              "middlename_" + language
                            ].substr(0, 1) +
                            "."
                        }}
                      </div>
                      <div v-else>
                        {{
                          document_signer.employee_staffs &&
                          document_signer.employee_staffs.employee[
                            "lastname_" + language
                          ] +
                            " " +
                            document_signer.employee_staffs.employee[
                              "firstname_" + language
                            ].substr(0, 1) +
                            "." +
                            document_signer.employee_staffs.employee[
                              "middlename_" + language
                            ].substr(0, 1) +
                            "."
                        }}
                      </div>
                    </div>
                  </template>
                </td>
                <td style="text-align:left; width:300px">{{ ok.documents.title }}</td>
                <td>{{ signer_status[ok.status] }}</td>
                <td>{{ (ok.due_date) }}</td>
                <td>
                  {{
                    $i18n.locale == "uz_latin"
                      ? ok.documents.employee.lastname_uz_latin +
                        " " +
                        ok.documents.employee.firstname_uz_latin.substr(0, 1) +
                        "." +
                        ok.documents.employee.middlename_uz_latin.substr(0, 1) +
                        "."
                      : ok.documents.employee.lastname_uz_cyril +
                        " " +
                        ok.documents.employee.firstname_uz_cyril.substr(0, 1) +
                        "." +
                        ok.documents.employee.middlename_uz_cyril.substr(0, 1) +
                        "."
                  }}
                </td>
              </tr>
              <tr>
                <td style="text-align: left; color: red" colspan="8">
                  {{ $t("employee.all") + ": " + okd[1].length }}
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
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
      <v-dialog v-model="signer" width="300" hide-overlay>
        <v-card color="white" style="text-align: center" class="pa-2">
          <template
            v-for="document_signer in this.all_signers.document_signers"
          >
            <div
              style="font-size: 11px"
              :key="document_signer.id"
              class="ma-0"
              v-if="all_signers.status != 6"
            >
              <div v-if="document_signer.signer_employee">
                {{
                  document_signer.signer_employee &&
                  document_signer.signer_employee["lastname_" + language] +
                    " " +
                    document_signer.signer_employee[
                      "firstname_" + language
                    ].substr(0, 1) +
                    "." +
                    document_signer.signer_employee[
                      "middlename_" + language
                    ].substr(0, 1) +
                    "."
                }}
              </div>
              <div v-else>
                {{
                  document_signer.employee_staffs &&
                  document_signer.employee_staffs.employee[
                    "lastname_" + language
                  ] +
                    " " +
                    document_signer.employee_staffs.employee[
                      "firstname_" + language
                    ].substr(0, 1) +
                    "." +
                    document_signer.employee_staffs.employee[
                      "middlename_" + language
                    ].substr(0, 1) +
                    "."
                }}
              </div>
            </div>
          </template>
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
      okdTab: [],
      loading: false,
      signer: false,
      all_signers: "",
      count_signers: 0,
      params: {
        2: this.$t("rep.params_2"),
        3: this.$t("rep.params_3"),
        8: this.$t("rep.params_8"),
      },
      signer_status: {
        0: this.$t("document.new"),
        1: this.$t("document.ok"),
        2: this.$t("document.cancel"),
        3: this.$t("document.process"),
        4: this.$t("rep.to_be_executed"),
      },
      param: "",
    };
  },
  computed: {
    countSignersLimit(document_signers) {
      return document_signers;
    },
    screenHeight() {
      return window.innerHeight - 175;
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
    

    headers() {
      return [
        { text: "ID:", value: "id", align: "center" },
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          align: "center",
          width: 200,
          sortable: false,
        },
      ];
    },
  },
  methods: {
    modalSigners(oks) {
      this.all_signers = oks;
      this.signer = true;
    },
    due_date(due_date){
      moment(due_date).format("YYYY-MM-DD");
    },
    getList() {
      let route = this.$route.params.type;
      let route_array = route.split("&");
      // console.log(route_array[0]);
      this.param = route_array[0];

      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/okd-report-tab", {
          search: this.search,
          route_array: route_array,
          language: this.$i18n.locale,
        })
        .then((response) => {
          // console.log(response);
          this.okdTab = response.data;

          this.loading = false;
        })
        .catch((error) => {
          this.errormodal = true;
          console.log(error);
          this.loading = false;
        });
    },
  },
  // watch: {
  //   $route(to, from) {
  //     this.filter.menu_item = this.$route.params.menu_item;
  //     this.filter.document_type_id = this.$route.params.document_type;
  //     this.filter.staff_id = null;
  //     // Cookies.set("filter", this.filter);
  //     this.getList();
  //   },
  // },
  mounted() {
    this.getList();
  },
   due_date(due_date){
      moment(due_date).format("YYYY-MM-DD");
    },
};
</script>
<style>
.hover :hover {
  font-size: 20px;
  color: #0a73bb;
}
</style>
