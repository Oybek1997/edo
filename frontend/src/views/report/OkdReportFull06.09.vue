<template>
  <v-card class="ma-2 pl-3 pr-3 pb-3">
    <v-card-title class="pa-1">
      <!-- <span :to="'/okd-report-full-toshkent'" target="_blank">OKD</span> -->
      <span>
        <v-btn
        text
            :to="'okd-report-full-toshkent'"
            target="_blank"
            color="info"
          >            
            {{$t("OKD")}}
          </v-btn>
      </span>
      <v-row class="pl-10">
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
        <v-col cols="4">
          <v-autocomplete
            class="mt-4"
            v-model="search.incoming_journal_id"
            :label="'Kiruvchi xujjat jurnallari'"
            :items="incoming_journal"
            item-value="id"
            :item-text="'name_' + $i18n.locale"
            clearable
            outlined
            hide-details
            multiple
            dense
          ></v-autocomplete>
        </v-col>
        <!-- <v-col cols="4">
          <v-select
            class="mt-4"
            :label='$t("templates")'
             v-model="search.controlDoc"
            :items="types"
            outlined
            :menu-props="{ bottom: true, offsetY: true }"
            dense
            clearable
            @change="getControlDoc()"
            @keyup.native.enter="getControlDoc()"
          ></v-select>
        </v-col> -->
        <!-- <v-spacer></v-spacer> -->
        <v-col>
          <v-btn
            :to="'/mailing/list'"
            target="_blank"
            class="mr-0 mt-4"
            color="info"
          >
            <v-icon>mdi-cog</v-icon>
            <!-- {{$t("message.all")}} -->
          </v-btn>
        </v-col>
        <v-col>
          <v-btn
            class="ml-1 mr-0 mt-4"
            @click="uploadExcel('table', 'Lorem Table')"
          >
            <v-icon color="green">mdi-download-multiple</v-icon>
            <span style="color: green">{{ $t("excel") }}</span>
          </v-btn>
        </v-col>
      </v-row>
    </v-card-title>
    <v-simple-table id="table" class="mainTable" dense fixed-header>
      <template v-slot:default>
        <tbody style="text-align: center">
          <tr>
            <td rowspan="3" style="color: #0b198f">
              {{ $t("rep.uz_ak_name") }}
            </td>
            <td rowspan="3" style="color: #0b198f">{{ $t("rep.params_0") }}</td>
            <td colspan="8" style="color: #0b198f">
              {{ $t("rep.done_from_them") }}
            </td>
            <!-- <td style="color: #0b198f">в срок</td>
          <td style="color: #0b198f">с наруше- нием срока</td>
          <td style="color: #0b198f">на исполнении</td>
          <td style="color: #0b198f">до 1 дня</td>
            <td style="color: #0b198f">до 2-3 дней</td>-->
            <!-- <td style="color: #0b198f">более 3 дней</td> -->
            <!-- <td style="color: #0b198f">Просрочен- ные документы</td> -->
          </tr>
          <tr>
            <!-- <td rowspan="2" style="color: #0b198f">Наименование структурных подразделений (управлений) и предприятии отрасли АК "Узватосаноат"</td> -->
            <!-- <td  style="color: #0b198f">Всего кол-во входящих документов</td> -->
            <td rowspan="2" style="color: #0b198f">{{ $t("rep.params_1") }}</td>
            <td colspan="2" style="color: #0b198f">
              {{ $t("rep.done_from_them") }}
            </td>
            <!-- <td style="color: #0b198f">с наруше- нием срока</td> -->
            <td rowspan="2" style="color: #0b198f">{{ $t("rep.params_4") }}</td>
            <td colspan="3" style="color: #0b198f">
              {{ $t("rep.to_be_executed") }}
            </td>
            <!-- <td style="color: #0b198f">до 2-3 дней</td>
            <td style="color: #0b198f">более 3 дней</td>-->
            <td
              class="hover_color"
              rowspan="2"
              style="color: #0b198f; background-color: #b0c4de"
            >
              <v-tab
                style="font-size: 10px"
                :to="
                  'okd-report-tab-full/' +
                  8 +
                  '&' +
                  search.from_date +
                  '&' +
                  search.to_date +
                  '&' +
                  search.controlDoc
                "
                target="_blank"
                >{{ $t("rep.params_8") }}
              </v-tab>
            </td>
          </tr>
          <tr>
            <!-- <td style="color: #0b198f"></td> -->
            <!-- <td style="color: #0b198f">Всего кол-во входящих документов</td> -->
            <!-- <td style="color: #0b198f">Выполнено</td> -->
            <td
              class="hover_color"
              style="color: #0b198f; background-color: #b0c4de"
            >
              <v-tab
                style="font-size: 10px"
                :to="
                  'okd-report-tab-full/' +
                  2 +
                  '&' +
                  search.from_date +
                  '&' +
                  search.to_date +
                  '&' +
                  search.controlDoc
                "
                target="_blank"
                >{{ $t("rep.params_2") }}
              </v-tab>
            </td>

            <td
              class="hover_color"
              style="color: #0b198f; background-color: #b0c4de"
            >
              <v-tab
                style="font-size: 10px"
                :to="
                  'okd-report-tab-full/' +
                  3 +
                  '&' +
                  search.from_date +
                  '&' +
                  search.to_date +
                  '&' +
                  search.controlDoc
                "
                target="_blank"
                >{{ $t("rep.params_3") }}
              </v-tab>
            </td>

            <!-- <td style="color: #0b198f">на исполнении</td> -->
            <td style="color: #0b198f">{{ $t("rep.params_5") }}</td>
            <td style="color: #0b198f">{{ $t("rep.params_6") }}</td>
            <td style="color: #0b198f">{{ $t("rep.params_7") }}</td>
            <!-- <td style="color: #0b198f">Просрочен- ные документы</td> -->
          </tr>
        </tbody>
        <tbody v-for="(ok, i) in okd" :key="i">
          <tr>
            <!-- <td>{{ ok[0][0]['name_'+$i18n.locale]+ok[0][0]['id'] }}</td> -->
            <td>{{ ok[0][0]["name_" + $i18n.locale] }}</td>
            <td class="hover" v-for="(count, t) in ok[1]" :key="count.id">
              <v-tab
                :to="
                  'okd-report-item-full/' +
                  ok[0][0].id +
                  '&' +
                  t +
                  '&' +
                  search.from_date +
                  '&' +
                  search.to_date +
                  '&' +
                  search.incoming_journal_id
                "
                target="_blank"
                >{{ count }}</v-tab
              >
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>

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

    <v-dialog v-model="errormodal" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          <!--          {{ // $t("errormodal") }}-->
          {{ $t("error date range!") }}
          <!--          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>-->
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-card>
</template>

<script>
const axios = require("axios").default;
import TableToExcel from "@linways/table-to-excel";
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data() {
    return {
      search: {
        from_date: moment().startOf("month").format("YYYY-MM-DD"),
        to_date: moment(new Date()).format("YYYY-MM-DD"),
        controlDoc: null,
        incoming_journal_id: "",
      },
      okd: [],
      incoming_journal: [],
      types: [
        { text: this.$t("document.all"), value: 1 },
        { text: this.$t("department.incoming"), value: 2 },
        { text: this.$t("department.order"), value: 3 },
        { text: this.$t("department.appeal"), value: 4 },
      ],
      loading: false,
      errormodal: false,
    };
  },
  computed: {
    headers() {
      return [
        { text: "#", value: "id", align: "center" },
        {
          // text: "Organization name",
          text: this.$t("dealers.name"),
          value: "name",
        },
      ];
    },
  },
  methods: {
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    },
    getControlDoc($event) {
      // this.controlDoc = $event;
      // console.log($event);
      this.getList();
    },
    getDoc() {
      this.search.controlDoc = null;
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/report-full", {
          search: this.search,
        })
        .then((response) => {
          // console.log(response.data)
          // if (response.data = 500){
          //   this.errormodal = true;
          // }else{
          this.okd = response.data;
          // }
          // this.staffs = response.data;

          this.loading = false;
        })
        .catch((error) => {
          this.errormodal = true;
          console.log(error);
          this.loading = false;
        });
    },
    getTableList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/directories", {
          pagination: "",
          search: "",
          filter: 1,
        })
        .then((response) => {
          this.incoming_journal = response.data.directories.data;
          // console.log(this.incoming_journal);
        })
        .catch((error) => {
          this.errormodal = true;
          console.log(error);
        });
    },
  },
  mounted() {
    this.getList();
    this.getTableList();
  },
};
</script>
<style scoped>
.hover :hover {
  font-size: 20px;
  color: #0a73bb;
}
.hover_color :hover {
  color: rgb(0, 0, 0);
}
</style>
