<template>
  <div>
  <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
      <!-- <v-spacer></v-spacer> -->
      <v-row>
      <v-col
        class="d-flex"
        cols="6"
        sm="4"
      >
        <v-autocomplete
          :items="departments"
          :search-input.sync="search"
          item-value="dep_id"
          item-text="text"
          v-model="selectedDepartment"
          @change="getChart(selectedDepartment.dep_id)"
          clearable
          return-object
          class="labelInput"
          elevation="0"
          hide-details
          dense
          filled
          solo
          label="Bo'imni tanlang"
          style="color: red"
        ></v-autocomplete>
      </v-col >
      <v-col
        class="d-flex"
        cols="6"
        sm="4"
      >
        <v-select
          hide-details
          dense
          solo
          item-text="title"
          item-value="id"
          :items="headcounters"
          v-model="selectedHead"
          label="Standard"
        ></v-select>
        </v-col>
        <v-spacer></v-spacer>

      </v-row>
      </v-card-title>
    </v-card>
  <div style="background-color:white;">
    <Chart v-if="orgType==10" :data="data" :chartType="chartType" :headCount = "selectedHead"></Chart>
    <Chart2 v-if="orgType==11" :data="data" :chartType="chartType" :headCount = "selectedHead"></Chart2>
    <Chart3 v-if="orgType==12" :data="data" :chartType="chartType" :headCount = "selectedHead"></Chart3>
  </div>
</div>
</template>
<script>
import * as d3 from 'd3';
import Chart from '@/components/newOrgChart';
import Chart2 from '@/components/newOrgChartNoPic';
import Chart3 from '@/components/newOrgChartShtat';
const axios = require("axios").default;
export default {
  data() {
    return {
      data: null,
      chartType: 1,
      selectedDepartment: null,
      departments: [],
      orgType: 10,
      search: null,
      headcounters: [
        { id: 1, title: "Joriy"},
        { id: 2, title: "Biznes reja"},
        { id: 3, title: "Tasdiqlangan"},
      ],
      selectedHead: 1,
    };
  },
  components: {
    Chart,
    Chart2,
    Chart3,
  },
  created() {
    this.getChart(1);
    // d3.csv(
    //   'https://raw.githubusercontent.com/bumbeishvili/sample-data/main/org.csv'
    // ).then((d) => {
    //   // console.log('fetched data', d);
    //   this.data = [
    //     { id: 1, code: '10000', name: '«UzAuto Motors» AJ', parentId: undefined},
    //     { id: 1421, code: '10000I', name: '«UzAuto Motors» AJ', parentId: 1},
    //     { id: 1574, code: '10101', name: '"BENCHMARKING" markazi (vaqtinchalik)', parentId: 1481},
    //     { id: 1696, code: '10200', name: 'Ichki kommunikatsiyalar bo‘limi', parentId: 1421},
    //     { id: 1864, code: '10201', name: 'Ichki kommunikatsiyalar bo‘limi', parentId: 1696},
    //     { id: 1799, code: '10299', name: 'Ichki kommunikatsiyalar bo‘limi', parentId: 1696},
    
  },
  methods: {
    getChart(id){
      this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/org-chart-test1", {
            id: id,
          })
          .then((res) => {
            this.data = res.data;
            console.log(this.data);
            this.loading = false;
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
    },
    getDepartments() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/linestop/getDepartments", {
          search: this.search,
        })
        .then((res) => {
          this.departments = res.data.map((v) => {
            v.text =
              v.dep_code + " " + v.fio + " " + v.department + " " + v.position;
            return v;
          });
        })
        .catch((err) => {
          console.error(err);
        });
    },
  },
  mounted() {
    // this.getChart();
    this.orgType = this.$route.params.id;
    this.getDepartments();
  },
  watch: {
    $route(to, from) {
      this.getChart(1);
      this.orgType = this.$route.params.id;
    },
  },
};
</script>
