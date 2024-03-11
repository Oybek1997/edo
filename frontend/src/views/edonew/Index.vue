<template>
  <div class="pa-6">
        <span class="pa-4">{{ $t('Main content page') }}</span>
        <v-spacer></v-spacer>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t('loadingText') }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
      index: 0,
      staticInfo:[        
      ],
      dialog: false,
      editMode: null,
      currentDate: new Date().toISOString().substr(0, 7),
      newMonth: new Date().getMonth() + 1,
      menu: false,
      items: [],
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "№", value: "id", align: "center", width: 30 },

      ];
    }
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    weekend(date){
      return ["0","6"].includes(moment(date).format('d'));
    },
    getList() {
      return 'Salom Shohruh'
    },

  },
  mounted() {
    this.getList();
  }
};
</script>
<style scoped>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%!important;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
}
.dashboard_bg {
  background: #fff;
}
.dashboard__title {
  color: #205c7c;
  font-size: 40px;
  font-weight: 700;
  text-shadow: 0 2px 3px rgba(60, 60, 60, 0.25);
}
.dashboard__togglers {
  display: flex;
  justify-content: center;
}
.dashborad_date_toggler {
  background: transparent;
  /* border: 1px solid #205c7c; */
  /* border-radius: 18px; */
  pointer-events: initial;
  overflow: hidden;
}
.dashboard_manager_toggler {
  background: transparent;
  /* border: 1px solid #205c7c; */
  /* border-radius: 18px; */
  pointer-events: initial;
  overflow: hidden;
  display: flex;
  flex-direction: row;
}
.dashboard__togglers-toggler-item {
  color: #205c7c;
  height: 34px;
  text-transform: capitalize;
  font-family: "Montserrat", Sans-serif;
}
.v-input__slot fieldset {
  border: none !important;
}
.v-text-field__slot {
  color: #fff !important;
}
.card_title {
  color: #000;
  font-size: 12px;
  line-height: 1;
  letter-spacing: 0.03em;
  font-weight: 700;
  text-transform: uppercase;
  display: block;
  display: -webkit-box;
  max-width: 100%;
  height: 41px;
  margin: 0 auto;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 8px;
}
.card_sub--title {
  font-size: 13px;
  line-height: 10px;
  margin-top: 8px;
  white-space: nowrap;
  overflow: hidden;
  color: #000;
  line-height: 1.2;
}
.card_title-number {
  font-size: 43px;
  font-weight: 600;
  color: #8275ff;
}
.card_title-sub--number {
  font-size: 19px;
  font-weight: 600;
  color: #00d669;
}
.text-ellipsis {
  max-width: 100%;
  display: inline-block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.top-chart__pipeline {
  margin-right: 15px;
}
.top-chart__pipelines-item {
  text-overflow: ellipsis;
  overflow: hidden;
  max-width: 100%;
  display: inline-block;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  text-transform: uppercase;
  line-height: 18px;
  color: #205c7c;
  text-decoration: none;
}
.top-chart_title {
  color: #fff;
  text-align: center;
  font-size: 14px;
  line-height: 1;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
  /* text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25); */
}
.top-chart_sub--title {
  color: #205c7c !important;
  font-size: 12px;
  line-height: 1;
  font-weight: 600;
  text-align: center;
  cursor: pointer;
}
.top-chart-number {
  display: block;
  color: #8275ff;
  text-align: center;
  font-weight: 600;
  font-size: 45px;
  /* text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25); */
  margin-top: -10px;
}
.top-chart-sub--number {
  /* color: #000 !important; */
  text-align: center;
  font-weight: 700;
  font-size: 13px;
  /* text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25); */
}
.top-chart_btn {
  color: #205c7c;
  text-align: center;
  font-size: 14px;
  line-height: 1;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
}
#top-chart-number {
  color: #205c7c;
}
#top-chart-sub--number {
  color: #205c7c;
  text-align: center;
  font-weight: 700;
  font-size: 13px;
  /* text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25); */
}
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
}
.fontSize35 {
  font-size: 30px;
}
</style>
