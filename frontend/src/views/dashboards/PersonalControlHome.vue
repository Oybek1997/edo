<template>
    <div class="fullHeight">
      <v-card
        class="pa-5 heightFull dashboard_bg"
        style="border:1px solid #CED4DA; border-radius:10px; box-shadow: none!important;"
      >
        <div
          app
          v-if="alert_news"
          dark
          style="
            display: flex;
            align-items: center;
            width: 70%;
            height: 30px;
            vertical-align: middle;
            margin: auto;
          "
        >
          <marquee
            scrollamount="10"
            style="
              color: #163e72;
              font-family: monospace;
              font-weight: bold;
              letter-spacing: 2px;
              font-size: 18px;
            "
          >{{ alert_news }}</marquee>
        </div>
        <v-row class="mx-0 mb-10" style="display: flex; align-items: flex-end">
          <v-col class="pa-0" md="12" sm="12" xs="12">
            <v-card class="d-flex align-content-start justify-space-between" flat tile>
              <v-hover>
                <template v-slot="{ hover }">
                  <v-card
                    class="ma-3"
                    width="25%"
                    :class="`elevation-${hover ? 6 : 1}`"
                    style="border-radius: 5px; background: #FBFCFE; border: 1px solid #fff; box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12)!important"
                    :to="'/documentsidebar/document-folder/list/in_out/0'"
                  >
                    <v-card-title class="card_title pa-3">{{ $t("Все документы") }}</v-card-title>
                    <v-card-title class="card_title-number px-3 pt-0 pb-3">
                      {{
                      catalog.inbox && catalog.outbox
                      ? catalog.inbox.new_count + catalog.outbox.new_count
                      : ""
                      }}
                    </v-card-title>
                    <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                    <v-card-title class="card_title-sub--number pa-3">
                      {{
                      catalog.inbox && catalog.outbox
                      ? catalog.inbox.count + catalog.outbox.count
                      : ""
                      }}
                    </v-card-title>
                    <v-card-subtitle
                      color="black"
                      class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle>
                  </v-card>
                </template>
              </v-hover>
              <v-hover>
                <template v-slot="{ hover }">
                  <v-card
                    class="ma-3"
                    width="25%"
                    :class="`elevation-${hover ? 6 : 1}`"
                    style="border-radius: 5px; background: #FBFCFE; border: 1px solid #fff; box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12)!important"
                    :to="
                      '/documentsidebar/document-folder/list/' +
                      (catalog.inbox ? catalog.inbox.originalName : '') +
                      '/0'
                    "
                  >
                    <v-card-title class="card_title pa-3">{{ $t("document.inboxs") }}</v-card-title>
                    <v-card-title
                      class="card_title-number px-3 pt-0 pb-3"
                    >{{ catalog.inbox ? catalog.inbox.new_count : "" }}</v-card-title>
                    <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                    <v-card-title class="card_title-sub--number pa-3">
                      {{
                      catalog.inbox ? catalog.inbox.count : ""
                      }}
                    </v-card-title>
                    <v-card-subtitle
                      color="black"
                      class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle>
                  </v-card>
                </template>
              </v-hover>
              <v-hover>
                <template v-slot="{ hover }">
                  <v-card
                    class="ma-3"
                    width="25%"
                    :class="`elevation-${hover ? 6 : 1}`"
                    style="border-radius: 5px; background: #FBFCFE; border: 1px solid #fff; box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12)!important"
                    :to="
                      '/documentsidebar/document-folder/list/' +
                      (catalog.outbox ? catalog.outbox.originalName : '') +
                      '/0'
                    "
                  >
                    <v-card-title class="card_title pa-3">{{ $t("document.outboxs") }}</v-card-title>
                    <v-card-title
                      class="card_title-number px-3 pt-0 pb-3"
                    >{{ catalog.outbox ? catalog.outbox.new_count : "" }}</v-card-title>
                    <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                    <v-card-title class="card_title-sub--number pa-3">
                      {{
                      catalog.outbox ? catalog.outbox.count : ""
                      }}
                    </v-card-title>
                    <v-card-subtitle
                      color="black"
                      class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle>
                  </v-card>
                </template>
              </v-hover>
              <v-hover>
                <template v-slot="{ hover }">
                  <v-card
                    class="ma-3"
                    width="25%"
                    :class="`elevation-${hover ? 6 : 1}`"
                    style="border-radius: 5px; background: #FBFCFE; border: 1px solid #fff; box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12)!important"
                    :to="
                      '/documentsidebar/document-folder/list/' +
                      (catalog.draft ? catalog.draft.originalName : '') +
                      '/0'
                    "
                  >
                    <v-card-title class="card_title pa-3">{{ $t("document.drafts") }}</v-card-title>
                    <v-card-title
                      class="card_title-number px-3 pt-0 pb-3"
                    >{{ catalog.draft ? catalog.draft.new_count : "" }}</v-card-title>
                    <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                    <v-card-title class="card_title-sub--number pa-3">
                      {{
                      catalog.draft ? catalog.draft.count : ""
                      }}
                    </v-card-title>
                    <v-card-subtitle
                      color="black"
                      class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle>
                  </v-card>
                </template>
              </v-hover>
            </v-card>
            <v-card class="d-flex align-content-start justify-space-between" flat tile>
              <v-hover>
                <template v-slot="{ hover }">
                  <v-card
                    class="ma-3"
                    width="25%"
                    :class="`elevation-${hover ? 6 : 1}`"
                    style="border-radius: 5px; background: #FBFCFE; border: 1px solid #fff; box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12)!important"
                    :to="
                      '/documentsidebar/document-folder/list/' +
                      (catalog.cancel ? catalog.cancel.originalName : '') +
                      '/0'
                    "
                  >
                    <v-card-title class="card_title pa-3">{{ $t("document.cancels") }}</v-card-title>
                    <v-card-title
                      class="card_title-number px-3 pt-0 pb-3"
                    >{{ catalog.cancel ? catalog.cancel.new_count : "" }}</v-card-title>
                    <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                    <v-card-title class="card_title-sub--number pa-3">
                      {{
                      catalog.cancel ? catalog.cancel.count : ""
                      }}
                    </v-card-title>
                    <v-card-subtitle
                      color="black"
                      class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle>
                  </v-card>
                </template>
              </v-hover>
              <v-hover>
                <template v-slot="{ hover }">
                  <v-card
                    class="ma-3"
                    width="25%"
                    :class="`elevation-${hover ? 6 : 1}`"
                    style="border-radius: 5px; background: #FBFCFE; border: 1px solid #fff; box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12)!important"
                    :to="'/documentsidebar/document-folder/list/in_out/0'"
                  >
                    <v-card-title class="card_title pa-3">{{ $t("Все документы") }}</v-card-title>
                    <v-card-title class="card_title-number px-3 pt-0 pb-3">
                      {{
                      catalog.inbox && catalog.outbox
                      ? catalog.inbox.new_count + catalog.outbox.new_count
                      : ""
                      }}
                    </v-card-title>
                    <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                    <v-card-title class="card_title-sub--number pa-3">
                      {{
                      catalog.inbox && catalog.outbox
                      ? catalog.inbox.count + catalog.outbox.count
                      : ""
                      }}
                    </v-card-title>
                    <v-card-subtitle
                      color="black"
                      class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle>
                  </v-card>
                </template>
              </v-hover>
              <v-hover>
                <template v-slot="{ hover }">
                  <v-card
                    class="ma-3"
                    width="25%"
                    :class="`elevation-${hover ? 6 : 1}`"
                    style="border-radius: 5px; background: #FBFCFE; border: 1px solid #fff; box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12)!important"
                    :to="
                      '/documentsidebar/document-folder/list/' +
                      (catalog.inbox ? catalog.inbox.originalName : '') +
                      '/0'
                    "
                  >
                    <v-card-title class="card_title pa-3">{{ $t("document.inboxs") }}</v-card-title>
                    <v-card-title
                      class="card_title-number px-3 pt-0 pb-3"
                    >{{ catalog.inbox ? catalog.inbox.new_count : "" }}</v-card-title>
                    <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                    <v-card-title class="card_title-sub--number pa-3">
                      {{
                      catalog.inbox ? catalog.inbox.count : ""
                      }}
                    </v-card-title>
                    <v-card-subtitle
                      color="black"
                      class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle>
                  </v-card>
                </template>
              </v-hover>
              <v-hover>
                <template v-slot="{ hover }">
                  <v-card
                    class="ma-3"
                    width="25%"
                    :class="`elevation-${hover ? 6 : 1}`"
                    style="border-radius: 5px; background: #FBFCFE; border: 1px solid #fff; box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12)!important"
                    :to="
                      '/documentsidebar/document-folder/list/' +
                      (catalog.outbox ? catalog.outbox.originalName : '') +
                      '/0'
                    "
                  >
                    <v-card-title class="card_title pa-3">{{ $t("document.outboxs") }}</v-card-title>
                    <v-card-title
                      class="card_title-number px-3 pt-0 pb-3"
                    >{{ catalog.outbox ? catalog.outbox.new_count : "" }}</v-card-title>
                    <v-divider class="mx-3" color="#DCE5EF"></v-divider>
                    <v-card-title class="card_title-sub--number pa-3">
                      {{
                      catalog.outbox ? catalog.outbox.count : ""
                      }}
                    </v-card-title>
                    <v-card-subtitle
                      color="black"
                      class="card_sub--title px-3 pt-0 py-3 ma-0"
                    >{{ $t("dashboard_all") }}</v-card-subtitle>
                  </v-card>
                </template>
              </v-hover>
            </v-card>
          </v-col>
          <v-col class="pa-0" md="12" sm="12" xs="12">
            <v-row class="ma-0">
              <v-col class="pa-3" md="12">
                <v-sheet height="64">
                  <v-toolbar flat class="px-0">
                    <!-- <v-btn outlined class="mr-4" color="grey darken-2" @click="setToday">Today</v-btn> -->
                    <v-toolbar-title
                      style="text-transform: capitalize; color:#5D596C; font-size: 21px; font-weight: 500;"
                      v-if="$refs.calendar"
                    >{{ $refs.calendar.title }}</v-toolbar-title>
                    <v-btn fab text small color="grey darken-2" @click="prev">
                      <v-icon small>mdi-chevron-left</v-icon>
                    </v-btn>
                    <v-btn fab text small color="grey darken-2" @click="next">
                      <v-icon small>mdi-chevron-right</v-icon>
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-menu bottom right>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn style="text-transform: capitalize; color:#5D596C; font-size: 16px; font-weight: 500;" outlined color="grey darken-2" v-bind="attrs" v-on="on">
                          <span>{{ typeToLabel[type] }}</span>
                          <v-icon right>mdi-menu-down</v-icon>
                        </v-btn>
                      </template>
                      <v-list>
                        <v-list-item @click="type = 'day'">
                          <v-list-item-title>Day</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="type = 'week'">
                          <v-list-item-title>Week</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="type = 'month'">
                          <v-list-item-title>Month</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="type = '4day'">
                          <v-list-item-title>4 days</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
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
                        <v-toolbar-title v-html="selectedEvent.name"></v-toolbar-title>
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
                        <v-btn text color="secondary" @click="selectedOpen = false">Cancel</v-btn>
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
                >qwre</v-card>
              </template>
            </v-hover>
          </v-col>
        </v-row>
        <v-row class="mx-1">
          <v-col
            cols="3"
            v-for="(item, index) in notifications.filter(v => v.value.length > 0)"
            :key="index"
          >
            <v-card :to="'/documentsidebar/document-folder/list/'+item.name+'/0'" hover outlined>
              <v-card-title class="text-h7">{{ $t(item.key) }}</v-card-title>
              <v-card-subtitle v-if="false">
                Listen to your favorite artists and albums whenever and wherever,
                online and offline.
              </v-card-subtitle>
              <v-card-title>
                <p
                  style="font-size:35px;font-weight:bold; color:#999;margin-bottom:-2px;"
                >{{ item.len }}</p>
                <v-spacer></v-spacer>
                <v-icon large>{{ item.icon }}</v-icon>
              </v-card-title>
            </v-card>
          </v-col>
        </v-row>
  
        <h2 class="text-center mb-6" v-if="my_reysters.length > 0">{{ $t("reysters") }}</h2>
        <v-row class="mx-0 mb-10 top-chart justify-center" v-if="my_reysters.length > 0">
          <v-col class="pa-0" md="3" sm="6" xs="12" v-for="(value, key) in my_reysters" :key="key">
            <v-hover>
              <template v-slot="{ hover }">
                <v-card
                  class="ma-2 text-center"
                  :class="`elevation-${hover ? 16 : 3}`"
                  :title="value['name_' + $i18n.locale]"
                  :to="'/dashboard-registry/' + value.id"
                >
                  <v-card
                    color="green"
                    class="white--text align-center elevation-0"
                    style="
                      position: relative;
                      border-bottom-right-radius: 0;
                      border-bottom-left-radius: 0;
                      height: 72px;
                    "
                  >
                    <v-card-title
                      class="top-chart_title text-ellipsis px-8"
                    >{{ value["name_" + $i18n.locale] }}</v-card-title>
                    <v-btn
                      class
                      dark
                      absolute
                      fab
                      bottom
                      left
                      color="#fff"
                      style="
                        color: #0b4c84;
                        margin-left: -10px;
                        font-size: 28px;
                        font-weight: 600;
                      "
                    >
                      {{ value.documents_count }}
                      <!-- <v-icon>mdi-plus</v-icon> -->
                    </v-btn>
                  </v-card>
                  <v-list-item class="px-0">
                    <v-list-item-content class="px-8 align-self-center" style="height: 60px">
                      <v-list-item-title
                        :title="value['name_' + $i18n.locale]"
                        class="top-chart_btn text-ellipsis text_nowrap mt-4"
                      >
                        {{ value.count }}
                        {{ value.department["name_" + $i18n.locale] }}
                      </v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-card>
              </template>
            </v-hover>
          </v-col>
        </v-row>
        <v-divider v-show="false" class="mb-10"></v-divider>
        <v-row v-if="false">
          <v-col sm="6" class="pl-6">
            <template>
              <v-simple-table>
                <template v-slot:default>
                  <tbody>
                    <tr>
                      <td>{{ $t("total_user") }}</td>
                      <td style="font-size: 18px; padding-left: 10px">
                        {{
                        user_all_count.user_all_count
                        ? user_all_count.user_all_count
                        : ""
                        }}
                      </td>
                    </tr>
                    <tr>
                      <td>{{ $t("user_eri") }}</td>
                      <td
                        style="font-size: 18px; color: green; padding-left: 10px"
                      >{{ user_eri.user_eri ? user_eri.user_eri : "" }}</td>
                    </tr>
                    <tr>
                      <td>{{ $t("user_ad") }}</td>
                      <td
                        style="font-size: 18px; color: red; padding-left: 10px"
                      >{{ user_ad.user_ad ? user_ad.user_ad : "" }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </template>
          </v-col>
          <v-col sm="6" class="pr-6">
            <template>
              <v-simple-table>
                <template v-slot:default>
                  <tbody>
                    <tr>
                      <td>{{ $t("total_documentation") }}</td>
                      <td
                        style="font-size: 18px; padding-left: 10px"
                      >{{ all_count.all_count ? all_count.all_count : "" }}</td>
                    </tr>
                    <tr>
                      <td>{{ $t("EDS_with") }}</td>
                      <td style="font-size: 18px; padding-left: 10px">
                        {{ doc_eri.doc_eri ? doc_eri.doc_eri : "" }} -
                        <span class>
                          {{
                          (
                          (100 * doc_eri.doc_eri) /
                          all_count.all_count
                          ).toFixed(2) + " %"
                          }}
                        </span>
                      </td>
                    </tr>
                    <tr>
                      <td>{{ $t("EDS_without") }}</td>
                      <td style="font-size: 18px; padding-left: 10px">
                        {{ doc_ad.doc_ad ? doc_ad.doc_ad : "" }} -
                        <span class>
                          {{
                          ((100 * doc_ad.doc_ad) / all_count.all_count).toFixed(
                          2
                          ) + " %"
                          }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </template>
          </v-col>
        </v-row>
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
  import DoughnutChart from "@/components/DoughnutChart";
  import PieChart from "@/components/PieChart";
  import Profile from "@/components/Profile";
  import Cookies from "js-cookie";
  
  export default {
    components: {
      PieChart,
      DoughnutChart,
      Profile
    },
    name: "Home",
    data() {
      return {
        user: null,
        departments: [],
        employees: [],
        all_count: [],
        doc_eri: [],
        doc_ad: [],
        user_all_count: [],
        user_eri: [],
        user_ad: [],
        calendarSection: false,
        report_by_date: {
          expired: 0,
          expired_done: 0,
          done: 0,
          pending: 0
        },
        catalog: {
          inbox: { count: 0, new_count: 0 },
          outbox: { count: 0, new_count: 0 },
          draft: { count: 0, new_count: 0 },
          cancel: { count: 0, new_count: 0 }
        },
        filter: {
          today: null,
          yesterday: null,
          week: null,
          month: null,
          between_dates: null,
          department_id: "",
          employee_id: ""
        },
        my_reysters: [],
        document_types: [],
        document_lists: [],
        source: "/",
        count_document: 0,
        loading: false,
        menu: "",
        start: "",
        select: ["4 Menendjer"],
        states: ["Alabama", "Alaska", "Arizona"],
        chartData1: [],
        chartData2: null,
  
        focus: "",
        type: "month",
        typeToLabel: {
          month: "Month",
          week: "Week",
          day: "Day",
          "4day": "4 Days"
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
          "grey darken-1"
        ],
        names: [
          "Meeting",
          "Holiday",
          "PTO",
          "Travel",
          "Event",
          "Birthday",
          "Conference",
          "Party"
        ],
        alert_news: null,
        notifications: [],
  
        focus: "",
        type: "month",
        typeToLabel: {
          month: "Month",
          week: "Week",
          day: "Day",
          "4day": "4 Days"
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
          "grey darken-1"
        ],
        names: [
          "Meeting",
          "Holiday",
          "PTO",
          "Travel",
          "Event",
          "Birthday",
          "Conference",
          "Party"
        ]
      };
    },
    methods: {
      changeFilter(arg) {},
      getRandomColor() {
        var letters = "0123456789ABCDEF";
        var color = "#";
        for (var i = 0; i < 3; i++) {
          color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
      },
      dashboard() {
        this.loading = true;
        axios
          .get(
            this.$store.state.backend_url + "api/dashboard/" + this.$i18n.locale
          )
          .then(res => {
            let data = res.data.boxes;
            this.report_by_date = res.data.report_by_date;
            this.my_reysters = res.data.my_reysters.filter(
              v => v.documents_count > 0
            );
            this.chartData1 = data.map(v => ({
              count: v.count,
              new_count: v.new_count,
              name: this.$t("message." + v.name),
              originalName: v.name
            }));
            this.catalog.inbox = this.chartData1[0];
            this.catalog.outbox = this.chartData1[1];
            this.catalog.draft = this.chartData1[2];
            this.catalog.cancel = this.chartData1[3];
            let labels = data.map(v => this.$t("message." + v.name));
            let counts = data.map(v => v.count);
            let colors = data.map(v => this.getRandomColor());
            this.chartData2 = {
              labels: labels,
              datasets: [
                {
                  backgroundColor: ["teal", "indigo", "blue", "orange", "grey"],
                  data: counts
                }
              ]
            };
            //---ma-1
            this.document_types = res.data.document_types;
            this.departments = res.data.departments;
            this.document_types.forEach(element => {
              this.count_document += element.counter;
            });
            this.loading = false;
            // this.getUserReport();
          })
          .catch(error => {
            console.log(error);
          });
      },
      getDocumentList() {
        axios
          .get(this.$store.state.backend_url + "api/documents/list")
          .then(res => {
            this.document_lists = res.data;
          })
          .catch(error => {
            console.log(error);
          });
      },
      getUserReport() {
        axios
          .get(this.$store.state.backend_url + "api/users/report")
          .then(res => {
            this.all_count = res.data.all_count;
            this.doc_eri = res.data.doc_eri;
            this.doc_ad = res.data.doc_ad;
            this.user_all_count = res.data.user_all_count;
            this.user_eri = res.data.user_eri;
            this.user_ad = res.data.user_ad;
          })
          .catch(error => {
            console.log(error);
          });
      },
      viewDay({ date }) {
        this.focus = date;
        this.type = "day";
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
      }
    },
    mounted() {
      this.$refs.calendar.checkChange();
      this.user = this.$store.getters.getUser();
      this.dashboard();
      if (this.$store.state.notifications.alert) {
        this.alert_news = "| ";
        this.$store.state.notifications.alert.forEach(element => {
          this.alert_news += element.content + " | ";
        });
      }
      let myInterval = setInterval(() => {
        if (this.$store.state.notifications["canceled"]) {
          // this.notifications.push({key:'negative', icon:'mdi-file-check-outline', name:'agreement',value:this.$store.state.notifications['agreement']});
          // this.notifications.push({key:'negative', icon:'mdi-file-cancel-outline', name:'canceled',value:this.$store.state.notifications['canceled']});
          // this.notifications.push({key:'notification.document_out_one', icon:'mdi-numeric-1-box-multiple', name:'document_out_one',value:this.$store.state.notifications['document_out_one']});
          // this.notifications.push({key:'notification.document_out_two', icon:'mdi-numeric-2-box-multiple', name:'document_out_two',value:this.$store.state.notifications['document_out_two']});
          // this.notifications.push({key:'notification.document_out_three', icon:'mdi-numeric-3-box-multiple', name:'document_out_three',value:this.$store.state.notifications['document_out_three']});
          // this.notifications.push({key:'expected', icon:'', name:'expected',value:this.$store.state.notifications['expected']});
          // this.notifications.push({key:'resolution_results', icon:'mdi-bell-check-outline', name:'resolution_results', len: this.$store.state.notifications['length_resolution_results'] value:this.$store.state.notifications['resolution_results']});
          this.notifications.push({
            key: "executor",
            icon: "mdi-lightning-bolt-outline",
            name: "executor",
            len: this.$store.state.notifications["length_executor"],
            value: this.$store.state.notifications["executor"]
          });
          this.notifications.push({
            key: "expired",
            icon: "mdi-fire",
            name: "expired",
            len: this.$store.state.notifications["length_expired"],
            value: this.$store.state.notifications["expired"]
          });
          this.notifications.push({
            key: "notification.nazorat",
            icon: "mdi-order-bool-ascending-variant",
            name: "nazorat",
            len: this.$store.state.notifications["length_nazorat"],
            value: this.$store.state.notifications["nazorat"]
          });
          this.notifications.push({
            key: "pending",
            icon: "mdi-timer-sand",
            name: "prosesing",
            len: this.$store.state.notifications["length_prosesing"],
            value: this.$store.state.notifications["prosesing"]
          });
          this.notifications.push({
            key: "resolutions",
            icon: "mdi-bell-plus-outline",
            name: "resolutions",
            len: this.$store.state.notifications["length_resolutions"],
            value: this.$store.state.notifications["resolutions"]
          });
          this.notifications.push({
            key: "substantiate",
            icon: "mdi-alert-outline",
            name: "substantiate",
            len: this.$store.state.notifications["length_substantiate"],
            value: this.$store.state.notifications["substantiate"]
          });
          // this.notifications.push({key:'for_info', icon:'mdi-information-variant', name:'information', len: this.$store.state.notifications['length_info'], value:this.$store.state.notifications['information']});
          // this.notifications.push({key:'star', icon:'mdi-star-outline', name:'star', len: this.$store.state.notifications['length_star'], value:this.$store.state.notifications['star']});
          // this.notifications.push({key:'expired', icon:'mdi-magnify', name:'watcher',value:this.$store.state.notifications['watcher']});
          // console.log(this.notifications);
          clearInterval(myInterval);
        }
      }, 1000);
    }
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
    height: auto;
  }
  .fontSize35 {
    font-size: 30px;
  }
  </style>
  