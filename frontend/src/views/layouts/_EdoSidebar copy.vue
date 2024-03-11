<template>
  <div style="position: relative">
    <div class="ma-0" id="edoSidebar">
      <div style="height: 95%; overflow-y: auto; overflow-x: hidden">
        <v-card
          class="mt-0"
          elevation="0"
          :style="{
            borderRadius: '10px',
            border: '1px solid #dce5ef',
            margin: '0px 0px 15px 0px',
          }"
          v-for="(item, index) in items"
          :key="index"
        >
          <v-list class="px-1 list-group">
            <v-list-item style="min-height: 40px">
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>
                  <v-icon left @click="toggleVisibility(item)">{{
                    item.visible ? "mdi-chevron-up" : "mdi-chevron-down"
                  }}</v-icon>
                  <span>{{ item.titel }}</span>
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider color="#DCE5EF"></v-divider>
          <v-list class="py-0 list-group list-group_child" flat>
            <v-list-item-group v-model="item.model" color="indigo">
              <v-list-item
                v-for="(itema, indexs) in item.list.filter((v) => v.visible)"
                :key="indexs"
                :to="itema.route"
                color="blue"
                style="min-height: 38px"
                :class="{ 'no-border': indexs === item.list.length - 1 }"
                active-class="active-link"
              >
                <v-list-item-icon class="px-2 mr-2 my-auto list-icons">
                  <v-icon :color="itema.color">{{ itema.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-0 list-text">
                  <v-list-item-title>{{ itema.text }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card>
      </div>
    </div>
    <div id="edoContent">
      <router-view></router-view>
    </div>
  </div>
</template>
<script>
const axios = require("axios").default;
import Cookies from "js-cookie";
import Swal from "sweetalert2";
import { colors } from "vuetify/lib";
export default {
  data() {
    return {
      model: null,
      timeline_count: null,
      year: new Date().getFullYear(),
      navBarColor: "#ff6347",
      isShown: true,
      itemSide: true,
      items: [],
      base64: "",
      employee: {
        base64: null,
      },
      employee: {},
      staff: null,
      activeItem: null,
    };
  },
  watch: {
    $route(to, from) {
      this.getList();
    },
  },
  computed: {},
  methods: {
    toggleVisibility(item) {
      item.visible = !item.visible;
      this.itemSide = !this.itemSide;
      item.list.forEach((itema) => {
        itema.visible = !itema.visible;
      });
    },
    getList() {
      if (this.$route.matched[2].path == "/linestopsidebar") {
        this.getLineStop();
      }
      if (this.$route.matched[2].path == "/supply-transport") {
        this.getSupplyTransport();
      }
      if (this.$route.matched[2].path == "/medpunktsidebar") {
        this.getMedPunkt();
      }
      if (this.$route.matched[2].path == "/manufacturingsidebar") {
        this.getmanufacturingEngineering();
      }
      if (this.$route.matched[2].path == "/adminpanel") {
        this.adminLinks();
      }
      if (this.$route.matched[2].path == "/inventory") {
        this.inventoryLinks();
      }
      if (this.$route.matched[2].path == "/kpi-sidebar") {
        this.kpiLinks();
      }
      if (this.$route.matched[2].path == "/documentsidebar") {
        this.getDocuments();
      }
      if (this.$route.matched[2].path == "/personcontrol") {
        this.getPersonControl();
      }
      if (this.$route.matched[2].path == "/chart") {
        this.chartLinks();
      }
      if (this.$route.matched[2].path == "/centrum-sidebar") {
        this.centrumLinks();
      }
      if (this.$route.matched[2].path == "/regulatory-documents") {
        this.getRegulatoryDocuments();
      }
    },
    chartLinks() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Charts"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-bank",
              text: this.$t("Chart-1"),
              route: "/chart/1",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-2"),
              route: "/chart/2",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-3"),
              route: "/chart/3",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-4"),
              route: "/chart/4",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-5"),
              route: "/chart/5",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-6"),
              route: "/chart/6",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-7"),
              route: "/chart/7",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-8"),
              route: "/chart/8",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-9"),
              route: "/chart/9",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-10"),
              route: "/chart/10",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-11"),
              route: "/chart/11",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-12"),
              route: "/chart/12",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-13"),
              route: "/chart/13",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-14"),
              route: "/chart/14",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("Chart-15"),
              route: "/chart/15",
              color: "#4CAF50",
              visible: true,
            },
          ],
        },
      ];
    },
    getLineStop() {
      this.items = [
        {
          id: 0,
          titel: "linestop.working_with_tickets",
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: "linestop.tickets_from_autobots",
              route: "/linestopsidebar/linestop-autotickets",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-plus-outline",
              text: "linestop.create_new_ticket",
              route: "/linestopsidebar/linestop-createpage",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-check-outline",
              text: "linestop.all_tickets",
              route: "/linestopsidebar/linestop-alltickets",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: "linestop.all_open_tickets",
              route: "/linestopsidebar/linestop-allopentickets",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 1,
          titel: "linestop.reports",
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-chart-timeline",
              text: "linestop.my_stops",
              route: "/linestopsidebar/linestop-mylinestops",
              color: "#F8A300",
              visible: true,
            },
            {
              icon: "mdi-timetable",
              text: "linestop.report_line",
              route: "/linestopsidebar/linestop-reportone",
              color: "#F8A300",
              visible: true,
            },
            {
              icon: "mdi-timetable",
              text: "linestop.report_reason",
              route: "/linestopsidebar/linestop-reporttwo",
              color: "#F8A300",
              visible: true,
            },
            // {
            //   icon: "mdi-timetable",
            //   text: this.$t("Ежедневный отчет"),
            //   route: "/linestopsidebar/linestop-reportthree",
            //   color: "#F8A300",
            //   visible: false,
            // },
          ],
        },
        {
          id: 2,
          titel: "linestop.administration",
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-tree",
              text: "linestop.line",
              route: "/linestopsidebar/linestop-lines",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-vector-point",
              text: "linestop.shops",
              route: "/linestopsidebar/linestop-shops",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-format-list-bulleted",
              text: "linestop.reason_stops",
              route: "/linestopsidebar/linestop-reasons",
              color: "#4CAF50",
              visible: true,
            },
            // {
            //   icon: "mdi-backburger",
            //   text: this.$t("Меры"),
            //   route: "/linestopsidebar/linestop-tickets",
            //   color: "#4CAF50",
            //   visible: true,
            // },
            // {
            //   icon: "mdi-store",
            //   text: this.$t("Поставщики"),
            //   route: "/linestopsidebar/linestop-tickets",
            //   color: "#4CAF50",
            //   visible: true,
            // },
            // {
            //   icon: "mdi-bank",
            //   text: "linestop.list_deportment",
            //   route: "/linestopsidebar/linestop-departments",
            //   color: "#4CAF50",
            //   visible: false,
            // },
            {
              icon: "mdi-home-assistant",
              text: "linestop.provider",
              route: "/linestopsidebar/linestop-providers",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-car-sports",
              text: "linestop.model",
              route: "/linestopsidebar/linestop-productmodel",
              color: "#4CAF50",
              visible: true,
            },
            // {
            //   icon: "mdi-history",
            //   text: this.$t("Логи остановок"),
            //   route: "/linestopsidebar/linestop-tickets",
            //   color: "#4CAF50",
            //   visible: true,
            // },
            // {
            //   icon: "mdi-account-star",
            //   text: this.$t("Роли"),
            //   route: "/linestopsidebar/linestop-tickets",
            //   color: "#4CAF50",
            //   visible: true,
            // },
          ],
        },
      ];
    },
    getSupplyTransport() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Asosiy menyular"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-car-battery",
              text: this.$t("Ehtiyot qismlari"),
              route: "/supply-transport/supply-spare-parts",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-car-sports",
              text: this.$t("Transport vositalari"),
              route: "/supply-transport/vehicals",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-account-plus",
              text: this.$t("Ta’mirlovchi chilangarlar"),
              route: "/supply-transport/repair-plumber",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-account-box-outline",
              text: this.$t("Yuklagich haydovchilar"),
              route: "/supply-transport/loader-driver",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-car-wash",
              text: this.$t("Nosoz texnikalar ta’mirlash"),
              route: "/suplly-transport/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-engine-outline",
              text: this.$t("Ta’mirlash ehtiyot qismlari"),
              route: "/suplly-transport/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-wrench",
              text: this.$t("Ta’mirdan chiqarish"),
              route: "/suplly-transport/",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 1,
          titel: this.$t("Hisobotlar"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-car-connected",
              text: this.$t("Texnik xolati va taqsimoti"),
              route: "/suplly-transport/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-engine",
              text: this.$t("Ehtiyot qismlarni tahlili"),
              route: "/suplly-transport/",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 2,
          titel: this.$t("Admin"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-car-sports",
              text: this.$t("Bo'limlar"),
              route: "/supply-transport/departments",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-car-sports",
              text: this.$t("Transportlar"),
              route: "/supply-transport/transports-type",
              color: "green",
              visible: true,
            },
          ],
        },
      ];
    },
    getMedPunkt() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Asosiy oyna"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-engine",
              text: this.$t("Bo'sh sahifa"),
              route: "/medpunktsidebar/medpunkt-homepage",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-engine",
              text: this.$t("Kod diagnostikasi"),
              route: "/medpunktsidebar/medpunkt-diagnosis-codes",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-engine",
              text: this.$t("MKBX"),
              route: "/medpunktsidebar/medpunkt-hospital-diagnosis",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-engine",
              text: this.$t("Kassalik varaqasi"),
              route: "/medpunktsidebar/medpunkt-registration-period-illness",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-engine",
              text: this.$t("Dorilar"),
              route: "/medpunktsidebar/medpunkt-medicines",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-engine",
              text: this.$t("Qabul"),
              route: "/medpunktsidebar/medpunkt-registration-patients",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-engine",
              text: this.$t("Parxez taomlar"),
              route: "/medpunktsidebar/medpunkt-diet-foods",
              color: "green",
              visible: true,
            },
          ],
        },
      ];
    },
    getRegulatoryDocuments() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Asosiy oyna"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-book-multiple",
              text: this.$t("Lavozim xujjatlari"),
              route: "/regulatory-documents/staffs/table",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 1,
          titel: this.$t("Lavozim yo'riqnomalar"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-book-open",
              text: this.$t("Barcha xujjatlari"),
              route: "/regulatory-documents/documents/list/lavozim-y/57",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-bookmark-remove",
              text: this.$t("Rad etilgan xujjatlari"),
              route: "/regulatory-documents/documents/list/lavozim-y-cancel/0",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 2,
          titel: this.$t("Kasbiy yo'riqnomalar"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-book-open",
              text: this.$t("Barcha xujjatlari"),
              route: "/regulatory-documents/documents/list/kasbiy-y/59",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-bookmark-remove",
              text: this.$t("Rad etilgan xujjatlari"),
              route: "/regulatory-documents/documents/list/kasbiy-y-cancel/0",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 3,
          titel: this.$t("Tarkibiy tuzilma to'g'risidagi nizom"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-book-open",
              text: this.$t("Barcha xujjatlari"),
              route: "/regulatory-documents/documents/list/tarkibiy-t/58",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-bookmark-remove",
              text: this.$t("Rad etilgan xujjatlari"),
              route: "/regulatory-documents/documents/list/tarkibiy-t-cancel/0",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 4,
          titel: this.$t("Standartlar"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-book-open",
              text: this.$t("Barcha xujjatlari"),
              route: "/regulatory-documents/documents/list/standard/0",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-bookmark-remove",
              text: this.$t("Rad etilgan xujjatlari"),
              route: "/regulatory-documents/documents/list/standard-cancel/0",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 5,
          titel: this.$t("Jarayon xaritasi"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-book-open",
              text: this.$t("Barcha xujjatlari"),
              route: "/regulatory-documents/documents/list/karta-p/0",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-bookmark-remove",
              text: this.$t("Rad etilgan xujjatlari"),
              route: "/regulatory-documents/documents/list/karta-p-cancel/0",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 5,
          titel: this.$t("Xavflar"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-book-open",
              text: this.$t("Barcha xujjatlari"),
              route: "/regulatory-documents/documents/list/risk/0",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-bookmark-remove",
              text: this.$t("Rad etilgan xujjatlari"),
              route: "/regulatory-documents/documents/list/risk-cancel/0",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 5,
          titel: this.$t("Annulirovan"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-book-open",
              text: this.$t("Bekor qilindi"),
              route: "/regulatory-documents/documents/list/annulirovan/0",
              color: "green",
              visible: true,
            },
          ],
        },
      ];
    },
    getmanufacturingEngineering() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Yig'uv texnologiyasi"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("Texnologik jarayonlar tekshiruvlari"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-check-outline",
              text: this.$t("QCOS ma’lumotlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Tex jarayonlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("PPCR xujjatlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Method Quality Control"),
              route: "/manufacturingsidebar/method-quality-control",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Quality Control"),
              route: "/manufacturingsidebar/quality-control",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Model"),
              route: "/manufacturingsidebar/model",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Status"),
              route: "/manufacturingsidebar/status",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 1,
          titel: this.$t("Sanoat muhandisligi"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("Ergonomika"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-check-outline",
              text: this.$t("GM GMS CI xujjatlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("R2R"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Standartlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 2,
          titel: this.$t("Presslash texnologiyasi"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("Texnologik jarayonlar tekshiruvlari"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-check-outline",
              text: this.$t("Tex jarayonlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("PPCR xujjatlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Yangi loyihalar uchun ma’lumotlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Standartlar"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 3,
          titel: this.$t("Ratsionalizatorlik takliflar"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("Шаблоны документов"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-check-outline",
              text: this.$t("Реквизиты"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Группа подписантов"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Торговые каталоги"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 4,
          titel: this.$t("Integratsiya bo’linmasi"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("Шаблоны документов"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-check-outline",
              text: this.$t("Реквизиты"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Группа подписантов"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Торговые каталоги"),
              route: "/manufacturingsidebar/",
              color: "green",
              visible: true,
            },
          ],
        },
      ];
    },
    inventoryLinks() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Material uchun"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-select-inverse",
              text: this.$t("Kiritilgan ma'lumotlar"),
              route: "/inventory/mobile-inventory",
              visible: true,
            },
            {
              icon: "mdi-tablet-dashboard",
              text: this.$t("Ombor bo'yicha hisobot"),
              route: "/inventory/inventory-status",
              visible: true,
            },
            {
              icon: "mdi-tablet-dashboard",
              text: this.$t("Adress bo'yicha hisobot"),
              route: "/inventory/location-report",
              visible: true,
            },
            {
              icon: "mdi-paperclip",
              text: this.$t("Part number bo'yicha hisobot"),
              route: "/inventory/partnumber-report",
              visible: true,
            },
          ],
        },
        {
          id: 1,
          titel: this.$t("Asosiy vositalar"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-select-inverse",
              text: this.$t("Kiritilgan ma'lumotlar"),
              route: "/inventory/mobile-inventory",
              visible: true,
            },
            {
              icon: "mdi-tablet-dashboard",
              text: this.$t("Ombor bo'yicha hisobot"),
              route: "/inventory/inventory-status",
              visible: true,
            },
            {
              icon: "mdi-tablet-dashboard",
              text: this.$t("Adress bo'yicha hisobot"),
              route: "/inventory/location-report",
              visible: true,
            },
            {
              icon: "mdi-paperclip",
              text: this.$t("Part number bo'yicha hisobot"),
              route: "/inventory/partnumber-report",
              visible: true,
            },
          ],
        },
        {
          id: 2,
          titel: this.$t("Tayyor Mahsulot"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-select-inverse",
              text: this.$t("Kiritilgan ma'lumotlar"),
              route: "/inventory/mobile-inventory",
              visible: true,
            },
            {
              icon: "mdi-tablet-dashboard",
              text: this.$t("Ombor bo'yicha hisobot"),
              route: "/inventory/inventory-status",
              visible: true,
            },
            {
              icon: "mdi-tablet-dashboard",
              text: this.$t("Adress bo'yicha hisobot"),
              route: "/inventory/location-report",
              visible: true,
            },
            {
              icon: "mdi-paperclip",
              text: this.$t("Part number bo'yicha hisobot"),
              route: "/inventory/partnumber-report",
              visible: true,
            },
          ],
        },
        {
          id: 3,
          titel: this.$t("Sozlamalar"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-human-child",
              text: this.$t("Inventarizatsiya Users"),
              route: "/inventory/user-inventory",
              color: "green",
              visible: true,
            },
          ],
        },
      ];
    },
    kpiLinks() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Asosiy menyu"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-finance",
              text: this.$t("KPI"),
              route: "/kpi-sidebar/kpi-new",
              visible: true,
            },
            {
              icon: "mdi-finance",
              text: this.$t("kpi-settings"),
              route: "/kpi-sidebar/kpi-settings",
              visible: true,
            },
            {
              icon: "mdi-finance",
              text: this.$t("kpi-report"),
              route: "/kpi-sidebar/kpi-report",
              visible: true,
            },
            {
              icon: "mdi-finance",
              text: this.$t("kpi-report2"),
              route: "/kpi-sidebar/kpi-report2",
              visible: true,
            },
            {
              icon: "mdi-finance",
              text: this.$t("kpi-report3"),
              route: "/kpi-sidebar/kpi-report3",
              visible: true,
            },
            {
              icon: "mdi-finance",
              text: this.$t("kpi-report4"),
              route: "/kpi-sidebar/kpi-report4",
              visible: true,
            },
            {
              icon: "mdi-finance",
              text: this.$t("kpi-report5"),
              route: "/kpi-sidebar/kpi-report5",
              visible: true,
            },
          ],
        },
      ];
    },
    adminLinks() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Работа с пользователями"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-account-multiple-outline",
              text: this.$t("user.index"),
              route: "/adminpanel/users/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-account-tie",
              text: this.$t("user.indexDiller"),
              route: "/adminpanel/users/diller",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-account-star",
              text: this.$t("userTemplate.navigation_main"),
              route: "/adminpanel/user-template/list",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 1,
          titel: this.$t("Правила и разрешения"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-account-lock-outline",
              text: this.$t("user.role-permission"),
              route: "/adminpanel/role-permission/list",
              color: "#F8A300",
              visible: true,
            },
            {
              icon: "mdi-account-key-outline",
              text: this.$t("user.permission"),
              route: "/adminpanel/users/permission",
              color: "#F8A300",
              visible: true,
            },
            {
              icon: "mdi-account-check",
              text: this.$t("user.role_permission_menu"),
              route: "/adminpanel/users/role-permission",
              color: "#F8A300",
              visible: true,
            },
          ],
        },
        {
          id: 2,
          titel: this.$t("Другие"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-bank",
              text: this.$t("about-company"),
              route: "/adminpanel/companies/about-company",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("company-indicator"),
              route: "/adminpanel/companies/company-indicator",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bank",
              text: this.$t("company.index"),
              route: "/adminpanel/companies/list",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-map-marker-outline",
              text: this.$t("message.countries"),
              route: "/adminpanel/countries/list",
              visible: true,
            },
            {
              icon: "mdi-map-marker-circle",
              text: this.$t("message.regions"),
              route: "/adminpanel/regions/list",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-flag",
              text: this.$t("message.natioanalities"),
              route: "/adminpanel/nationalities/list",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-google-maps",
              text: this.$t("message.districts"),
              route: "/adminpanel/districts/list",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-bell",
              text: this.$t("message.notifications"),
              route: "/adminpanel/notifications/list",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-folder-open",
              text: this.$t("refreshDocument"),
              route: "/adminpanel/refresh-document/list",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-account-lock-outline",
              text: this.$t("Template Report"),
              route: "/adminpanel/template",
              color: "#4CAF50",
              visible: true,
            },
          ],
        },
      ];
    },
    getDocuments() {
      this.items = [
        {
          id: 0,
          titel: this.$t("sideConsole.create_document"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("created_documents"),
              route: "/documentsidebar/document-folder/1",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-check-outline",
              text: this.$t("favorit_documents"),
              route: "/documentsidebar/template-favorite",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("often_documents"),
              route: "/documentsidebar/document-often",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 1,
          titel: this.$t("sideConsole.folder_document"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-folder-download-outline",
              text: this.$t("message.inbox"),
              route: "/documentsidebar/document-folder/2",
              color: "#f8a300",
              visible: true,
            },
            {
              icon: "mdi-folder-upload-outline",
              text: this.$t("message.outbox"),
              route: "/documentsidebar/document-folder/3",
              color: "#f8a300",
              visible: true,
            },
            {
              icon: "mdi-folder-text-outline",
              text: this.$t("message.draft"),
              route: "/documentsidebar/document-folder/4",
              color: "#f8a300",
              visible: true,
            },
            {
              icon: "mdi-folder-remove-outline",
              text: this.$t("message.cancel"),
              route: "/documentsidebar/document-folder/5",
              color: "#f8a300",
              visible: true,
            },
            {
              icon: "mdi-folder-outline",
              text: this.$t("all_documents"),
              route: "/documentsidebar/document-folder/list/all/0",
              color: "#f8a300",
              visible: true,
            },
          ],
        },
        {
          id: 2,
          titel: this.$t("sideConsole.folder_report"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-document-outline",
              text: this.$t("report.my_documents"),
              route: "/documentsidebar/my-document-report",
              visible: true,
              color: "green",
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("message.lsp_report"),
              route: "/documentsidebar/report/lsp",
              visible: true,
              color: "green",
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("department.index"),
              route: "/documentsidebar/reports/department/0",
              visible: true,
              color: "green",
            },
            {
              icon: "mdi-folder-open",
              text: this.$t("control_punkt.report"),
              route: "/documentsidebar/control-punkt-report",
              visible: true,
              color: "green",
            },
            {
              icon: "mdi-file-check-outline",
              text: this.$t("message.znz"),
              route: "/documentsidebar/report/znz",
              visible: true,
              color: "green",
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("department.okd"),
              route: "/documentsidebar/okd-report-full",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              // text: "Контроль исполнения документов(детальный)",
              text: this.$t("department.myokddetail"),
              route: "/documentsidebar/okd-report-full-toshkent",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("department.myokd"),
              route: "/documentsidebar/document-report-employee",
              color: "green",
              visible: true,
            },
            // {
            //   icon: "mdi-file-document-outline",
            //   text: this.$t("my_documents"),
            //   route: "/documentsidebar/document-attribute-report/my",
            //   visible: true,
            //   color: "green",
            // },
            // {
            //   icon: "mdi-file-document-outline",
            //   text: this.$t("my_documents_2"),
            //   route: "/documentsidebar/document-attribute-report/my-inbox",
            //   visible: true,
            //   color: "green",
            // },

            // {
            //   icon: "mdi-file-document-outline",
            //   text: this.$t("all_documents"),
            //   route: "/documentsidebar/document-attribute-report/all",
            //   visible: true,
            //   color: "green",
            // },
            // {
            //   icon: "mdi-folder-open",
            //   text: this.$t("Template reports"),
            //   route: "/documentsidebar/document-attribute-report/selected",
            //   visible: true,
            //   color: "green",
            // },
          ],
        },
        {
          id: 0,
          titel: this.$t("Параметры"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-clipboard-file-outline",
              text: this.$t("message.document_template"),
              route: "/documentsidebar/document-templates/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-folder-open",
              text: this.$t("message.requisites"),
              route: "/documentsidebar/company-requisites/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-checkbox-multiple-marked-outline",
              text: this.$t("message.signers_group"),
              route: "/documentsidebar/signers-group/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-checkbox-multiple-marked-outline",
              text: this.$t("Imzoda qatnashmaydiganlar"),
              route: "/documentsidebar/signers-group/notsigner",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-checkbox-multiple-marked-outline",
              text: this.$t("purchase_catalogs.catalogs"),
              route: "/documentsidebar/purchase-catalogs/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-checkbox-multiple-marked-outline",
              text: this.$t("partners.index"),
              route: "/documentsidebar/partners/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-folder-open",
              text: this.$t("message.document_types"),
              route: "/documentsidebar/document-types/list",
              color: "green",
              visible: true,
            },
            // {
            //   icon: "mdi-folder-open",
            //   text: this.$t("ChiefEmployee"),
            //   route: "/documentsidebar/chief-employee/list",
            //   color: "green",
            //   visible: true,
            // },
            // {
            //   icon: "mdi-folder-open",
            //   text: this.$t("message.unioncom"),
            //   route: "/documentsidebar/unioncom/list",
            //   color: "green",
            //   visible: true,
            // },
            // {
            //   icon: "mdi-folder-open",
            //   text: this.$t("message.bank"),
            //   route: "/documentsidebar/bank/list",
            //   color: "green",
            //   visible: true,
            // },
            // {
            //   icon: "mdi-folder-open",
            //   text: this.$t("message.tmib"),
            //   color: "green",
            //   route: "/documentsidebar/tmib/list",
            //   visible: true,
            // },
            // {
            //   icon: "mdi-folder-open",
            //   text: this.$t("message.tmig"),
            //   route: "/documentsidebar/tmig/list",
            //   color: "green",
            //   visible: true,
            // },
            // {
            //   icon: "mdi-folder-open",
            //   text: this.$t("message.embassy"),
            //   route: "/documentsidebar/embassy/list",
            //   color: "green",
            //   visible: true,
            // },
            {
              icon: "mdi-folder-open",
              text: this.$t("directory.index"),
              route: "/documentsidebar/directories/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-clipboard-account-outline",
              text: this.$t("organization.index"),
              route: "/documentsidebar/organization/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-clipboard-account-outline",
              text: this.$t("requestdoc.index"),
              route: "/documentsidebar/requestdoc/list",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-folder-open",
              text: this.$t("Document History"),
              route: "/documentsidebar/document-event/documents/list",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 0,
          titel: this.$t("Blanka shablonlari"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("Blanka shablonlari"),
              route: "/documentsidebar/blank-homepage",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-file-document-multiple-outline",
              text: this.$t("Blankalar ro'yxati"),
              route: "/documentsidebar/blank-templates/get-blank",
              color: "green",
              visible: true,
            },
          ],
        },
        {
          id: 0,
          titel: this.$t("Arxiv"),
          color: "#fbfcfe",
          visible: true,
          list: [
          {
              icon: "mdi-account-check",
              text: this.$t("Xodim"),
              route: "/documentsidebar/documents/list/archive/employee",
              color: "green",
              visible: true,
            },
            {
              icon: "mdi-source-fork",
              text: this.$t("Shtat"),
              route: "/documentsidebar/documents/list/archive/staff",
              color: "green",
              visible: true,
            },
          ],
        },
      ];
    },
    archiveLinks() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Asosiy oyna"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-archive-outline",
              text: this.$t("employee.name"),
              route: "/documents/list/archive/employee",
              visible: true,
            },
            {
              icon: "mdi-archive-outline",
              text: this.$t("position.index"),
              route: "/documents/list/archive/staff",
              visible: true,
            },
          ],
        },
      ];
    },
    centrumLinks() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Asosiy oyna"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-archive-outline",
              text: this.$t("Akt Report"),
              route: "/centrum-sidebar/act-report",
              visible: true,
              // visible: this.$store.getters.checkPermission("centrum"),
            },
            {
              icon: "mdi-archive-outline",
              text: this.$t("Attribute Report"),
              route: "/centrum-sidebar/attribute-report",
              visible: true,
              // visible: this.$store.getters.checkPermission("centrum"),
            },
            {
              icon: "mdi-archive-outline",
              text: this.$t("uploadFiles"),
              route: "/centrum-sidebar/import",
              visible: true,
              // visible: this.$store.getters.checkPermission("centrum"),
            },
            {
              icon: "mdi-archive-outline",
              text: this.$t("info"),
              route: "/centrum-sidebar/info",
              visible: true,
            },
            {
              icon: "mdi-archive-outline",
              text: this.$t("Shablon"),
              route: "/centrum-sidebar/shablon",
              visible: true,
            },
            {
              icon: "mdi-archive-outline",
              text: this.$t("Akt Report"),
              route: "/centrum-sidebar/report",
              visible: true,
              // visible: this.$store.getters.checkPermission("centrum"),
            },
            {
              icon: "mdi-file-document-outline",
              text: this.$t("Cancelled Acts"),
              route: "/documents/list/akt-cancel/0",
              visible: true,
            },
          ],
        },
      ];
    },
    getPersonControl() {
      this.items = [
        {
          id: 0,
          titel: this.$t("Управление персоналом"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("База данных сотрудников"),
              route: "/personcontrol/employees/list",
              color: "#00B950",
              visible: true,
            },
            {
              icon: "mdi-file-plus-outline",
              text: this.$t("Уволенные сотрудники"),
              route: "/personcontrol/dismissed-employees/list",
              color: "#00B950",
              visible: true,
            },
            {
              icon: "mdi-file-powerpoint-outline",
              text: this.$t("Критик ходимларни бошкариш"),
              route: "/linestopsidebar/linestop-tickets",
              color: "#00B950",
              visible: true,
            },
          ],
        },
        {
          id: 1,
          titel: this.$t("Организационный менеджмент"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-card-account-details-outline x-small",
              text: this.$t("staff.structures"),
              route: "/personcontrol/test-orgchart2/1",
              color: "#F8A300",
              visible: true, //this.$store.getters.checkPermission("shtat-korish"),
            },
            {
              icon: "mdi-card-account-details-outline x-small",
              text: this.$t("staff.index"),
              route: "/personcontrol/staffs/list",
              color: "#F8A300",
              visible: this.$store.getters.checkPermission("shtat-korish"),
            },
            {
              icon: "mdi-card-account-details-outline x-small",
              text: this.$t("staff.placement"),
              route: "/personcontrol/staffs/employeelist",
              color: "#F8A300",
              visible: this.$store.getters.checkPermission("shtat-korish"),
            },
            {
              icon: "mdi-card-account-details-outline x-small",
              text: this.$t("staff.vacancy"),
              route: "/personcontrol/staffs/vacancy",
              color: "#F8A300",
              visible: this.$store.getters.checkPermission("shtat-korish"),
            },
            {
              icon: "mdi-account-plus-outline",
              text: this.$t("critical.index"),
              color: "#F8A300",
              route: "/personcontrol/staff-criticals/list",
              visible: this.$store.getters.checkPermission("critical-index"),
            },

            {
              icon: "mdi-select-group x-small",
              text: this.$t("department.index"),
              route: "/personcontrol/departments/list",
              color: "#F8A300",
              visible: this.$store.getters.checkPermission("department-index"),
            },
            {
              icon: "mdi-select-group x-small",
              text: this.$t("Funksional bo'limlar"),
              route: "/personcontrol/departments/functionallist",
              color: "#F8A300",
              visible: this.$store.getters.checkPermission("department-index"),
            },
            {
              icon: "mdi-group x-small",
              text: this.$t("department.tree"),
              route: "/personcontrol/departments/tree",
              color: "#F8A300",
              visible: this.$store.getters.checkPermission(
                "department-index_tree"
              ),
            },
            // {
            //   icon: "mdi-ungroup",
            //   text: this.$t("message.departmentType"),
            //   route: "/department-types/list",
            //   visible: this.$store.getters.checkPermission("department_type-index"),
            // },
            // {
            //   icon: "mdi-account-network-outline",
            //   text: this.$t("message.positions"),
            //   route: "/personcontrol/positions/list",
            //   visible: this.$store.getters.checkPermission("position-index"),
            // },
            // {
            //   icon: "mdi-account-hard-hat",
            //   text: this.$t("message.positionType"),
            //   route: "/personcontrol/position-types/list",
            //   visible: this.$store.getters.checkPermission("position_type-index"),
            // },
            {
              icon: "mdi-file-alert-outline",
              text: this.$t("message.tariffScale"),
              route: "/personcontrol/tariff-scales/list",
              color: "#F8A300",
              visible:
                this.$store.getters.checkPermission("tariff_scale-index"),
            },
            // {
            //   icon: "mdi-cash-plus",
            //   text: this.$t("message.coefficient"),
            //   route: "/personcontrol/coefficients/list",
            //   visible: this.$store.getters.checkPermission("coefficient-index"),
            // },
            // {
            //   icon: "mdi-currency-usd",
            //   text: this.$t("message.currency"),
            //   route: "/personcontrol/currencies/list",
            //   visible: this.$store.getters.checkPermission("currency-index"),
            // },
            // {
            //   icon: "mdi-currency-usd",
            //   text: this.$t("message.currencyHistory"),
            //   route: "/personcontrol/currencies/history",
            //   visible: this.$store.getters.checkPermission("currency-index"),
            // },
            {
              icon: "mdi-office-building-outline",
              text: this.$t("message.accessDepartment"),
              route: "/personcontrol/access-departments/list",
              color: "#F8A300",
              visible: this.$store.getters.checkPermission(
                "access-department-index"
              ),
            },
            // {
            //   icon: "mdi-account-convert",
            //   text: this.$t("message.accessType"),
            //   route: "/personcontrol/access-types/list",
            //   color: "#F8A300",
            //   visible: this.$store.getters.checkPermission("access-type-index"),
            // },
            // {
            //   icon: "mdi-chart-timeline-variant",
            //   text: this.$t("message.ranges"),
            //   route: "/personcontrol/ranges/list",
            //   visible: this.$store.getters.checkPermission("range-index"),
            // },
            // {
            //   icon: "mdi-account-convert",
            //   text: this.$t("message.personalType"),
            //   route: "/personcontrol/personal-types/list",
            //   visible: this.$store.getters.checkPermission("personal_type-index"),
            // },
            // {
            //   icon: "mdi-wallet-outline",
            //   text: this.$t("message.expenceType"),
            //   route: "/personcontrol/expence-types/list",
            //   visible: this.$store.getters.checkPermission("expence_type-index"),
            // },
            // {
            //   icon: "mdi-email-alert-outline",
            //   text: this.$t("message.requirement"),
            //   route: "/personcontrol/requirements/list",
            //   visible: this.$store.getters.checkPermission("requirement-index"),
            // },
            // {
            //   icon: "mdi-email-variant",
            //   text: this.$t("message.requirementType"),
            //   route: "/personcontrol/requirement-types/list",
            //   visible: this.$store.getters.checkPermission(
            //     "requirement_type-index"
            //   ),
            // },
            // {
            //   icon: "mdi-webhook",
            //   text: this.$t("object_type.index"),
            //   route: "/personcontrol/object-types/list",
            //   visible: this.$store.getters.checkPermission("object_type-index"),
            // },
            // {
            //   icon: "mdi-account-key-outline",
            //   text: this.$t("sap_transaction.index"),
            //   route: "/personcontrol/sap-transaction/list",
            //   visible: this.$store.getters.checkPermission(
            //     "sap-transactions-index"
            //   ),
            // },
            // {
            //   icon: "mdi-clipboard-account-outline",
            //   text: this.$t("joint_venture.index"),
            //   route: "/personcontrol/joint-venture/list",
            //   visible: this.$store.getters.checkPermission("joint-ventures-index"),
            // },
            {
              icon: "mdi-folder-table-outline",
              text: this.$t("Тарифная шкала"),
              route: "/personcontrol",
              color: "#F8A300",
              visible: true,
            },
            {
              icon: "mdi-cog-outline",
              text: this.$t("Другие Настройки"),
              route: "/personcontrol/other-setting/list",
              color: "#6C869F",
              visible: true,
            },
          ],
        },
        {
          id: 2,
          titel: this.$t("Рекрутинг"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-none",
              text: this.$t("Подбор персонала"),
              route: "/linestopsidebar/linestop-tickets",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-none",
              text: this.$t("База резюме и объективки"),
              route: "/linestopsidebar/linestop-tickets",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-none",
              text: this.$t("Интеграция с jobs.uzauto.uz"),
              route: "/linestopsidebar/linestop-tickets",
              color: "#4CAF50",
              visible: true,
            },
            {
              icon: "mdi-none",
              text: this.$t("Другие настройки"),
              route: "/linestopsidebar/linestop-tickets",
              color: "#4CAF50",
              visible: true,
            },
          ],
        },
        {
          id: 3,
          titel: this.$t("СКУД и Табельный учёт"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-none",
              text: this.$t("Подбор персонала"),
              route: "/linestopsidebar/linestop-tickets",
              color: "#4CAF50",
              visible: true,
            },
          ],
        },
        {
          id: 4,
          titel: this.$t("salary"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-none",
              text: this.$t("Подбор персонала"),
              route: "/linestopsidebar/linestop-tickets",
              color: "#4CAF50",
              visible: true,
            },
          ],
        },
        {
          id: 4,
          titel: this.$t("Общие настройки"),
          color: "#fbfcfe",
          visible: true,
          list: [
            {
              icon: "mdi-none",
              text: this.$t("Подбор персонала"),
              route: "/linestopsidebar/linestop-tickets",
              color: "#4CAF50",
              visible: true,
            },
          ],
        },
      ];
    },

    getXisobot() {
      this.items = [];
    },
    getHujjatlatAylanmasi() {
      this.items = [];
    },
    isLastItem(index) {
      return index === this.items.length - 1;
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
<style scoped>
#edoSidebar {
  height: 93vh;
  top: 93px;
  max-height: calc(100% - 0px);
  transform: translateX(0%);
  width: 20%;
  background-color: rgb(241, 245, 248);
  position: fixed;
  padding: 20px;
}
::-webkit-scrollbar {
  width: 5px;
  display: none;
}

.no-border .v-list-item {
  border-bottom: none !important;
}

#edoContent {
  width: 80%;
  position: fixed;
  max-height: calc(80%);
  max-height: calc(85% - 0px);
  margin-left: 20%;
  background-color: rgb(241, 245, 248);
  padding: 0px 20px 20px 0px;
  margin-top: 20px;
  margin-bottom: 30px;
  overflow-y: auto;
  overflow-x: hidden;
}

.left-aside .v-list-item--link {
  border-bottom: 1px solid #e5e5e9;
}

.left-aside .v-list-item--link:hover {
  background: #f0f0f5;
}

.left-aside .v-list-group {
  border-bottom: 1px solid #e5e5e9;
  max-width: 90%;
}

#edonew-header {
  /* height: 90px !important; */
  left: 0 !important;
  background: #fff;
  /* box-shadow: none !important; */
  box-shadow: 0 5px 4px rgba(0, 0, 0, 0.2);
}

.left-header {
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  justify-content: center;
  width: 200px;
}
.center-header {
  left: 0 !important;
  height: 80px;
}
.v-slide-group__wrapper {
  height: 100px;
}
.center-header_text {
  text-align: center;
  width: auto;
  line-height: 1.4em;
  overflow: hidden;
  text-decoration: inherit;
  -o-text-overflow: ellipsis;
  text-overflow: ellipsis;
  white-space: nowrap;
  cursor: pointer;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 12px;
  color: #474747 !important;
}
.right-header {
  position: absolute;
  width: 5%;
  right: 0 !important;
}
.v-menu {
  background-color: red !important;
}
.zIndex {
  z-index: 0 !important;
}
#edonew-sidebar {
  top: 90px !important;
}

.footer {
  background: #e7e5dd;
  bottom: 0;
  height: 38px;
  left: 0;
  position: fixed;
  width: 100%;
  z-index: 1000;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: start;
  -ms-flex-align: start;
  align-items: flex-start;
}

.left-footer {
  width: 200px;
  padding: 6px;
  text-align: center;
}

.left-footer .left-footer_button i {
  color: #0298e9;
  box-sizing: border-box;
  font-size: 24px;
  display: inline-block;
  height: 24px;
  line-height: 1;
  position: relative;
  width: 24px;
}

.right-footer {
  width: 484px;
  background-color: #e7e5dd;
  padding: 0 26px;
  position: relative;
  white-space: nowrap;
  z-index: 999;
  height: 38px;
  display: flex;
  align-items: center;
}

.right-footer span {
  display: inline-block;
  padding: 0 12px;
  text-align: center;
}

.greyscale-image {
  cursor: pointer;
  transform: scale(1.1);
  transition: transform 0.3s ease;
}

.ungreyscale-image {
  filter: grayscale(100%);
}
.theme--light.v-card {
  background-color: #ffffff;
  color: rgba(255, 255, 255, 0.87);
}

.v-toolbar__content {
  padding: 0 !important;
}
.center-footer {
  line-height: 1.4em;
  overflow: hidden;
  text-decoration: inherit;
  -o-text-overflow: ellipsis;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 14px;
  text-align: center;
  margin-top: 10px;
}
.side-bar-textstyle {
  line-height: 1.4em;
  overflow: hidden;
  text-decoration: inherit;
  -o-text-overflow: ellipsis;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 12px;
  text-align: left;
  margin: 0;
  padding: 0;
}
.v-list {
  display: block;
  padding: 0px 0;
  position: static;
  -webkit-transition: -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
  transition: -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
  transition: box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
  transition: box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1),
    -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
}
.toggleClass_open {
  top: 70px;
  position: fixed;
  z-index: 990;
}
.toggleClass_close {
  position: fixed;
  z-index: 990;
  top: -12px;
}
.prog_bar_open {
  z-index: 7;
  width: 100%;
  position: fixed;
  top: 0px;
}
.prog_bar_close {
  z-index: 7;
  width: 100%;
  position: fixed;
  top: 90px;
}

.mainTopPadding {
  padding-top: 0px !important;
}
.tab-btn {
  white-space: nowrap; /* This prevents text from wrapping */
}
.avatar-image {
  cursor: pointer;
  transform: scale(1.1);
  transition: transform 0.3s ease;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

.unavatar-image {
  filter: none;
}
.avatarNavbar {
  border-radius: 90%;
}
/* width */
::-webkit-scrollbar {
  width: 5px;
}
#navbar {
  background: #f5fafa;
}
/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
/* Handle */
::-webkit-scrollbar-thumb {
  background: rgb(50, 133, 189);
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
.nav-menu {
  line-height: 1.4em;
  overflow: hidden;
  text-decoration: inherit;
  -o-text-overflow: ellipsis;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 12px;
  text-align: left;
  margin-left: -20px;
  color: black !important;
}

.tab-icon {
  font-size: 18px !important;
  color: black !important;
}

.v-menu__content {
  margin-left: -30px;
  margin-top: 1px;
  /* background-color: rgba(163, 24, 24, 0.0); */
}
.text-truncate {
  color: #fff !important;
  font-size: 18px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  text-align: center;
}
.v-item--active,
.v-list-item--active {
  color: red;
}
.list-group .v-list-item--active {
  color: red;
}

.list-group .list-icons i {
  color: #00b950;
  font-size: 20px;
}

.list-group .list-text div {
  color: #6c869f;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.list-group .list-text span {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-navigation-drawer__border {
  border: none !important;
}
.v-navigation-drawer > .v-navigation-drawer__border {
  display: none;
}
.active-link {
  background-color: #e0e0e0;
  color: #000;
}
</style>
