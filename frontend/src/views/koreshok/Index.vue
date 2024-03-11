<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <v-form ref="myForm">
          <v-row>
            <v-col cols="12" xl="2" lg="2" md="2" sm="3" xs="3" class="ml-2">
              <v-text-field
                v-if="$store.getters.checkRole('koreshok_admin')"
                v-model="tabel"
                :label="$t('profile.tabel')"
                hide-details
                dense
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="12" xl="2" lg="2" md="2" sm="3" xs="3">
              <v-select
                :items="['2017', '2018', '2019', '2020', '2021', '2022']"
                v-model="from_year"
                dense
                hide-details
                outlined
              ></v-select>
            </v-col>
            <v-col cols="12" xl="2" lg="2" md="2" sm="3" xs="3">
              <v-select
                :items="[
                  { value: '01', text: 'Январь' },
                  { value: '02', text: 'Февраль' },
                  { value: '03', text: 'Март' },
                  { value: '04', text: 'Апрель' },
                  { value: '05', text: 'Май' },
                  { value: '06', text: 'Июнь' },
                  { value: '07', text: 'Июль' },
                  { value: '08', text: 'Август' },
                  { value: '09', text: 'Сентябрь' },
                  { value: '10', text: 'Октябрь' },
                  { value: '11', text: 'Ноябрь' },
                  { value: '12', text: 'Декабрь' },
                ]"
                v-model="from_month"
                dense
                hide-details
                outlined
              ></v-select>
            </v-col>
            <v-col>
              <v-btn color="success" class @click="getList(1)">Просмотр</v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-title>
      <v-card-text>
        <v-alert type="error" :value="!status">Нет результатов</v-alert>
        <iframe
          v-if="base64"
          width="100%"
          :height="screenHeight + 20"
          :src="'data:application/pdf;base64,' + base64"
        ></iframe>
      </v-card-text>
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
export default {
  data() {
    return {
      loading: false,
      base64: "",
      from_year: "",
      to_year: "",
      from_month: "",
      to_month: "",
      tabel: "",
      status: 1
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    }
  },
  methods: {
    getList(seq) {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/koreshok", {
          from_year: this.from_year,
          from_month: this.from_month,
          to_year: this.from_year,
          to_month: this.from_month,
          tabel: this.tabel,
          seq: seq
        })
        .then(response => {
          this.base64 = response.data.base64;
          this.status = response.data.status;
          this.from_year = response.data.from_year;
          this.from_month = response.data.from_month;
          this.to_year = response.data.to_year;
          this.to_month = response.data.to_month;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    }
  },
  mounted() {
    this.from_year = moment().format("Y");
    if (moment().format("D") < 10) {
      this.from_month = moment()
        .subtract("months", 2)
        .format("MM");
    } else {
      this.from_month = moment()
        .subtract("months", 1)
        .format("MM");
    }
    this.to_year = moment().format("Y");
    this.to_month = moment().format("MM");
    let user = this.$store.getters.getUser();
    this.tabel = user.employee.tabel;
    this.getList(0);
  }
};
</script>