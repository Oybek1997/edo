<template>
  <div>
    {{ 'sasas' }}  

      <v-dialog v-model="loading" width="300" hide-overlay>
        <v-card color="primary" dark>
          <v-card-text style="color: white">
            {{ $t("loadingText") }}
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
  props: ["resumeEmployee", "tabel"],
  watch: {
    // resumeEmployee: function (elementNew, elementOld) {
    //   this.getAvatar(elementNew.id);
    // },
    tabel: function (elementNew, elementOld) {
      // this.filter.tabel = elementNew;
      // this.getAvatar(elementNew.id);
      this.getList(elementNew);
    },
  },
  data() {
    return {
      loading: false,
      filter: {
        tabel: "",
      },
      form: {},
      // employee
      currentUrl: window.location.href,
      dialog: false,
      employeeParties: false,
      viewId: "",
      editMode: null,
      base64: null,
      employee: [],
      Parties: [],
      isActives: [
        {
          value: 0,
          text: "Not Active",
        },
        {
          value: 1,
          text: "Active",
        },
      ],
      dialogHeaderText: "",
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
  },
  mounted() {
  },
  created() { },
};
</script>
<style scoped>
table {
  width: 100%;
}

.v-card__text {
  font-size: 1.275rem;
  font-weight: 400;
  line-height: 2.075rem;
  letter-spacing: 0.0071428571em;
  color: rgba(0, 0, 0, 1) !important;
}
</style>
