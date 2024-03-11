<template>
  <div class="fullHeight">
    <v-card class="heightFull" style="border-radius: 10px; border: 1px solid #dce5ef" elevation="0">
      <v-card-title class="px-4 py-3">
        <span>{{ $t('linestop.report_line') }}</span>
        <v-spacer></v-spacer>
        <v-responsive max-width="200" v-if="false"></v-responsive>
      </v-card-title>

      <v-card-text class="px-0">
        <v-row class="mx-0 px-5 pt-5 pb-0">
          <v-col class="py-0 pl-0 pr-5" cols="3">
            <v-text-field
              v-model="start_date"
              hide-details="auto"
              class="input_text white"
              type="datetime-local"
              :label="$t('linestop.from')"
              dense
              clearable
              outlined
              persistent-placeholder
            ></v-text-field>
          </v-col>
          <v-col class="py-0 pl-0 pr-5" cols="3">
            <v-text-field
              v-model="end_date"
              hide-details="auto"
              class="input_text white"
              type="datetime-local"
              :label="$t('linestop.to')"
              dense
              clearable
              outlined
            ></v-text-field>
          </v-col>
          <v-btn class="ma-2" outlined x-small fab color="indigo" @click="getList()">
            <v-icon>mdi-magnify</v-icon>
          </v-btn>
        </v-row>
        <v-row class="ma-2">
          <v-col cols="12">
            <v-data-table
              class="doc-template_data-table"
              :headers="headers"
              :items="items"
              :hide-default-footer="true"
              style="border: 1px solid #dce5ef; width: 100%; border-radius: 3 3 3 3;"
            >
              <template v-slot:item.id="{ item }">
                {{ items.indexOf(item) + from + 1 }}
              </template>
              <template v-slot:item.total_duration_minutes="{ item }">
                {{ parseInt(item.total_duration_minutes) }}
              </template>
            </v-data-table>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="6">
            <h3 class="ml-5" style="text-align: center"> {{ $t('linestop.ticket_diagramm') }}</h3>
            <PieChart
              v-if="chartData && chartData.datasets.length > 0"
              :data="chartData"
              :key="chartDataKey"
            />
          </v-col>
          <v-col cols="6">
            <h3 class="ml-5" style="text-align: center">{{ $t('linestop.minut_diagramm') }}</h3>
            <PieChart
              v-if="chartData2 && chartData2.datasets.length > 0"
              :data="chartData2"
              :key="chartData2Key"
            />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <v-dialog v-model="loading" width="300">
      <v-card color="primary" dark>
        <v-card-text>
          Loading...
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import PieChart from "@/components/PieChartLinestop";
const axios = require("axios").default;
const moment = require("moment");

export default {
  components: {
    PieChart,
  },
  data() {
    return {
      chartData: { labels: [], datasets: [] },
      chartData2: { labels: [], datasets: [] },
      items: [],
      from: 0,
      start_date: "2024-01-01T00:00",
      end_date: moment().format("YYYY-MM-DDTHH:mm"),
      loading: false,
      chartDataKey: 0,
      chartData2Key: 0,
      headers: [
        { text: "#", value: "id", align: "center" },
        { text: this.$t("linestop.line"), value: "line_comment" },
        { text: this.$t("linestop.all_tickets"), value: "total_tickets", align: "right" },
        {
          text: this.$t("linestop.total_minute"),
          value: "total_duration_minutes",
          align: "right",
        },
      ],
    };
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/linestops/linestatistics", {
          start_date: this.start_date,
          end_date: this.end_date,
        })
        .then((response) => {
          const data = response.data;
          this.items = data.map((item) => ({
            ...item,
            total_duration_minutes: parseFloat(item.total_duration_minutes).toFixed(2),
          }));
          this.chartData = {
            labels: this.items.map((item) => item.line_name),
            datasets: [
              {
                data: this.items.map((item) => item.total_tickets),
                backgroundColor: this.items.map(() => this.getRandomColor()),
              },
            ],
          };

          this.chartData2 = {
            labels: this.items.map((item) => item.line_name),
            datasets: [
              {
                data: this.items.map((item) => parseFloat(item.total_duration_minutes)),
                backgroundColor: this.items.map(() => this.getRandomColor()),
              },
            ],
          };
          this.chartDataKey += 1;
          this.chartData2Key += 1;
          this.loading = false;
        })
        .catch((err) => {
          console.error(err);
          this.loading = false;
        });
    },
    getRandomColor() {
      const letters = "0123456789ABCDEF";
      let color = "#";
      for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    },
  },
  mounted() {
    this.getList();
  },
};
</script>

<style scoped>
.headerTitle {
  color: #1e43a2;
  font-size: 18px;
  font-weight: 400;
  line-height: normal;
  font-style: normal;
  font-family: "Inter", sans-serif;
}
.header_same_title {
  color: #f8a300;
  font-size: 14px;
  font-weight: 500;
  line-height: normal;
  font-style: normal;
  font-family: "Inter", sans-serif;
}
.titleInputs {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  line-height: normal;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-textarea.v-text-field--enclosed.v-text-field--outlined:not(.v-input--dense)
  textarea {
  margin-top: 0px !important;
}
.inline-items {
  display: flex;
  vertical-align: middle;
}

.list-group .list-icons i {
  color: #00b950;
  font-size: 16px;
}

.list-group .list-text div {
  color: #6c869f;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.list-group .list-text span {
  color: #333;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.text_nowrap {
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.tdClass {
  display: block;
  color: #fff;
  font-size: 10px;
  font-style: normal;
  line-height: normal;
  font-weight: 600;
  max-width: 160px;
  height: 16px;
  border-radius: 15px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.tdClassnew {
  display: block;
  color: #fff;
  font-size: 9px;
  font-style: normal;
  line-height: normal;
  font-weight: 400;
  max-width: 90px;
  height: 16px;
  border-radius: 15px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropzone {
  padding: 0px !important;
  min-height: 70px !important;
}
.cardTitle {
  color: black;
  font-size: 14px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 400;
  word-wrap: break-word;
}
.bottomCol {
  display: flex;
  justify-content: center;
  align-items: end;
}

.commentButton {
  border: 1px solid #f8a300;
  background-color: #ffffff !important;
  width: 200px;
}
.form-add_employee .input_text {
  border: 1px solid #dce5ef !important;
  border-radius: 1px;
}
.input_text {
  border: 1px solid #dce5ef !important;
  border-radius: 5px;
  height: 40px;
}
.minWidthChart div .apexcharts-canvas {
  min-width: 50%;
  max-width: 60%;
}
</style>
