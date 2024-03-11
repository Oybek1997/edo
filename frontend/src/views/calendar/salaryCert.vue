<template>
  <div>
    <v-card>
      <v-card-title>
        {{$t('salary_cert')}}
        <v-spacer></v-spacer>
        <div style="width: 200px">
          <v-text-field
            v-model="tabel"
            :label="$t('employee.tabel')"
            outlined
            dense
            hide-details
            @keyup.enter="getList"
          ></v-text-field>
        </div>
        <v-btn color="blue" outlined class="ml-2" @click="getList">
          <v-icon>mdi-magnify</v-icon>
        </v-btn>
        <v-btn
          color="success"
          v-if="html[2] && html[2] != ''"
          outlined
          class="ml-2"
          @click="createDocument"
        >{{ $t("message.createInfo") }}</v-btn>
      </v-card-title>
      <v-card-text>
        <div v-html="html[1]" style="font-size: 16px; font-weight:bold;margin-bottom: 5px;"></div>
        <div v-html="html[0]"></div>
      </v-card-text>
    </v-card>
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
export default {
  // name: "Profile",
  data() {
    return {
      html: [],
      tabel: "",
      loading: false,
    };
  },
  methods: {
    getList() {
      if (this.tabel) {
        this.loading = true;
        axios
          .get(
            this.$store.state.backend_url +
              "api/salary-cart/view/" +
              this.tabel +
              "/" +
              this.$i18n.locale
          )
          .then((res) => {
            this.html = res.data;
            this.loading = false;
          })
          .catch((err) => {
            this.loading = false;
          });
      }
    },
    createDocument() {
      if (this.tabel) {
        this.loading = true;
        axios
          .get(
            this.$store.state.backend_url +
              "api/salary-cart/create/" +
              this.tabel +
              "/" +
              this.$i18n.locale
          )
          .then((res) => {
            this.html = res.data;
            if(this.html[2] != 1)
            this.$router.push("/document/" + this.html[3]);
            this.loading = false;
          })
          .catch((err) => {
            this.loading = false;
          });
      }
    },
  },
  mounted() {
    this.getList();
  },
};
</script> 
<style scoped>
.fullHeight {
  height: 100%;
}
</style>
