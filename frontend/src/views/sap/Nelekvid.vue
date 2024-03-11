<template>
  <div>
    <v-card class="ma-4">
      <v-card-title primary-title>
        Nolikvid detallar bo'yicha hisobot
        <v-spacer></v-spacer>
        <span v-if="likviddate">
          {{ likviddate }} holatiga ko'ra
        </span>
      </v-card-title>
      <v-card-subtitle>
        <v-radio-group
          v-model="filter.manufacture"
          row
        >
          <v-radio
            label="Asaka"
            value="2100"
          ></v-radio>
          <v-radio
            label="Xorazm"
            value="2200"
          ></v-radio>
          <v-radio
            label="Toshkent"
            value="2300"
          ></v-radio>
          <v-btn @click="getList">Ko'rish</v-btn>
          <v-spacer></v-spacer>
          <v-btn
          v-if="likviddate"
          class="mx-3"
          @click="
            getExcel(1);
            excel = [];
          "
        >
          <!-- <span style="color: #4caf50">MC EXCEL</span> -->
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn>
          </v-radio-group>
      </v-card-subtitle>
      <v-card-text>
        <table border="1" id="table">
          <thead>
            <tr>
              <th rowspan="2" style="width: 50px">#</th>
              <!-- <th>{{ $t("Manufacture") }}</th> -->
              <th>{{ $t("Product") }}</th>
              <th>{{ $t("Days") }}</th>
              <th>{{ $t("Location") }}</th>
              <th>{{ $t("Stock") }}</th>
              <th>{{ $t("Unit") }}</th>
            </tr>
            <tr>
              <!-- <td>
                <select @change="getList" v-model.lazy="filter.manufacture">
                  <option></option>
                  <option value="2100">2100</option>
                  <option value="2200">2200</option>
                  <option value="2300">2300</option>
                </select>
              </td>-->
              <td>
                <input
                  @keyup.enter="getList"
                  id="input1"
                  type="text"
                  v-model.lazy="filter.product"
                />
              </td> 
              <td>
                <input
                  @keyup.enter="getList"
                  id="input2"
                  type="text"
                  v-model.lazy="filter.days"
                />
              </td>
              <td>
                <input
                  @keyup.enter="getList"
                  id="input3"
                  type="text"
                  v-model.lazy="filter.location"
                />
              </td>
              <td>
                <input
                  @keyup.enter="getList"
                  id="input4"
                  type="text"
                  v-model.lazy="filter.stock"
                />
              </td>
              <td>
                <input
                  @keyup.enter="getList"
                  id="input5"
                  type="text"
                  v-model.lazy="filter.unit"
                />
              </td>
            </tr>
          </thead>
          <tbody v-for="(item, key) in items" :key="key">
            <tr v-for="(i, k) in item" :key="k">
              <td class="text-center" :rowspan="item.length" v-if="k == 0">
                {{ Object.values(items).indexOf(item) + from + 1 }}
              </td>
              <!-- <td class="text-left" :rowspan="item.length" v-if="k == 0">
                {{ i.manufacture }}
              </td> -->
              <td class="text-left" :rowspan="item.length" v-if="k == 0">
                {{ i.product }}
              </td>
              <td class="text-right" :rowspan="item.length" v-if="k == 0">
                {{ i.days }}
              </td>
              <td class="text-right">{{ i.location }}</td>
              <td class="text-right">{{ i.stock }}</td>
              <td class="text-right">{{ i.unit }}</td>
            </tr>
          </tbody>
        </table>
      </v-card-text>
      <v-row class="my-0">
        <v-col></v-col>
        <v-col xl="1" lg="2" md="4">
          <v-select
            v-model="perPage"
            :items="[20, 50, 100, 200, 500, 1000]"
            color="#78909C"
            outlined
            dense
            hide-details
            @change="perPageUpdate"
          >
          </v-select>
        </v-col>
        <v-col xl="4" lg="6" md="7">
          <v-btn
            :disabled="arrow1"
            color="#78909C"
            outlined
            class="mx-1"
            @click="firstPage"
            ><v-icon>mdi-arrow-collapse-left</v-icon></v-btn
          >
          <v-btn
            :disabled="arrow2"
            color="#78909C"
            outlined
            class="mx-1"
            @click="prevPage"
            ><v-icon>mdi-arrow-left</v-icon></v-btn
          >
          {{ from }}-{{ to }} of {{ total }}
          <v-btn
            :disabled="arrow3"
            color="#78909C"
            outlined
            class="mx-1"
            @click="nextPage"
            ><v-icon>mdi-arrow-right</v-icon></v-btn
          >
          <v-btn
            :disabled="arrow4"
            color="#78909C"
            outlined
            class="mx-1"
            @click="lastPage"
            ><v-icon>mdi-arrow-collapse-right</v-icon></v-btn
          >
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
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text class="px-3">
          <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="excel"
              :name="'nolikvid_' + likviddate + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
import TableToExcel from "@linways/table-to-excel";
const moment = require("moment");
export default {
  data() {
    return {
      filter: {
        manufacture: null,
        product: null,
        days: null,
        location: null,
        stock: null,
        unit: null,
      },
      zavod: null,
      loading: false,
      items: [],
      likviddate: null,
      downloadExcel: false,
      excel: [],
      page: 1,
      perPage: 20,
      arrow1: true,
      arrow2: true,
      arrow3: true,
      arrow4: true,
      last_page: null,
      from: 0,
      total: 0,
      to: 0,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/sap/get-nolekvid-report", {
          filter: this.filter,
          page: this.page,
          perPage: this.perPage,
        })
        .then((response) => {
          this.items = response.data[0];
          if(this.items.length != 0){

            let arr = Object.keys(this.items)[0] ;
            this.likviddate = moment(String(this.items[arr][0].created_at)).format("DD.MM.YYYY"); 
            // console.log(this.likviddate);
          }
          this.from = response.data[2];
          this.total = response.data[1];
          this.to = response.data[2] + this.perPage>response.data[1]?response.data[1]:response.data[2] + this.perPage;
          this.last_page = Math.floor(response.data[1]/this.perPage);
          if (this.to != this.total) {
            this.arrow3 = false;
            this.arrow4 = false;
          } else {
            this.arrow3 = true;
            this.arrow4 = true;
          }
          if (this.from != 0) {
            this.arrow1 = false;
            this.arrow2 = false;
          } else {
            this.arrow1 = true;
            this.arrow2 = true;
          }
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"), {name: "Nolikvid_" + this.likviddate + ".xlsx"});
    },
    nextPage() {
      this.page += 1;
      this.updatePage();
    },
    prevPage() {
      this.page -= 1;
      this.updatePage();
    },
    lastPage() {
      this.page = this.last_page + 1;
      this.updatePage();
    },
    firstPage() {
      this.page = 1;
      this.updatePage();
    },
    perPageUpdate() {
      this.page = 1;
      this.updatePage();
    },
    updatePage() {
      this.getList();
    },
    getExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/sap/get-nolekvid-report", {
          filter: this.filter,
          page: page,
          perPage: 1000,
        })
        .then((response) => {
          new_array = response.data[0];
          Object.values(new_array).forEach(element => {
            this.excel = this.excel.concat(element);
          });
          if (this.excel.length % 1000 == 0) {
            this.getExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
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

<style>
table {
  border-collapse: collapse;
  width: 100%;
  background-color: white;
  border-color: #eee;
}
table > tbody > tr > td {
  padding: 0px 4px 0px;
}
table > thead > tr > th {
  text-align: center;
}
table > thead > tr > td {
  padding: 4px;
}
#input1,
#input2,
#input3,
#input4,
#input5,
select {
  padding: 2px !important;
  border: 1px solid #ccc !important;
  width: 100% !important;
}
</style>
