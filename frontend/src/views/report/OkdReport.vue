<template>
  <v-card class="ma-2 pl-3 pr-3 pb-3">
    <v-card-title class="pa-1">
      <span>OKD</span>
      <v-row class="pl-10">
        <v-col cols="3">
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
        <v-col cols="3">
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
        <v-col>
          <v-select
            class="mt-4"
            :label='$t("message.control_document")'
             v-model="search.controlDoc"
            :items="types"
            outlined
            :menu-props="{ bottom: true, offsetY: true }"
            dense
            clearable
            @change="getControlDoc()"
            @keyup.native.enter="getControlDoc()"
          ></v-select>
        </v-col>
        <!-- <v-col>
          <v-btn
            @click="getDoc();"
            class="ml-1 mt-4"
            color="info"
          >{{$t("message.all")}}</v-btn>
        </v-col> -->
      </v-row>
    </v-card-title>
    <v-simple-table class="mainTable" dense fixed-header>
      <template v-slot:default>
        <tbody style="text-align: center">
          <tr>
            <td rowspan="3" style="color: #0b198f">{{$t("rep.uz_ak_name")}}</td>
            <td rowspan="3" style="color: #0b198f">{{$t("rep.params_0")}}</td>
            <td colspan="8" style="color: #0b198f">{{$t("rep.done_from_them")}}</td>
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
            <td rowspan="2" style="color: #0b198f">{{$t("rep.params_1")}}</td>
            <td colspan="2" style="color: #0b198f">{{$t("rep.done_from_them")}}</td>
            <!-- <td style="color: #0b198f">с наруше- нием срока</td> -->
            <td rowspan="2" style="color: #0b198f">{{$t("rep.params_4")}}</td>
            <td colspan="3" style="color: #0b198f">{{$t("rep.to_be_executed")}}</td>
            <!-- <td style="color: #0b198f">до 2-3 дней</td>
            <td style="color: #0b198f">более 3 дней</td>-->
            <td class="hover_color" rowspan="2" style="color: #0b198f;  background-color: #B0C4DE">
              <v-tab style="font-size:10px;"
                :to="'okd-report-tab/' + 8 + '&' + search.from_date + '&' + search.to_date + '&' +  search.controlDoc"
              >{{$t("rep.params_8")}}
              </v-tab>
              </td>
          </tr>
          <tr>
            <!-- <td style="color: #0b198f"></td> -->
            <!-- <td style="color: #0b198f">Всего кол-во входящих документов</td> -->
            <!-- <td style="color: #0b198f">Выполнено</td> -->
            <td class="hover_color" style="color: #0b198f;  background-color: #B0C4DE">
              <v-tab style="font-size:10px;"
                :to="'okd-report-tab/' + 2 + '&' + search.from_date + '&' + search.to_date + '&' +  search.controlDoc"
              >{{$t("rep.params_2")}}
              </v-tab>
              </td>

            <td class="hover_color" style="color: #0b198f;  background-color: #B0C4DE">
              <v-tab style="font-size:10px;"
                :to="'okd-report-tab/' + 3 + '&' + search.from_date + '&' + search.to_date + '&' +  search.controlDoc"
              >{{$t("rep.params_3")}}
              </v-tab>
              </td>

            <!-- <td style="color: #0b198f">на исполнении</td> -->
            <td style="color: #0b198f">{{$t("rep.params_5")}}</td>
            <td style="color: #0b198f">{{$t("rep.params_6")}}</td>
            <td style="color: #0b198f">{{$t("rep.params_7")}}</td>
            <!-- <td style="color: #0b198f">Просрочен- ные документы</td> -->
          </tr>
        </tbody>
        <tbody v-for="(ok, i) in okd" :key="i">
          <tr>
            <td>{{ ok[0][0]['name_'+$i18n.locale]+ok[0][0]['id'] }}</td>
            <td class="hover" v-for="(count,t) in ok[1]" :key="count.id">
              <v-tab
                :to="'okd-report-item/'+ ok[0][0].id +'&'+  t + '&' + search.from_date + '&' + search.to_date + '&' +  search.controlDoc"
              >{{count}}</v-tab>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>

    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
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
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data() {
    return {
      search: {
        from_date: "",
        to_date: "",
        controlDoc: null
      },
      okd: [],
      types: [
        {text:this.$t("document.all"), value:1},
        {text:this.$t("department.incoming"), value:2},
        {text:this.$t("department.order"), value:3},
        {text:this.$t("department.appeal"), value:4}
        ],
      loading: false,
      errormodal: false
    };
  },
  computed: {
    headers() {
      return [
        { text: "#", value: "id", align: "center" },
        {
          // text: "Organization name",
          text: this.$t("dealers.name"),
          value: "name"
        }
      ];
    }
  },
  methods: {
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
        .post(this.$store.state.backend_url + "api/okd-report", {
          search: this.search
        })
        .then(response => {
          // console.log(response.data)
          // if (response.data = 500){
          //   this.errormodal = true;
          // }else{
          this.okd = response.data;
          // }
          // this.staffs = response.data;

          this.loading = false;
        })
        .catch(error => {
          this.errormodal = true;
          console.log(error);
          this.loading = false;
        });
    }
  },
  mounted() {
    this.getList();
  }
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
