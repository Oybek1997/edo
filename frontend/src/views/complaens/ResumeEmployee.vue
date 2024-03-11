<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-dialog v-model="resumeDialog" width="60%" hide-overlay>
      <!-- <resume :resumeEmployee="resumeEmployee" :tabel="forTabelResume"> -->
      <resume :tabel="tabel"> </resume>
    </v-dialog>
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
import TableToExcel from "@linways/table-to-excel";
const axios = require("axios").default;
const moment = require("moment");
import resume from "@/components/resume.vue";
import Swal from "sweetalert2";
import Axios from "axios";
const Cookies = require("js-cookie");
// import moment from 'moment';
export default {
  components: {
    resume,   
  },
  data() {
    return {
      tabel:null,
      date: null,
      loading: false,
      isLoading: false,
      expanded: [],
      search: "",
      resumeDialog: false,     
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    user() {
      let localStorage = window.localStorage;
      return JSON.parse(localStorage.getItem("user"));
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    }
  },
  methods: {
    employee_resume(){
      this.resumeDialog = true;
    },
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    },
    format_date(value) {
      if (value) {
        return moment(String(value)).format("DD.MM.YYYY");
      }
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    toggleExpand($event) { },
    rowClick(item, row) {
      row.expand(!row.isExpanded);
    },
  },
  watch: {
    $route(to, from) {
      this.tabel = this.$route.params.menu_item;     
    }
  },
  mounted() {
    this.tabel = this.$route.params.menu_item;  
    this.employee_resume();

  }
};
</script>
<style scoped>
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialogTitle {
  color: #000;
  font-size: 16px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.labelTitle {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.headerSearch {
  width: 100%;
  height: 34px;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_searchBtn {
  background: #ff9f0e;
  border: 0.2px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px !important;
  height: 34px !important;
  border-radius: 1px;
  width: 25px;
  padding: 0 13px;
}
.filterBtn {
  color: #000;
  font-size: 12px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  border: 1px solid #e6e6e6;
  /* border-right: 0px; */
  border-left: 0px;
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
}
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > tbody > tr > td {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropdown-list .v-list-item .v-list-item__title {
  color: #000;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialog-head_title {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-dialog > .v-card > .v-card__text {
  padding: 0px 0px 0px 0px;
}
.doc-template_data-table table > tbody > tr > td {
  white-space: normal;
  max-width: 50px;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>