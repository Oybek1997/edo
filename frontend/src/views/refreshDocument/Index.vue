<template>
  <v-container>
    <v-card
      class="heightFull dialog-form mb-4"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <legend><h1 class="headerTitle">Uvolnitelniy uchun so'rov</h1></legend>
      <v-row class="mb-9">
        <v-col cols="12" md="4">
          <v-text-field
            v-model="uvolnitelStart"
            label="Uvolnitelniy boshlanishi"
            required
            outlined
            hide-details
            type="number"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="uvolnitelEnd"
            label="Uvolnitelniy tugashi"
            required
            outlined
            hide-details
            type="number"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-btn
              class="mr-3"
              color="#3FCB5D"
              right
              small
              dark
              elevation="0"
              @click="uvolnitelSend"
              style="
                text-transform: none;
                border-radius: 5px;
                padding: 5px 20px;
              "
            >
              {{ $t("Uvolnitelniylarni olish") }}
            </v-btn>
        </v-col>
      </v-row>
    </v-card>
    <v-card
      class="heightFull dialog-form mb-4"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <legend><h1 class="headerTitle">Dokument raqamlari uchun so'rov</h1></legend>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="documentNumberStart"
            label="Dokument raqami boshlanishi"
            required
            outlined
            hide-details
            type="number"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="documentNumberEnd"
            label="Dokument raqami yakunlanishi"
            required
            outlined
            hide-details
            type="number"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-btn
              class="mr-3"
              color="#3FCB5D"
              right
              small
              dark
              elevation="0"
              @click="documentNumberSend"
              style="
                text-transform: none;
                border-radius: 5px;
                padding: 5px 20px;
              "
            >
              {{ $t("Dokument raqamlarini olish") }}
            </v-btn>
        </v-col>
      </v-row>
    </v-card>
    <v-card
      class="heightFull dialog-form"
      style="border-radius: 10px; border: 1px solid #dce5ef;"
      elevation="0"
    >
      <legend><h1 class="headerTitle">Shtamp qo'yish uchun</h1></legend>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="stampStart"
            label="Boshlanishi"
            required
            outlined
            hide-details
            type="number"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="4">
          <v-text-field
            v-model="stampEnd"
            label="Yakunlanishi"
            required
            outlined
            hide-details
            type="number"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-btn
              class="mr-3"
              color="#3FCB5D"
              right
              small
              dark
              elevation="0"
              @click="documentStampSend"
              style="
                text-transform: none;
                border-radius: 5px;
                padding: 5px 20px;
              "
            >
              {{ $t("Stamp bosish") }}
            </v-btn>
        </v-col>
      </v-row>
    </v-card>
    <p class="text-h4" :class="color">{{ this.requestNumber }}</p>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="success" dark>
        <v-card-text>
          So'rovingiz bajarilmoqda...
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
const axios = require("axios").default;

export default {
  data: () => ({
    drawer: true,
    stampStart: null,
    stampEnd: null,
    uvolnitelStart: "",
    uvolnitelEnd: "",
    requestNumber: "",
    documentNumberStart: "",
    documentNumberEnd: "",
    color: "primary--text",
    loading: false,
  }),
  metaInfo: {
    title: "Requests",
  },
  components: {},
  methods: {
    documentStampSend() {
      this.loading = true;
      let step = 5;
      this.requestNumber =
        (this.stampEnd - this.stampStart) / step + " ta so'rov qoldi...";
      let start = this.stampStart;
      let end =
        this.stampEnd - this.stampStart > step
          ? parseInt(this.stampStart) + step
          : this.stampEnd;
      // console.log(start,end,this.stampStart,this.stampEnd,step);
      axios
        .get(
          this.$store.state.backend_url +
            "api/documents/add-stamp/" +
            start +
            "/" +
            end
        )
        .then((response) => {
          console.log(response);
          if (end < this.stampEnd) {
            this.stampStart = parseInt(this.stampStart) + step;
            this.documentStampSend();
          } else {
            this.requestNumber = "So'rovlar bajarildi!";
            this.color = "success--text";
            this.loading = false;
          }
        })
        .catch((error) => {
          console.log(error);
          if (end < this.stampEnd) {
            this.stampStart = parseInt(this.stampStart) + step;
            this.documentStampSend();
          } else {
            this.requestNumber = "So'rovlar bajarildi!";
            this.color = "success--text";
            this.loading = false;
          }
        });
    },
    uvolnitelSend() {
      this.loading = true;
      this.requestNumber =
        (this.uvolnitelEnd - this.uvolnitelStart) / 100 + " ta so'rov qoldi...";
      axios
        .get(
          this.$store.state.backend_url +
            `uvolnitelniy-refresh/${this.uvolnitelStart}/${
              parseInt(this.uvolnitelStart) + 100
            }`
        )
        .then((response) => {
          console.log(response);

          this.uvolnitelStart = parseInt(this.uvolnitelStart) + 100;
          if (this.uvolnitelStart < this.uvolnitelEnd) {
            this.uvolnitelSend();
          } else {
            this.requestNumber = "So'rovlar bajarildi!";
            this.color = "success--text";
            this.loading = false;
          }
        });
    },
    documentNumberSend() {
      this.loading = true;
      this.requestNumber =
        (this.documentNumberEnd - this.documentNumberStart) / 100 +
        " ta so'rov qoldi...";
      axios
        .get(
          this.$store.state.backend_url +
            `get-document-number/${this.documentNumberStart}/${
              parseInt(this.documentNumberStart) + 100
            }`
        )
        .then((response) => {
          console.log(response);

          this.documentNumberStart = parseInt(this.documentNumberStart) + 100;
          if (this.documentNumberStart < this.documentNumberEnd) {
            this.documentNumberSend();
          } else {
            this.requestNumber = "So'rovlar bajarildi!";
            this.color = "success--text";
            this.loading = false;
          }
        });
    },
  },
};
</script>
<style>
.heightFull {
  height: 100%;
  background: #fff;
  padding: 12px;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>
