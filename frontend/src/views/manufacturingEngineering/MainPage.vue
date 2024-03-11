<template>
  <div class="fullHeight">
    <v-card
      class="pa-5 heightFull dashboard_bg"
      style="
        border: 1px solid #ced4da;
        border-radius: 10px;
        box-shadow: none !important;
      "
    >
      <v-row class="mx-0 mb-10" style="display: flex; align-items: flex-end">
        <v-col class="pa-0" md="12" sm="12" xs="12">
          <v-card
            class="d-flex align-content-start justify-space-between"
            flat
            tile
          >
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-3"
                  width="25%"
                  :class="`elevation-${hover ? 6 : 1}`"
                  style="
                    border-radius: 5px;
                    background: #fbfcfe;
                    border: 1px solid #fff;
                    box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.14),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.12) !important;
                  "
                >
                  <v-card-title class="card_title pa-3">{{
                    $t("Все документы")
                  }}</v-card-title>
                  <v-card-title class="card_title-number px-3 pt-0 pb-3">
                    0
                  </v-card-title>
                  <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                  <v-card-title class="card_title-sub--number pa-3">
                    0
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle
                  >
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-3"
                  width="25%"
                  :class="`elevation-${hover ? 6 : 1}`"
                  style="
                    border-radius: 5px;
                    background: #fbfcfe;
                    border: 1px solid #fff;
                    box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.14),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.12) !important;
                  "
                >
                  <v-card-title class="card_title pa-3">{{
                    $t("Входящие документы")
                  }}</v-card-title>
                  <v-card-title class="card_title-number px-3 pt-0 pb-3">
                    0
                  </v-card-title>
                  <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                  <v-card-title class="card_title-sub--number pa-3">
                    0
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle
                  >
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-3"
                  width="25%"
                  :class="`elevation-${hover ? 6 : 1}`"
                  style="
                    border-radius: 5px;
                    background: #fbfcfe;
                    border: 1px solid #fff;
                    box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.14),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.12) !important;
                  "
                >
                  <v-card-title class="card_title pa-3">{{
                    $t("Исходящие документы")
                  }}</v-card-title>
                  <v-card-title class="card_title-number px-3 pt-0 pb-3">
                    0
                  </v-card-title>
                  <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                  <v-card-title class="card_title-sub--number pa-3">
                    0
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle
                  >
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-3"
                  width="25%"
                  :class="`elevation-${hover ? 6 : 1}`"
                  style="
                    border-radius: 5px;
                    background: #fbfcfe;
                    border: 1px solid #fff;
                    box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.14),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.12) !important;
                  "
                >
                  <v-card-title class="card_title pa-3">{{
                    $t("Покрасочный цех")
                  }}</v-card-title>
                  <v-card-title class="card_title-number px-3 pt-0 pb-3">
                    0
                  </v-card-title>
                  <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                  <v-card-title class="card_title-sub--number pa-3">
                    0
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle
                  >
                </v-card>
              </template>
            </v-hover>
          </v-card>
          <v-card
            class="d-flex align-content-start justify-space-between"
            flat
            tile
          >
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-3"
                  width="25%"
                  :class="`elevation-${hover ? 6 : 1}`"
                  style="
                    border-radius: 5px;
                    background: #fbfcfe;
                    border: 1px solid #fff;
                    box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.14),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.12) !important;
                  "
                >
                  <v-card-title class="card_title pa-3">{{
                    $t("Поставщики")
                  }}</v-card-title>
                  <v-card-title class="card_title-number px-3 pt-0 pb-3">
                    0
                  </v-card-title>
                  <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                  <v-card-title class="card_title-sub--number pa-3">
                    0
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle
                  >
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-3"
                  width="25%"
                  :class="`elevation-${hover ? 6 : 1}`"
                  style="
                    border-radius: 5px;
                    background: #fbfcfe;
                    border: 1px solid #fff;
                    box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.14),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.12) !important;
                  "
                >
                  <v-card-title class="card_title pa-3">{{
                    $t("Оборудование")
                  }}</v-card-title>
                  <v-card-title class="card_title-number px-3 pt-0 pb-3">
                    0
                  </v-card-title>
                  <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                  <v-card-title class="card_title-sub--number pa-3">
                    0
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle
                  >
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-3"
                  width="25%"
                  :class="`elevation-${hover ? 6 : 1}`"
                  style="
                    border-radius: 5px;
                    background: #fbfcfe;
                    border: 1px solid #fff;
                    box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.14),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.12) !important;
                  "
                >
                  <v-card-title class="card_title pa-3">{{
                    $t("Человеческий фактор")
                  }}</v-card-title>
                  <v-card-title class="card_title-number px-3 pt-0 pb-3">
                    0
                  </v-card-title>
                  <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                  <v-card-title class="card_title-sub--number pa-3">
                    0
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle
                  >
                </v-card>
              </template>
            </v-hover>
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-3"
                  width="25%"
                  :class="`elevation-${hover ? 6 : 1}`"
                  style="
                    border-radius: 5px;
                    background: #fbfcfe;
                    border: 1px solid #fff;
                    box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.14),
                      0px 0px 0px 0px rgba(0, 0, 0, 0.12) !important;
                  "
                >
                  <v-card-title class="card_title pa-3">{{
                    $t("Другие")
                  }}</v-card-title>
                  <v-card-title class="card_title-number px-3 pt-0 pb-3">
                    0
                  </v-card-title>
                  <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                  <v-card-title class="card_title-sub--number pa-3">
                    0
                  </v-card-title>
                  <v-card-subtitle
                    color="black"
                    class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle
                  >
                </v-card>
              </template>
            </v-hover>
          </v-card>
        </v-col>
        <v-col class="pa-0" md="12" sm="12" xs="12">
          <v-row class="fill-height">
            <v-col>
              <v-sheet height="64">
                <v-toolbar flat>
                  <!-- <v-btn outlined class="mr-4" color="grey darken-2" @click="setToday">Today</v-btn> -->
                  <v-toolbar-title v-if="$refs.calendar">{{
                    $refs.calendar.title
                  }}</v-toolbar-title>
                  <v-btn fab text small color="grey darken-2" @click="prev">
                    <v-icon small>mdi-chevron-left</v-icon>
                  </v-btn>
                  <v-btn fab text small color="grey darken-2" @click="next">
                    <v-icon small>mdi-chevron-right</v-icon>
                  </v-btn>
                  <v-spacer></v-spacer>
                </v-toolbar>
              </v-sheet>
              <v-sheet height="400">
                <v-calendar
                  ref="calendar"
                  v-model="focus"
                  color="primary"
                  :events="events"
                  :event-color="getEventColor"
                  :type="type"
                  @click:event="showEvent"
                  @click:more="viewDay"
                  @click:date="viewDay"
                  @change="updateRange"
                ></v-calendar>
                <v-menu
                  v-model="selectedOpen"
                  :close-on-content-click="false"
                  :activator="selectedElement"
                  offset-x
                >
                  <v-card color="grey lighten-4" min-width="350px" flat>
                    <v-toolbar :color="selectedEvent.color" dark>
                      <v-btn icon>
                        <v-icon>mdi-pencil</v-icon>
                      </v-btn>
                      <v-toolbar-title
                        v-html="selectedEvent.name"
                      ></v-toolbar-title>
                      <v-spacer></v-spacer>
                      <v-btn icon>
                        <v-icon>mdi-heart</v-icon>
                      </v-btn>
                      <v-btn icon>
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </v-toolbar>
                    <v-card-text>
                      <span v-html="selectedEvent.details"></span>
                    </v-card-text>
                    <v-card-actions>
                      <v-btn
                        text
                        color="secondary"
                        @click="selectedOpen = false"
                        >Cancel</v-btn
                      >
                    </v-card-actions>
                  </v-card>
                </v-menu>
              </v-sheet>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
      <v-row class="mx-0 mb-10 top-chart justify-center" v-if="calendarSection">
        <v-col class="pa-0" md="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                class="ma-2 text-center"
                :class="`elevation-${hover ? 8 : 3}`"
                style="height: 200px"
                >qwre</v-card
              >
            </template>
          </v-hover>
        </v-col>
      </v-row>
    </v-card>
    <!-- loading qismi boshlandi -->
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
    <!-- loading qismi tugadi -->
    <!-- Singli date qismi boshlandi -->
    <div class="text-center">
      <v-dialog v-model="dialog" width="auto">
        <v-card class="pa-5">
          <v-card-text>
            <v-card class="singleHeightFull">
              <v-row class="mx-0">
                <v-col>
                  <h1>Test</h1>
                </v-col>
              </v-row>
            </v-card>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
    <!-- Singli date qismi tugadi -->
  </div>
</template>

<script>
const axios = require("axios").default;
import DoughnutChart from "@/components/DoughnutChart";
import PieChart from "@/components/PieChart";
import Profile from "@/components/Profile";
import Cookies from "js-cookie";

export default {
  components: {
    PieChart,
    DoughnutChart,
    Profile,
  },
  name: "Home",
  data() {
    return {
      user: null,
      all_count: [],
      calendarSection: false,
      filter: {
        today: null,
        yesterday: null,
        week: null,
        month: null,
        between_dates: null,
        department_id: "",
        employee_id: "",
      },
      loading: false,
      dialog: false,
      menu: "",
      start: "",
      select: ["4 Menendjer"],
      focus: "",
      type: "month",
      typeToLabel: {
        month: "Month",
        week: "Week",
        day: "Day",
        "4day": "4 Days",
      },
      selectedEvent: {},
      selectedElement: null,
      selectedDate: null,
      selectedOpen: false,
      events: [],
      colors: [
        "blue",
        "indigo",
        "deep-purple",
        "cyan",
        "green",
        "orange",
        "grey darken-1",
      ],
      names: [
        "Meeting",
        "Holiday",
        "PTO",
        "Travel",
        "Event",
        "Birthday",
        "Conference",
        "Party",
      ],
      alert_news: null,
      notifications: [],

      focus: "",
      type: "month",
      typeToLabel: {
        month: "Month",
        week: "Week",
        day: "Day",
        "4day": "4 Days",
      },
      selectedEvent: {},
      selectedElement: null,
      selectedOpen: false,
      events: [],
      colors: [
        "blue",
        "indigo",
        "deep-purple",
        "cyan",
        "green",
        "orange",
        "grey darken-1",
      ],
      names: [
        "Meeting",
        "Holiday",
        "PTO",
        "Travel",
        "Event",
        "Birthday",
        "Conference",
        "Party",
      ],
      items: [],
      dataTableOptions: { page: 1, itemsPerPage: 10 },
      page: 1,
      from: 0,
      server_items_length: -1,
    };
  },
  computed: {
    headers() {
      let headers = [
        {
          text: "",
          value: "data-table-expand",
          align: "center",
          width: 20,
          class: "grey lighten-3",
        },
        {
          text: this.$t("Ticket ID"),
          value: "id",
          width: 50,
          class: "grey lighten-3",
        },
        {
          text: this.$t("Линия"),
          value: "plcdata.line.line",
          align: "center",
          width: 100,
          class: "grey lighten-3",
          sortable: false,
        },
        {
          text: this.$t("Цех"),
          value: "plcdata.line.shop.name",
          width: 50,
          class: "grey lighten-3",
          sortable: false,
        },
        {
          text: this.$t("Сектор"),
          value: "plcdata.sector",
          align: "center",
          width: 100,
          class: "grey lighten-3",
          sortable: false,
        },
        {
          text: this.$t("Время остановки"),
          value: "plcdata.stopdt",
          class: "grey lighten-3",
          align: "center",
          width: 100,
          sortable: false,
        },
        {
          text: this.$t("Время запуска"),
          value: "plcdata.startdt",
          class: "grey lighten-3",
          align: "center",
          width: 100,
          sortable: false,
        },
        {
          text: this.$t("Продолжительность"),
          value: "duration",
          align: "center",
          width: 100,
          class: "grey lighten-3",
          sortable: false,
        },
        {
          text: this.$t("Статус"),
          value: "status",
          align: "center",
          width: 100,
          class: "grey lighten-3",
          sortable: false,
        },
        {
          text: this.$t(""),
          value: "creater",
          width: 100,
          class: "blue-grey lighten-5 ",
          visible: true,
        },
      ];
      let filtered_headers = headers.filter((header) => {
        if (this.$route.params.document_type != 4) {
          return (
            header.value != "summary" &&
            header.value != "registration" &&
            header.value != "korrespondent" &&
            header.value != "type"
          );
        } else return headers;
      });

      return filtered_headers;
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    changeFilter(arg) {},
    getRandomColor() {
      var letters = "0123456789ABCDEF";
      var color = "#";
      for (var i = 0; i < 3; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    },
    viewDay({ date }) {
      console.log(date);
      this.selectedDate = date;
      this.dialog = true;
      this.getList();
    },
    getEventColor(event) {
      return event.color;
    },
    setToday() {
      this.focus = "";
    },
    prev() {
      this.$refs.calendar.prev();
    },
    next() {
      this.$refs.calendar.next();
    },
    showEvent({ nativeEvent, event }) {},
    updateRange({ start, end }) {},
    calendarData() {
      alert("working..");
      this.calendarSection = true;
    },
  },
  mounted() {
    this.$refs.calendar.checkChange();
    this.user = this.$store.getters.getUser();
    if (this.$store.state.notifications.alert) {
      this.alert_news = "| ";
      this.$store.state.notifications.alert.forEach((element) => {
        this.alert_news += element.content + " | ";
      });
    }
    let myInterval = setInterval(() => {
      if (this.$store.state.notifications["canceled"]) {
        this.notifications.push({
          key: "executor",
          icon: "mdi-lightning-bolt-outline",
          name: "executor",
          len: this.$store.state.notifications["length_executor"],
          value: this.$store.state.notifications["executor"],
        });
        this.notifications.push({
          key: "expired",
          icon: "mdi-fire",
          name: "expired",
          len: this.$store.state.notifications["length_expired"],
          value: this.$store.state.notifications["expired"],
        });
        this.notifications.push({
          key: "notification.nazorat",
          icon: "mdi-order-bool-ascending-variant",
          name: "nazorat",
          len: this.$store.state.notifications["length_nazorat"],
          value: this.$store.state.notifications["nazorat"],
        });
        this.notifications.push({
          key: "pending",
          icon: "mdi-timer-sand",
          name: "prosesing",
          len: this.$store.state.notifications["length_prosesing"],
          value: this.$store.state.notifications["prosesing"],
        });
        this.notifications.push({
          key: "resolutions",
          icon: "mdi-bell-plus-outline",
          name: "resolutions",
          len: this.$store.state.notifications["length_resolutions"],
          value: this.$store.state.notifications["resolutions"],
        });
        this.notifications.push({
          key: "substantiate",
          icon: "mdi-alert-outline",
          name: "substantiate",
          len: this.$store.state.notifications["length_substantiate"],
          value: this.$store.state.notifications["substantiate"],
        });
        clearInterval(myInterval);
      }
    }, 1000);
  },
};
</script>
<style scoped>
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
  color: #6c869f;
  font-size: 12px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 400;
  word-wrap: break-word;
  line-height: 1;
  letter-spacing: 0.05em;
  display: block;
  /* display: -webkit-box;
    max-width: 100%;
    height: 41px;
    margin: 0 auto;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 8px; */
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
  color: #8275ff;
  font-size: 20px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  line-height: 1;
}
.card_title-sub--number {
  color: #00b950;
  font-size: 14px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  line-height: 1;
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
  height: auto;
}
.fontSize35 {
  font-size: 30px;
}
.singleHeightFull {
  height: auto;
  border-radius: 10px 10px 10px 10px;
}
.fullHeight {
  height: calc(100% - 10px);
}
.heightFull {
  border-radius: 10px 10px 10px 10px;
}
.custom-row-class {
  min-height: 45px;
  max-height: 45px;
}
.mainTable {
  margin-top: 10px;
}
.v-data-table {
  width: 100%;
  height: 103%;
  line-height: 13px !important;
  margin: 0px 0px 0px 0px;
  border-radius: 10px 0px 10px 10px;
  border: 1px solid #aaa;
}
.DIV_search {
  /*Qidiruv qatori uchun*/
  width: 99.3%;
  height: 40px;
  margin: 2px 0px -10px 0px;
}
.DIV_row {
  color: #ffffff;
  width: 70px;
  border-radius: 5px;
  text-align: center;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 38px;
  overflow: hidden;
  border-radius: 0 0 0 0;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-item--active {
  background-color: #f1f5f8 !important;
}
.dense {
  padding: 0px;
  height: 10px !important;
}

.dense .v-text-field__details {
  display: none !important;
}

.dense .v-text-field {
  padding: 0px;
  margin: 0px;
}
.dense div div div {
  margin-bottom: 0px !important;
}
.customspan {
  border: 0.5px solid rgb(240, 237, 237);
  border-radius: 5px;
  width: 100% !important;
  height: 30px !important;
  color: blue-grey;
}
.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  white-space: normal !important;
}
.txt_searchBtn {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  height: 38px !important;
  border-radius: 0px;
}
.text_nowrap {
  display: -webkit-box;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.divSlider {
  font-size: 8px;
  margin: -10px 0px 0px 0px;
  text-align: center;
}
.department_text {
  /*v-card ичидаги бўлим номи */
  white-space: normal;
  display: inline-block;
  display: -webkit-box;
  /* max-width: 50px; */
  color: #ffffff;
  height: 25px;
  width: 90%;
  /* border:1px solid red; */
  margin: 0px 10px auto;
  background-color: #2c8dff;
  font-size: 15px;
  font-weight: 100;
  line-height: 1.6;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.divBTN {
  margin: -10px 5px auto;
  /* border:1px solid red; */
}
.divPagination {
  display: flex;
  width: 100%;
  justify-content: end;
}
.tdClass {
  display: block;
  color: #fff;
  width: 100%;
  height: 30px;
  border-radius: 15px;
  padding: 1px 5px;
  cursor: default;
  outline: none;
}
.tdTextNowRap {
  white-space: normal;
  display: inline-block;
  display: -webkit-box;
  /* max-width: 50px; */
  color: #ffffff;
  height: 25px;
  width: 90%;
  /* border:1px solid red; */
  margin: 0px 10px auto;
  background-color: #2c8dff;
  font-size: 15px;
  font-weight: 100;
  line-height: 1.6;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  border: 1px solid red;
}
.titleStyle {
  margin: -5px 0px 0px 0px;
  font-size: 18px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>
