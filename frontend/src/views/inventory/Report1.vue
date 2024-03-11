@@ -0,0 +1,461 @@
<template>
  <div>
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
const moment = require("moment");
import Swal from "sweetalert2";
import Template from "../document/Template.vue";
export default {
  components: { Template },
  data() {
    return {
      loading:false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 220;
    },
    headers() {
    },
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/inventory/report", {})
        .then((res) => {
          this.items = res.data.data;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
